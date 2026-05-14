<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesCategoryModel extends Model
{
    use SoftDeletes;

    protected $table = 'sales_category_models';

    protected $fillable = [
        'category_name',
        'description',
        'is_active',
        'priority',
    ];

    // Automatically handle deleted_at as a date
    protected $dates = ['deleted_at'];
}
