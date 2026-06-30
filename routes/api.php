<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\CartController;
use App\Http\Controllers\V1\TransactionController;

Route::post('/cart/add', [CartController::class, 'save'])->name('api.cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('api.cart.update');

// CartController::class previously had a method `removeItem`, but the route
// name + this binding call `remove` — controller has been renamed to `remove`
// to match. If you'd rather keep `removeItem`, change this line instead.
Route::delete('/cart/remove', [CartController::class, 'remove'])->name('api.cart.remove');

Route::post('/cart', [CartController::class, 'getCart'])->name('api.cart.get');
Route::post('/payment/verify', [TransactionController::class, 'verifyPay'])->name('payment.verify');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
