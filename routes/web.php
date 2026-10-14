<?php

use App\Http\Controllers\Web\AuthPageController;
use App\Http\Controllers\Web\CategoryPageController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProductPageController;
use App\Http\Controllers\Web\SalePageController;
use App\Http\Controllers\Web\TaxPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthPageController::class, 'login'])->name('login.page');
Route::get('/register', [AuthPageController::class, 'register'])->name('register.page');

Route::get('/products', [ProductPageController::class, 'index'])->name('products.index');
Route::get('/categories', [CategoryPageController::class, 'index'])->name('categories.index');
Route::get('/sales', [SalePageController::class, 'index'])->name('sales.index');
Route::get('/tax', [TaxPageController::class, 'index'])->name('tax.index');
