<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v2\ProductController;

Route::middleware(['auth:sanctum', 'auth.admin'])->group(function () {

   Route::delete('product/{id}', [ProductController::class, 'destroy']);
   Route::post('product/update', [ProductController::class, 'update']);
   Route::post('product', [ProductController::class, 'store']);
   Route::get('getAdminProducts', [ProductController::class, 'getAdminProducts']);

   Route::post('toggleProductPromo', [ProductController::class, 'toggleProductPromo']);
   Route::get('getProductPromo/{promoId}', [ProductController::class, 'getProductPromo']);
   Route::get('findNotDiscountProduct', [ProductController::class, 'findNotDiscountProduct']);
   Route::get('searchAdminProducts/{key}', [ProductController::class, 'searchAdminProducts']);

   Route::post('submitProductPromo', [ProductController::class, 'submitProductPromo']);
   Route::get('getProductPromo/{promoId}', [ProductController::class, 'getProductPromo']);
   Route::get('findProductWithoutPromo/{key}', [ProductController::class, 'findProductWithoutPromo']);
});
Route::get('product/{slug}', [ProductController::class, 'show']);
Route::get('productById/{id}', [ProductController::class, 'productById']);
Route::post('addProductReview', [ProductController::class, 'addProductReview']);
Route::get('loadProductReview/{id}', [ProductController::class, 'loadProductReview']);
Route::get('products', [ProductController::class, 'index']);
Route::post('getProductsFavorites', [ProductController::class, 'getProductsFavorites']);
Route::get('search/{key}', [ProductController::class, 'search']);
Route::get('getProductsByCategory/{id}', [ProductController::class, 'getProductsByCategory']);
