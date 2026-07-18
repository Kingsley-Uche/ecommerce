<?php

namespace App\Http\Controllers;

use App\Models\CartModel;
use App\Models\Orders;
use App\Models\ProductModel;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Verify Signature
            |--------------------------------------------------------------------------
            */

            $signature = $request->header('x-paystack-signature');

            $computedSignature = hash_hmac(
                'sha512',
                $request->getContent(),
                config('services.paystack_secret')
            );

            if (
                empty($signature) ||
                !hash_equals($computedSignature, $signature)
            ) {

                Log::warning('Invalid Paystack webhook signature.', [
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'message' => 'Invalid signature'
                ], 401);

            }

            /*
            |--------------------------------------------------------------------------
            | Parse Payload
            |--------------------------------------------------------------------------
            */

            $event = $request->input('event');
            $data  = $request->input('data', []);

            if ($event !== 'charge.success') {

                return response()->json([
                    'message' => 'Event ignored.'
                ], 200);

            }

            /*
            |--------------------------------------------------------------------------
            | Validate Payload
            |--------------------------------------------------------------------------
            */

            if (
                empty($data['reference']) ||
                empty($data['status']) ||
                $data['status'] !== 'success'
            ) {

                Log::warning('Invalid Paystack payload.', $data);

                return response()->json([
                    'message' => 'Invalid payload.'
                ], 400);

            }

            $reference = $data['reference'];

            DB::beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | Lock Order
            |--------------------------------------------------------------------------
            */

            $order = Orders::where('payment_ref', $reference)->where('payment_status', 'pending')
                ->lockForUpdate()
                ->first();

            if (!$order) {

                DB::rollBack();

                Log::warning('Webhook received for unknown order.', [
                    'reference' => $reference
                ]);

                return response()->json([
                    'message' => 'Order not found.'
                ], 200);

            }

            /*
            |--------------------------------------------------------------------------
            | Idempotency
            |--------------------------------------------------------------------------
            */

            

            /*
            |--------------------------------------------------------------------------
            | Verify Amount
            |--------------------------------------------------------------------------
            */

            $paidAmount = round(
                ($data['amount'] ?? 0) / 100,
                2
            );

            if ((float) $order->total_cost !== $paidAmount) {

                throw new \Exception(
                    "Amount mismatch for {$reference}"
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Verify Currency
            |--------------------------------------------------------------------------
            */

           

            /*
            |--------------------------------------------------------------------------
            | Fetch Ordered Products
            |--------------------------------------------------------------------------
            */

            $items = ProductOrder::where('order_id', $order->id)->get();

            foreach ($items as $item) {

                $product = ProductModel::lockForUpdate()
                    ->find($item->product_id);

                if (!$product) {

                    throw new \Exception(
                        "Product {$item->product_id} not found."
                    );

                }

                if ($product->stock < $item->qty_bought) {

                    throw new \Exception(
                        "{$product->name} is out of stock."
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Reduce Stock
                |--------------------------------------------------------------------------
                */

                $product->decrement(
                    'stock',
                    $item->qty_bought
                );

                /*
                |--------------------------------------------------------------------------
                | Increase Sales Counter
                |--------------------------------------------------------------------------
                */

                $product->increment(
                    'num_sold',
                    $item->qty_bought
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Update Order
            |--------------------------------------------------------------------------
            */

            $order->update([

                'payment_status' => 'confirmed',

                'total_paid' => $paidAmount,

                'paid_at' => now(),

            ]);

            /*
            |--------------------------------------------------------------------------
            | Remove Cart
            |--------------------------------------------------------------------------
            */

            CartModel::where(
                'cart_token',
                $order->cart_token
            )->delete();

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | Queue Receipt / Notification
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | SendReceiptJob::dispatch($order);
            |
            */

            Log::info('Payment confirmed.', [

                'reference' => $reference,

                'order_id' => $order->id,

            ]);

            return response()->json([
                'message' => 'Webhook processed.'
            ], 200);

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Paystack webhook failed.', [

                'message' => $e->getMessage(),

                'trace' => $e->getTraceAsString(),

            ]);

            return response()->json([
                'message' => 'Internal server error.'
            ], 500);

        }
    }
}