<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductImageModel;
use App\Models\ProductCategoryModel;
use App\Models\SalesCategoryModel;

class ProductModel extends Model
{
    use SoftDeletes;

    protected $table = 'product_models';

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'sales_category_models_id',
        'stock',
        'is_front_page',
    ];
    
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $dates = ['deleted_at'];

    public function category()
    {
        return $this->belongsTo(ProductCategoryModel::class, 'category_id')
            ->select('id', 'name', 'excerpt', 'description', 'status', 'priority', 'image_path');
    }
    
    public function images()
    {
        return $this->hasMany(ProductImageModel::class, 'product_id')
            ->select('product_id', 'image_path', 'id');
    }

    public function sales_category()
    {
        return $this->belongsTo(SalesCategoryModel::class, 'sales_category_models_id')
            ->select('id', 'category_name', 'priority', 'is_active');
    }
}
