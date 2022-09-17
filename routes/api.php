<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TripayController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\FrontApiController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PasswordResetController;

Route::middleware(['auth:sanctum', 'auth.admin'])->group(function() {

    Route::get('userList', [UserController::class, 'userList']);
    Route::get('findUser/{key}', [UserController::class, 'findUser']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);

    Route::delete('product/{id}', [ProductController::class, 'destroy']);
    Route::post('product/update', [ProductController::class, 'update']);
    Route::post('product', [ProductController::class, 'store']);
    Route::get('getAdminProducts', [ProductController::class, 'getAdminProducts']);

    Route::post('sliders', [SliderController::class, 'store']);
    Route::delete('sliders/{id}', [SliderController::class, 'destroy']);
    Route::post('update-slider-weight', [SliderController::class, 'updateSliderWeight']);

    Route::apiResource('categories', CategoryController::class)->only('store', 'update', 'destroy');

    Route::post('shop', [StoreController::class, 'update']);

    Route::apiResource('posts', PostController::class)->only('store', 'update', 'destroy');

    Route::apiResource('blocks', BlockController::class)->only('store', 'update', 'destroy');
    // Update
    Route::get('update', [UpdateController::class, 'overview']);
    Route::post('update', [UpdateController::class, 'update']);  
    Route::post('clearCache', [UpdateController::class, 'clearCache']);  
    

    Route::post('toggleProductPromo', [ProductController::class, 'toggleProductPromo']);
    Route::get('getProductPromo/{promoId}', [ProductController::class, 'getProductPromo']);
    Route::get('findNotDiscountProduct', [ProductController::class, 'findNotDiscountProduct']);
    Route::get('searchAdminProducts/{key}', [ProductController::class, 'searchAdminProducts']);
    // new
    Route::apiResource('promo', PromoController::class);
    Route::get('getPromoDetail/{id}', [PromoController::class, 'getPromoDetail']);
    Route::post('removeProductPromo', [PromoController::class, 'removeProductPromo']);

    Route::post('submitProductPromo', [ProductController::class, 'submitProductPromo']);
    Route::get('getProductPromo/{promoId}', [ProductController::class, 'getProductPromo']);
    Route::get('findProductWithoutPromo/{key}', [ProductController::class, 'findProductWithoutPromo']);

    Route::apiResource('customer_service', CustomerServiceController::class);
    
});

Route::middleware('auth:sanctum')->group(function() {

    Route::get('adminConfig', [ConfigController::class, 'adminConfig']);
    Route::post('config', [ConfigController::class, 'update']);
    
    Route::get('user', [UserController::class, 'index']);
    Route::post('user/logout', [UserController::class, 'logout']);
    Route::post('user/update', [UserController::class, 'update']);
    Route::post('refreshToken', [UserController::class, 'refreshToken']);
    
});

Route::middleware(['throttle:auth'])->group(function() {
    
    Route::post('user/login', [UserController::class, 'login']);
    Route::post('user/register', [UserController::class, 'register']);
    Route::post('user/addNewUser', [UserController::class, 'addNewUser']);
    Route::post('user/update', [UserController::class, 'update']);
    Route::post('requestPasswordToken', [PasswordResetController::class, 'requestPasswordToken']);
    Route::get('validateToken/{token}', [PasswordResetController::class, 'validateToken']);
    Route::post('resetPassword', [PasswordResetController::class, 'resetPassword']);

});

Route::apiResource('posts', PostController::class)->only('index', 'show');
Route::get('post/{slug}', [PostController::class, 'getPostBySlug']);

Route::get('product/{slug}', [ProductController::class, 'show']);
Route::get('productById/{id}', [ProductController::class, 'productById']);

Route::post('addProductReview', [ProductController::class, 'addProductReview']);
Route::get('loadProductReview/{id}', [ProductController::class, 'loadProductReview']);
Route::get('products', [ProductController::class, 'index']);
Route::post('getProductsFavorites', [ProductController::class, 'getProductsFavorites']);
Route::get('search/{key}', [ProductController::class, 'search']);
Route::get('getProductsByCategory/{id}', [ProductController::class, 'getProductsByCategory']);

Route::get('sliders', [SliderController::class, 'index']);
Route::get('shop', [StoreController::class, 'index']);

Route::get('category', [CategoryController::class, 'index']);
Route::get('category/{id}', [CategoryController::class, 'show']);

Route::get('blocks',[BlockController::class, 'index']);
Route::get('blocks/{id}',[BlockController::class, 'show']);

Route::get('config',[ConfigController::class, 'show']);

Route::post('sendNotify', [NotifyController::class, 'sendNotify']);

Route::get('getInitialData', [FrontApiController::class, 'home']);

Route::get('getCustomerService', [CustomerServiceController::class, 'getCustomerService']);