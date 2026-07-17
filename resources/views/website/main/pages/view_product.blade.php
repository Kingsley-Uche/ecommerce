@extends('website.main.landpage')

@section('content')

<style>
:root {
    --paper:    #faf8f4;
    --paper-2:  #f2efe7;
    --ink:      #1c1a17;
    --ink-mid:  #635d52;
    --ink-soft: #a59c8c;
    --line:     #e4e0d8;
    --clay:     #b5562e;
    --clay-dim: #e9d2c5;
    --moss:     #5c6650;
    --font-display: 'Fraunces', Georgia, serif;
    --font-mono:    'Space Grotesk', 'Courier New', monospace;
    --ease: cubic-bezier(.4,0,.2,1);
}

*, *::before, *::after { box-sizing: border-box; }
a { text-decoration: none; color: inherit; }
img { display: block; max-width: 100%; }
button { font-family: inherit; }

.catalogue { font-family: var(--font-mono); color: var(--ink); background: var(--paper); }
.wrap { max-width: 1180px; margin: 0 auto; padding: 0 1.75rem; }

/* ── Breadcrumb ─────────────────────────────────────────── */
.breadcrumb-bar {
    padding: 1.1rem 0;
    border-bottom: 1px solid var(--line);
    font-size: .72rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--ink-soft);
    display: flex;
    align-items: center;
    gap: .5rem;
}
.breadcrumb-bar a { color: var(--ink-mid); transition: color .2s; }
.breadcrumb-bar a:hover { color: var(--clay); }
.breadcrumb-bar i { font-size: .65rem; }

/* ── Product layout ─────────────────────────────────────── */
.product-page {
    padding: 3.5rem 0 5rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5rem;
    align-items: start;
}

/* ── Gallery ────────────────────────────────────────────── */
.gallery { position: sticky; top: 2rem; }

.gallery-main {
    width: 100%;
    aspect-ratio: 1 / 1;
    background: var(--paper-2);
    border: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 1rem;
}
.gallery-main img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: opacity .3s var(--ease), transform .4s var(--ease);
}
.gallery-main img.switching { opacity: 0; transform: scale(.97); }

.gallery-thumbs {
    display: flex;
    gap: .65rem;
    flex-wrap: wrap;
}
.thumb {
    width: 72px;
    height: 72px;
    border: 1px solid var(--line);
    background: var(--paper-2);
    cursor: pointer;
    overflow: hidden;
    transition: border-color .2s var(--ease);
    flex-shrink: 0;
}
.thumb img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.thumb:hover  { border-color: var(--ink-mid); }
.thumb.active { border-color: var(--clay); border-width: 2px; }

/* ── Product info panel ─────────────────────────────────── */
.product-info { display: flex; flex-direction: column; gap: 1.5rem; }

.product-eyebrow {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--clay);
}

.product-name {
    font-family: var(--font-display);
    font-size: clamp(1.85rem, 3.5vw, 2.75rem);
    font-weight: 600;
    line-height: 1.1;
    margin: .3rem 0;
}

.product-price {
    font-family: var(--font-display);
    font-size: 2rem;
    font-weight: 600;
    color: var(--ink);
}

.stock-ledger {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .78rem;
    color: var(--ink-mid);
    letter-spacing: .04em;
}
.stock-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: var(--moss);
    flex-shrink: 0;
}
.stock-dot.low { background: var(--clay); }
.stock-dot.out { background: var(--ink-soft); }

.divider { border: none; border-top: 1px solid var(--line); margin: 0; }

/* description */
.product-desc {
    font-size: .92rem;
    color: var(--ink-mid);
    line-height: 1.75;
}
.desc-clamp { display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }
.desc-toggle {
    background: none;
    border: none;
    font-family: var(--font-mono);
    font-size: .78rem;
    font-weight: 600;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--clay);
    cursor: pointer;
    padding: 0;
    margin-top: .5rem;
    text-decoration: underline;
    text-underline-offset: 3px;
}

