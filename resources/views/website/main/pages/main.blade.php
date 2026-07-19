@extends('website.main.landpage')

@section('content')

<style>
/* ============================================================
   TOKENS
   ============================================================ */
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
    --radius:   2px;
    --font-display: 'Fraunces', Georgia, serif;
    --font-mono:    'Space Grotesk', 'Courier New', monospace;
    --ease: cubic-bezier(.4,0,.2,1);
    --product-min-width: 260px;
}

*, *::before, *::after { box-sizing: border-box; }
a { text-decoration: none; color: inherit; }
img { display: block; max-width: 100%; }
button { font-family: inherit; }

.catalogue {
    font-family: var(--font-mono);
    color: var(--ink);
    background: var(--paper);
    font-size: 15px;
}

.wrap { max-width: 1180px; margin: 0 auto; padding: 0 1.75rem; }

/* ============================================================
   MASTHEAD
   ============================================================ */
.masthead { border-bottom: 1px solid var(--line); }

.masthead-bar {
    display: flex; justify-content: space-between; align-items: center;
    padding: .85rem 0; font-size: .72rem; letter-spacing: .08em;
    text-transform: uppercase; color: var(--ink-mid);
    border-bottom: 1px solid var(--line);
}
.masthead-bar .contact-bits { display: flex; gap: 1.5rem; }
.masthead-bar .contact-bits span { display: flex; align-items: center; gap: .4rem; }
.masthead-bar i { color: var(--clay); font-size: .85rem; }

.masthead-main {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 2rem; padding: 3rem 0 2.25rem;
}

.brand-logo { height: 34px; width: auto; margin-bottom: 1.1rem; }

.brand-name {
    font-family: var(--font-display); font-weight: 600;
    font-size: clamp(2.75rem, 7vw, 5.25rem); line-height: .92;
    letter-spacing: -.01em;
}

.brand-tagline {
    font-size: .95rem; color: var(--ink-mid); max-width: 32ch;
    text-align: right; line-height: 1.6; padding-bottom: .35rem;
}

/* ============================================================
   INDEX — Modern Category Cards (Ideal Style)
   ============================================================ */
.index-section { padding: 3.5rem 0 4rem; border-bottom: 1px solid var(--line); }

.index-eyebrow {
    font-size: .7rem; letter-spacing: .14em; text-transform: uppercase;
    color: var(--clay); font-weight: 700; margin-bottom: 1.5rem;
}

.index-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(245px, 1fr));
    gap: 1.4rem;
}

/* Better column control */
@media (min-width: 992px) { .index-grid { grid-template-columns: repeat(auto-fill, minmax(235px, 1fr)); } }
@media (min-width: 1200px) { .index-grid { grid-template-columns: repeat(4, 1fr); } }
@media (min-width: 1600px) { .index-grid { grid-template-columns: repeat(5, 1fr); } }

.index-card {
    display: flex; align-items: center; justify-content: space-between;
    background: var(--paper-2); border: 1px solid var(--line);
    border-radius: var(--radius); padding: 1.6rem 1.4rem;
    transition: all 0.3s var(--ease); color: var(--ink);
    min-height: 138px;
}

.index-card:hover {
    background: white; border-color: var(--clay);
    transform: translateY(-4px);
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);
}

.index-card-content { flex: 1; }

.index-num {
    font-family: var(--font-display); font-style: italic;
    color: var(--ink-soft); font-size: 1.05rem; margin-bottom: 0.35rem;
    display: block;
}

.index-name {
    font-family: var(--font-display); font-size: 1.28rem;
    font-weight: 600; line-height: 1.25; margin-bottom: 0.4rem;
}

.index-count {
    font-size: 0.82rem; color: var(--ink-mid);
}

.index-card-arrow {
    font-size: 1.15rem; font-weight: 600; color: var(--clay);
    transition: transform 0.3s var(--ease);
}

.index-card:hover .index-card-arrow { transform: translateX(8px); }

/* ============================================================
   CATEGORY & PRODUCT GRID
   ============================================================ */
.cat-block { padding: 4rem 0; border-bottom: 1px solid var(--line); }

.cat-block-head {
    display: flex; justify-content: space-between; align-items: flex-end;
    gap: 2rem; margin-bottom: 2.5rem;
}

.cat-id { font-family: var(--font-display); font-style: italic; font-size: 1.05rem; color: var(--clay); }
.cat-title { font-family: var(--font-display); font-size: clamp(1.85rem, 4vw, 2.85rem); font-weight: 600; line-height: 1.05; margin: .3rem 0 .6rem; }
.cat-desc { color: var(--ink-mid); max-width: 56ch; font-size: .9rem; line-height: 1.65; }
.cat-meta { text-align: right; font-size: .75rem; color: var(--ink-soft); letter-spacing: .06em; text-transform: uppercase; white-space: nowrap; }

