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
.nav-tabs .nav-link {
    color: #6c757d;
    transition: all 0.25s ease;
    position: relative;
}

.nav-tabs .nav-link:hover {
    color: #1c1a17;
    background: transparent;
}

.nav-tabs .nav-link::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 0;
    height: 3px;
    background-color: #1c1a17;
    transition: width 0.3s ease;
}

.nav-tabs .nav-link:hover::after,
.nav-tabs .nav-link.active::after {
    width: 100%;
}

.nav-tabs .nav-link.active {
    color: #1c1a17;
    font-weight: 600;
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

.unit-toggle .unit-btn {
    transition: all 0.2s ease;
}

.unit-toggle .unit-btn.active {
    background-color: #1c1a17 !important;
    color: white !important;
    border: none;
}

.unit-toggle .unit-btn:hover:not(.active) {
    background-color: #f8f9fa;
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
                        <button class="btn btn-dark"
        data-bs-toggle="modal"
        data-bs-target="#sizeGuideModal">
    Size Guide
</button>
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
<div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h4 class="modal-title fw-bold" id="sizeGuideModalLabel">
          <i class="bi bi-rulers me-2 text-black"></i> Size Guide
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-0">
        <!-- Header -->
        <div class="guide-head bg-light py-4 border-bottom">
          <div class="container-fluid px-4">
            <div class="text-center">
              <span class="guide-eyebrow text-uppercase text-muted fw-semibold">Fit &amp; Sizing</span>
              <h1 class="guide-title display-6 fw-bold mb-2">Size Guide</h1>
              <p class="guide-intro text-muted mb-3">
                All measurements are in centimetres unless toggled to inches. If you're between sizes, we recommend sizing up for a relaxed fit or sizing down for a more tailored look.
              </p>
              <div class="unit-toggle d-inline-flex rounded-pill border overflow-hidden" role="group" aria-label="Unit selector">
                <button class="unit-btn active btn btn-sm px-4 py-2" id="btn-cm" onclick="setUnit('cm')">cm</button>
                <button class="unit-btn btn btn-sm px-4 py-2" id="btn-in" onclick="setUnit('in')">inches</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs nav-justified border-bottom-0 px-4 pt-4 bg-light" id="sizeGuideTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active d-flex align-items-center justify-content-center gap-2" id="howto-tab" data-bs-toggle="tab" data-bs-target="#howto" type="button" role="tab">
              <i class="bi bi-arrows-expand"></i> How to Measure
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center justify-content-center gap-2" id="women-tab" data-bs-toggle="tab" data-bs-target="#women" type="button" role="tab">
              <i class="bi bi-person"></i> Women
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center justify-content-center gap-2" id="men-tab" data-bs-toggle="tab" data-bs-target="#men" type="button" role="tab">
              <i class="bi bi-person-fill"></i> Men
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center justify-content-center gap-2" id="kids-tab" data-bs-toggle="tab" data-bs-target="#kids" type="button" role="tab">
              <i class="bi bi-people"></i> Kids
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center justify-content-center gap-2" id="shoes-tab" data-bs-toggle="tab" data-bs-target="#shoes" type="button" role="tab">
              <i class="bi bi-shoe"></i> Footwear
            </button>
          </li>
        </ul>

        <div class="tab-content p-4" id="sizeGuideTabContent">

          <!-- HOW TO MEASURE -->
          <div class="tab-pane fade show active" id="howto" role="tabpanel">
            <section class="guide-section">
              <div class="container-fluid">
                <span class="section-eyebrow text-muted fw-semibold">STEP 1</span>
                <h2 class="h3 fw-bold">How to Measure Yourself</h2>
                <p class="text-muted">Use a soft measuring tape and stand naturally. Measure directly against your body, not over clothes.</p>

                <div class="row g-4">
                  <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center">
                      <div class="card-body">
                        <i class="bi bi-arrows-expand fs-1 text-dark mb-3"></i>
                        <h5>Chest / Bust</h5>
                        <p class="text-muted small">Wrap the tape around the fullest part of your chest, keeping it horizontal.</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center">
                      <div class="card-body">
                        <i class="bi bi-bezier2 fs-1 text-dark mb-3"></i>
                        <h5>Waist</h5>
                        <p class="text-muted small">Measure around your natural waistline — the narrowest part of your torso.</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center">
                      <div class="card-body">
                        <i class="bi bi-arrows-angle-expand fs-1 text-dark mb-3"></i>
                        <h5>Hips</h5>
                        <p class="text-muted small">Stand with feet together. Measure around the fullest part of your hips.</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center">
                      <div class="card-body">
                        <i class="bi bi-rulers fs-1 text-dark mb-3"></i>
                        <h5>Inseam</h5>
                        <p class="text-muted small">From top of inner thigh to ankle bone.</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center">
                      <div class="card-body">
                        <i class="bi bi-arrow-down-up fs-1 text-dark mb-3"></i>
                        <h5>Shoulder Width</h5>
                        <p class="text-muted small">From edge of one shoulder to the other across your back.</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center">
                      <div class="card-body">
                        <i class="bi bi-person-standing fs-1 text-dark mb-3"></i>
                        <h5>Height</h5>
                        <p class="text-muted small">Stand barefoot against a wall. Measure from floor to top of head.</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="alert alert-dark mt-4">
                  <i class="bi bi-lightbulb-fill me-2"></i>
                  <strong>Tip:</strong> If your measurements fall into two different sizes, choose the larger size for comfort.
                </div>
              </div>
            </section>
          </div>

          <!-- WOMEN -->
          <div class="tab-pane fade" id="women" role="tabpanel">
            <section class="guide-section">
              <div class="container-fluid">
                <span class="section-eyebrow text-muted fw-semibold">Women's Clothing</span>
                <h2 class="h3 fw-bold">Women's Size Chart</h2>
                <p class="text-muted">Our women's clothing follows international sizing. Nigerian women typically find our sizes run true to standard UK sizing.</p>
                
                <div class="table-responsive">
                  <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Size</th>
                        <th>UK</th>
                        <th>EU</th>
                        <th>US</th>
                        <th>Chest <span class="unit-label">cm</span></th>
                        <th>Waist <span class="unit-label">cm</span></th>
                        <th>Hips <span class="unit-label">cm</span></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr><td><span class="size-label badge bg-secondary">XS</span></td><td>6</td><td>34</td><td>2</td><td class="measure" data-cm="80–83">80–83</td><td class="measure" data-cm="61–64">61–64</td><td class="measure" data-cm="86–89">86–89</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">S</span></td><td>8</td><td>36</td><td>4</td><td class="measure" data-cm="84–87">84–87</td><td class="measure" data-cm="65–68">65–68</td><td class="measure" data-cm="90–93">90–93</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">M</span></td><td>10–12</td><td>38–40</td><td>6–8</td><td class="measure" data-cm="88–95">88–95</td><td class="measure" data-cm="69–76">69–76</td><td class="measure" data-cm="94–101">94–101</td></tr>
                      <tr class="table-primary"><td><span class="size-label badge bg-dark">L</span></td><td>14–16</td><td>42–44</td><td>10–12</td><td class="measure" data-cm="96–103">96–103</td><td class="measure" data-cm="77–84">77–84</td><td class="measure" data-cm="102–109">102–109</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">XL</span></td><td>18–20</td><td>46–48</td><td>14–16</td><td class="measure" data-cm="104–111">104–111</td><td class="measure" data-cm="85–92">85–92</td><td class="measure" data-cm="110–117">110–117</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">XXL</span></td><td>22–24</td><td>50–52</td><td>18–20</td><td class="measure" data-cm="112–119">112–119</td><td class="measure" data-cm="93–100">93–100</td><td class="measure" data-cm="118–125">118–125</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </section>
          </div>

          <!-- MEN -->
          <div class="tab-pane fade" id="men" role="tabpanel">
            <section class="guide-section">
              <div class="container-fluid">
                <span class="section-eyebrow text-muted fw-semibold">Men's Clothing</span>
                <h2 class="h3 fw-bold">Men's Size Chart</h2>
                <p class="text-muted">Men's sizes are measured at chest, waist and hip. Shirt collar and sleeve length are listed separately below.</p>
                
                <div class="table-responsive mb-5">
                  <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Size</th>
                        <th>UK / EU</th>
                        <th>US</th>
                        <th>Chest <span class="unit-label">cm</span></th>
                        <th>Waist <span class="unit-label">cm</span></th>
                        <th>Hips <span class="unit-label">cm</span></th>
                        <th>Shoulder <span class="unit-label">cm</span></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr><td><span class="size-label badge bg-secondary">XS</span></td><td>44</td><td>34</td><td class="measure" data-cm="84–88">84–88</td><td class="measure" data-cm="70–74">70–74</td><td class="measure" data-cm="86–90">86–90</td><td class="measure" data-cm="41–42">41–42</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">S</span></td><td>46</td><td>36</td><td class="measure" data-cm="89–93">89–93</td><td class="measure" data-cm="75–79">75–79</td><td class="measure" data-cm="91–95">91–95</td><td class="measure" data-cm="43–44">43–44</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">M</span></td><td>48–50</td><td>38–40</td><td class="measure" data-cm="94–98">94–98</td><td class="measure" data-cm="80–84">80–84</td><td class="measure" data-cm="96–100">96–100</td><td class="measure" data-cm="45–46">45–46</td></tr>
                      <tr class="table-primary"><td><span class="size-label badge bg-dark">L</span></td><td>52–54</td><td>42–44</td><td class="measure" data-cm="99–104">99–104</td><td class="measure" data-cm="85–90">85–90</td><td class="measure" data-cm="101–106">101–106</td><td class="measure" data-cm="47–48">47–48</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">XL</span></td><td>56–58</td><td>46–48</td><td class="measure" data-cm="105–110">105–110</td><td class="measure" data-cm="91–96">91–96</td><td class="measure" data-cm="107–112">107–112</td><td class="measure" data-cm="49–50">49–50</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">XXL</span></td><td>60–62</td><td>50–52</td><td class="measure" data-cm="111–117">111–117</td><td class="measure" data-cm="97–103">97–103</td><td class="measure" data-cm="113–119">113–119</td><td class="measure" data-cm="51–53">51–53</td></tr>
                    </tbody>
                  </table>
                </div>

                <h5 class="mt-4">Shirt Collar &amp; Sleeve</h5>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Size</th>
                        <th>Collar <span class="unit-label">cm</span></th>
                        <th>Sleeve <span class="unit-label">cm</span></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr><td><span class="size-label badge bg-secondary">S</span></td><td class="measure" data-cm="37–38">37–38</td><td class="measure" data-cm="82–84">82–84</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">M</span></td><td class="measure" data-cm="39–40">39–40</td><td class="measure" data-cm="85–87">85–87</td></tr>
                      <tr><td><span class="size-label badge bg-dark">L</span></td><td class="measure" data-cm="41–42">41–42</td><td class="measure" data-cm="88–90">88–90</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">XL</span></td><td class="measure" data-cm="43–44">43–44</td><td class="measure" data-cm="91–93">91–93</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </section>
          </div>

          <!-- KIDS -->
          <div class="tab-pane fade" id="kids" role="tabpanel">
            <section class="guide-section">
              <div class="container-fluid">
                <span class="section-eyebrow text-muted fw-semibold">Children's Clothing</span>
                <h2 class="h3 fw-bold">Kids' Size Chart</h2>
                <p class="text-muted">Kids' sizes are based on height and age as a guide only — children vary significantly. Always check the chest and waist measurements.</p>
                
                <div class="table-responsive">
                  <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Label</th>
                        <th>Age (guide)</th>
                        <th>Height <span class="unit-label">cm</span></th>
                        <th>Chest <span class="unit-label">cm</span></th>
                        <th>Waist <span class="unit-label">cm</span></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr><td><span class="size-label badge bg-secondary">2Y</span></td><td>1–2 yrs</td><td class="measure" data-cm="86–92">86–92</td><td class="measure" data-cm="52–53">52–53</td><td class="measure" data-cm="50–51">50–51</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">4Y</span></td><td>3–4 yrs</td><td class="measure" data-cm="98–104">98–104</td><td class="measure" data-cm="54–56">54–56</td><td class="measure" data-cm="52–53">52–53</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">6Y</span></td><td>5–6 yrs</td><td class="measure" data-cm="110–116">110–116</td><td class="measure" data-cm="58–60">58–60</td><td class="measure" data-cm="54–55">54–55</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">8Y</span></td><td>7–8 yrs</td><td class="measure" data-cm="122–128">122–128</td><td class="measure" data-cm="62–64">62–64</td><td class="measure" data-cm="57–58">57–58</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">10Y</span></td><td>9–10 yrs</td><td class="measure" data-cm="134–140">134–140</td><td class="measure" data-cm="66–69">66–69</td><td class="measure" data-cm="60–62">60–62</td></tr>
                      <tr><td><span class="size-label badge bg-secondary">12Y</span></td><td>11–12 yrs</td><td class="measure" data-cm="146–152">146–152</td><td class="measure" data-cm="72–76">72–76</td><td class="measure" data-cm="63–66">63–66</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </section>
          </div>

          <!-- FOOTWEAR -->
          <div class="tab-pane fade" id="shoes" role="tabpanel">
            <section class="guide-section">
              <div class="container-fluid">
                <span class="section-eyebrow text-muted fw-semibold">Footwear</span>
                <h2 class="h3 fw-bold">Shoe Size Chart</h2>
                <p class="text-muted">Measure your foot length while standing on a flat surface. Place your heel against a wall and mark the tip of your longest toe.</p>
                
                <div class="table-responsive">
                  <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>UK</th>
                        <th>EU</th>
                        <th>US (Men)</th>
                        <th>US (Women)</th>
                        <th>Foot Length <span class="unit-label">cm</span></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr><td>3</td><td>36</td><td>4</td><td>5.5</td><td class="measure" data-cm="22.5">22.5</td></tr>
                      <tr><td>4</td><td>37</td><td>5</td><td>6.5</td><td class="measure" data-cm="23.5">23.5</td></tr>
                      <tr><td>5</td><td>38</td><td>6</td><td>7.5</td><td class="measure" data-cm="24.0">24.0</td></tr>
                      <tr><td>6</td><td>39</td><td>7</td><td>8.5</td><td class="measure" data-cm="24.8">24.8</td></tr>
                      <tr><td>7</td><td>40–41</td><td>8</td><td>9.5</td><td class="measure" data-cm="25.7">25.7</td></tr>
                      <tr><td>8</td><td>42</td><td>9</td><td>10.5</td><td class="measure" data-cm="26.5">26.5</td></tr>
                      <tr><td>9</td><td>43</td><td>10</td><td>11.5</td><td class="measure" data-cm="27.3">27.3</td></tr>
                      <tr><td>10</td><td>44</td><td>11</td><td>12.5</td><td class="measure" data-cm="28.0">28.0</td></tr>
                      <tr><td>11</td><td>45</td><td>12</td><td>13</td><td class="measure" data-cm="28.8">28.8</td></tr>
                      <tr><td>12</td><td>46</td><td>13</td><td>—</td><td class="measure" data-cm="29.6">29.6</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-dark px-5" data-bs-dismiss="modal">Close</button>
      </div>
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
<script>
// Unit Toggle Functionality
function setUnit(unit) {
    // Remove active class from both buttons
    document.querySelectorAll('.unit-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Add active class to clicked button
    if (unit === 'cm') {
        document.getElementById('btn-cm').classList.add('active');
    } else {
        document.getElementById('btn-in').classList.add('active');
    }

    // Update all measurements
    document.querySelectorAll('.measure').forEach(el => {
        const cmValue = el.getAttribute('data-cm');
        
        if (!cmValue) return;

        if (unit === 'cm') {
            el.textContent = cmValue;
        } else {
            // Convert cm to inches
            const range = cmValue.split('–');
            let converted;

            if (range.length === 2) {
                const minIn = (parseFloat(range[0]) / 2.54).toFixed(0);
                const maxIn = (parseFloat(range[1]) / 2.54).toFixed(0);
                converted = `${minIn}–${maxIn}`;
            } else {
                // Single value
                converted = (parseFloat(cmValue) / 2.54).toFixed(1);
            }
            el.textContent = converted;
        }
    });

    // Update unit labels
    document.querySelectorAll('.unit-label').forEach(label => {
        label.textContent = unit;
    });
}
</script>

@endsection