/* quantity selector */
.qty-row {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}
.qty-label {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--ink-mid);
}
.qty-ctrl {
    display: flex;
    align-items: center;
    border: 1px solid var(--line);
}
.qty-btn {
    width: 38px; height: 38px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
    color: var(--ink);
    transition: background .2s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.qty-btn:hover { background: var(--paper-2); }
.qty-input {
    width: 52px;
    height: 38px;
    border: none;
    border-left: 1px solid var(--line);
    border-right: 1px solid var(--line);
    text-align: center;
    font-family: var(--font-mono);
    font-size: .95rem;
    background: var(--paper);
    color: var(--ink);
    -moz-appearance: textfield;
}
.qty-input::-webkit-inner-spin-button,
.qty-input::-webkit-outer-spin-button { -webkit-appearance: none; }

/* CTA buttons */
.cta-row {
    display: flex;
    gap: .85rem;
    flex-wrap: wrap;
}
.btn-primary-cta {
    flex: 1;
    min-width: 160px;
    padding: 1rem 1.5rem;
    background: var(--ink);
    color: var(--paper);
    border: none;
    font-family: var(--font-mono);
    font-size: .8rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background .2s var(--ease);
}
.btn-primary-cta:hover:not(:disabled) { background: var(--clay); }
.btn-primary-cta:disabled {
    background: var(--ink-soft);
    cursor: not-allowed;
}

/* ── Responsive ─────────────────────────────────────────── */
@media (max-width: 860px) {
    .product-page { grid-template-columns: 1fr; gap: 2.5rem; }
    .gallery { position: static; }
}
@media (max-width: 560px) {
    .thumb { width: 58px; height: 58px; }
    .cta-row { flex-direction: column; }
    .btn-primary-cta { min-width: unset; }
}
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;1,500&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="catalogue">

    {{-- ── BREADCRUMB ──────────────────────────────────────── --}}
    <div class="breadcrumb-bar">
        <div class="wrap" style="display:flex;align-items:center;gap:.5rem;width:100%;max-width:1180px;">
            <a href="{{ route('home') }}">Home</a>
            <i class="bi bi-chevron-right"></i>
            @if(!empty($category))
                <a href="{{ route('category.products', ['category_id' => encrypt($category->id)]) }}">
                    {{ $category->name }}
                </a>
                <i class="bi bi-chevron-right"></i>
            @endif
            <span style="color:var(--ink);">{{ \Illuminate\Support\Str::limit($product->name, 40) }}</span>
        </div>
    </div>

    {{-- ── PRODUCT ──────────────────────────────────────────── --}}
    <div class="wrap">
        <div class="product-page">

            {{-- ── GALLERY ──────────────────────────────────── --}}
            <div class="gallery">
                @php
                    $images   = $product->images ?? collect();
                    $firstImg = $images->first()?->image_path;
                @endphp

                {{-- Main image --}}
                <div class="gallery-main">
                    <img
                        id="main-image"
                        src="{{ $firstImg ? asset('storage/' . $firstImg) : asset('assets/img/default-product.png') }}"
                        alt="{{ $product->name }}"
                    >
                </div>

                {{-- Thumbnails — only rendered when there are multiple images --}}
                @if($images->count() > 1)
                    <div class="gallery-thumbs">
                        @foreach($images as $i => $img)
                            <div
                                class="thumb {{ $i === 0 ? 'active' : '' }}"
                                data-src="{{ asset('storage/' . $img->image_path) }}"
                                onclick="switchImage(this)"
                                role="button"
                                tabindex="0"
                                aria-label="View image {{ $i + 1 }}"
                                onkeydown="if(event.key==='Enter'||event.key===' ')switchImage(this)"
                            >
                                <img
                                    src="{{ asset('storage/' . $img->image_path) }}"
                                    alt="{{ $product->name }} image {{ $i + 1 }}"
                                    loading="lazy"
                                >
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── INFO PANEL ───────────────────────────────── --}}
            <div class="product-info">

                {{-- Category label --}}
                @if(!empty($category))
                    <span class="product-eyebrow">{{ $category->name }}</span>
                @endif

                {{-- Name --}}
                <h1 class="product-name">{{ $product->name }}</h1>

                {{-- Price --}}
                <div class="product-price">₦{{ number_format($product->price, 2) }}</div>

                {{-- Stock --}}
                @php
                    $stock      = (int) ($product->stock ?? 0);
                    $stockClass = $stock <= 0 ? 'out' : ($stock <= 5 ? 'low' : '');
                    $stockLabel = $stock <= 0 ? 'Out of stock' : ($stock <= 5 ? "Only {$stock} left" : 'In stock');
                @endphp
                <div class="stock-ledger">
                    <span class="stock-dot {{ $stockClass }}"></span>
                    {{ $stockLabel }}
                </div>

                <hr class="divider">

                {{-- Description --}}
                @if(!empty($product->description))
                    <div>
                        <p class="product-desc" id="product-desc">
                            {{ $product->description }}
                        </p>
                        @if(strlen($product->description) > 220)
                            <button
                                class="desc-toggle"
                                id="desc-toggle"
                                onclick="toggleDesc()"
                                type="button"
                            >Read more</button>
                        @endif
                    </div>
                @endif

                <hr class="divider">

                {{-- Quantity --}}
                <div class="qty-row">
                    <span class="qty-label">Qty</span>
                    <div class="qty-ctrl">
                        <button
                            type="button"
                            class="qty-btn"
                            onclick="adjustQty(-1)"
                            aria-label="Decrease quantity"
                        >−</button>
                        <input
                            type="number"
                            id="qty-input"
                            class="qty-input"
                            value="1"
                            min="1"
                            max="{{ $stock }}"
                            {{ $stock <= 0 ? 'disabled' : '' }}
                        >
                        <button
                            type="button"
                            class="qty-btn"
                            onclick="adjustQty(1)"
                            aria-label="Increase quantity"
                        >+</button>
                    </div>
                </div>

                {{-- Add to cart --}}
                <form
                    method="post"
                    action="{{ route('api.cart.add') }}"
                    id="product-cart-form"
                    class="cta-row"
                >
                    @csrf
                    <input type="hidden" name="product_name" value="{{ $product->name }}">
                    <input type="hidden" name="product_id"   value="{{ $product->id }}">
                    <input type="hidden" name="quantity"     id="form-qty" value="1">

                    <button
                        type="submit"
                        class="btn-primary-cta form_button"
                        {{ $stock <= 0 ? 'disabled' : '' }}
                    >
                        @if($stock <= 0)
                            <i class="bi bi-x-circle me-1"></i> Sold Out
                        @else
                            <i class="bi bi-cart-plus me-1"></i> Add to Cart
                        @endif
                    </button>
                </form>

            </div>
            {{-- /info panel --}}

        </div>
    </div>

