<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImageModel extends Model
{
    //
    protected $table = 'product_image_models';
    protected $fillable = [
        'product_id',
        'image_path',
    ];
}
