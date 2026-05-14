<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductImageModel;
use App\Models\SalesCategoryModel;


class ProductController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'category_id'   => 'required|exists:product_category_models,id',
            'sales_category_models_id'=>'nullable|exists:sales_category_models,id',
            'stock'         => 'required|integer|min:0',
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);

        
        $product = ProductModel::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'price'       => $data['price'],
            'category_id' => $data['category_id'],
            'sales_category_models_id'=>$data['sales_category_models_id'],
            'stock'       => $data['stock'],
        ]);

        if ($request->hasFile('images')) {
            $productImages = [];

            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');

                $productImages[] = [
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            ProductImageModel::insert($productImages);
        }

        return redirect()->route('admin.product.index')
            ->with('success', 'Product created successfully.');
    }


    public function index()
    {
        $products = ProductModel::with(['category','images', 'sales_category'])->paginate(10);
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
        $product = ProductModel::with('category', 'images', 'sales_category')->findOrFail($id);
        $categories = ProductCategoryModel::where('status', 'active')->get();
        $sales_category = SalesCategoryModel::where('is_active', true)->select('id', 'category_name')->get();
        return view('admin.dashboard.pages.products.edit', compact('product', 'categories','sales_category'));
    }


    public function updateProduct(Request $request, int $id)
    {
        $data = $request->validate([
            'name'          => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'nullable|numeric|min:0',
            'category_id'   => 'nullable|exists:product_category_models,id',
            'sales_category_model_id'=>'nullable|exists:sales_category_models,id',
            'stock'         => 'nullable|integer|min:0',
            'images'        => 'nullable|array',
            'images.*'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = ProductModel::findOrFail($id);
        $product->update($data);

        // Handle new images
        if ($request->hasFile('images')) {

            // Delete old images
            $oldImages = ProductImageModel::where('product_id', $product->id)->get();
            foreach ($oldImages as $old) {
                $file = storage_path('app/public/' . $old->image_path);
                if (file_exists($file)) unlink($file);
            }
            ProductImageModel::where('product_id', $product->id)->delete();

            // Upload new images
            $productImages = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');

                $productImages[] = [
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            ProductImageModel::insert($productImages);
        }

        return redirect()->route('admin.product.index')
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