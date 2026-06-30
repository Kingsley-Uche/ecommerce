<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryAssign extends Model
{
    //
    protected $fillable = ['product_id', 'category_id'];

     public function category()
    {
        return $this->belongsTo(ProductCategoryModel::class, 'category_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}

