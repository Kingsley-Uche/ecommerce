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

.catalogue { font-family: var(--font-mono); color: var(--ink); background: var(--paper); font-size: 15px; }
.wrap { max-width: 1180px; margin: 0 auto; padding: 0 1.75rem; }

.search-head { padding: 3rem 0 2.5rem; border-bottom: 1px solid var(--line); }
.search-eyebrow {
    font-size: .7rem; letter-spacing: .14em; text-transform: uppercase;
    color: var(--clay); font-weight: 700; margin-bottom: .75rem; display: block;
}
.search-title {
    font-family: var(--font-display);
    font-size: clamp(1.75rem, 4vw, 2.75rem);
    font-weight: 600; line-height: 1.1; margin-bottom: .5rem;
}
.search-meta { font-size: .85rem; color: var(--ink-mid); }
.search-meta strong { color: var(--ink); }

.results-section { padding: 3rem 0 4rem; }

.empty-state {
    text-align: center;
    padding: 4rem 1rem;
    color: var(--ink-mid);
}
.empty-state h3 {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: .5rem;
}
.empty-state p { font-size: .9rem; max-width: 40ch; margin: 0 auto; }

.product-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    border-top: 1px solid var(--line);
    border-left: 1px solid var(--line);
}
.product-cell {
    border-right: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
    padding: 1.5rem 1.5rem 1.65rem;
    display: flex; flex-direction: column;
    transition: background .25s var(--ease);
}
.product-cell:hover { background: var(--paper-2); }

.stock-ledger {
    font-size: .68rem; color: var(--ink-soft); letter-spacing: .04em;
    display: flex; align-items: center; gap: .4rem; margin-bottom: .9rem;
}
.stock-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--moss); flex-shrink: 0; }
.stock-dot.low { background: var(--clay); }
.stock-dot.out { background: var(--ink-soft); }

.pc-image {
    height: 170px; display: flex; align-items: center; justify-content: center;
    background: var(--paper); margin-bottom: 1.1rem; overflow: hidden;
}
.pc-image img { max-height: 150px; width: auto; object-fit: contain; transition: transform .4s var(--ease); }
.product-cell:hover .pc-image img { transform: scale(1.04); }

.pc-name { font-family: var(--font-display); font-size: 1.02rem; font-weight: 500; line-height: 1.35; margin-bottom: .4rem; }
.pc-name a:hover { color: var(--clay); }
.pc-desc { font-size: .78rem; color: var(--ink-mid); line-height: 1.55; margin-bottom: 1rem; flex: 1; }

.pc-bottom {
    display: flex; align-items: center; justify-content: space-between; gap: .75rem;
    padding-top: .85rem; border-top: 1px solid var(--line);
}
.pc-price { font-family: var(--font-display); font-size: 1.15rem; font-weight: 600; }

.add-cart-btn {
    background: var(--ink); color: var(--paper); border: none;
    font-family: var(--font-mono); font-size: .7rem; font-weight: 600;
    letter-spacing: .08em; text-transform: uppercase;
    padding: .55rem .9rem; cursor: pointer; transition: background .2s var(--ease);
}
.add-cart-btn:hover { background: var(--clay); }
.add-cart-btn:disabled { background: var(--ink-soft); cursor: not-allowed; }
.cart-form { display: contents; }

@media (max-width: 560px) {
    .product-row { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
}
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;1,500&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="catalogue">

    <section class="search-head">
        <div class="wrap">
            <span class="search-eyebrow">Search</span>
            <h1 class="search-title">
                @if($term !== '')
                    Results for &ldquo;{{ $term }}&rdquo;
                @else
                    Search our catalogue
                @endif
            </h1>

            @if($term !== '')
                <p class="search-meta"><strong>{{ count($products) }}</strong> item{{ count($products) === 1 ? '' : 's' }} found</p>
            @endif

            <form method="POST" action="{{ route('product-search') }}" class="mt-4" style="max-width: 480px;">
                @csrf
                <div class="input-group">
                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        placeholder="Search for products"
                        value="{{ $term }}"
                        autofocus
                    >
                    <button class="btn btn-dark" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="results-section">
        <div class="wrap">
            @if($term === '')
                <div class="empty-state">
                    <h3>Type something to search</h3>
                    <p>Search by product name or description to find what you're looking for.</p>
                </div>
            @elseif(count($products) === 0)
                <div class="empty-state">
                    <h3>No results for &ldquo;{{ $term }}&rdquo;</h3>
                    <p>Try a different term, or check the spelling.</p>
                </div>
            @else
                <div class="product-row">
                    @foreach($products as $product)
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

                            <div class="pc-image">
                                @if($image)
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $product['name'] }}" loading="lazy">
                                @else
                                    <img src="{{ asset('assets/img/default-product.png') }}" alt="{{ $product['name'] }}" loading="lazy">
                                @endif
                            </div>

                            <p class="pc-name">
                                <a href="{{ url('/product/' . \Illuminate\Support\Str::slug($product['name'])) }}">
                                    {{ \Illuminate\Support\Str::limit($product['name'], 48) }}
                                </a>
                            </p>

                            @if(!empty($product['description']))
                                <p class="pc-desc">{{ \Illuminate\Support\Str::limit($product['description'], 80) }}</p>
                            @endif

                            <div class="pc-bottom">
                                <span class="pc-price">₦{{ number_format($product['price'], 2) }}</span>

                                <form method="post" action="{{ route('api.cart.add') }}" class="cart-form">
                                    @csrf
                                    <input type="hidden" name="product_name" value="{{ $product['name'] }}">
                                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="add-cart-btn form_button" {{ $stock <= 0 ? 'disabled' : '' }}>
                                        {{ $stock <= 0 ? 'Sold out' : 'Add to cart' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</div>

@endsection