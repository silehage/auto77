<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v2\AssetController;

Route::middleware(['auth:sanctum', 'auth.admin'])->group(function () {
   Route::post('assets', [AssetController::class, 'uploadFile']);
   Route::delete('assets/{id}', [AssetController::class, 'deleteFile']);
});
