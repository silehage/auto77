<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RajaongkirController extends Controller
{
    protected $apiKey, $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('rajaongkir.api_key');
        
        $this->apiUrl = config('rajaongkir.api_url');

        if(config('rajaongkir.account_type') == 'pro') {
            $this->apiUrl = config('rajaongkir.api_url_pro');
        } 
    }

    public function getProvince()
    {
        $route = 'province';

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($this->apiUrl . $route);

            if($response->ok()) {

                return $response->json(); 

            } else {

                return null;
            }
        } catch (\Throwable $th) {

            return null;
        }

    }
    public function getCity($provinceId)
    {

        $route = 'city?province=' . $provinceId;

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($this->apiUrl . $route);

            if($response->ok()) {
                return $response->json(); 
            } else {
                return null;
            }

        } catch (\Throwable $th) {
            return null;
        }

        
    }
    public function getSubdistrict($cityId)
    {
        $route = 'subdistrict?city=' . $cityId;

        try {

            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($this->apiUrl . $route);
    
            if($response->ok()) {
                return $response->json(); 
            } else {
                return null;
            }
            
        } catch (\Throwable $th) {

            return null;
        }

    }

    public function getCost($data)
    {
        $route = 'cost';
        $temp = 'ongkir_'.$data->destination.$data->weight.$data->courier;

        if(Cache::get($temp)) {
           return Cache::get($temp);
        } else {

            try {
                $response = Http::withHeaders([
                    'key' => $this->apiKey
                ])->post($this->apiUrl . $route, [
                    'origin' => $data->origin,
                    'destination' => $data->destination,
                    'weight' => $data->weight,
                    'courier' => trim($data->courier),
                ]);
        
                if($response->ok()) {
                    
                    Cache::put($temp, $response->json(), now()->addHours(3));

                    return $response->json();
                } else {
                    return null;
                }
            } catch (\Throwable $th) {
                return null;
            }
        }

    }
}
