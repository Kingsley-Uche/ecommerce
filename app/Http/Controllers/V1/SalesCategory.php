<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesCategoryModel;
use Illuminate\Support\Facades\DB;

class SalesCategory extends Controller
{
    public function index()
    {
        // Paginate with only essential fields — faster
        $categories = SalesCategoryModel::select('id', 'description','category_name','is_active','priority')->where('is_active', true)
            ->orderBy('priority', 'asc')
            ->paginate(20);

        return view('admin.dashboard.pages.sales_category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:sales_category_models,category_name',
            'description'   => 'nullable|string',
            'is_active'     => 'required|in:0,1',
            'priority'      => 'required|integer|unique:sales_category_models,priority',
        ]);

        DB::transaction(function () use ($validated) {
            SalesCategoryModel::create($validated);
        });

        return redirect()
            ->route('admin.sales.category.index')
            ->with('success', 'Sales category created successfully.');
    }

    public function show($id)
    {
        $category = SalesCategoryModel::findOrFail($id);
        return view('admin.dashboard.pages.sales_category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
    
        $validated = $request->validate([
            'category_name' => 'nullable|string|max:255|unique:sales_category_models,category_name,' . $id,
            'description'   => 'nullable|string',
            'is_active'     => 'nullable|in:0,1',
            'priority'      => 'nullable|integer|unique:sales_category_models,priority,' . $id,
        ]);


        $category = SalesCategoryModel::findOrFail($id);
    

        DB::transaction(function () use ($category, $validated) {
            $category->update($validated);
        });

        return redirect()
            ->route('admin.sales.category.index')
            ->with('success', 'Sales category updated successfully.');
    }

    public function destroy($id)
    {
        $category = SalesCategoryModel::findOrFail($id);

        DB::transaction(function () use ($category) {
            $category->delete();
        });

        return redirect()
            ->back()
            ->with('success', 'Category deleted successfully.');
    }

    public function loadCreateForm()
    {
        return view('admin.dashboard.pages.sales_category.create');
    }

    public function getAllCategories()
    {
        $all_categories = SalesCategoryModel::select('id','category_name','priority','is_active')
            ->orderBy('priority','asc')
            ->get();

        return response()->json($all_categories);
    }
}
