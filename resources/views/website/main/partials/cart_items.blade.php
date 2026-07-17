{{--
    website/main/partials/cart_items.blade.php

    Single source of truth for cart row markup.
    Used by cart.blade.php (full page) and cart_modal.blade.php (modal).

    Expects:
      $cartItems → Collection of CartModel, each with ->product eager loaded
                   (product has: id, name, description, price, images)
      $count     → int, total quantity across cart

    Column layout (12 cols total):
      Product  4 | Size  2 | Price  2 | Qty  2 | Total  1 | Remove  1
--}}

@if($cartItems->isEmpty())

    <div class="text-center py-5 text-muted">
        <i class="bi bi-cart-x" style="font-size: 2.5rem;"></i>
        <p class="mt-3 mb-0">Your cart is empty.</p>
        <a href="{{ route('home') }}" class="btn btn-dark mt-3">Continue Shopping</a>
    </div>

@else

    @foreach($cartItems as $item)
        @php
            $product      = $item->product;
            $price        = (float) ($product->price ?? 0);
            $qty          = (int) $item->quantity;
            $total        = $price * $qty;
            $image        = $product->images->first()->image_path ?? null;
            $selectedSize = $item->size ?? '';   // ← read from the DB row
        @endphp

        <div class="cart-item border-bottom py-3"
             data-product-id="{{ $product->id ?? '' }}">

            <div class="row align-items-center g-1">

                {{-- Product (image + name + description) — col 4 --}}
                <div class="col-lg-4 col-12 mb-2 mb-lg-0">
                    <div class="product-info d-flex align-items-center gap-2">
                        <img
                            src="{{ $image
                                ? asset('storage/' . $image)
                                : asset('assets/img/default-product.png') }}"
                            alt="{{ $product->name ?? 'Product' }}"
                            width="60"
                            class="img-fluid rounded flex-shrink-0"
                            style="object-fit: contain; height: 60px;"
                        >
                        <div>
                            <h6 class="mb-0 small fw-semibold">
                                {{ $product->name ?? 'Unknown product' }}
                            </h6>
                            @if(!empty($product->description))
                                <small class="text-muted">
                                    {{ \Illuminate\Support\Str::limit($product->description, 50) }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Size — col 2
                     Pre-selects whatever size is stored on $item->size.
                     Sends size[] in the update form so CartController::update()
                     can persist the chosen size back to the DB.                --}}
                <div class="col-lg-2 col-4 text-center mb-2 mb-lg-0">
                    <label class="form-label small text-muted d-block d-lg-none mb-0">Size</label>
                    <select
                        name="size[]"
                        class="form-select form-select-sm size-selector"
                        data-product-id="{{ $product->id ?? '' }}"
                        data-cart-item-id="{{ $item->id ?? '' }}"
                    >
                        <option value=""  {{ $selectedSize === ''   ? 'selected' : '' }}>— Size —</option>
                        <option value="XS" {{ $selectedSize === 'XS'  ? 'selected' : '' }}>XS</option>
                        <option value="S"  {{ $selectedSize === 'S'   ? 'selected' : '' }}>S</option>
                        <option value="M"  {{ $selectedSize === 'M'   ? 'selected' : '' }}>M</option>
                        <option value="L"  {{ $selectedSize === 'L'   ? 'selected' : '' }}>L</option>
                        <option value="XL" {{ $selectedSize === 'XL'  ? 'selected' : '' }}>XL</option>
                        <option value="XXL"{{ $selectedSize === 'XXL' ? 'selected' : '' }}>XXL</option>
                    </select>
                </div>

                {{-- Price — col 2 --}}
                <div class="col-lg-2 col-4 text-center mb-2 mb-lg-0">
                    <label class="form-label small text-muted d-block d-lg-none mb-0">Price</label>
                    <span class="current-price">
                        <strong>₦{{ number_format($price, 2) }}</strong>
                    </span>
                </div>

                {{-- Quantity — col 2 --}}
                <div class="col-lg-2 col-4 text-center mb-2 mb-lg-0">
                    <label class="form-label small text-muted d-block d-lg-none mb-0">Qty</label>
                    <div class="quantity-selector d-flex align-items-center justify-content-center gap-1">

                        <button type="button"
                                class="quantity-btn decrease btn btn-sm btn-outline-dark"
                                data-price="{{ $price }}">
                            <i class="bi bi-dash"></i>
                        </button>

                        <input
                            type="number"
                            class="quantity-input form-control form-control-sm text-center"
                            value="{{ $qty }}"
                            min="1"
                            name="quantity[]"
                            style="width: 52px;"
                        >

                        {{-- Hidden fields read by CartController::update() --}}
                        <input type="hidden" name="initial_quantity[]" value="{{ $qty }}">
                        <input type="hidden" name="product_id[]"       value="{{ $product->id ?? '' }}">
                        <input type="hidden" name="total"              value="{{ number_format($total, 2, '.', '') }}">

                        <button type="button"
                                class="quantity-btn increase btn btn-sm btn-outline-dark"
                                data-price="{{ $price }}">
                            <i class="bi bi-plus"></i>
                        </button>

                    </div>
                </div>

                {{-- Line total — col 1 --}}
                <div class="col-lg-1 col-2 text-center item-total">
                    <label class="form-label small text-muted d-block d-lg-none mb-0">Total</label>
                    <strong>₦{{ number_format($total, 2) }}</strong>
                </div>

                {{-- Remove — col 1 --}}
                <div class="col-lg-1 col-2 text-center">
                    <button
                        type="button"
                        class="remove-item btn btn-sm btn-link text-danger p-0 mt-lg-0 mt-2"
                        data-product-id="{{ $product->id ?? '' }}"
                        aria-label="Remove {{ $product->name ?? 'item' }}"
                    >
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

            </div>
        </div>
    @endforeach

@endif