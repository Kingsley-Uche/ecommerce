<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentRef;
use App\Models\CartModel;
use App\Models\StoreDetailsModel;
use App\Models\Orders;
use App\Models\ProductOrder;
use App\Models\ProductModel;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderPaidMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{

public function initiatePay(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | 1. VALIDATE REQUEST
    |--------------------------------------------------------------------------
    */

    $validator = Validator::make($request->all(), [

        'product_id'           => 'required|array|min:1',
        'product_id.*'         => 'required|integer|distinct|min:1',

        'quantity'             => 'required|array|min:1',
        'quantity.*'           => 'required|integer|min:0|max:999',

        'customer_name'        => 'required|string|min:2|max:100',
        'customer_email'       => 'required|email:rfc,dns|max:150',
        'customer_phone'       => ['required','regex:/^[0-9\-\+\(\)\s]{6,20}$/'],

        'delivery_location'    => 'required|string|min:2|max:200',
        'delivery_address'     => 'required|string|min:4|max:255',

        'cart_token'           => 'required|string|max:150',
        'size'                 =>'nullable|array',
         'size.*'                 =>'nullable|string',
         'extra_information'=>'nullable|string|max:640'

    ]);

    if ($validator->fails()) {

        return response()->json([
            'status'  => 'error',
            'message' => 'Invalid cart data.',
            'errors'  => $validator->errors(),
        ],422);

    }

    /*
    |--------------------------------------------------------------------------
    | 2. SANITIZE INPUT
    |--------------------------------------------------------------------------
    */

    $data = $validator->validated();

    $clean = fn ($value) => trim(
        strip_tags(
            htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
        )
    );

    $data['customer_name']      = $clean($data['customer_name']);
    $data['customer_email']     = strtolower($clean($data['customer_email']));
    $data['customer_phone']     = $clean($data['customer_phone']);
    $data['delivery_location']  = $clean($data['delivery_location']);
    $data['delivery_address']   = $clean($data['delivery_address']);

    $cartToken = $clean($data['cart_token']);

    /*
    |--------------------------------------------------------------------------
    | Quantity/Product consistency
    |--------------------------------------------------------------------------
    */

    if (
    count($data['product_id']) !== count($data['quantity']) ||
    count($data['product_id']) !== count($data['size'])
) {

    return response()->json([
        'status'=>'error',
        'message'=>'Invalid cart payload.'
    ],422);

}

      

    $userId = auth()->id();

    /*
    |--------------------------------------------------------------------------
    | 3. FETCH CART
    |--------------------------------------------------------------------------
    */

    $cartItems = CartModel::with([
            'product:id,name,price,stock,num_sold'
        ])
        ->where(function ($query) use ($userId,$cartToken){

            if ($userId){

                $query->where('user_id',$userId)
                      ->orWhere('cart_token',$cartToken);

            }else{

                $query->where('cart_token',$cartToken);

            }

        })
        ->whereIn(
            'product_id',
            array_map('intval',$data['product_id'])
        )
        ->get();

    if ($cartItems->isEmpty()) {

        return response()->json([
            'status'  => 'error',
            'message' => 'No cart items found.'
        ],404);

    }

    /*
    |--------------------------------------------------------------------------
    | Ensure every requested product exists in cart
    |--------------------------------------------------------------------------
    */

    if ($cartItems->count() !== count($data['product_id'])) {

        return response()->json([
            'status'  => 'error',
            'message' => 'Some products are no longer available in your cart.'
        ],409);

    }

    /*
    |--------------------------------------------------------------------------
    | Re-index quantities by product id
    |--------------------------------------------------------------------------
    */

   $requestedProducts = [];

foreach ($data['product_id'] as $index => $productId) {

    $requestedProducts[(int)$productId] = [
        'quantity' => (int) $data['quantity'][$index],
        'size'     => $data['size'][$index] ?? null,
    ];

}

    /*
    |--------------------------------------------------------------------------
    | Validate stock & calculate subtotal
    |--------------------------------------------------------------------------
    */

    $subtotal = 0;

    foreach ($cartItems as $item){

  $productData = $requestedProducts[$item->product_id] ?? null;

$requestedQty = $productData['quantity'] ?? 0;

$requestedSize = $productData['size'] ?? null;
        /*
        |--------------------------------------------------------------
        | Remove zero quantity from checkout
        |--------------------------------------------------------------
        */

        if ($requestedQty <= 0){

            continue;

        }

        if (!$item->product){

            return response()->json([
                'status'=>'error',
                'message'=>"Product no longer exists."
            ],404);

        }

        /*
        |--------------------------------------------------------------
        | Stock validation
        |--------------------------------------------------------------
        */

        if ($item->product->stock < $requestedQty){

            return response()->json([
                'status'=>'error',
                'message'=> "{$item->product->name} has only {$item->product->stock} item(s) remaining."
            ],409);

        }

        $subtotal += ($item->product->price * $requestedQty);

    }

    if ($subtotal <= 0){

        return response()->json([
            'status'=>'error',
            'message'=>'Your cart is empty.'
        ],400);

    }

    /*
    |--------------------------------------------------------------------------
    | TAX
    |--------------------------------------------------------------------------
    */

    $taxRate = 0;

    $taxAmount = round(
        $subtotal * $taxRate,
        2
    );

    $total = round(
        $subtotal + $taxAmount,
        2
    );

    /*
    |--------------------------------------------------------------------------
    | STORE DETAILS
    |--------------------------------------------------------------------------
    */

    $shop = StoreDetailsModel::select(
        'store_name',
        'email',
        'phone',
        'address',
        'tagline',
        'logo_path',
        'social_links'
    )->first();

    if (!$shop){

        return response()->json([
            'status'=>'error',
            'message'=>'Store configuration is missing.'
        ],500);

    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT REFERENCE
    |--------------------------------------------------------------------------
    */

    $payment_ref = $this->generateRef($shop->store_name);

    /*
    |--------------------------------------------------------------------------
    | PAYSTACK INITIALIZATION
    |--------------------------------------------------------------------------
    */

    $ch = curl_init();

    try{

        curl_setopt_array($ch,[

            CURLOPT_URL => "https://api.paystack.co/transaction/initialize",

            CURLOPT_POST => true,

            CURLOPT_POSTFIELDS => http_build_query([

                'email'     => $data['customer_email'],
                'amount'    => (int) round($total * 100),
                'reference' => $payment_ref,

            ]),

            CURLOPT_HTTPHEADER => [

                "Authorization: Bearer ".config('services.paystack_secret'),

                "Cache-Control: no-cache",

            ],

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_TIMEOUT => 30,

        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)){

            throw new \Exception(curl_error($ch));

        }

        $response = json_decode($result,true);

        if (
            !$response ||
            !isset($response['status']) ||
            $response['status'] !== true
        ){

            throw new \Exception(
                $response['message'] ?? 'Unable to initialize payment.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Continue to Part 2
        |--------------------------------------------------------------------------
        */
        /*
        |--------------------------------------------------------------------------
        | DATABASE TRANSACTION (ACID)
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Create or update order
            |--------------------------------------------------------------------------
            */

            $order = Orders::updateOrCreate(

                [
                    'cart_token' => $cartToken,
                ],

                [
                    'payment_ref'        => $payment_ref,
                    'user_name'          => $data['customer_name'],
                    'email_address'      => $data['customer_email'],
                    'phone'              => $data['customer_phone'],
                    'delivery_city'      => $data['delivery_location'],
                    'delivery_address'   => $data['delivery_address'],
                    'total_cost'         => $total,
                    'total_paid'         => 0,
                    'payment_status'     => 'pending',
                    'extra_information' => $data['extra_information'] ?? null,
                    'product_id'=>$data['product_id'],
                ]

            );

            /*
            |--------------------------------------------------------------------------
            | Remove previous order items (idempotency)
            |--------------------------------------------------------------------------
            */

            ProductOrder::where('order_id', $order->id)->delete();

            /*
            |--------------------------------------------------------------------------
            | Create order items
            |--------------------------------------------------------------------------
            */
foreach ($cartItems as $item) {

    $productData = $requestedProducts[$item->product_id] ?? null;

    $requestedQty = $productData['quantity'] ?? 0;

    $requestedSize = $productData['size'] ?? null;


    if ($requestedQty <= 0) {
        continue;
    }


    /*
    |--------------------------------------------------------------------------
    | Lock product row
    |--------------------------------------------------------------------------
    */

    $product = ProductModel::lockForUpdate()
        ->find($item->product_id);


    if (!$product) {

        throw new \Exception(
            "Product {$item->product_id} no longer exists."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Double check stock inside transaction
    |--------------------------------------------------------------------------
    */

    if ($product->stock < $requestedQty) {

        throw new \Exception(
            "{$product->name} is out of stock."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Create Product Order
    |--------------------------------------------------------------------------
    */

    ProductOrder::create([

        'order_id'   => $order->id,

        'product_id' => $product->id,

        'qty_bought' => $requestedQty,

        'size'       => $requestedSize,

    ]);


    /*
    |--------------------------------------------------------------------------
    | Update cart quantity
    |--------------------------------------------------------------------------
    */

    $item->update([

        'quantity' => $requestedQty

    ]);

}

            /*
            |--------------------------------------------------------------------------
            | Payment Reference
            |--------------------------------------------------------------------------
            */

            PaymentRef::updateOrCreate(

                [
                    'payment_ref' => $payment_ref
                ],

                [
                    'order_id' => $order->id
                ]

            );

            /*
            |--------------------------------------------------------------------------
            | Remove zero-quantity items
            |--------------------------------------------------------------------------
            */

            CartModel::where(function ($query) use ($userId, $cartToken) {

                    if ($userId) {

                        $query->where('user_id', $userId)
                              ->orWhere('cart_token', $cartToken);

                    } else {

                        $query->where('cart_token', $cartToken);

                    }

                })
                ->whereIn(
                    'product_id',
                    array_keys($requestedProducts)
                )
                ->where('quantity', 0)
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Commit Transaction
            |--------------------------------------------------------------------------
            */

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'status'  => 'success',

                'message' => 'Payment initialized successfully.',

                'data' => [

                    'authorization_url' =>
                        $response['data']['authorization_url'],

                    'reference' =>
                        $response['data']['reference'],

                    'access_code' =>
                        $response['data']['access_code'],

                    'cart_token' =>
                        $cartToken,

                    'order_id' =>
                        $order->id,

                ]

            ], 200);

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Checkout transaction failed.', [

                'payment_ref' => $payment_ref,

                'cart_token'  => $cartToken,

                'message'     => $e->getMessage(),

                'trace'       => $e->getTraceAsString(),

            ]);

            return response()->json([

                'status'  => 'error',

                'message' => 'Unable to process your order. Please try again.',

            ], 500);

        }

    } catch (\Throwable $e) {

        Log::error('Paystack initialization failed.', [

            'message' => $e->getMessage(),

            'trace'   => $e->getTraceAsString(),

        ]);

        return response()->json([

            'status'  => 'error',

            'message' => 'Payment initialization failed.',

        ], 500);

    } finally {

        curl_close($ch);

    }

}
public function generateRef($shop_name)
{
    // Clean + remove spaces + remove illegal characters
    $cleanName = preg_replace('/[^A-Za-z0-9]/', '', strtoupper(trim($shop_name)));

    $maxSlugLength = 25 - strlen('YYYY-MM-DD-HH-MM-SS');
    $slug = substr($cleanName, 0, $maxSlugLength);

    $payment_ref = $slug . '-PAY-' . date('Y-m-d-H-i-s');
    $original = $payment_ref;

    $counter = 0;
    while (PaymentRef::where('payment_ref', $payment_ref)->exists()) {
        $counter++;
        $suffix = '-' . $counter;
        $payment_ref = substr($original, 0, (25 - strlen($suffix))) . $suffix;
    }

    return strtoupper($payment_ref);
}


      
public function checkout(Request $request, $cartToken)
{
    // Sanitize token
    $cartToken = strip_tags($cartToken);


    $userId = $request->user() ? $request->user()->id : null; //will this work if user is logged in?

    // Validate existence of token or logged-in user
    if (!$userId && !$cartToken) {
        return back()->withErrors(["message" => "Invalid cart information."]);
    }
if($userId && $cartToken){
    $userId=null;
    //use cart token only if both are present
}
    // Fetch cart items
    $cartItems = CartModel::when($userId, fn($q) => $q->where('user_id', $userId))
        ->when(!$userId, fn($q) => $q->where('cart_token', $cartToken))
        ->with('product.images')
        ->get();
    

    // If cart is empty, redirect back with message
    if ($cartItems->isEmpty()) {
        return back()->withErrors(["message" => "Cart is empty."]);
    }


    // Fetch shop/store details
    $shop_data = StoreDetailsModel::select(
        'store_name',
        'address',
        'phone',
        'email',
        'logo_path',
        'tagline',
        'social_links',
        'social_icons'
    )->first();

    return view('website.main.pages.checkout', compact('cartItems', 'shop_data'));
}


// public function verifyPay(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'reference' => 'required|string|max:150',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'status' => 'error',
//             'errors' => $validator->errors(),
//         ], 422);
//     }

//     $reference = trim(strip_tags($request->reference));

//     $curl = curl_init();

//     try {

//         curl_setopt_array($curl, [
//             CURLOPT_URL => "https://api.paystack.co/transaction/verify/{$reference}",
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_TIMEOUT => 30,
//             CURLOPT_HTTPHEADER => [
//                 "Authorization: Bearer " . config('services.paystack_secret'),
//                 "Cache-Control: no-cache",
//             ],
//         ]);

//         $response = curl_exec($curl);

//         if (curl_errno($curl)) {
//             throw new \Exception(curl_error($curl));
//         }

//         $result = json_decode($response, true);

//         if (
//             !$result ||
//             !isset($result['status']) ||
//             $result['status'] !== true
//         ) {
//             throw new \Exception(
//                 $result['message'] ?? 'Unable to verify transaction.'
//             );
//         }

//         $payment = $result['data'];

//         /*
//         |--------------------------------------------------------------------------
//         | Verify payment succeeded
//         |--------------------------------------------------------------------------
//         */

//         if (
//             $payment['status'] !== 'success' ||
//             strtolower($payment['gateway_response']) !== 'successful'
//         ) {

//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Payment has not been completed.'
//             ], 400);

//         }

//         DB::beginTransaction();

//         /*
//         |--------------------------------------------------------------------------
//         | Lock order
//         |--------------------------------------------------------------------------
//         */

//         $order = Orders::where('payment_ref', $reference)->where('payment_status', 'pending')
//             ->lockForUpdate()
//             ->first();

//         if (!$order) {
//             throw new \Exception('Order not found.');
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Idempotency
//         |--------------------------------------------------------------------------
//         */


//         /*
//         |--------------------------------------------------------------------------
//         | Verify amount
//         |--------------------------------------------------------------------------
//         */

//         $paidAmount = round($payment['amount'] / 100, 2);

//         if ((float)$order->total_cost != $paidAmount) {

//             throw new \Exception(
//                 "Amount mismatch. Expected {$order->total_cost}, received {$paidAmount}"
//             );

//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Verify currency
//         |--------------------------------------------------------------------------
//         */

//         if ($payment['currency'] !== 'NGN') {

//             throw new \Exception('Invalid payment currency.');

//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Process products
//         |--------------------------------------------------------------------------
//         */

//         $items = ProductOrder::where('order_id', $order->id)->get();

//         foreach ($items as $item) {

//             $product = ProductModel::lockForUpdate()->find($item->product_id);

//             if (!$product) {
//                 throw new \Exception(
//                     "Product {$item->product_id} not found."
//                 );
//             }

//             if ($product->stock < $item->qty_bought) {

//                 throw new \Exception(
//                     "{$product->name} is out of stock."
//                 );

//             }

//             $product->decrement(
//                 'stock',
//                 $item->qty_bought
//             );

//             $product->increment(
//                 'num_sold',
//                 $item->qty_bought
//             );

//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Update order
//         |--------------------------------------------------------------------------
//         */

//         $order->update([
//             'total_paid' => $paidAmount,
//             'payment_status' => 'confirmed',
//             'paid_at' => now(),
//         ]);

//         /*
//         |--------------------------------------------------------------------------
//         | Remove customer's cart
//         |--------------------------------------------------------------------------
//         */

//         CartModel::where('cart_token', $order->cart_token)->delete();

//         DB::commit();

//         return response()->json([
//             'status' => 'success',
//             'message' => 'Transaction confirmed.',
//         ]);

//     } catch (\Throwable $e) {

//         DB::rollBack();

//         Log::error('Payment verification failed.', [
//             'reference' => $reference,
//             'message' => $e->getMessage(),
//             'trace' => $e->getTraceAsString(),
//         ]);

//         return response()->json([
//             'status' => 'error',
//             'message' => 'Unable to verify payment.',
//         ], 500);

//     } finally {

//         curl_close($curl);

//     }
// }
public function verifyPay(Request $request)
{
    $validator = Validator::make($request->all(), [
        'reference' => 'required|string|max:150',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422);
    }

    $reference = trim(strip_tags($request->reference));

    try {
        // Optimized HTTP request via Laravel Http Client (Cleaner & faster overhead management)
        $response = Http::withToken(config('services.paystack_secret'))
            ->withHeaders(['Cache-Control' => 'no-cache'])
            ->timeout(30)
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if ($response->failed() || !($response->json('status') === true)) {
            throw new \Exception($response->json('message') ?? 'Unable to verify transaction.');
        }

        $payment = $response->json('data');

        if (
            $payment['status'] !== 'success' ||
            strtolower($payment['gateway_response']) !== 'successful'
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment has not been completed.'
            ], 400);
        }

        if ($payment['currency'] !== 'NGN') {
            throw new \Exception('Invalid payment currency.');
        }

        $paidAmount = round($payment['amount'] / 100, 2);

        // Execute critical database operations inside a transaction block
        return DB::transaction(function () use ($reference, $paidAmount, $payment) {

            $order = Orders::where('payment_ref', $reference)
                ->where('payment_status', 'pending')
                ->lockForUpdate()
                ->first();

            if (!$order) {
                throw new \Exception('Order not found or already processed.');
            }

            if ((float)$order->total_cost != $paidAmount) {
                throw new \Exception("Amount mismatch. Expected {$order->total_cost}, received {$paidAmount}");
            }

            $items = ProductOrder::where('order_id', $order->id)->get();
            $productIds = $items->pluck('product_id')->toArray();

            // Bulk lock and fetch all products to eliminate N+1 query overhead inside loops
            $products = ProductModel::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $product = $products->get($item->product_id);

                if (!$product) {
                    throw new \Exception("Product ID {$item->product_id} not found.");
                }

                if ($product->stock < $item->qty_bought) {
                    throw new \Exception("{$product->name} is out of stock.");
                }

                // Batch or individual direct attribute modification (Queued for execution)
                $product->decrement('stock', $item->qty_bought);
                $product->increment('num_sold', $item->qty_bought);
            }

            // Update order status
            $order->update([
                'total_paid' => $paidAmount,
                'payment_status' => 'confirmed',
                'paid_at' => now(),
            ]);

            // Clear Cart token
            CartModel::where('cart_token', $order->cart_token)->delete();
            

            // Dispatch Email asynchronously (outside blocking scope if queue workers are active)
            try {
                $customerEmail = $order->email_address ?? null;
                if ($customerEmail) {
                    Mail::to($customerEmail)->queue(new OrderPaidMail($order));
                    
                }
            } catch (\Throwable $mailEx) {
                Log::error('Failed to queue order confirmation email.', [
                    'order_id' => $order->id,
                    'error' => $mailEx->getMessage()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction confirmed.',
            ]);
        });

    } catch (\Throwable $e) {
        Log::error('Payment verification failed.', [
            'reference' => $reference,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage() ?: 'Unable to verify payment.',
        ], 500);
    }
}
     
    }
      
