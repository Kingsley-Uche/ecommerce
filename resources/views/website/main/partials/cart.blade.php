{{--
    website/main/partials/cart_items.blade.php

    Expects:
      $cartItems → Collection of CartModel, each with ->product eager loaded
                   product has: id, name, description, price, images, sizes
      $count     → int, total quantity across the cart
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
                $product      = $item->product;
                $price        = (float) ($product->price ?? 0);
                $qty          = (int) $item->quantity;
                $total        = $price * $qty;
                $image        = $product->images->first()->image_path ?? null;
                $selectedSize = $item->size ?? null;
                $hasSizes     = $product->sizes && $product->sizes->isNotEmpty();
            @endphp

            <div class="cart-item border-bottom py-3"
                 data-product-id="{{ $product->id ?? '' }}">

                <div class="row align-items-center g-2">

                    {{-- Product — image + name + description --}}
                    <div class="col-lg-4 col-12 mb-2 mb-lg-0">
                        <div class="product-info d-flex align-items-center gap-3">
                            <img
                                src="{{ $image
                                    ? asset('storage/' . $image)
                                    : asset('assets/img/default-product.png') }}"
                                alt="{{ $product->name ?? 'Product' }}"
                                width="70"
                                class="img-fluid rounded flex-shrink-0"
                                style="object-fit: contain; height: 70px;"
                            >
                            <div>
                                <h6 class="mb-1">{{ $product->name ?? 'Unknown product' }}</h6>
                                @if(!empty($product->description))
                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($product->description, 60) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Size selector --}}
                    <div class="col-lg-2 col-6 text-center mb-2 mb-lg-0">
                        <label class="form-label small text-muted d-block d-lg-none">Size</label>
                        <select
                            class="form-select form-select-sm size-selector"
                            data-product-id="{{ $product->id ?? '' }}"
                            data-cart-item-id="{{ $item->id ?? '' }}"
                            style="width: auto; margin: 0 auto;"
                        >
                            @if($hasSizes)
                                @foreach($product->sizes as $size)
                                    @php $sizeVal = $size->size ?? $size; @endphp
                                    <option
                                        value="{{ $sizeVal }}"
                                        {{ $selectedSize == $sizeVal ? 'selected' : '' }}
                                    >
                                        {{ $sizeVal }}
                                    </option>
                                @endforeach
                            @else
                                {{-- Fallback generic sizes --}}
                                @foreach(['S','M','L','XL','XXL'] as $sizeVal)
                                    <option
                                        value="{{ $sizeVal }}"
                                        {{ $selectedSize == $sizeVal ? 'selected' : '' }}
                                    >
                                        {{ $sizeVal }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <input
                            type="hidden"
                            name="size[]"
                            value="{{ $selectedSize ?? 'M' }}"
                            class="size-input"
                        >
                    </div>

                    {{-- Price --}}
                    <div class="col-lg-2 col-6 text-center mb-2 mb-lg-0">
                        <label class="form-label small text-muted d-block d-lg-none">Price</label>
                        <span class="current-price">
                            <strong>₦{{ number_format($price, 2) }}</strong>
                        </span>
                    </div>

                    {{-- Quantity stepper --}}
                    <div class="col-lg-2 col-6 text-center">
                        <label class="form-label small text-muted d-block d-lg-none">Qty</label>
                        <div class="quantity-selector d-flex align-items-center justify-content-center gap-1">

                            <button
                                type="button"
                                class="quantity-btn decrease btn btn-sm btn-outline-dark"
                                data-price="{{ $price }}"
                            >
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
                      
                            <select name="size[]" class="form-control form-control-sm" style="width: 56px;">
                            <input type="hidden" name="initial_quantity[]" value="{{ $qty }}">
                            <input type="hidden" name="product_id[]"       value="{{ $product->id ?? '' }}">
                            <input type="hidden" name="total"              value="{{ number_format($total, 2, '.', '') }}">

                            <button
                                type="button"
                                class="quantity-btn increase btn btn-sm btn-outline-dark"
                                data-price="{{ $price }}"
                            >
                                <i class="bi bi-plus"></i>
                            </button>

                        </div>
                    </div>

                    {{-- Line total --}}
                    <div class="col-lg-1 col-4 text-center item-total">
                        <label class="form-label small text-muted d-block d-lg-none">Total</label>
                        <strong>₦{{ number_format($total, 2) }}</strong>
                    </div>

                    {{-- Remove --}}
                    <div class="col-lg-1 col-2 text-center">
                        <button
                            type="button"
                            class="remove-item btn btn-sm btn-link text-danger p-0 mt-3 mt-lg-0"
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

    {{-- Hidden cart item IDs for JS reference --}}
    <div id="cart-update-data" style="display:none;">
        @foreach($cartItems as $item)
            <input type="hidden" class="cart-item-id" value="{{ $item->id ?? '' }}">
        @endforeach
    </div>

@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Size selector → update hidden input + optional AJAX ── */
    document.querySelectorAll('.size-selector').forEach(function (select) {
        select.addEventListener('change', function () {
            const productId  = this.dataset.productId;
            const cartItemId = this.dataset.cartItemId;
            const size       = this.value;

            // Keep the hidden input in sync so the cart-update form sends it
            const hiddenInput = this.closest('.cart-item').querySelector('.size-input');
            if (hiddenInput) {
                hiddenInput.value = size;
            }

            // AJAX update — fires immediately when size is changed
            updateCartItemSize(cartItemId, productId, size);
        });
    });

    async function updateCartItemSize(cartItemId, productId, size) {
        // Requires a route:
        //   Route::post('/cart/size', [CartController::class, 'updateSize'])->name('cart.update.size');
        // If you don't have this route yet, this call will silently fail and
        // the size is still sent when the "Update Cart" button is pressed
        // via the form's size[] hidden inputs.
        const url = window.CART_SIZE_URL ?? null;
        if (!url) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    cart_item_id: cartItemId,
                    product_id:   productId,
                    size:         size,
                }),
            });

            const json = await response.json().catch(() => null);

            if (!response.ok) {
                if (typeof toast === 'function') {
                    toast(json?.message ?? 'Could not update size.', 'error');
                }
                return;
            }

            if (typeof toast === 'function') {
                toast(json?.message ?? 'Size updated.', 'success');
            }

            // If size change affects price, update the displayed price
            if (json?.new_price) {
                const cartItem   = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
                const priceEl    = cartItem?.querySelector('.current-price strong');
                const totalEl    = cartItem?.querySelector('.item-total strong');
                const qtyInput   = cartItem?.querySelector('.quantity-input');

                if (priceEl) {
                    priceEl.textContent = '₦' + parseFloat(json.new_price).toLocaleString('en-NG', {
                        minimumFractionDigits: 2,
                    });
                }

                if (totalEl && qtyInput) {
                    const newTotal = parseFloat(json.new_price) * parseInt(qtyInput.value);
                    totalEl.textContent = '₦' + newTotal.toLocaleString('en-NG', {
                        minimumFractionDigits: 2,
                    });
                }

                // Update the data-price on +/- buttons so quantity changes
                // calculate from the new price
                cartItem?.querySelectorAll('.quantity-btn').forEach(btn => {
                    btn.dataset.price = json.new_price;
                });
            }

        } catch (err) {
            console.error('Size update failed:', err);
        }
    }
});
</script>
@endpush