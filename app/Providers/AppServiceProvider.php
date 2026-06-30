<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\CartModel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
          Schema::defaultStringLength(191);
          Paginator::useBootstrap();
          View::composer('website.components.cart_modal', function ($view) {
            $request = request();
 
            $userId    = auth()->id();
            $cartToken = $request->cookie('cart_token');
 
            $cartItems = CartModel::query()
                ->when($userId, fn ($q) => $q->where('user_id', $userId))
                ->when(!$userId, fn ($q) => $q->where('cart_token', $cartToken))
                ->with('product.images')
                ->get();
 
            $view->with('cartItems', $cartItems);
        });
    }
}
