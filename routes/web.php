<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AdminController;
use App\Http\Controllers\V1\ProductCategory;
use App\Http\Controllers\V1\ProductController;
use App\Http\Controllers\V1\StoreDetailsController;
use App\Http\Controllers\V1\SalesCategory;
use App\Http\Controllers\V1\WebsiteController;
use App\Http\Controllers\V1\CartController;
use App\Http\Controllers\V1\CategoryAssignController;
use App\Http\Controllers\V1\TransactionController;

Route::get('/', [WebsiteController::class, 'index'])->name('home');
Route::get('/login', [AdminController::class, 'showLoginform'])->name('login');
Route::post('/payment/initiate', [TransactionController::class, 'initiatePay'])->name('payment.initiate');
Route::get('/payment/checkout/{cart_token}', [TransactionController::class, 'checkout'])->name('payment.checkout');
Route::post('/payment/checkout', [TransactionController::class, 'initiatePay'])->name('checkout.submit');

Route::get('/get/category/products/{category_id}', [WebsiteController::class, 'getproductsByCategory'])->name('category.products');

Route::prefix('cart')->group(function () {
    // NOTE: loadCartView() reads the cart_token cookie, not {cart_id} from
    // the URL — the segment is accepted but currently unused server-side.
    // The header currently passes cart_id=0 as a placeholder. Once the
    // frontend JS no longer needs a numeric id in the URL, this {cart_id}
    // segment can be dropped: Route::get('/load', ...)->name('cart.view');
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

        Route::prefix('product/category/assign')->group(function () {
            Route::get('/create', [CategoryAssignController::class, 'Create'])->name('admin.category-assign.create.load');
            Route::get('/', [CategoryAssignController::class, 'View'])->name('admin.category-assign.index');
            Route::post('/create', [CategoryAssignController::class, 'CreateProductCategory'])->name('admin.category-assign.store');
            Route::get('/edit/{id}', [CategoryAssignController::class, 'EditProductCategory'])->name('admin.category-assign.edit');
            Route::put('/update/{id}', [CategoryAssignController::class, 'UpdateProductCategory'])->name('admin.category-assign.update');
            Route::delete('/delete/{id}', [CategoryAssignController::class, 'DeleteProductCategory'])->name('admin.category-assign.destroy');
        });

        Route::prefix('product')->group(function () {
            Route::get('/create', [ProductController::class, 'loadCreateProduct'])->name('admin.product.create.load');
            Route::get('/', [ProductController::class, 'index'])->name('admin.product.index');
            Route::post('/create', [ProductController::class, 'store'])->name('admin.product.store');
            Route::get('/edit/{id}', [ProductController::class, 'ProductById'])->name('admin.product.edit');
            Route::put('/update/{id}', [ProductController::class, 'UpdateProduct'])->name('admin.product.update');
            Route::delete('/delete/{id}', [ProductController::class, 'DeleteProduct'])->name('admin.product.destroy');
            Route::get('/product/image/delete/{id}', [ProductController::class, 'deleteImage'])->name('admin.product.delete-image');
        });

        Route::prefix('store-details')->group(function () {
            Route::get('/', [StoreDetailsController::class, 'index'])->name('admin.store_details.index');
            Route::post('/store', [StoreDetailsController::class, 'store'])->name('admin.store_details.store');
            Route::get('/edit/{id}', [StoreDetailsController::class, 'edit'])->name('admin.store_details.edit');
            Route::put('/update/{id}', [StoreDetailsController::class, 'update'])->name('admin.store_details.update');
            Route::delete('/delete', [StoreDetailsController::class, 'destroy'])->name('admin.store_details.destroy');
            Route::get('/show', [StoreDetailsController::class, 'show'])->name('admin.store_details.show');
            Route::get('/create', [StoreDetailsController::class, 'loadCreateForm'])->name('admin.store_details.create');
        });

        Route::get('/store/frontpage', [StoreDetailsController::class, 'loadEditFrontPage'])->name('admin.store_front_page.index');
        Route::post('/store/frontpage/update', [StoreDetailsController::class, 'updateFrontPage'])->name('admin.store_front_page.update');
        Route::get('/store/frontpage/create', [StoreDetailsController::class, 'loadFrontpage'])->name('admin.store_front_page.create');
        Route::post('/store/frontpage/store', [StoreDetailsController::class, 'createFrontPage'])->name('admin.store_front_page.store');
    });
});