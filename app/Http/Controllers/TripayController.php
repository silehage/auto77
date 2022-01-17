<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TripayController extends Controller
{
    protected $apiKey, $privateKey, $merchantCode, $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('tripay.api_key');
        $this->privateKey = config('tripay.private_key');
        $this->merchantCode = config('tripay.merchant_code');

        $this->apiUrl = config('tripay.api_sanbox_url');

        if(config('tripay.mode') == 'production') {
            $this->apiUrl = config('tripay.api_url');
        }
        
    }
    public function getPaymentChanels()
    {
        $route ='merchant/payment-channel';

        if(Cache::get('tripay_payment_chanels')) {

            return Cache::get('tripay_payment_chanels');
        } else {
            try {
                $response = Http::acceptJson()->withToken($this->apiKey)->get($this->apiUrl . $route);
        
                if($response->ok()) {

                    Cache::put('tripay_payment_chanels', $response->json(), Carbon::now()->addDays(2));

                   return response()->json($response->json(), 200);
                }

                return response()->json(null, 400);
                
            } catch (\Throwable $th) {

                return response()->json(null, 400);
            }
        }

    }
    public function getPaymentChanel($method)
    {
        $route ='merchant/payment-channel';

        $response = Http::acceptJson()->withToken($this->apiKey)->get($this->apiUrl . $route, [
            'code' => $method
        ]);
        
        if($response->ok()) {

            return response()->json($response->json(), 200);

        } else {
            return response()->json(null, 400);
        }
        
    }

    public function createTransaction($order, $request)
    {
          $data = [
                'method'            => $request->payment_method,
                'merchant_ref'      => $order->order_ref,
                'amount'            => $order->order_total,
                'customer_name'     => $order->customer_name,
                'customer_email'    => $order->customer_email,
                'customer_phone'    => $order->customer_whatsapp,
                'order_items'       => $request->items,
                "callback_url"      => route('tripay.callback'),
                'expired_time'      => (time()+(24*60*60)), // 24 jam
                'signature'         => $this->getSignature($order)
            ];

          $route = 'transaction/create';
         
          $response = Http::withToken($this->apiKey)->post($this->apiUrl . $route, $data);

          return $response->json();


          if($response->ok()) {
              return $response->json();
          } else {
              return null;
          }
          
        
    }
    public function getTransactionDetail($ref)
    {
        $route = 'transaction/detail';

        if(!$ref) return null;

        $response = Http::withToken($this->apiKey)->get($this->apiUrl . $route,[
            'reference' => $ref
        ]);

        if($response->ok()) {
            return $response->json();
        } else {
            return null;
        }
    }
    public function callback(Request $request)
    {
 
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE') ?? '';

        $json = $request->getContent();

        $signature = hash_hmac('sha256', $json, $this->privateKey);

        if( $callbackSignature !== $signature ) {
            return "Invalid Signature"; // signature tidak valid, hentikan proses
        }
            
        $event = $request->server('HTTP_X_CALLBACK_EVENT');
        
        if( $event == 'payment_status' )
        {
            $merchantRef = $request->merchant_ref;
            
            $invoice = Order::where('order_ref', $merchantRef)
            ->where('order_status', 'UNPAID')
            ->first();
            
            
            if( !$invoice ) {
                
                return "Invoice not found or current status is not UNPAID";
            }

            $transaction = $invoice->transaction;

            if ((int) $request->total_amount !== (int) $invoice->order_total) {
                return 'Invalid amount';
            }
  

            if( $request->status == 'PAID' ) // handle status PAID
            {
                $invoice->update([
                    'order_status'	=> 'PAID',
                ]);
                
                $transaction->update([
                    'status' => 'PAID',
                    'paid_at' => Carbon::createFromTimestamp($request->paid_at),
                    'note' => $request->note
                ]);

                foreach($invoice->items as $item) {
                    DB::table('products')->where('id', $item->product_id)->decrement('stock', $item->quantity);
                }


                return response()->json([
                    'success' => true
                    ]);
            }
            elseif( $request->status == 'EXPIRED' ) // handle status EXPIRED
            {
                $invoice->update([
                    'order_status'	=> 'CANCELED',
                ]);

                $transaction->update([
                    'status' => 'CANCELED',
                    'note' => $request->note
                ]);


                return response()->json([
                    'success' => true
                    ]);
            }
            elseif( $request->status == 'FAILED' ) // handle status FAILED
            {
                $invoice->update([
                    'order_status'	=> 'CANCELED',
                ]);

                $transaction->update([
                    'status' => 'CANCELED',
                    'note' => $request->note
                ]);

                return response()->json([
                    'success' => true
                    ]);
            }
        }

        return "No action was taken";
    }
    protected function getSignature($order)
    {
        return hash_hmac('sha256', $this->merchantCode.$order->order_ref.$order->order_total, $this->privateKey);
    }
}
