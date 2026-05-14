<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategoryModel extends Model
{
    use SoftDeletes;

    protected $table = 'product_category_models';

    protected $fillable = [
        'name',
        'description',
        'excerpt',
        'status',
        'priority',
        'image_path',
    ];

    // Automatically handle deleted_at as a date
    protected $dates = ['deleted_at'];
}
