{{--
    website/main/partials/cart.blade.php

    Server-rendered cart contents. Used by:
      - CartController::loadCartView()  → full page view (route: cart.view)
      - Can also be @include'd directly inside cart_modal.blade.php's
        #cart_items_container if you want the modal to render via Blade
        on first load instead of waiting on inner.js's fetch.

    Expects:
      $cartItems  → Collection of CartModel, each with ->product eager loaded
                    (product has: id, name, description, price, images)
      $count      → int, total quantity across the cart

    Column layout intentionally mirrors CheckoutContent() in inner.js
    (product / price / quantity / total) so the page looks identical
    whether it's rendered server-side here or rebuilt client-side by JS
    inside the cart modal.
--}}

@if($cartItems->isEmpty())

    <div class="text-center py-5 text-muted" id="cart_items_container">
        <i class="bi bi-cart-x" style="font-size: 2.5rem;"></i>
        <p class="mt-3 mb-0">Your cart is empty.</p>
        <a href="{{ route('home') }}" class="btn btn-dark mt-3">Continue Shopping</a>
    </div>

@else

    <div id="cart_items_container">
        @foreach($cartItems as $item)
            @php
                $product = $item->product;
                $price   = (float) ($product->price ?? 0);
                $qty     = (int) $item->quantity;
                $total   = $price * $qty;
                $image   = $product->images->first()->image_path ?? null;
            @endphp

            <div class="cart-item border-bottom py-3" data-product-id="{{ $product->id ?? '' }}">
                <div class="row align-items-center">

                    {{-- Product --}}
                    <div class="col-lg-5 col-12 mb-2 mb-lg-0">
                        <div class="product-info d-flex align-items-center">
                            <img
                                src="{{ $image ? asset('storage/' . $image) : asset('assets/img/default-product.png') }}"
                                alt="{{ $product->name ?? 'Product' }}"
                                width="70"
                                class="img-fluid me-3 rounded"
                                style="object-fit: contain; height: 70px;"
                            >
                            <div>
                                <h6 class="mb-1">{{ $product->name ?? 'Unknown product' }}</h6>
                                @if(!empty($product->description))
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($product->description, 60) }}</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="col-lg-2 col-4 text-center">
                        <span class="current-price"><strong>₦{{ number_format($price, 2) }}</strong></span>
                    </div>

                    {{-- Quantity --}}
                    <div class="col-lg-2 col-4 text-center">
                        <div class="quantity-selector d-flex align-items-center justify-content-center gap-1">
                            <button type="button" class="quantity-btn decrease btn btn-sm btn-outline-dark" data-price="{{ $price }}">
                                <i class="bi bi-dash"></i>
                            </button>

                            <input
                                type="number"
                                class="quantity-input form-control form-control-sm text-center"
                                value="{{ $qty }}"
                                min="1"
                                name="quantity[]"
                                style="width: 56px;"
                            >
                            <input type="hidden" name="initial_quantity[]" value="{{ $qty }}">
                            <input type="hidden" name="product_id[]" value="{{ $product->id ?? '' }}">
                            <input type="hidden" name="total" value="{{ number_format($total, 2, '.', '') }}">

                            <button type="button" class="quantity-btn increase btn btn-sm btn-outline-dark" data-price="{{ $price }}">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Line total --}}
                    <div class="col-lg-2 col-3 text-center item-total">
                        <strong>₦{{ number_format($total, 2) }}</strong>
                    </div>

                    {{-- Remove --}}
                    <div class="col-lg-1 col-1 text-center">
                        <button
                            type="button"
                            class="remove-item btn btn-sm btn-link text-danger p-0"
                            data-product-id="{{ $product->id ?? '' }}"
                            aria-label="Remove {{ $product->name ?? 'item' }}"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

@endif
