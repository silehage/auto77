<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use Tripay;

class TripayController extends Controller
{
    protected $privateKey;

    public function __construct()
    {
        $this->apiKey = config('tripay.api_key');
        
    }
    public function getPaymentChanels()
    {
        
        if(Cache::has('tripay_payment_chanels')) {

            return response()->json([
                'success' => true,
                'data' => Cache::get('tripay_payment_chanels')
            ]);
            
            
        } else {
            
            $json = Tripay::paymentChanels();

            $obj = json_decode($json);
    
            if($obj->success == true) {

                Cache::put('tripay_payment_chanels', $obj->data, now()->addHours(6));

                return response()->json($obj);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => $obj->message
                ]);
            }

        }

    }

    public function getTransactionDetail($ref)
    {
        $payload = [
            'reference' => $ref
          ];
      
        $json = Tripay::transactionDetail($payload);

        $obj = json_decode($json);

        if($obj->success == true) {

            return response()->json($obj);

        } else {

            return response()->json([
                'success' => false,
                'message' => $obj->message
            ]);
        }

    }

    public function calculatorFee(Request $request)
    {
        $payload = $request->validate([
            'code' => 'required',
            'amount' => 'required'
        ]);

        $json = Tripay::calculatorFee($payload);

        $obj = json_decode($json);

        if($obj->success == true) {

            return response()->json($obj);

        } else {

            return response()->json([
                'success' => false,
                'message' => $obj->message
            ]);
        }

    }
    public function callback(Request $request)
    {
 
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE') ?? '';

        $json = $request->getContent();

        $data = json_decode($json);

        $signature = hash_hmac('sha256', $json, $this->privateKey);

        if( $callbackSignature !== $signature ) {
            return "Invalid Signature"; // signature tidak valid, hentikan proses
        }
            
        $event = $request->server('HTTP_X_CALLBACK_EVENT');
        
        if( $event == 'payment_status' )
        {
            $merchantRef = $data->merchant_ref;
            
            $order = Order::where('order_ref', $merchantRef)
                ->where('order_status', 'UNPAID')
                ->first();
            
            
            if( !$order ) {
                
                return "Invoice not found or current status is not UNPAID";
            }

            $transaction = $order->transaction;

            if ((int) $data->total_amount !== (int) $order->order_total) {
                return 'Invalid amount';
            }
  

            if( $data->status == 'PAID' ) // handle status PAID
            {
                $order->update([
                    'order_status'	=> 'PAID',
                ]);
                
                $transaction->update([
                    'status' => 'PAID',
                    'paid_at' => Carbon::createFromTimestamp($data->paid_at),
                    'note' => $data->note
                ]);


                return response()->json(['success' => true ]);
            }
            elseif( $data->status == 'EXPIRED' ) // handle status EXPIRED
            {
                $order->update([
                    'order_status'	=> 'CANCELED',
                ]);

                $transaction->update([
                    'status' => 'CANCELED',
                    'note' => $data->note
                ]);

                $this->resetStock($order);


                return response()->json(['success' => true ]);
            }
            elseif( $data->status == 'FAILED' ) // handle status FAILED
            {
                $order->update([
                    'order_status'	=> 'CANCELED',
                ]);

                $transaction->update([
                    'status' => 'CANCELED',
                    'note' => $data->note
                ]);

                $this->resetStock($order);

                return response()->json(['success' => true ]);
            }
        }

        return "No action was taken";
    }

    protected function resetStock($order) 
    {
        foreach($order->items as $item) {

            DB::table('products')->where('sku', $item->sku)->increment('stock', intval($item->quantity));
            DB::table('product_variant_values')->where('item_sku', $item->sku)->increment('item_stock', intval($item->quantity));

        }
    }

}
