<?php

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProductController;
use App\Http\Resources\ProductCollection;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('test', [ProductController::class, 'index']);
Route::get('show/{id}', [ProductController::class, 'show']);

Route::get('/', [FrontController::class, 'homepage']);
Route::get('products', [FrontController::class, 'products']);
Route::get('products/category/{category}', [FrontController::class, 'productCategory']);
Route::get('product/{slug}', [FrontController::class, 'productDetail']);
Route::get('posts', [FrontController::class, 'postIndex']);
Route::get('post/{slug}', [FrontController::class, 'postDetail']);
Route::get('clear-cache', [FrontController::class, 'clearCache']);
Route::get('/{any}', [FrontController::class, 'any'])->where('any','^(?!api).*$');

function set($data)
{
  $pricing = [
    'default_price' => $data->price,
    'current_price' => $data->price,
    'discount_value' => 0,
    'discount_percent' => 0,
    'is_discount' => false 
];

$disc = null;

if($data->discount_id) {
    $disc = $data->discount;
} elseif($data->promote_id) {
    $disc = $data->promote->discount;
}

if($disc) {

    $pricing['is_discount'] = true;

    $discValue = 0;
    

    if($disc->unit == 'percent') {

        $discValue = ($data->price*$disc->value) / 100;

        $pricing['current_price'] = $data->price - ($data->price*$disc->value / 100);
        $pricing['discount_percent'] = $disc->value;
        
     } else{

         $discValue = $disc->value;
         $pricing['current_price'] = $data->price - (int) $disc->value;
         $pricing['discount_percent'] = number_format(((int)$disc->value / $data->price)*100, 1);

    }

    $pricing['discount_value'] = $discValue;
 }

return $pricing;
}
