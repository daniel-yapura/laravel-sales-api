<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Middleware\LogRequests;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('jwt.auth')->group(function () {
    Route::apiResource('/product', ProductController::class)
        ->middleware(LogRequests::class);

    Route::get('/info/tax/{id}', [InfoController::class, 'iva']);

    Route::get('/sale', [SaleController::class, 'get']);
    Route::post('/sale', [SaleController::class, 'create']);

    Route::get('who', [AuthController::class, 'who']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});
