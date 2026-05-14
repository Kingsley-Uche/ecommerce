@extends('website.main.landpage')

@section('content')

<main class="main">
    <!-- Hero Section -->
    <section id="hero" class="hero section">

      <div class="hero-container">
        <div class="hero-content">
          <div class="content-wrapper" data-aos="fade-up" data-aos-delay="100">
            <h1 class="hero-title">Discover Amazing Products</h1>
            <p class="hero-description">Explore our curated collection of premium items designed to enhance your lifestyle. From fashion to tech, find everything you need with exclusive deals and fast shipping.</p>
            <div class="hero-actions" data-aos="fade-up" data-aos-delay="200">
              <a href="#products" class="btn-primary">Shop Now</a>
              <a href="#categories" class="btn-secondary">Browse Categories</a>
            </div>
            <div class="features-list" data-aos="fade-up" data-aos-delay="300">
              <div class="feature-item">
                <i class="bi bi-truck"></i>
                <span>Free Shipping</span>
              </div>
              <div class="feature-item">
                <i class="bi bi-award"></i>
                <span>Quality Guarantee</span>
              </div>
              <div class="feature-item">
                <i class="bi bi-headset"></i>
                <span>24/7 Support</span>
              </div>
            </div>
          </div>
        </div>

        <div class="hero-visuals">
          <div class="product-showcase" data-aos="fade-left" data-aos-delay="200">
            <div class="product-card featured">
              <!-- Featured Product -->
               <div class="hero-product-slider swiper init-swiper">
    <script type="application/json" class="swiper-config">
    {
        "loop": true,
        "speed":3000,
        "autoplay": {
            "delay": 5000,
        },
        "slidesPerView": 1,
        direction": "vertical",
        "slidesPerView": 1,
                  "direction": "vertical",
                  "effect": "slide"
    }
    </script>

    <div class="swiper-wrapper">
        @php
            $frontpageProducts = $shop_data['products'] ?? [];
        @endphp

        @if(!empty($frontpageProducts))
            @foreach($frontpageProducts as $product)
                <div class="swiper-slide">
                    <img src="{{ asset('storage/' . ($product['image'] ?? 'default.jpg')) }}" alt="{{ $product['name'] }}">
                    <div class="product-info">
                        <h4>{{ $product['name'] }}</h4>
                        <p>₦{{ number_format($product['price'], 2) }}</p>
                    </div>
                </div>
            @endforeach
        @else
            <div class="swiper-slide">No products available</div>
        @endif
    </div>
</div>

            </div>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->


    <!-- Promo Cards Section -->
    <section id="promo-cards" class="promo-cards section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
          
<h5>Shop by Category</h5>
                <div class="col-lg-12">
                    <div class="row gy-4">
                        @for($i = 1; $i <= 20; $i++)
                            @if(isset($product_categ[$i]))
                                @php
                                    $cat = $product_categ[$i];
                                    $img = 'assets/img/product/product-' . ($i == 1 ? 'm-5' : ($i == 2 ? '8' : ($i == 3 ? '3' : '12'))) . '.webp';
                                @endphp
                                <div class="col-xl-3 col-md-6">
                                    <div class="category-card {{ $i == 1 ? 'cat-men' : ($i == 2 ? 'cat-kids' : ($i == 3 ? 'cat-cosmetics' : 'cat-accessories')) }}" data-aos="fade-up" data-aos-delay="{{ 200 + ($i * 100) }}">
                                        <div class="category-image">
                                            <img src="{{ asset($img) }}" alt="{{ $cat['category_name'] }}" class="img-fluid">
                                        </div>
                                        <div class="category-content">
                                            <h4>{{ $cat['category_name'] }}</h4>
                                            <p>{{ count($cat['products']) }} products</p>
                                            <a href="{{ route('category.products', ['category_id' => encrypt($i)]) }}" class="card-link">View products <i class="bi bi-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Promo Cards Section -->

    <!-- Best Sellers Section -->
    @if(isset($sale_categ_data) && count($sale_categ_data) > 0)
        @foreach($sale_categ_data as $index => $category)
            <section id="best-sellers-{{ $loop->index }}" class="best-sellers section">
                <!-- Section Title -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>{{ $category['sales_category_name'] }}</h2>
                    <p>{{ $category['sales_category_description'] ?? 'Explore our top-selling items' }}</p>
                </div>

                <div class="container" data-aos="fade-up" data-aos-delay="100">
                    <div class="row g-5">
                  @foreach($category['products'] as $product)
    <div class="col-lg-3 col-md-6">
    <div class="product-item">

        <div class="product-image"
             style="height: 22vh; overflow: hidden; display: flex; justify-content: center; align-items: center; background: #f8f8f8; border-radius: 8px;">

            <!-- OPTIONAL BADGE -->
            @if(!empty($product['badge']))
                <div class="product-badge trending-badge">{{ $product['badge'] }}</div>
            @endif

            @php
                $img = $product['images'][0]->image_path ?? 'assets/img/default-product.png';
            @endphp

            <img src="{{ asset('storage/' . $img) }}"
                 alt="{{ $product['name'] }}"
                 class="p-1"
                 loading="lazy"
                 style="height: 18vh; width: auto; object-fit: contain;"> 
        </div>

        <div class="product-info">

            <div class="product-category">
                {{ $category['sales_category_name'] }}
            </div>

            <h4 class="product-name">
                <a href="{{ url('/product/' . Str::slug($product['name'])) }}">
                    {{ Str::limit($product['name'], 40) }}
                </a>
            </h4>
              <form method ='post' name='add_to_cart' id='add_to_cart' action="{{route('api.cart.add')}}">
                              <input type ='hidden' name='product_name' value="{{$product['name']}}">
                              <input type ='hidden' name='product_id' value="{{$product['id']}}">
                              <input type='hidden' name='quantity' value='1'>
                          <button class="cart-btn btn-sm form_button">Add to Cart</button>
                          </form>
                 <div class="mt-3">
                          <button class="btn btn-outline-dark btn-sm w-100 mb-2" 
                                  type="button" 
                                  data-bs-toggle="collapse" 
                                  data-bs-target="#productDetails{{ $product['id'] }}" 
                                  aria-expanded="false" 
                                  aria-controls="productDetails{{ $product['id'] }}">
                            Description
                          </button>
                          <div class="collapse mt-2" id="productDetails{{ $product['id'] }}">
                            <div class="card card-body border-light text-start small">
                              <h6 class="fw-bold mb-2">Description</h6>
                              <p class="mb-0 text-muted">{{ $product['description'] ?? 'No description available.' }}</p>
                            </div>
                          </div>
            <div class="product-rating">
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star-fill"></i>
                    @endfor
                </div>
                <span class="rating-count">({{ rand(20, 170) }})</span>
            </div>

            <div class="product-price">
                ₦{{ number_format($product['price'], 2) }}
            </div>

        </div>
    </div>
