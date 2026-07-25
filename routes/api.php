<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\CartController;
use App\Http\Controllers\V1\TransactionController;
use App\Http\Controllers\PaystackWebhookController;

Route::post('/cart/add', [CartController::class, 'save'])->name('api.cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('api.cart.update');
Route::post('/paystack/webhook', [PaystackWebhookController::class, 'handle']);
Route::get('/load/{cart_id?}', [CartController::class, 'loadCartView'])->name('cart.view');
// CartController::class previously had a method `removeItem`, but the route
// name + this binding call `remove` — controller has been renamed to `remove`
// to match. If you'd rather keep `removeItem`, change this line instead.
Route::post('/cart/remove', [CartController::class, 'remove'])->name('api.cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clearCart'])->name('api.cart.clear');
Route::post('/cart/size', [CartController::class, 'updateSize'])->name('cart.update.size');
Route::post('/cart', [CartController::class, 'getCart'])->name('api.cart.get');
Route::post('/payment/verify', [TransactionController::class, 'verifyPay'])->name('payment.verify');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
