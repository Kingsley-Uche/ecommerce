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
     * Resolve the current cart owner: either the authenticated user's id,
     * or a guest cart_token (cookie first, falling back to request input).
     */
    private function resolveCartOwner(Request $request): array
    {
        $userId    = auth()->id();
        $cartToken = $request->cookie('cart_token') ?? $request->input('cart_token');

        if (!$userId && !$cartToken) {
            $cartToken = (string) Str::uuid();
            Cookie::queue('cart_token', $cartToken, 43200); // 30 days
        }

        return [$userId, $cartToken];
    }

    /**
     * Total item count across the whole cart (sum of quantities),
     * used to keep the header badge accurate.
     */
    private function cartCount($userId, $cartToken): int
    {
        return (int) CartModel::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn ($q) => $q->where('cart_token', $cartToken))
            ->sum('quantity');
    }

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
        [$userId, $cartToken] = $this->resolveCartOwner($request);

        $query = CartModel::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn ($q) => $q->where('cart_token', $cartToken));

        $existing = $query->where('product_id', $data['product_id'])->first();

        if ($existing) {
            // Item already in cart: bump the quantity instead of rejecting the add
            $existing->update([
                'quantity' => $existing->quantity + $data['quantity'],
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Cart quantity updated',
                'data'    => ['cart_token' => $cartToken],
                'count'   => $this->cartCount($userId, $cartToken),
            ], 200);
        }

        CartModel::create([
            'user_id'    => $userId,
            'cart_token' => $cartToken,
            'product_id' => $data['product_id'],
            'quantity'   => $data['quantity'],
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product added to cart',
            'data'    => ['cart_token' => $cartToken],
            'count'   => $this->cartCount($userId, $cartToken),
        ], 200);
    }

    /**
     * Update the quantity of one or more products in the cart.
     * Setting quantity to 0 removes that item.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'product_id'   => 'required|array',
            'product_id.*' => 'required|integer',
            'quantity'     => 'required|array',
            'quantity.*'   => 'required|integer|min:0',
        ]);

        [$userId, $cartToken] = $this->resolveCartOwner($request);

        $cartItems = CartModel::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn ($q) => $q->where('cart_token', $cartToken))
            ->whereIn('product_id', $data['product_id'])
            ->get()
            ->keyBy('product_id');

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Items not found in cart',
            ], 404);
        }

        foreach ($data['product_id'] as $index => $productId) {
            $item = $cartItems->get($productId);

            if (!$item) {
                continue;
            }

            $newQty = $data['quantity'][$index];

            if ($newQty === 0) {
                $item->delete();
            } else {
                $item->update(['quantity' => $newQty]);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Cart updated successfully',
            'count'   => $this->cartCount($userId, $cartToken),
        ], 200);
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

        $userId    = auth()->id();
        $cartToken = $request->input('cart_token') ?? $request->cookie('cart_token');

        if (!$userId && !$cartToken) {
            return response()->json([
                'message' => 'No cart identifier available',
            ], 422);
        }

        $cartItems = CartModel::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn ($q) => $q->where('cart_token', $cartToken))
            ->with('product.images')
            ->get();

        return response()->json([
            'status'     => 'success',
            'count'      => (int) $cartItems->sum('quantity'),
            'data'       => $cartItems,
            'cart_token' => $cartToken,
        ], 200);
    }

    /**
     * Remove one product from cart.
     * Route name is api.cart.remove → method must be named `remove` to match.
     */
    public function remove(Request $request)
    {
        $data = $request->validate(['product_id' => 'required|integer']);

        [$userId, $cartToken] = $this->resolveCartOwner($request);

        $deleted = CartModel::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn ($q) => $q->where('cart_token', $cartToken))
            ->where('product_id', $data['product_id'])
            ->delete();

        if (!$deleted) {
            return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Item removed successfully',
            'count'   => $this->cartCount($userId, $cartToken),
        ], 200);
    }

    /**
     * Clear all items from cart
     */
    public function clearCart(Request $request)
    {
        [$userId, $cartToken] = $this->resolveCartOwner($request);

        $deleted = CartModel::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn ($q) => $q->where('cart_token', $cartToken))
            ->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Cart cleared successfully',
            'deleted' => $deleted,
            'count'   => 0,
        ], 200);
    }

    /**
     * Render the cart view/modal contents for a given cart.
     * NOTE: was previously a debug stub (`dd('hii')`).
     */
    public function loadCartView(Request $request, $cart_id = null)
    {
        $userId    = auth()->id();
        $cartToken = $request->cookie('cart_token');

        $cartItems = CartModel::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn ($q) => $q->where('cart_token', $cartToken))
            ->with('product.images') // images needed by the cart_items partial
            ->get();

        return view('website.main.partials.cart', [
            'cartItems' => $cartItems,
            'count'     => (int) $cartItems->sum('quantity'),
        ]);
    }
}