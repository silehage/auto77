<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;

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

Route::get('/', [FrontController::class, 'homepage']);
Route::get('products', [FrontController::class, 'products']);
Route::get('products/category/{category}', [FrontController::class, 'productCategory'])->name('product.bycategory');
Route::get('product/{slug}', [FrontController::class, 'productDetail'])->name('product.show');
Route::get('posts', [FrontController::class, 'postIndex']);
Route::get('post/{slug}', [FrontController::class, 'postDetail'])->name('post.show');
Route::get('sitemap.xml', [FrontController::class, 'sitemap']);
Route::get('p/invoice/{id}', [FrontController::class, 'showInvoice']);
Route::get('clear-cache', [FrontController::class, 'clearCache']);
Route::get('/{any}', [FrontController::class, 'any'])->where('any', '^(?!api).*$');
