<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StoreDetailsModel extends Model
{
    protected $table = 'store_details_models'; // Clean & conventional

    protected $fillable = [
        'store_name',
        'email',
        'phone',
        'address',
        'tagline',
        'logo_path',
        'social_icons',
        'social_links',
        'products',
    ];

    /**
     * Cast JSON columns to arrays automatically
     */
    protected $casts = [
        'social_icons' => 'array',
        'social_links' => 'array',
        'products'     => 'array',
        'tagline'      => 'string',
        'logo_path'    => 'string',
    ];

    /**
     * Accessor: Full URL for store logo
     */
    public function getLogoUrlAttribute(): string
    {
        return $this->logo_path
            ? Storage::disk('public')->url($this->logo_path)
            : asset('images/default-store-logo.png');
    }

    /**
     * Accessor: Full URL for a single product image
     * Usage: $store->getProductImageUrl($productArray)
     */
    public function getProductImageUrl($product): string
    {
        if (!is_array($product)) {
            return asset('images/no-product.png');
        }

        return isset($product['image']) && $product['image']
            ? Storage::disk('public')->url($product['image'])
            : asset('images/no-product.png');
    }

    /**
     * Accessor: Return all product image URLs in one go
     * Usage: $store->product_image_urls
     */
    public function getProductImageUrlsAttribute(): array
    {
        $urls = [];
        $products = $this->products ?? [];

        foreach ($products as $product) {
            $urls[] = $this->getProductImageUrl($product);
        }

        return $urls;
    }

    /**
     * Scope: Get the current (only) store
     */
    public function scopeCurrent($query)
    {
        return $query->first();
    }

    /**
     * Optional: Delete associated files when model is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($store) {
            // Delete logo
            if ($store->logo_path) {
                Storage::disk('public')->delete($store->logo_path);
            }

            // Delete all product images
            if (is_array($store->products)) {
                foreach ($store->products as $product) {
                    if (isset($product['image'])) {
                        Storage::disk('public')->delete($product['image']);
                    }
                }
            }
        });
    }
}