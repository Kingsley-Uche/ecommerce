<header id="header" class="header sticky-top">
    {{-- This script block below needs <meta name="csrf-token" content="{{ csrf_token() }}">
         in the page's <head> (in landpage.blade.php's layout) for the fetch
         calls to authenticate with Laravel's CSRF middleware. --}}

    {{-- ============================================================
         TOP BAR — real shop_data only
         ============================================================ --}}
    <div class="top-bar py-2">
      <div class="container-fluid container-xl">
        <div class="row align-items-center">
          <div class="col-lg-4 d-none d-lg-flex">
            @if(!empty($shop_data['phone']))
              <div class="top-bar-item">
                <i class="bi bi-telephone-fill me-2"></i>
                <span>Need help? Call us: </span>
                <a href="{{ 'tel:' . $shop_data['phone'] }}">{{ $shop_data['phone'] }}</a>
              </div>
            @endif
          </div>

          <div class="col-lg-4 col-md-12 text-center">
            @if(!empty($shop_data['tagline']))
              <div class="top-bar-item">
                <span>{{ $shop_data['tagline'] }}</span>
              </div>
            @endif
          </div>

          <div class="col-lg-4 d-none d-lg-block">
            <div class="d-flex justify-content-end">
              @if(!empty($shop_data['social_links']) && is_array($shop_data['social_links']))
                <div class="top-bar-item d-flex gap-3">
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
          </div>
        </div>
      </div>
    </div>

    {{-- ============================================================
         MAIN HEADER — store_name, search, account, cart
         ============================================================ --}}
    <div class="main-header">
      <div class="container-fluid container-xl">
        <div class="d-flex py-3 align-items-center justify-content-between">

          {{-- Logo / store name --}}
          <a href="{{ route('home') }}" class="logo d-flex align-items-center">
            @if(!empty($shop_data['logo_path']))
              <img src="{{ asset('storage/' . $shop_data['logo_path']) }}" alt="{{ $shop_data['store_name'] ?? 'Store' }}" class="logo-img me-2">
            @endif
            <h1 class="sitename mb-0">{{ $shop_data['store_name'] ?? 'Our Shop' }}</h1>
          </a>

          {{-- Search — no search route defined yet, see note. Form disabled until added --}}
          <form class="search-form desktop-search-form" method="GET" action="#">
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Search for products" value="{{ request('q') }}" disabled>
              <button class="btn" type="submit" disabled>
                <i class="bi bi-search"></i>
              </button>
            </div>
          </form>

          {{-- Actions --}}
          <div class="header-actions d-flex align-items-center justify-content-end">

            {{-- Mobile search toggle --}}
            <button class="header-action-btn mobile-search-toggle d-xl-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearch" aria-expanded="false" aria-controls="mobileSearch">
              <i class="bi bi-search"></i>
            </button>

            {{-- Account --}}
            <div class="dropdown account-dropdown">
              <button class="header-action-btn" data-bs-toggle="dropdown">
                <i class="bi bi-person"></i>
              </button>
              <div class="dropdown-menu">
                <div class="dropdown-header">
                  <h6>Welcome to <span class="sitename">{{ $shop_data['store_name'] ?? 'our store' }}</span></h6>
                  <p class="mb-0">Access account &amp; manage orders</p>
                </div>
                <div class="dropdown-body">
                  {{-- TODO: account.profile / account.orders / account.wishlist / account.settings
                       routes do not exist yet — see note below the file --}}
                  <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-person-circle me-2"></i>
                    <span>My Profile</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-bag-check me-2"></i>
                    <span>My Orders</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-heart me-2"></i>
                    <span>My Wishlist</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="bi bi-gear me-2"></i>
                    <span>Settings</span>
                  </a>
                </div>
                <div class="dropdown-footer">
                  <a href="{{ route('login') }}" class="btn btn-primary w-100">Sign In</a>
                </div>
              </div>
            </div>

            {{-- Wishlist — no route defined yet, see note --}}
            <a href="#" class="header-action-btn d-none d-md-block">
              <i class="bi bi-heart"></i>
              <span class="badge">0</span>
            </a>

            {{-- Cart --}}
            <a id="cart_icon" data-cart-url="{{ route('cart.view', ['cart_id' => 0]) }}" class="header-action-btn" data-bs-toggle="modal" data-bs-target="#cart_modal">
              <i class="bi bi-cart3"></i>
              <span class="badge text-small text-white" id="item_number"></span>
            </a>

            {{-- Mobile nav toggle --}}
            <i class="mobile-nav-toggle d-xl-none bi bi-list me-0"></i>
          </div>
        </div>
      </div>
    </div>

    {{-- ============================================================
         NAVIGATION — built from $product_categ, ordered by priority
         ============================================================ --}}
    <div class="header-nav">
      <div class="container-fluid container-xl position-relative">
        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="{{ route('home') }}">Home</a></li>

            @if(isset($product_categ) && count($product_categ))
              @foreach($product_categ as $catId => $cat)
                <li>
                  <a href="{{ route('category.products', ['category_id' => encrypt($catId)]) }}">
                    {{ $cat['category_name'] }}
                  </a>
                </li>
              @endforeach
            @endif

            </ul>
        </nav>
      </div>
    </div>

    {{-- ============================================================
         MOBILE SEARCH
         ============================================================ --}}
    <div class="collapse" id="mobileSearch">
      <div class="container">
        <form class="search-form" method="GET" action="#">
          <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Search for products" value="{{ request('q') }}" disabled>
            <button class="btn" type="submit" disabled>
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
</header>

<script>
/* ============================================================
   CART BADGE — loads real count on page load, and exposes a
   helper any add-to-cart form/button can call after a response.
   ============================================================ */
(function () {
    const badge = document.getElementById('item_number');
    if (!badge) return;

    function setCount(count) {
        const n = parseInt(count, 10) || 0;
        badge.textContent = n;
        badge.style.display = n > 0 ? '' : 'none';
    }

    // api.cart.get is a POST route — cart_token (if any) comes from the
    // cart_token cookie automatically; guests with no cookie yet and no
    // logged-in user simply get an empty cart back.
    function loadCartCount() {
        fetch("{{ route('api.cart.get') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({}),
        })
            .then(res => res.json())
            .then(data => setCount(data.count))
            .catch(() => setCount(0));
    }

    // Expose globally so add-to-cart forms (e.g. the "Add to Cart" buttons
    // on product cards) can update the badge immediately from the JSON
    // response returned by api.cart.add, instead of waiting on a reload.
    window.updateCartBadge = setCount;

    document.addEventListener('DOMContentLoaded', loadCartCount);
})();
</script>
