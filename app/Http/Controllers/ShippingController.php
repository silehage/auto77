<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\RajaongkirController;

class ShippingController extends Controller
{
    public function getProvince()
    {

        $data = null;
        if(Cache::has('provinces')) {
            $data = Cache::get('provinces');

            return response()->json($data, 200);

        } else {
            $rajaongkir = new RajaongkirController();

            $json = $rajaongkir->getProvince();

            if($json) {

                Cache::forever('provinces', $json);
                return response()->json($json, 200);

            } else {

                return response()->json(null, 400);
            }


        }

    }
    
    public function getCity($province_id)
    {

        if(Cache::has('city_by_'. $province_id)) {

           $data = Cache::get('city_by_'. $province_id);
           return response()->json($data, 200);

        } else { 
            $rajaongkir = new RajaongkirController();

            $json = $rajaongkir->getCity($province_id);

            if($json) {

                Cache::forever('city_by_'. $province_id, $json);

                return response()->json($json, 200);

            } else {

                return response()->json(null, 400);
            }

        }    

    }
    public function getSubdistrict($city_id)
    {

        if(Cache::has('subdistrict_by_'. $city_id)) {
            $data = Cache::get('subdistrict_by_'. $city_id);

            return response()->json($data, 200);

        } else { 

            $rajaongkir = new RajaongkirController();

            $json = $rajaongkir->getSubdistrict($city_id);

            if($json) {

                Cache::forever('subdistrict_by_'. $city_id, $json);

                return response()->json($json, 200);

            } else {

                return response()->json(null, 400);
            }
        
        }

    }
    public function getCost(Request $request)
    {
       $rajaongkir = new RajaongkirController();

       $json = $rajaongkir->getCost($request);

       if($json) {

        return response()->json($json, 200);

        } else {

            return response()->json(null, 400);
        }
    }
}