</div>

<script>
/* ── Gallery ─────────────────────────────────────────────── */
function switchImage(thumb) {
    const main = document.getElementById('main-image');

    // Fade out
    main.classList.add('switching');

    setTimeout(function () {
        main.src = thumb.dataset.src;
        main.classList.remove('switching');
    }, 220);

    // Active state on thumbnails
    document.querySelectorAll('.thumb').forEach(function (t) {
        t.classList.remove('active');
    });
    thumb.classList.add('active');
}

/* ── Quantity stepper ────────────────────────────────────── */
function adjustQty(delta) {
    var input = document.getElementById('qty-input');
    var max   = parseInt(input.max, 10) || 999;
    var val   = Math.max(1, Math.min(max, parseInt(input.value, 10) + delta));
    input.value = val;
    document.getElementById('form-qty').value = val;
}

document.getElementById('qty-input')?.addEventListener('input', function () {
    var max = parseInt(this.max, 10) || 999;
    var val = Math.max(1, Math.min(max, parseInt(this.value, 10) || 1));
    this.value = val;
    document.getElementById('form-qty').value = val;
});

/* ── Description toggle ──────────────────────────────────── */
(function () {
    var desc    = document.getElementById('product-desc');
    var toggle  = document.getElementById('desc-toggle');
    var clamped = true;

    if (desc && toggle) {
        desc.classList.add('desc-clamp');
    }

    window.toggleDesc = function () {
        clamped = !clamped;
        desc.classList.toggle('desc-clamp', clamped);
        toggle.textContent = clamped ? 'Read more' : 'Show less';
    };
})();
</script>

@endsection