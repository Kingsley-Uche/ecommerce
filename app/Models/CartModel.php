<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    protected $table = 'cart_models';

    protected $fillable = [
        'user_id',
        'cart_token',
        'product_id',
        'quantity',
        'cart_status',
    ];
    

    public $timestamps = true;
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id','id')
        ->select('id','name','description','price')
        ->with('images');
    }
}
