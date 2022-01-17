<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{

    public function show()
    {
        return response([
            'success' => true,
            'results' => Config::first()
        ], 200);
    }
    public function adminConfig()
    {
        $hiddenFields = [
            'rajaongkir_apikey',
            'tripay_api_key',
            'tripay_private_key',
            'tripay_merchant_code',
            'telegram_bot_token',
            'telegram_user_id',
        ];

        return response([
            'success' => true,
            'results' => Config::first()->makeVisible($hiddenFields)
        ], 200);
    }

    public function update(Request $request)
    {
        $config = Config::first();

        $config->update($request->all());

        // $config->update([
        //     'theme' => $request->theme,
        //     'theme_color' => $request->theme_color,
        //     'home_view_mode' => $request->home_view_mode,
        //     'product_view_mode' => $request->product_view_mode,
        //     'rajaongkir_type' => $request->rajaongkir_type,
        //     'rajaongkir_apikey' => $request->rajaongkir_apikey,
        //     'rajaongkir_couriers' => $request->rajaongkir_couriers,
        //     'warehouse_id' => $request->warehouse_id,
        //     'warehouse_address' => $request->warehouse_address,
        //     'tripay_api_key' => $request->tripay_api_key,
        //     'tripay_private_key' => $request->tripay_private_key,
        //     'tripay_mode' => $request->tripay_mode,
        //     'tripay_merchant_code' => $request->tripay_merchant_code,
        //     'telegram_bot_token' => $request->telegram_bot_token,
        //     'telegram_user_id' => $request->telegram_user_id,
        //     'is_notifypro' => $request->boolean('is_notifypro'),
        //     'is_payment_gateway' => $request->boolean('is_payment_gateway'),
        //     'is_guest_checkout' => $request->boolean('is_guest_checkout'),
        //     'is_whatsapp_checkout' => $request->boolean('is_whatsapp_checkout'),
        //     'notifypro_interval' => $request->notifypro_interval,
        //     'notifypro_timeout' => $request->notifypro_timeout,
        //     'cod_list' => $request->cod_list,
        // ]);

    Cache::forget('shop_config');
    Cache::forget('admin_config');

        return response([
            'success' => true,
            'results' => null
        ], 200);
    }

}
