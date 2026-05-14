<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategoryModel;

class ProductCategory extends Controller
{
    //
    public function LoadCreateCategory(Request $request)
    {
        
        return view('admin.dashboard.pages.product-category.create');
    }
    public function ViewProductCategories()
    {
        $categories = ProductCategoryModel::paginate(20);
        return view('admin.dashboard.pages.product-category.index', compact('categories'));
    }
    public function CreateProductCategory(Request $request)
    {
    
        //code to create product category
        $data = $request->validate([
            'name' => 'required|string|max:20',
            'description' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
            'priority' => 'nullable|integer|unique:product_category_models,priority',
            'image' => 'nullable|image|max:1024',
        ]);
        // Logic to save the product category to the database would go here
if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product-categories', 'public');
            $data['image_path'] = $imagePath;
        }


        // Assuming ProductCategory is a model
        ProductCategoryModel::create($data);

        return redirect()->route('admin.product-category.index')->with('success', 'Product category created successfully.');

    }

    public function EditProductCategory($id)
    {
        $category = ProductCategoryModel::findOrFail((int)$id);
        return view('admin.dashboard.pages.product-category.edit', compact('category'));
    }

    public function UpdateProductCategory(Request $request, $id)
    {
        $category = ProductCategoryModel::findOrFail((int)$id);

        $data = $request->validate([
            'name' => 'required|string|max:20',
            'description' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
            'priority' => 'nullable|integer|unique:product_category_models,priority,' . $id,
            'image' => 'nullable|image|max:1024|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product-categories', 'public');
            $data['image_path'] = $imagePath;
        }

        $category->update($data);

        return redirect()->route('admin.product-category.index')->with('success', 'Product category updated successfully.');
    }

    public function DeleteProductCategory($id)
    {
        $category = ProductCategoryModel::findOrFail($id);
        //unlink image if exists
        if ($category->image_path && file_exists(public_path('storage/' . $category->image_path))) {
            unlink(public_path('storage/' . $category->image_path));
        }       
        $category->delete();

        return redirect()->route('admin.product-category.index')->with('success', 'Product category deleted successfully.');
    }
    
}
