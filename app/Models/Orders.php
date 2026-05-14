<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{
    use SoftDeletes; // enable soft deletes

    protected $fillable = [
        'payment_ref',
        'user_name',
        'email_address',
        'phone',
        'delivery_city',
        'delivery_address',
        'product_id',
        'order_status',
        'payment_status',
        'cart_token',
        'total_cost', 
        'total_paid',
        
    ];

    // Cast JSON fields properly
    protected $casts = [
        'product_id' => 'array',
    ];
}
