<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Add a product to cart
     */
   public function save(Request $request)
{
    
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|integer',
        'quantity'   => 'required|integer|min:1',
    ]);


    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $data = $validator->validated();   
    $userId    = auth()->id();
    $cartToken = $request->cookie('cart_token');
    

    // If guest user has no cart token → create once
    if (!$cartToken && !$userId) {
        $cartToken = (string) Str::uuid();
        Cookie::queue('cart_token', $cartToken, 43200); // 30 days
    }

    // Build base query once
    $query = CartModel::query()
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->when(!$userId, fn($q) => $q->where('cart_token', $cartToken));

    // Fetch cart item (fast single DB query)
    $existing = $query->where('product_id', $data['product_id'])->first();
    

    if ($existing) {
        $quantity = (int)$existing->quantity;

        // No change needed → early return (fast!)
        
            return response()->json([
                'status'  => 'error',
                'message' => 'Item already in cart',
                'data'    => ['cart_token' => $cartToken],
                'count'=>$data['total_quantity']
            ], 200);
       

    } else {
        // Insert new cart item
        CartModel::create([
            'user_id'    => $userId,
            'cart_token' => $cartToken,
            'product_id' => $data['product_id'],
            'quantity'   => $data['quantity'],
        ]);
       $newQuantity = $data['quantity']+1;
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'Product added to cart',
        'data'    => ['cart_token' => $cartToken],
        'count'=>$newQuantity,
    ], 200);
}


    /**
     * Update the quantity of a product in the cart
     */
 public function update(Request $request)
{
    $data = $request->validate([
        'product_id'   => 'required|array',
        'product_id.*' => 'required|integer',
        'quantity'     => 'required|array',
        'quantity.*'   => 'required|integer|min:0',
    ]);

    $userId = auth()->id();
    $cartToken = $request->query('cart_token') ?? $request->cookie('cart_token');

    // fetch user's cart items
    $cartItems = CartModel::when($userId, fn($q) => $q->where('user_id', $userId))
        ->when(!$userId, fn($q) => $q->where('cart_token', $cartToken))
        ->whereIn('product_id', $data['product_id'])
        ->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['status' => 'error', 'message' => 'Items not found in cart'], 404);
    }
    
$updated =0;
    foreach ($cartItems as $index => $item) {

        $newQty = $data['quantity'][$index];

        if ($newQty == 0) {
            // DELETE the item
            $item->delete();
        } else {
            // UPDATE the quantity
            $item->update([
                'quantity' => $newQty
            ]);
            $updated = +$newQty;
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Cart updated successfully',
        'count'=>$updated,
    ],201);
}



    /**
     * Get all cart items
     */
  public function getCart(Request $request)
{
    $validator = Validator::make($request->all(), [
        'cart_token' => 'sometimes|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Invalid cart token'], 422);
    }

    $userId = auth()->id();
    $cartToken = $request->input('cart_token'); // POST body

    if (!$userId && !$cartToken) {
        return response()->json([
            'message' => 'No cart identifier available',
        ], 422);
    }

    $cartItems = CartModel::when($userId, fn($q) => $q->where('user_id', $userId))
        ->when(!$userId, fn($q) => $q->where('cart_token', $cartToken))
        ->with('product')
        ->get();
        $count =0;
        foreach($cartItems as $item){
            $count = +$item->quantity;
        }

    return response()->json([
        'status' => 'success',
        'count'  => $count,
        'data'   => $cartItems,
        'cart_token'=>$cartToken
    ], 200);
}


    /**
     * Remove one product from cart
     */
    public function removeItem(Request $request)
    {
        $data = $request->validate(['product_id' => 'required|integer']);

        $userId = auth()->id();
        $cartToken = $request->cookie('cart_token');

        $deleted = CartModel::when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('cart_token', $cartToken))
            ->where('product_id', $data['product_id'])
            ->delete();

        if (!$deleted) {
            return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
        }

        return response()->json(['status' => 'success', 'message' => 'Item removed successfully'], 200);
    }

    /**
     * Clear all items from cart
     */
    public function clearCart(Request $request)
    {
        $userId = auth()->id();
        $cartToken = $request->cookie('cart_token');

        $deleted = CartModel::when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('cart_token', $cartToken))
            ->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Cart cleared successfully',
            'deleted' => $deleted
        ], 200);
    }
    public function loadCartView(Request $request){
        dd('hii');

    }
}
          