</div>

@endforeach

                    </div>
                </div>
            </section>
        @endforeach
    @endif
        
    <!-- /Best Sellers Section -->

    <!-- Cards Section -->
    
    <section id="cards" class="cards section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            @if(isset($product_categ) && count($product_categ) > 0)
                @foreach($product_categ as $cat)
                    <div class="mb-5">
                        <h3 class="category-title mb-4">
                              <i class="bi bi-award"></i>  {{ $cat['category_name'] }}
                        </h3>

                        <div class="row gy-4">
                            @foreach(collect($cat['products'])->take(10) as $product)
                                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                                    <div class="product-card shadow-sm p-2 rounded">
                                        <div class="product-image">
                                            @php
                                                $image = $product['images']->first()->image_path ?? null;
                                            @endphp
                                            <img 
                                                src="{{ asset('storage/' . $image) }}" 
                                                alt="{{ $product['name'] }}" 
                                                class="img-fluid rounded"
                                                style="height:15vh;object-fit:contain;"
                                            >
                                        </div>

                                        <div class="product-info mt-3">
                                            <h4 class="product-name fw-bold">{{ Str::limit($product['name'], 30) }}</h4>
                                            <p class="small text-muted">{{ Str::limit($product['description'], 60) }}</p>
                                            <div class="product-price fw-bold">
                                                ₦{{ number_format($product['price'], 2) }}
                                            </div>
                                             <div class="product-price fw-bold">
                                                <form method ='post' name='add_to_cart' id='add_to_cart' action="{{route('api.cart.add')}}">
                                                 <input type ='hidden' name='product_name' value="{{$product['name']}}">
                                                <input type ='hidden' name='product_id' value="{{$product['id']}}">
                                                <input type='hidden' name='quantity' value='1'>
                                                <button class='btn btn-dark text-white form_button cart-btn'> Add to cart</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                @endif
        </div>
    </section>
    <!-- /Cards Section -->

    <!-- Call To Action Section -->
    <section id="call-to-action" class="call-to-action section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="main-content text-center" data-aos="zoom-in" data-aos-delay="200">
                        <div class="offer-badge" data-aos="fade-down" data-aos-delay="250">
                            <span class="limited-time">Limited Time</span>
                            <span class="offer-text">50% OFF</span>
                        </div>
                        <h2 data-aos="fade-up" data-aos-delay="300">Exclusive Flash Sale</h2>
                        <p class="subtitle" data-aos="fade-up" data-aos-delay="350">Don't miss out on our biggest sale of the year. Premium quality products at unbeatable prices for the next 48 hours only.</p>
                        <div class="countdown-wrapper" data-aos="fade-up" data-aos-delay="400">
                            <div class="countdown d-flex justify-content-center" data-count="2025/12/31">
                                <div><h3 class="count-days"></h3><h4>Days</h4></div>
                                <div><h3 class="count-hours"></h3><h4>Hours</h4></div>
                                <div><h3 class="count-minutes"></h3><h4>Minutes</h4></div>
                                <div><h3 class="count-seconds"></h3><h4>Seconds</h4></div>
                            </div>
                        </div>
                        <div class="action-buttons" data-aos="fade-up" data-aos-delay="450">
                            <a href="#" class="btn-shop-now form_button form_button">Shop Now</a>
                            <a href="#" class="btn-view-deals">View All Deals</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row featured-products-row" data-aos="fade-up" data-aos-delay="500">
                @if(isset($sale_categ_data[0]['products']))
                    @foreach(collect($sale_categ_data[0]['products'])->take(4) as $index => $product)
                        <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="{{ 100 + ($index * 50) }}">
                            <div class="product-showcase" style="height:10vh">
                                <div class="product-image" >
                                    @php
                                        $img = $product['images'][0]->image_path ?? 'assets/img/product/product-5.webp';
                                    @endphp
                                    <img src="{{ asset('storage/' . $img) }}" alt="{{ $product['name'] }}" class="img-fluid">
                                    <div class="discount-badge">-{{ rand(30, 60) }}%</div>
                                </div>
                                <div class="product-details">
                                    <h6>{{ Str::limit($product['name'], 25) }}</h6>
                                    <div class="price-section">
                                        <span class="original-price">₦{{ number_format($product['price'] * 1.8, 2) }}</span>
                                        <span class="sale-price">₦{{ number_format($product['price'], 2) }}</span>
                                    </div>
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= 4 ? '-fill' : '' }}"></i>
                                        @endfor
                                        <span class="rating-count">({{ rand(100, 400) }})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                   
            </div>
        </div>
    </section>
    <!-- /Call To Action Section -->
</main>
@endsection()