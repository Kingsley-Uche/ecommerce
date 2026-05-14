<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\StoreDetailsModel;
use App\Models\ProductModel;
use App\Models\ProductImageModel;
use App\Models\ProductCategoryModel;
use App\Models\SalesCategoryModel;
use App\Models\CartModel;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public $shop_data;
    public function __construct()
    {
        //
                $this->shop_data = StoreDetailsModel::select(
            'store_name','address','phone','email','logo_path','tagline','social_links', 'social_icons','products'
        )->first();
    }   
    public function index(Request $request)
    {

        // Fetch products + category details in one optimized query
        $products = ProductModel::select(
            'product_models.id',
            'product_models.name',
            'product_models.description',
            'product_models.price',
            'product_models.stock',
            'product_models.category_id',
            'product_models.sales_category_models_id',
            'product_category_models.name as category_name',
            'product_category_models.description as category_description',
            'product_category_models.image_path',
            'product_category_models.priority as category_priority'
        )
        ->join('product_category_models', 'product_category_models.id', '=', 'product_models.category_id')
        ->where('product_category_models.status', 'active')
        ->orderBy('product_category_models.priority', 'desc')
        ->orderBy('product_models.category_id')
        ->get();

        $shop_data = StoreDetailsModel::select(
            'store_name','address','phone','email','logo_path','tagline','social_links', 'social_icons','products'
        )->first();

        if ($products->isEmpty()) {
            return view('website.main.landpage', [
                'shop_data' => $shop_data,
                'product_categ' => [],
                'sale_categ_data' => []
            ]);
        }

        // Extract IDs
        $productIDs = $products->pluck('id')->toArray();
        $salesCategoryIDs = $products->pluck('sales_category_models_id')->filter()->unique()->toArray();

        // Load images once, grouped by product_id
        $productImages = ProductImageModel::whereIn('product_id', $productIDs)
            ->select('product_id','image_path')
            ->get()
            ->groupBy('product_id');

        // Load active sales categories
        $salesCategories = SalesCategoryModel::whereIn('id', $salesCategoryIDs)
            ->where('is_active', true)
            ->select('id','category_name','priority')
            ->orderBy('priority', 'desc')
            ->get()
            ->keyBy('id');

        $product_categ = [];
        $sale_categ_data = [];

        foreach ($products as $item) {

            /** ✅ Group products by PRODUCT CATEGORY */
            if (!isset($product_categ[$item->category_id])) {
                $product_categ[$item->category_id] = [
                    'category_name'        => $item->category_name,
                    'category_description' => $item->category_description,
                    'category_image'       => $item->image_path,
                    'category_priority'    => $item->category_priority,
                    'products'             => []
                ];
            }

            $productDetails = [
                'name'        => $item->name,
                'description' => $item->description,
                'price'       => $item->price,
                'stock'       => $item->stock,
                'images'      => $productImages[$item->id] ?? [],
                'id'=>$item->id,
            ];

            $product_categ[$item->category_id]['products'][] = $productDetails;


            
            if ($item->sales_category_models_id && isset($salesCategories[$item->sales_category_models_id])) {

                if (!isset($sale_categ_data[$item->sales_category_models_id])) {
                    $sale_categ_data[$item->sales_category_models_id] = [
                        'sales_category_name'     => $salesCategories[$item->sales_category_models_id]->category_name,
                        'sales_category_priority' => $salesCategories[$item->sales_category_models_id]->priority,
                        'sales_category_description' => $salesCategories[$item->sales_category_models_id]->description ?? '',
                        'products'                => []
                    ];
                }

                $sale_categ_data[$item->sales_category_models_id]['products'][] = $productDetails;
            }
        }
        return view(
            'website.main.pages.main',
            compact('shop_data', 'product_categ', 'sale_categ_data')
        );
    }
        public function getCheckout(Request $request){
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

    return view('website.main.pages.products_by_category', compact('products', 'category', 'category_id'))->with('shop_data', $this->shop_data);
}
  
    }

