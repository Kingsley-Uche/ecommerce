<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRef extends Model
{
    //
    protected $fillable =[
        'id','payment_ref','order_id'
    ];
}
