<!-- ============================================================
     CART MODAL
============================================================= -->
<div class="modal fade"
     id="cart_modal"
     tabindex="-1"
     aria-labelledby="cart_modal_label"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="cart_modal_label">
                    Cart Contents
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <section id="cart" class="cart section">
                    <div class="container-fluid">
                        <div class="row">

                            <!-- Cart Items -->
                            <div class="col-lg-8">
                                <div class="cart-items">

                                    <!-- Header -->
                                    <div class="cart-header d-none d-lg-block">
                                        <div class="row align-items-center">
                                            <div class="col-lg-6">
                                                <h5>Product</h5>
                                            </div>
                                            <div class="col-lg-2 text-center">
                                                <h5>Price</h5>
                                            </div>
                                            <div class="col-lg-2 text-center">
                                                <h5>Quantity</h5>
                                            </div>
                                            <div class="col-lg-2 text-center">
                                                <h5>Total</h5>
                                            </div>
                                        </div>
                                    </div>

                                    <form
                                        id="update_cart_form"
                                        method="POST"
                                        action="{{ route('api.cart.update') }}">

                                        @csrf

                                        <div id="cart_items_container">

                                            @forelse($cartItems as $item)

                                                @php
                                                    $product = $item['product'] ?? [];
                                                    $qty = $item['quantity'] ?? 1;
                                                    $selectedSize = $item['size'] ?? '';
                                                    $productId = $product['id'] ?? null;
                                                    $cartItemId = $item['id'] ?? null;
                                                    $productName = $product['name'] ?? 'Unknown Product';
                                                    $productDescription = $product['description'] ?? null;
                                                    $price = $product['price'] ?? 0;
                                                    $total = $price * $qty;
                                                    $images = $product['images'] ?? collect();
                                                    $image = optional($images->first())->image_path;
                                                @endphp

                                                <div class="cart-item" data-product-id="{{ $productId }}">
                                                    <div class="row align-items-center">

                                                        <!-- Product Information -->
                                                        <div class="col-lg-6 col-12 mt-3 mt-lg-0 mb-lg-0 mb-3">
                                                            <div class="product-info d-flex align-items-center">
                                                                <div class="product-image">
                                                                    <img
                                                                        src="{{ $image ? asset('storage/'.$image) : asset('assets/img/default-product.png') }}"
                                                                        class="img-fluid"
                                                                        loading="lazy"
                                                                        alt="{{ $productName }}">
                                                                </div>

                                                                <div class="product-details">
                                                                    <h6 class="product-title">
                                                                        {{ $productName }}
                                                                    </h6>

                                                                    @if($productDescription)
                                                                        <small class="text-muted d-block mb-2">
                                                                            {{ Str::limit($productDescription, 80) }}
                                                                        </small>
                                                                    @endif

                                                                    <div class="product-meta">
                                                                        @if(!empty($product['color']))
                                                                            <span class="product-color me-2">
                                                                                Color: {{ $product['color'] }}
                                                                            </span>
                                                                        @endif

                                                                        <span class="product-size">
                                                                            Size:
                                                                            <select
                                                                                name="size[]"
                                                                                class="form-select form-select-sm d-inline-block w-auto ms-1 size-selector"
                                                                                data-product-id="{{ $productId }}"
                                                                                data-cart-item-id="{{ $cartItemId }}">

                                                                                @foreach(['XS','S','M','L','XL','XXL'] as $size)
                                                                                    <option
                                                                                        value="{{ $size }}"
                                                                                        {{ $selectedSize == $size ? 'selected' : '' }}>
                                                                                        {{ $size }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </span>
                                                                    </div>

                                                                    <button
                                                                        type="button"
                                                                        class="remove-item btn btn-link text-danger p-0 mt-2"
                                                                        data-product-id="{{ $productId }}"
                                                                        aria-label="Remove {{ $productName }}">
                                                                        <i class="bi bi-trash"></i> Remove
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Price -->
                                                        <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                                                            <div class="price-tag">
                                                                <span class="current-price">
                                                                    ₦{{ number_format($price, 2) }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Quantity -->
                                                        <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                                                            <div class="quantity-selector d-inline-flex align-items-center">
                                                                <button
                                                                    type="button"
                                                                    class="quantity-btn decrease btn btn-sm btn-outline-secondary"
                                                                    data-price="{{ $price }}">
                                                                    <i class="bi bi-dash"></i>
                                                                </button>

                                                                <input
                                                                    type="number"
                                                                    class="quantity-input form-control form-control-sm text-center mx-1"
                                                                    name="quantity[]"
                                                                    value="{{ $qty }}"
                                                                    min="1"
                                                                    style="width: 50px;">

                                                                <input
                                                                    type="hidden"
                                                                    name="initial_quantity[]"
                                                                    value="{{ $qty }}">

                                                                <input
                                                                    type="hidden"
                                                                    name="product_id[]"
                                                                    value="{{ $productId }}">

                                                                <button
                                                                    type="button"
                                                                    class="quantity-btn increase btn btn-sm btn-outline-secondary"
                                                                    data-price="{{ $price }}">
                                                                    <i class="bi bi-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Total -->
                                                        <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center mr-1">
                                                            <div class="item-total">
                                                                <span class="badge rounded-pill bg-dark px-1 py-2 fs-6 item-total-badge" data-price="{{ $price }}">
                                                                    ₦{{ number_format($total, 2) }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            @empty
                                                <div class="text-center py-5">
                                                    <i class="bi bi-cart-x fs-1 text-muted"></i>
                                                    <p class="mt-3 text-muted">
                                                        Your cart is empty.
                                                    </p>
                                                </div>
                                            @endforelse

                                        </div>

                                        <!-- Cart Actions -->
                                        <div class="cart-actions mt-4">
                                            <div class="row">
                                                <!-- Coupon -->
                                                <div class="col-lg-6 mb-3 mb-lg-0">
                                                    <div class="coupon-form">
                                                        <div class="input-group">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                name="coupon_code"
                                                                id="coupon_code"
                                                                placeholder="Coupon code">
                                                            <button
                                                                class="btn btn-outline-secondary"
                                                                type="button"
                                                                id="apply_coupon">
                                                                Apply Coupon
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Update / Clear -->
                                                <div class="col-lg-6 text-lg-end">
                                                    <button
                                                        type="submit"
                                                        class="btn btn-outline-dark me-2">
                                                        <i class="bi bi-arrow-clockwise"></i> Update Cart
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-danger"
                                                        id="clear_cart">
                                                        <i class="bi bi-trash"></i> Clear Cart
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>

                            <!-- =======================================================
                                ORDER SUMMARY
                            ======================================================== -->
                            <div class="col-lg-4 mt-4 mt-lg-0">
                                <div class="cart-summary border p-4 rounded bg-light">
                                    <h4 class="summary-title mb-3">
                                        Order Summary
                                    </h4>

                                    <!-- Subtotal -->
                                    <div class="summary-item d-flex justify-content-between mb-2">
                                        <span class="summary-label">Subtotal</span>
                                        <span class="summary-value" id="cart_subtotal">
                                            ₦{{ number_format(collect($cartItems)->sum(fn($item) => ($item['product']['price'] ?? 0) * ($item['quantity'] ?? 1)), 2) }}
                                        </span>
                                    </div>

                                    <!-- Shipping -->
                                    <div class="summary-item shipping-item mb-2">
                                        <span class="summary-label d-block mb-1">Shipping</span>
                                        <div class="shipping-options ps-3">
                                            <div class="form-check">
                                                <input class="form-check-input shipping-option" type="radio" name="shipping" id="shipping_standard" value="standard" checked>
                                                <label class="form-check-label" for="shipping_standard">
                                                    Standard Delivery - ₦<span id="shipping_standard_price">0.00</span>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input shipping-option" type="radio" name="shipping" id="shipping_express" value="express">
                                                <label class="form-check-label" for="shipping_express">
                                                    Express Delivery - ₦<span id="shipping_express_price">0.00</span>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input shipping-option" type="radio" name="shipping" id="shipping_free" value="free">
                                                <label class="form-check-label" for="shipping_free">
                                                    Free Shipping
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tax -->
                                    <div class="summary-item d-flex justify-content-between mb-2">
                                        <span class="summary-label">Tax</span>
                                        <span class="summary-value" id="cart_tax">₦0.00</span>
                                    </div>

                                    <!-- Discount -->
                                    <div class="summary-item discount d-flex justify-content-between mb-2">
                                        <span class="summary-label">Discount</span>
                                        <span class="summary-value" id="cart_discount">₦0.00</span>
                                    </div>

                                    <!-- Total -->
                                    <div class="summary-total d-flex justify-content-between border-top pt-3 mt-3 fw-bold fs-5">
                                        <span class="summary-label">Total</span>
                                        <span class="summary-value" id="cart_total"> 
                                             ₦{{ number_format(collect($cartItems)->sum(fn($item) => ($item['product']['price'] ?? 0) * ($item['quantity'] ?? 1)), 2) }}
                                            </span>
                                    </div>

                                    <!-- Checkout Button -->
                                    <div class="checkout-button mt-4">
                                        <a href="#" class="btn btn-dark w-100 cart_update py-2" data-info="checkout">
                                            Proceed to Checkout <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>

                                    <!-- Continue Shopping -->
                                    <div class="continue-shopping mt-2">
                                        <a href="#" class="btn btn-link text-decoration-none w-100 cart_update text-dark" data-info="continue">
                                            <i class="bi bi-arrow-left"></i> Continue Shopping
                                        </a>
                                    </div>

                                    <!-- Payment Methods -->
                                    <div class="payment-methods mt-4 text-center">
                                        <p class="payment-title text-muted small mb-2">We Accept</p>
                                        <div class="payment-icons fs-4 text-secondary d-flex justify-content-around">
                                            <i class="bi bi-credit-card"></i>
                                            <i class="bi bi-paypal"></i>
                                            <i class="bi bi-wallet2"></i>
                                            <i class="bi bi-bank"></i>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-outline-dark"
                    data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>