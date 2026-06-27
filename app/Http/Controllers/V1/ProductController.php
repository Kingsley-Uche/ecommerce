<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductImageModel;
use App\Models\SalesCategoryModel;
use App\Models\CategoryAssign;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
  public function store(Request $request)
{
    $data = $request->validate([

        'products' => 'required|array|min:1',

        'products.*.name' => 'required|string|max:255',

        'products.*.description' => 'nullable|string',

        'products.*.price' => 'required|numeric|min:0',

        'products.*.stock' => 'required|integer|min:0',

        'products.*.categories' => 'required|array|min:1',

        'products.*.categories.*' => 'exists:product_category_models,id',

        'products.*.images' => 'nullable|array',

        'products.*.images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

    ]);
    DB::transaction(function () use ($request, $data) {

        foreach ($data['products'] as $index => $item) {

            $product = ProductModel::create([
                'name'        => $item['name'],
                'description' => $item['description'] ?? null,
                'price'       => $item['price'],
                'stock'       => $item['stock'],
                'num_sold'    => 0,
            ]);

            // Save Categories
            $categories = [];

            foreach ($item['categories'] as $category) {

                $categories[] = [
                    'product_id' => $product->id,
                    'category_id'=> $category,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            CategoryAssign::insert($categories);

            // Save Images
            if ($request->hasFile("products.$index.images")) {

                $images = [];

                foreach ($request->file("products.$index.images") as $image) {

                    $path = $image->store('products', 'public');

                    $images[] = [
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                ProductImageModel::insert($images);
            }
        }
    });

    return redirect()
        ->route('admin.product.index')
        ->with('success', 'Products created successfully.');
}
    public function index()
    {
        $products = ProductModel::with(['categories','images'])->paginate(10);
        return view('admin.dashboard.pages.products.index', compact('products'));

    }

    
    public function loadCreateProduct()
    {
        $categories = ProductCategoryModel::where('status', 'active')->select('id', 'name')->get();
        $sales_category = SalesCategoryModel::where('is_active', true)->select('id', 'category_name')->get();
        return view('admin.dashboard.pages.products.create', compact(['categories','sales_category']));
    }


    public function productById(int $id)
{
    $product = ProductModel::with([
        'categories',
        'images',
        'sales_category'
    ])->findOrFail($id);

    $categories = ProductCategoryModel::where('status', 'active')
        ->orderBy('name')
        ->get();

  

    return view(
        'admin.dashboard.pages.products.edit',
        compact('product', 'categories')
    );
}

public function updateProduct(Request $request, int $id)
{
    $data = $request->validate([
        'name'                    => 'required|string|max:255',
        'description'             => 'nullable|string',
        'price'                   => 'required|numeric|min:0',
        'stock'                   => 'required|integer|min:0',

        'sales_category_models_id' => 'nullable|exists:sales_category_models,id',

        'category_id'             => 'required|array|min:1',
        'category_id.*'           => 'exists:product_category_models,id',

        'images'                  => 'nullable|array',
        'images.*'                => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
    ]);

    DB::transaction(function () use ($data, $request, $id) {

        $product = ProductModel::findOrFail($id);

        // Update Product
        $product->update([
            'name'                     => $data['name'],
            'description'              => $data['description'] ?? null,
            'price'                    => $data['price'],
            'stock'                    => $data['stock'],
            'sales_category_models_id' => $data['sales_category_models_id'] ?? null,
        ]);

        // Update Categories
        $product->categories()->sync($data['category_id']);

        // Replace Images (only if new ones were uploaded)
        if ($request->hasFile('images')) {

            // Delete existing image files
            foreach ($product->images as $image) {

                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }

            // Delete image records
            $product->images()->delete();

            // Save new images
            $images = [];

            foreach ($request->file('images') as $image) {

                $path = $image->store('products', 'public');

                $images[] = [
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            ProductImageModel::insert($images);
        }
    });

    return redirect()
        ->route('admin.product.index')
        ->with('success', 'Product updated successfully.');
}
    public function deleteProduct(int $id)
    {
        $product = ProductModel::findOrFail($id);

        // Delete associated images
        $images = ProductImageModel::where('product_id', $product->id)->get();
        foreach ($images as $image) {
            $file = storage_path('app/public/' . $image->image_path);
            if (file_exists($file)) unlink($file);
        }
        ProductImageModel::where('product_id', $product->id)->delete();

        // Delete product
        $product->delete();

        return redirect()->route('admin.product.index')
            ->with('success', 'Product deleted successfully.');
    }
    public function deleteImage($id)
{
    $img = ProductImageModel::findOrFail($id);
    Storage::disk('public')->delete($img->image_path);
    $img->delete();

    return back()->with('success', 'Image deleted successfully');
}
}