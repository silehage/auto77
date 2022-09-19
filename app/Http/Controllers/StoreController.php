<?php

namespace App\Http\Controllers;


use App\Models\Config;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Store as Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;

class StoreController extends Controller
{

    public function index()
    {
        $shop = Cache::rememberForever('shop', function () {
            return Shop::first();
        });
        $config = Cache::rememberForever('shop_config', function () {
            return Config::first();
        });
        return response([
            'success' => true,
            'results' => [
                'shop' => $shop,
                'config' => $config
            ]
        ], 200);
    }

    public function update(Request $request)
    {
        
        DB::beginTransaction();

        try {
            $shop = Shop::first();

            $path = public_path('/upload/images');

            if(! File::exists($path)) {
                File::makeDirectory($path, 0755, true, true);
            }

            $iconPath = public_path('/icon');

            if (!File::isDirectory($iconPath)) {
    
                File::makeDirectory($iconPath, 0777, true, true);
            }

            $shop->name = $request->name;
            $shop->phone = $request->phone;
            if(!$shop->slug) {
                $shop->slug = $request->name? Str::slug($request->name) : null;
            }
            $shop->address = $request->address;
            $shop->description = $request->description;
            $shop->slogan = $request->slogan;
            $shop->google_play_url = $request->google_play_url;

            if($file = $request->file('logo')) {
                File::delete(
                    'icon/icon/icon-large.png',
                    'icon/icon/icon-medium.png',
                );
                if($shop->logo_path) {
                    File::delete($shop->logo_path);
                }
           
                $rawFile = Image::make($file);

                $rawFile->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('png')->save('icon/icon-large.png');

                $rawFile->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('png')->save('icon/icon-medium.png');

                $filepath = 'upload/images/' . Str::random(20) . '.png'; 

                $rawFile->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->crop(300, 80)->encode('png')->save($filepath);

                $shop->logo_path = $filepath;

                
            }
            if($file = $request->file('favicon')) {
           
                $rawIcon = Image::make($file);

                File::delete(
                    'icon/icon-96x96.png',
                    'icon/favicon.png',
                );

                $rawIcon->resize(120,120)->encode('png')->save('icon/icon-96x96.png');
                $rawIcon->resize(64,64)->encode('png')->save('icon/favicon.png'); 

                $shop->favicon = 'icon/favicon.png';
                
            }

            $shop->save();

            DB::commit();
            Cache::forget('shop');

            return response([
                'success' => true,
                'results' => $shop
            ], 200);
            
        } catch (\Throwable $th) {

            DB::rollBack();

            return response([
                'success' => false,
                'message' => $th->getMessage(),
                'results' => null
            ], 500);
        }

        
    }
}
