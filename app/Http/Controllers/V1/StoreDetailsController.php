<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\StoreDetailsModel;
use App\Models\ProductModel;

class StoreDetailsController extends Controller
{
    public function index()
    {
        $store = StoreDetailsModel::first();
        return view('admin.dashboard.pages.store.store_data.index', compact('store'));
    }

    /**
     * LOAD CREATE OR EDIT FORM
     */
    public function loadCreateForm()
    {
        if (StoreDetailsModel::exists()) {
            return $this->edit();
        }

        return view('admin.dashboard.pages.store.store_data.create');
    }


    /**
     * CREATE STORE (ONLY ONE ALLOWED)
     */
    public function store(Request $request)
    {
        if (StoreDetailsModel::exists()) {
            return redirect()->route('admin.store_details.index')
                ->with('error', 'Store already exists. Only one store allowed.');
        }

        $data = $request->validate([
            'store_name' => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:20',
            'tagline'    => 'nullable|string|max:255',
            'address'    => 'required|string|max:500',
            'logo'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'social_icons'   => 'nullable|array|max:10',
            'social_icons.*' => 'nullable|string|max:50',
            'social_links'   => 'nullable|array|max:10',
            'social_links.*' => 'nullable|url|max:255',
        ]);

        $logoPath = $this->uploadLogo($request);

        StoreDetailsModel::create([
            'store_name'   => $data['store_name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'],
            'tagline'      => $data['tagline'] ?? null,
            'address'      => $data['address'],
            'logo_path'    => $logoPath,
            'social_icons' => $data['social_icons'] ?? null,
            'social_links' => $data['social_links'] ?? null,
        ]);

        return redirect()->route('admin.store_details.index')
            ->with('success', 'Store created successfully!');
    }


    /**
     * EDIT STORE
     */
    public function edit()
    {
        $store = StoreDetailsModel::firstOrFail();
        return view('admin.dashboard.pages.store.store_data.edit', compact('store'));
    }


    /**
     * UPDATE STORE
     */
    public function update(Request $request)
    {
        $store = StoreDetailsModel::firstOrFail();

        $data = $request->validate([
            'store_name' => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:20',
            'tagline'    => 'nullable|string|max:255',
            'address'    => 'required|string|max:500',
            'logo'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'social_icons'   => 'nullable|array|max:10',
            'social_icons.*' => 'nullable|string|max:50',
            'social_links'   => 'nullable|array|max:10',
            'social_links.*' => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('logo')) {
            $this->deleteFile($store->logo_path);
            $store->logo_path = $request->file('logo')->store('store_logos', 'public');
        }

        $store->update([
            'store_name'   => $data['store_name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'],
            'tagline'      => $data['tagline'] ?? null,
            'address'      => $data['address'],
            'social_icons' => $data['social_icons'] ?? null,
            'social_links' => $data['social_links'] ?? null,
        ]);

        return redirect()->route('admin.store_details.index')
            ->with('success', 'Store updated successfully!');
    }


    /**
     * DELETE STORE
     */
    public function destroy()
    {
        $store = StoreDetailsModel::firstOrFail();

        $this->deleteFile($store->logo_path);

        $store->delete();

        return redirect()->route('admin.store_details.index')
            ->with('success', 'Store deleted successfully.');
    }




    /* ======================================================
     FRONT PAGE PRODUCT MANAGEMENT (SEPARATED CLEANLY)
    ====================================================== */


    public function loadFrontpage()
    {        
        return view('admin.dashboard.pages.store.frontpage.create');
    }
    public function loadEditFrontPage(){

        $product_data = ProductModel::where('is_front_page', true)->get();
        return view('admin.dashboard.pages.store.frontpage', compact('product_data'));

    }


    public function createFrontpage(Request $request)
    {
        $data = $request->validate([
            'product_images'          => 'nullable|array|max:10',
            'product_images.*'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
            'product_names'           => 'nullable|array|max:10',
            'product_names.*'         => 'nullable|string|max:255',
            'product_prices'          => 'nullable|array|max:10',
            'product_prices.*'        => 'nullable|numeric|min:0',
            'product_descriptions'    => 'nullable|array|max:10',
            'product_descriptions.*'  => 'nullable|string|max:1000',
            'product_stock'           => 'nullable|array|max:10',
            'product_stock.*'         => 'nullable|integer|min:0',
        ]);

        $products = $this->buildProductsArray($request, $data, [], true);

        ProductModel::insert($products);

        return redirect()->route('admin.store_details.index')
            ->with('success', 'Front page products created successfully!');
    }


    public function updateFrontPage(Request $request)
    {
        $data = $request->validate([
            'product_images'          => 'sometimes|array|max:10',
            'product_images.*'        => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
            'product_names'           => 'required|array|max:10',
            'product_names.*'         => 'required|string|max:255',
            'product_prices'          => 'required|array|max:10',
            'product_prices.*'        => 'required|numeric|min:0',
            'product_descriptions'    => 'required|array|max:10',
            'product_descriptions.*'  => 'required|string|max:1000',
            'product_stock'           => 'required|array|max:10',
            'product_stock.*'         => 'required|integer|min:0',
            'product_id'              => 'required|array',
            'product_id.*'            => 'required|integer'
        ]);

        $existing = ProductModel::whereIn('id', $request->product_id)->get()->keyBy('id');

        $products = $this->buildProductsArray($request, $data, $existing, true);

        ProductModel::upsert(
            $products,
            ['id'],
            ['name', 'price', 'description', 'stock', 'image', 'is_front_page']
        );

        return redirect()->route('admin.store_details.index')
            ->with('success', 'Front page products updated successfully!');
    }



    /* ======================
       PRIVATE HELPERS
    ====================== */

    private function uploadLogo($request)
    {
        return $request->hasFile('logo')
            ? $request->file('logo')->store('store_logos', 'public')
            : null;
    }

    private function deleteFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }


    /**
     * Build product arrays for both store products & frontpage products
     */
    private function buildProductsArray($request, $data, $existingProducts = [], $isFrontPage = false)
    {
        $products = [];

        $names  = $data['product_names'] ?? [];
        $prices = $data['product_prices'] ?? [];
        $descs  = $data['product_descriptions'] ?? [];
        $stocks = $data['product_stock'] ?? [];
        $ids    = $data['product_id'] ?? [];
        $images = $request->file('product_images') ?? [];

        foreach ($names as $i => $name) {

            $imagePath = null;
            // upload new image
            if (!empty($images[$i])) {
                $imagePath = $images[$i]->store('products', 'public');
            }
            // keep old if exists
            elseif (isset($existingProducts[$ids[$i]]) && $existingProducts[$ids[$i]]->image) {
                $imagePath = $existingProducts[$ids[$i]]->image;
            }

            $products[] = [
                'id'          => $ids[$i] ?? null,
                'name'        => $name,
                'price'       => $prices[$i] ?? null,
                'description' => $descs[$i] ?? null,
                'stock'       => $stocks[$i] ?? null,
                'image'       => $imagePath,
                'is_front_page' => $isFrontPage,
            ];
        }

        return $products;
    }
}