/* Product Grid */
.product-row {
    display: grid;
    gap: 0;
    border-top: 1px solid var(--line);
    border-left: 1px solid var(--line);
    grid-template-columns: repeat(auto-fill, minmax(var(--product-min-width), 1fr));
}

@media (min-width: 1400px) { .product-row { grid-template-columns: repeat(6, 1fr); } }
@media (min-width: 1800px) { .product-row { grid-template-columns: repeat(8, 1fr); } }

.product-cell {
    border-right: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
    padding: 1.4rem 1.25rem 1.5rem;
    display: flex; flex-direction: column;
    transition: background .25s var(--ease);
}
.product-cell:hover { background: var(--paper-2); }

/* ... (keep all your existing .stock-ledger, .pc-image, .pc-name, etc. styles) ... */

/* ============================================================
   FOOTER
   ============================================================ */
.site-footer { padding: 3.5rem 0 2.5rem; }

.footer-grid {
    display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 2.5rem;
    padding-bottom: 2.5rem; border-bottom: 1px solid var(--line);
}

/* ... keep your footer styles ... */

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 860px) {
    .masthead-main { flex-direction: column; align-items: flex-start; gap: 1rem; }
    .brand-tagline { text-align: left; }
    .cat-block-head { flex-direction: column; align-items: flex-start; }
    .cat-meta { text-align: left; }
    .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
}
@media (max-width: 560px) {
    .masthead-bar .contact-bits { gap: .9rem; }
    .masthead-bar .contact-bits span:nth-child(3) { display: none; }
}
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;1,500&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="catalogue">

    {{-- ====================================================
         MASTHEAD — store_name, tagline, logo, contact, all real
         ==================================================== --}}
    <header class="masthead">
        <div class="wrap masthead-bar">
            <div class="contact-bits">
                @if(!empty($shop_data['phone']))
                    <span><i class="bi bi-telephone"></i> {{ $shop_data['phone'] }}</span>
                @endif
                @if(!empty($shop_data['email']))
                    <span><i class="bi bi-envelope"></i> {{ $shop_data['email'] }}</span>
                @endif
                @if(!empty($shop_data['address']))
                    <span><i class="bi bi-geo-alt"></i> {{ $shop_data['address'] }}</span>
                @endif
            </div>

            @if(!empty($shop_data['social_links']) && is_array($shop_data['social_links']))
                <div class="contact-bits">
                    @foreach($shop_data['social_links'] as $platform => $url)
                        @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener">{{ ucfirst($platform) }}</a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="wrap masthead-main">
            <div>
                @if(!empty($shop_data['logo_path']))
                    <img class="brand-logo" src="{{ asset('storage/' . $shop_data['logo_path']) }}" alt="{{ $shop_data['store_name'] ?? 'Store' }} logo">
                @endif
                <h1 class="brand-name">{{ $shop_data['store_name'] ?? 'Our Shop' }}</h1>
            </div>

            @if(!empty($shop_data['tagline']))
                <p class="brand-tagline">{{ $shop_data['tagline'] }}</p>
            @endif
        </div>
    </header>

     {{-- ====================================================
         INDEX — categories ordered by priority, as a TOC
         ==================================================== --}}
{{-- ====================================================
     INDEX — Modern Category Cards
     ==================================================== --}}
<section class="index-section">
    <div class="wrap">
        <span class="index-eyebrow">Catalogue Index</span>
        <h2 class="cat-title" style="margin-bottom: 2.5rem;">Explore Our Collections</h2>

        @if(count($product_categ))
            <div class="index-grid">
                @foreach($product_categ as $catId => $cat)
                    <a href="#cat-{{ $catId }}" class="index-card">
                        <div class="index-card-content">
                            <span class="index-num">{{ sprintf('%02d', $loop->iteration) }}</span>
                            <h3 class="index-name">{{ $cat['category_name'] }}</h3>
                            <p class="index-count">
                                {{ count($cat['products']) }} item{{ count($cat['products']) !== 1 ? 's' : '' }}
                            </p>
                        </div>
                        <div class="index-card-arrow">
                            View →
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="index-empty">No categories are available right now. Please check back shortly.</p>
        @endif
    </div>
