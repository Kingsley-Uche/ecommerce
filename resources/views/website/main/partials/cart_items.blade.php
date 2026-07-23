{{-- ============================================================
     CART ITEM PARTIAL (FIXED COLUMN GRID ALIGNMENT)
     ============================================================ --}}
@php
    $productId = $product['id'] ?? null;
    $productName = $product['name'] ?? 'Unknown product';
    $productDescription = $product['description'] ?? null;
    $price = $product['price'] ?? 0;
    $qty = $qty ?? 1;
    $selectedSize = $selectedSize ?? '';
    $total = $price * $qty;
    $images = $product['images'] ?? collect();
    $image = optional($images->first())->image_path;
    $cartItemId = $item['id'] ?? null;
@endphp

<div class="cart-item border-bottom py-3" data-product-id="{{ $productId }}">
    <div class="row align-items-center g-2">

        {{-- Image: col-lg-1 --}}
        <div class="col-lg-1 col-3 text-center">
            <img src="{{ $image ? asset('storage/'.$image) : asset('assets/img/default-product.png') }}"
                 alt="{{ $productName }}"
                 class="img-fluid rounded"
                 style="height:50px;width:50px;object-fit:contain;">
        </div>

        {{-- Product Name & Description: col-lg-3 --}}
        <div class="col-lg-3 col-9">
            <h6 class="mb-0 small fw-semibold text-truncate">
                {{ $productName }}
            </h6>
            @if($productDescription)
                <small class="text-muted d-block" style="line-height: 1.25; max-height: 2.5em; overflow: hidden;">
                    {{ $productDescription }}
                </small>
            @endif
        </div>

        {{-- Size: col-lg-2 --}}
        <div class="col-lg-2 col-4 text-center mt-2 mt-lg-0">
            <label class="d-lg-none small text-muted d-block">Size</label>
            <select name="size[]"
                    class="form-select form-select-sm size-selector"
                    data-product-id="{{ $productId }}"
                    data-cart-item-id="{{ $cartItemId }}">
                <option value="">-</option>
                @foreach(['XS','S','M','L','XL','XXL'] as $size)
                    <option value="{{ $size }}" {{ $selectedSize == $size ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Price: col-lg-2 --}}
        <div class="col-lg-2 col-4 text-center mt-2 mt-lg-0">
            <label class="d-lg-none small text-muted d-block">Price</label>
            <strong class="small text-nowrap">
                ₦{{ number_format($price, 2) }}
            </strong>
        </div>

        {{-- Quantity: col-lg-2 --}}
        <div class="col-lg-2 col-4 text-center mt-2 mt-lg-0">
            <label class="d-lg-none small text-muted d-block">Qty</label>
            <div class="d-flex justify-content-center align-items-center gap-1">
                <button type="button"
                        class="quantity-btn decrease btn btn-sm btn-outline-dark px-1 py-0"
                        style="line-height: 1; height: 28px;"
                        data-price="{{ $price }}">
                    <i class="bi bi-dash"></i>
                </button>

                <input type="number"
                       class="quantity-input form-control form-control-sm text-center px-1"
                       name="quantity[]"
                       value="{{ $qty }}"
                       min="1"
                       style="width: 40px; height: 28px;">

                <input type="hidden" name="initial_quantity[]" value="{{ $qty }}">
                <input type="hidden" name="product_id[]" value="{{ $productId }}">
                <input type="hidden" name="total" value="{{ number_format($total, 2, '.', '') }}">

                <button type="button"
                        class="quantity-btn increase btn btn-sm btn-outline-dark px-1 py-0"
                        style="line-height: 1; height: 28px;"
                        data-price="{{ $price }}">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>

        {{-- Total: col-lg-1 --}}
        

        {{-- Remove: col-lg-1 --}}
        <div class="col-lg-1 col-6 text-center mt-2 mt-lg-0">
            <label class="d-lg-none small text-muted d-block">Remove</label>
            <button type="button"
                    class="remove-item btn btn-link text-danger p-0"
                    data-product-id="{{ $productId }}"
                    aria-label="Remove {{ $productName }}">
                <i class="bi bi-trash fs-6"></i>
            </button>
        </div>

    </div>
</div>