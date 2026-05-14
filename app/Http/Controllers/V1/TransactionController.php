<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentRef;
use App\Models\CartModel;
use App\Models\StoreDetailsModel;
use App\Models\Orders;
use Illuminate\Support\Facades\Validator;
use Exception;


class TransactionController extends Controller
{

public function initiatePay(Request $request)
{
    // --------------------------
    // 1. STRICT VALIDATION
    // --------------------------
    $validator = Validator::make($request->all(), [
        'product_id'        => 'required|array',
        'product_id.*'      => 'required|integer|min:1',

        'quantity'          => 'required|array',
        'quantity.*'        => 'required|integer|min:0|max:999',

        'customer_name'     => 'required|string|min:2|max:100',
        'customer_email'    => 'required|email',
        'customer_phone'    => ['required', 'regex:/^[0-9\-\+\(\)\s]{6,20}$/'],

        'delivery_location' => 'required|string|min:2|max:200',
        'delivery_address'  => 'required|string|min:4|max:255',

        'cart_token'        => 'required|string|max:150',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Invalid cart data',
            'errors'  => $validator->errors(),
            'status'  => 'error'
        ], 422);
    }

    // --------------------------
    // 2. SANITIZATION (ACID MODE)
    // --------------------------
    $data = $validator->validated();

    $clean = fn($v) => trim(strip_tags(htmlspecialchars($v, ENT_QUOTES, 'UTF-8')));

    $data['customer_name']     = $clean($data['customer_name']);
    $data['customer_email']    = $clean($data['customer_email']);
    $data['customer_phone']    = $clean($data['customer_phone']);
    $data['delivery_location'] = $clean($data['delivery_location']);
    $data['delivery_address']  = $clean($data['delivery_address']);
    $cartToken = $clean($data['cart_token']);

    $userId = auth()->id();

    // --------------------------
    // 3. FETCH & VALIDATE CART
    // --------------------------
    $cartItems = CartModel::with('product:id,name,price')
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->when(!$userId, fn($q) => $q->where('cart_token', $cartToken))
        ->whereIn('product_id', array_map('intval', $data['product_id']))
        ->get();

    if ($cartItems->isEmpty()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Items not found in cart'
        ], 404);
    }

    // --------------------------
    // 4. PROCESS CART ITEMS
    // --------------------------
    $subtotal = 0;

    foreach ($cartItems as $index => $item) {
        $newQty = intval($data['quantity'][$index] ?? 0);

        if ($newQty === 0) {
            $item->delete();
            continue;
        }

        $item->update(['quantity' => $newQty]);
        $subtotal += ($item->product->price * $newQty);
    }

    if ($subtotal <= 0) {
        return response()->json([
            'status'  => 'error',
            'message' => 'No valid items to process'
        ], 400);
    }

    // --------------------------
    // 5. TAX & TOTAL
    // --------------------------
    $taxRate = 0.075;
    $total   = $subtotal + ($subtotal * $taxRate);

    // --------------------------
    // 6. FETCH STORE INFO
    // --------------------------
    $shop = StoreDetailsModel::select(
        'store_name','email','phone','address',
        'tagline','logo_path','social_links'
    )->first();

    // --------------------------
    // 7. GENERATE PAYMENT REF
    // --------------------------
    $payment_ref = $this->generateRef($shop->store_name);

    // --------------------------
    // 8. INIT PAYSTACK
    // --------------------------
    $ch = curl_init();

    try {
        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://api.paystack.co/transaction/initialize",
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'email'     => $data['customer_email'],
                'amount'    => intval($total * 100),
                'reference' => $payment_ref,
            ]),
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer " . env("PAYMENTBEARER"),
                "Cache-Control: no-cache"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('cURL error: ' . curl_error($ch));
        }

        $response = json_decode($result, true);

        if (!$response || ($response['status'] ?? false) !== true) {
            throw new \Exception('Paystack initialization failed: ' . ($response['message'] ?? 'Unknown error'));
        }

        // --------------------------
        // 9. CREATE ORDER + PAYMENTREF
        // --------------------------
        $orders = Orders::updateOrCreate(
            ['cart_token' => $cartItems->first()->cart_token],
            [
                'payment_ref'     => $payment_ref,
                'user_name'       => $data['customer_name'],
                'email_address'   => $data['customer_email'],
                'phone'           => $data['customer_phone'],
                'delivery_city'   => $data['delivery_location'],
                'delivery_address'=> $data['delivery_address'],
                'product_id'      => json_encode($data['product_id']),
                'cart_token'         => $cartItems->first()->cart_token,
                'total_cost'=>$total,
            ]
        );

        PaymentRef::create([
            'payment_ref' => $payment_ref,
            'order_id'    => $orders->id,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment initialized',
            'data'    => [
                'authorization_url' => $response['data']['authorization_url'],
                'reference'         => $response['data']['reference'],
                'access_code'       => $response['data']['access_code'],
                'cart_token'        => $cartToken
            ],
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Payment initialization error: ' . $e->getMessage(),
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
        'social_icons',
        'products'
    )->first();

    return view('website.main.pages.checkout', compact('cartItems', 'shop_data'));
}


    public function verifyPay(Request $request){
          //this is for Paystack
         $validator = Validator::make($request->all(), [
            'reference' => 'required|string',
        ]);

    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        
        $curl = curl_init();
        $reference=strip_tags($request->reference);
     curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/{$reference}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . env("PAYMENTBEARER"),
        "Cache-Control: no-cache"
    ],
]);


        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
         // echo "cURL Error #:" . $err;

         return response()->json(['success' => false, 'message' => 'Network error', 'error'=>$err], 422);

        } else {
            $info = json_decode($response);
            $data = $info->data;
    $amount = round(floatval($data->amount/100),2);
     $currency = $data->currency;  

     $orders = Orders::where('payment_ref',$data->reference)->update(
            [
                'total_paid'=>$amount,
                'payment_status'=>'confirmed',
            ]
            );
          
        

            return response()->json(['status' => 'success', 'message' => 'transaction confirmed',], 200);
        }
    


    }
     
    }
      
