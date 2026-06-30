<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\StoreDetailsModel;
use App\Models\ProductModel;
use App\Models\ProductImageModel;
use App\Models\ProductCategoryModel;
use App\Models\CategoryAssign;
use App\Models\SalesCategoryModel;
use App\Models\CartModel;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public $shop_data;

    public function __construct()
    {
        $this->shop_data = StoreDetailsModel::select(
            'store_name', 'address', 'phone', 'email', 'logo_path',
            'tagline', 'social_links', 'social_icons', 'products'
        )->first();
    }

    public function index(Request $request)
    {
        // Load products
        $products = ProductModel::select(
                'id', 'name', 'description', 'price', 'stock', 'sales_category_models_id'
            )
            ->get();

        $shop_data = StoreDetailsModel::select(
            'store_name', 'address', 'phone', 'email', 'logo_path',
            'tagline', 'social_links', 'social_icons', 'products'
        )->first();

        if ($products->isEmpty()) {
            return view('website.main.landpage', [
                'shop_data'     => $shop_data,
                'product_categ' => [],
            ]);
        }

        $productIds = $products->pluck('id');

        $productImages = ProductImageModel::whereIn('product_id', $productIds)
            ->select('product_id', 'image_path')
            ->get()
            ->groupBy('product_id');

        $categoryAssignments = CategoryAssign::with([
                'category:id,name,description,image_path,priority,status'
            ])
            ->whereIn('product_id', $productIds)
            ->get()
            ->groupBy('product_id');

        $product_categ = [];

        foreach ($products as $product) {

            $productDetails = [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'stock'       => $product->stock,
                'images'      => $productImages[$product->id] ?? collect(),
            ];

            foreach ($categoryAssignments[$product->id] ?? [] as $assignment) {

                if (!$assignment->category || $assignment->category->status !== 'active') {
                    continue;
                }

                $category = $assignment->category;

                if (!isset($product_categ[$category->id])) {
                    $product_categ[$category->id] = [
                        'category_name'        => $category->name,
                        'category_description' => $category->description,
                        'category_image'       => $category->image_path,
                        'category_priority'    => $category->priority,
                        'products'             => [],
                    ];
                }

                $product_categ[$category->id]['products'][] = $productDetails;
            }
        }

        uasort($product_categ, function ($a, $b) {
            return $b['category_priority'] <=> $a['category_priority'];
        });

        return view(
            'website.main.pages.main',
            compact('shop_data', 'product_categ')
        );
    }

    /**
     * Product search — matches against name and description, returns
     * the same product card shape used throughout the catalogue page
     * (id, name, description, price, stock, images) so the existing
     * product-cell partial / markup can be reused as-is on results.
     */
    public function search(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        $products = collect();

        if ($term !== '') {
            $matches = ProductModel::select('id', 'name', 'description', 'price', 'stock')
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                          ->orWhere('description', 'like', "%{$term}%");
                })
                ->orderBy('name')
                ->get();

            $productIds = $matches->pluck('id');

            $productImages = ProductImageModel::whereIn('product_id', $productIds)
                ->select('product_id', 'image_path')
                ->get()
                ->groupBy('product_id');

            $products = $matches->map(function ($product) use ($productImages) {
                return [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'description' => $product->description,
                    'price'       => $product->price,
                    'stock'       => $product->stock,
                    'images'      => $productImages[$product->id] ?? collect(),
                ];
            });
        }

        return view('website.main.pages.search', [
            'shop_data' => $this->shop_data,
            'term'      => $term,
            'products'  => $products,
        ]);
    }

    public function getCheckout(Request $request)
    {
        $cart_token = $request->cookie('cart_token');
        $products = CartModel::where('cart_token', $cart_token)->with('product')->get();
        return view('website.main.checkout', compact('products'));
    }

    public function getProductsByCategory(Request $request, $category_id)
    {
        $category_id = (int) decrypt($category_id);

        $products = ProductModel::where('category_id', $category_id)
            ->with('images')
            ->get();

        $category = ProductCategoryModel::select('name', 'description', 'id')
            ->get();

        return view('website.main.pages.products_by_category', compact('products', 'category', 'category_id'))
            ->with('shop_data', $this->shop_data);
    }
}
