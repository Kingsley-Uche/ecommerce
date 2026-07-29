<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\ProductModel;
use App\Models\ProductOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orders()
    {
        $orders = Orders::select(
                'id', 'payment_ref', 'user_name', 'email_address', 'phone',
                'delivery_city', 'delivery_address', 'product_id', 'order_status',
                'payment_status', 'cart_token', 'total_cost', 'total_paid', 'created_at'
            )
            ->latest('created_at')
            ->paginate(25);

        return view('admin.dashboard.pages.orders.index', compact('orders'));
    }

public function show($id)
{
    $order = Orders::findOrFail($id);

    $product_order = ProductOrder::where('order_id', $order->id)
        ->with([
            'product:id,name,price'
        ])
        ->select(
            'id',
            'order_id',
            'product_id',
            'qty_bought',
            'size'
        )
        ->get();


    return view(
        'admin.dashboard.pages.orders.show',
        compact('order', 'product_order')
    );
}

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status'   => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order = Orders::findOrFail($id);

        $order->update([
            'order_status'   => $request->order_status,
            'payment_status' => $request->payment_status,
        ]);

        return redirect()
            ->route('admin.orders.show', $id)
            ->with('success', 'Order status updated successfully.');
    }
}