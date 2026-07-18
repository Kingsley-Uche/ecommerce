<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'qty_bought',
        'size'
    ];


    public function product()
    {
        return $this->belongsTo(
            ProductModel::class,
            'product_id',
            'id'
        )->select(
            'id',
            'name',
            'price'
        );
    }


    public function order()
    {
        return $this->belongsTo(
            Orders::class,
            'order_id',
            'id'
        );
    }
}