</section>

    {{-- ====================================================
         CATEGORY BLOCKS — products with stock ledger
         ==================================================== --}}
    @foreach($product_categ as $catId => $cat)
        <section class="cat-block" id="cat-{{ $catId }}">
            <div class="wrap">
                <div class="cat-block-head">
                    <div>
                        <span class="cat-id">{{ sprintf('%02d', $loop->iteration) }} / {{ sprintf('%02d', count($product_categ)) }}</span>
                        <h2 class="cat-title">{{ $cat['category_name'] }}</h2>
                        @if(!empty($cat['category_description']))
                            <p class="cat-desc">{{ $cat['category_description'] }}</p>
                        @endif
                    </div>
                    <div class="cat-meta">{{ count($cat['products']) }} listed</div>
                </div>

                <div class="product-row">
                    @foreach($cat['products'] as $product)
                        @php
                            $image = $product['images']->first()->image_path ?? null;
                            $stock = $product['stock'] ?? 0;
                            $stockClass = $stock <= 0 ? 'out' : ($stock <= 5 ? 'low' : '');
                            $stockLabel = $stock <= 0 ? 'Out of stock' : ($stock <= 5 ? $stock . ' left' : 'In stock');
                        @endphp
                        <div class="product-cell">
                            <div class="stock-ledger">
                                <span class="stock-dot {{ $stockClass }}"></span>
                                {{ $stockLabel }}
                            </div>

                            <a href="{{ route('product.show', ['id' => $product['id']]) }}"
                               class="pc-image"
                               style="display:flex;">
                                @if($image)
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $product['name'] }}" loading="lazy">
                                @else
                                    <img src="{{ asset('assets/img/default-product.png') }}" alt="{{ $product['name'] }}" loading="lazy">
                                @endif
                            </a>

                            {{-- Product name — links to dedicated product page --}}
                            <a href="{{ route('product.show', ['id' => $product['id']]) }}"
                               class="pc-name"
                               style="display:block;">
                                {{ \Illuminate\Support\Str::limit($product['name'], 48) }}
                                <span style="display:inline-block;margin-left:.4rem;font-size:.68rem;
                                             font-weight:600;letter-spacing:.06em;text-transform:uppercase;
                                             color:var(--clay);border-bottom:1px solid var(--clay-dim);
                                             padding-bottom:1px;vertical-align:middle;">View →</span>
                            </a>

                            @if(!empty($product['description']))
                                <p class="pc-desc">{{ \Illuminate\Support\Str::limit($product['description'], 80) }}</p>
                            @endif

                            <div class="pc-bottom">
                                <span class="pc-price">₦{{ number_format($product['price'], 2) }}</span>

                                <div style="display:flex;gap:.5rem;align-items:center;">
                                    {{-- Details link — takes user to the full product page --}}
                                    <a href="{{ route('product.show', ['id' => $product['id']]) }}"
                                       class="details-btn">
                                        Details
                                    </a>

                                    {{-- Add to cart --}}
                                    <form method="post" action="{{ route('api.cart.add') }}" class="cart-form">
                                        @csrf
                                        <input type="hidden" name="product_name" value="{{ $product['name'] }}">
                                        <input type="hidden" name="product_id"   value="{{ $product['id'] }}">
                                        <input type="hidden" name="quantity"     value="1">
                                        <button type="submit"
                                                class="add-cart-btn form_button"
                                                {{ $stock <= 0 ? 'disabled' : '' }}>
                                            {{ $stock <= 0 ? 'Sold out' : 'Add to cart' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    {{-- ====================================================
         FOOTER — store details only, no invented content
         ==================================================== --}}
    <footer class="site-footer">
        <div class="wrap">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3 class="brand-name">{{ $shop_data['store_name'] ?? 'Our Shop' }}</h3>
                    @if(!empty($shop_data['tagline']))
                        <p>{{ $shop_data['tagline'] }}</p>
                    @endif

                    @if(!empty($shop_data['social_links']) && is_array($shop_data['social_links']))
                        <div class="social-row">
                            @foreach($shop_data['social_links'] as $platform => $url)
                                @if($url)
                                    <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($platform) }}">
                                        @if(!empty($shop_data['social_icons'][$platform]))
                                            <i class="bi bi-{{ $shop_data['social_icons'][$platform] }}"></i>
                                        @else
                                            <i class="bi bi-link-45deg"></i>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="footer-col">
                    <h5>Contact</h5>
                    @if(!empty($shop_data['phone']))
                        <p>{{ $shop_data['phone'] }}</p>
                    @endif
                    @if(!empty($shop_data['email']))
                        <p>{{ $shop_data['email'] }}</p>
                    @endif
                    @if(!empty($shop_data['address']))
                        <p>{{ $shop_data['address'] }}</p>
                    @endif
                </div>

                <div class="footer-col">
                    <h5>Catalogue</h5>
                    @foreach($product_categ as $catId => $cat)
                        <a href="#cat-{{ $catId }}">{{ $cat['category_name'] }}</a>
                    @endforeach
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} {{ $shop_data['store_name'] ?? 'Our Shop' }}</span>
                <span>All rights reserved</span>
            </div>
        </div>
    </footer>

</div>

@endsection