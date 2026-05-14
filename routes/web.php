<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AdminController;
use App\Http\Controllers\V1\ProductCategory;
use App\Http\Controllers\V1\ProductController;
use App\Http\Controllers\V1\StoreDetailsController;
use App\Http\Controllers\V1\SalesCategory;
use App\Http\Controllers\V1\WebsiteController;
use App\Http\Controllers\V1\CartController;
use App\Http\Controllers\V1\TransactionController;

Route::get('/', [WebsiteController::class, 'index'])->name('home');
Route::get('/login', [AdminController::class, 'showLoginform'])->name('login');
Route::post('/payment/initiate', [TransactionController::class, 'initiatePay'])->name('payment.initiate');
Route::get('/payment/checkout/{cart_token}', [TransactionController::class, 'checkout'])->name('payment.checkout');
Route::post('/payment/checkout', [TransactionController::class, 'initiatePay'])->name('checkout.submit');

Route::get('/get/category/products/{category_id}', [WebsiteController::class, 'getproductsByCategory'])->name('category.products');
 Route::prefix('cart')->group(function () {
     Route::get('/load/{cart_id}', [CartController::class, 'loadCartView'])->name('cart.view');


 });
Route::prefix('admin')->group(function () {
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/check', [AdminController::class, 'showLoginform'])->name('admin.login');
    Route::post('/check', [AdminController::class, 'login'])->name('admin.login.submit');
    Route::get('/change/password', [AdminController::class, 'changePassword'])->name('password.request');
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::prefix('product-category')->group(function () {              
            Route::get('/create', [ProductCategory::class, 'LoadCreateCategory'])->name('admin.product-category.create.load');
            Route::get('/', [ProductCategory::class, 'ViewProductCategories'])->name('admin.product-category.index');
            Route::post('/create', [ProductCategory::class, 'CreateProductCategory'])->name('admin.product-category.store');
            Route::get('/edit/{id}', [ProductCategory::class, 'EditProductCategory'])->name('admin.product-category.edit');
            Route::put('/update/{id}', [ProductCategory::class, 'UpdateProductCategory'])->name('admin.product-category.update');
            Route::delete('/delete/{id}', [ProductCategory::class, 'DeleteProductCategory'])->name('admin.product-category.destroy');

    });
    Route::prefix('product')->group(function () {
        Route::get('/create', [ProductController::class, 'loadCreateProduct'])->name('admin.product.create.load');
        Route::get('/', [ProductController::class, 'index'])->name('admin.product.index');
        Route::post('/create', [ProductController::class, 'create'])->name('admin.product.store');
        Route::get('/edit/{id}', [ProductController::class, 'ProductById'])->name('admin.product.edit');
        Route::put('/update/{id}', [ProductController::class, 'UpdateProduct'])->name('admin.product.update');
        Route::delete('/delete/{id}', [ProductController::class, 'DeleteProduct'])->name('admin.product.destroy');
        Route::get('/product/image/delete/{id}', [ProductController::class, 'deleteImage']) ->name('admin.product.delete-image');

    });
    Route::prefix('store-details')->group(function () {
        Route::get('/', [StoreDetailsController::class, 'index'])->name('admin.store_details.index');
        Route::post('/store', [StoreDetailsController::class, 'store'])->name('admin.store_details.store');
        Route::get('/edit/{id}', [StoreDetailsController::class, 'edit'])->name('admin.store_details.edit');
        Route::put('/update}', [StoreDetailsController::class, 'update'])->name('admin.store_details.update');
        Route::delete('/delete', [StoreDetailsController::class, 'destroy'])->name('admin.store_details.destroy');
        Route::get('/show', [StoreDetailsController::class, 'show'])->name('admin.store_details.show');
        Route::get('/create', [StoreDetailsController::class, 'loadCreateForm'])->name('admin.store_details.create');
        
    });
    Route::get('/store/frontpage', [StoreDetailsController::class, 'loadEditFrontPage'])->name('admin.store_front_page.index');
    Route::post('/store/frontpage/update', [StoreDetailsController::class, 'updateFrontPage'])->name('admin.store_front_page.update');
    Route::get('/store/frontpage/create', [StoreDetailsController::class, 'loadFrontpage'])->name('admin.store_front_page.create');
    Route::post('/store/frontpage/store', [StoreDetailsController::class, 'createFrontPage'])->name('admin.store_front_page.store');
    Route::prefix('sales-category')->group(function () {
        Route::get('/', [SalesCategory::class, 'index'])->name('admin.sales.category.index');
        Route::post('/store', [SalesCategory::class, 'store'])->name('admin.sales.category.store');
        Route::get('/edit/{id}', [SalesCategory::class, 'show'])->name('admin.sales.category.edit');
        Route::put('/update/{id}', [SalesCategory::class, 'update'])->name('admin.sales.category.update');
        Route::delete('/delete/{id}', [SalesCategory::class, 'destroy'])->name('admin.sales.category.destroy');
        Route::get('/show/{id}', [SalesCategory::class, 'show'])->name('admin.sales.category.show');
        Route::get('/create', [SalesCategory::class, 'loadCreateForm'])->name('admin.sales.category.create');   
});
 

});
});