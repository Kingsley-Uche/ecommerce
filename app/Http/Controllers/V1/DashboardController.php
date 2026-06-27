<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use App\Models\Orders;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //

public function index()
{
    $categories = ProductCategoryModel::where('status', 'active')
        ->select('id', 'name')
        ->orderBy('name')
        ->get();

    $startTime = Carbon::now()->startOfYear();
    $endTime   = Carbon::now();

    $products = ProductModel::select('name', 'stock', 'num_sold')->get();

    $revenue = Orders::where('payment_status', 'confirmed')
        ->whereBetween('created_at', [$startTime, $endTime])
        ->sum('total_paid');

    $productBreakdown = $products->map(function ($product) {

        $total = $product->stock + $product->num_sold;


        return [
            'name' => $product->name,
            'stock' => $product->stock,
            'sold' => $product->num_sold,
            'product_status' => $product->num_sold > 0
                ? round($product->stock / $product->num_sold, 2)
                : null,
            'stock_percentage' => $total > 0
                ? round(($product->stock / $total) * 100, 2)
                : 0,
            'sold_percentage' => $total > 0
                ? round(($product->num_sold / $total) * 100, 2)
                : 0,
        ];
    });
    

    return view('admin.dashboard.pages.index', [
        'categories'        => $categories,
        'revenue'           => $revenue,
        'productBreakdown'  => $productBreakdown,
        'totalProducts'     => $products->count(),
    ]);
}
}
