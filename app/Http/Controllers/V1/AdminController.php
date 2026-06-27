<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use App\Models\Orders;
use Carbon\Carbon;
class AdminController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin logout
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Authenticate user
        if (Auth::attempt($credentials, $request->remember)) {
            
            // Check if user is admin
            $this->EnsureAdmin();
            

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid login credentials']);
    }

    /**
     * Dashboard view
     */
   
public function dashboard()
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
    

    return view('admin.dashboard.pages.home.index', [
        'categories'        => $categories,
        'revenue'           => $revenue,
        'productBreakdown'  => $productBreakdown,
        'totalProducts'     => $products->count(),
    ]);
}
    /**
     * Show change password form
     */
    public function changePassword()
    {
        return view('admin.auth.change_password');
    }
    private function EnsureAdmin(){
        if (auth()->user()->is_admin != 1) {
                Auth::logout();
                return back()->withErrors(['email' => 'Access denied. Admins only.']);
            }
            return true;


    }
}
