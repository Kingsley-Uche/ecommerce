@extends('website.main.landpage')

@section('content')
@php
  $category_data = $category->firstWhere('id', $category_id);
@endphp

<main class="main">

  <!-- Page Title -->
  <div class="page-title light-background py-4 mb-4 border-bottom">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">{{ $category_data->name ?? 'Category' }}</h1>
      <nav class="breadcrumbs">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">{{ $category_data->name ?? 'Category' }}</li>
        </ol>
      </nav>
    </div>
  </div>
  <!-- End Page Title -->

  <div class="container py-4">
    <div class="row">

      <!-- Sidebar -->
      <div class="col-lg-2 sidebar mb-4 mb-lg-0">
        <div class="widgets-container p-3 border rounded shadow-sm bg-light">
          <h4 class="widget-title mb-3">Product Categories</h4>
          <ul class="category-tree list-unstyled mb-0">
            @foreach($category as $cat)
              <li class="category-item mb-2">
                <a href="{{ route('category.products', ['category_id' => encrypt($cat->id)]) }}" 
                   class="category-link d-block px-2 py-1 rounded 
                   {{ isset($category_data) && $cat->id === $category_data->id 
                      ? 'fw-bold text-primary bg-white shadow-sm' 
                      : 'text-dark' }}">
                  {{ $cat->name }}
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
      <!-- /Sidebar -->

      <!-- Products Section -->
      <div class="col-lg-10">
        <section id="category-product-list" class="category-product-list section">
          <div class="container px-0" data-aos="fade-up" data-aos-delay="100">
            <div class="row g-4">

              @if(!empty($products) && count($products) > 0)
                @foreach($products as $product)
                  @php
                    $imageCount = $product->images->count();
                    $hasMultipleImages = $imageCount > 1;
                  @endphp

                  <div class="col-6 col-xl-4">
                    <div class="product-card border rounded shadow-sm " data-aos="zoom-in">
                      <div class="position-relative">
                        <!-- Swiper for Product Images -->
                        <div class="product-swiper swiper init-swiper">
                          <script type="application/json" class="swiper-config">
                            {
                              "loop": {{ $hasMultipleImages ? 'true' : 'false' }},
                              "speed": 600,
                              "autoplay": {{ $hasMultipleImages ? '{ "delay": 3000, "disableOnInteraction": false }' : 'false' }},
                              "slidesPerView": 1,
                              "spaceBetween": 0,
                              "effect": "slide",
                              "direction": "horizontal",
                              "pagination": {
                                "el": ".swiper-pagination",
                                "clickable": true
                              }
                            }
                          </script>

                          <div class="swiper-wrapper">
                            @foreach($product->images as $image)
                              <div class="swiper-slide swiper-image" >
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     class="img-fluid rounded-top" 
                                     alt="{{ $product->name }}" style="height:15vh>
                              </div>
                            @endforeach
                          </div>

                          <!-- Show pagination only if multiple images -->
                          @if($hasMultipleImages)
                            <div class="swiper-pagination"></div>
                          @endif
                        </div>
                        <!-- /Swiper -->

                        <div class="product-overlay position-absolute top-0 start-0 w-100 
                                    d-flex justify-content-center align-items-center opacity-0 
                                    hover-opacity-100 transition">
                          <div class="product-actions d-flex gap-2">
                            <button type="button" class="btn btn-outline-light btn-sm" title="Quick View">
                              <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-light btn-sm" title="Add to Cart">
                              <i class="bi bi-cart-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <div class="product-details p-3 text-center">
                        <h5 class="product-title text-truncate mb-1">{{ $product->name }}</h5>
                        <div class="product-meta d-flex justify-content-between align-items-center small text-muted">
                          <span class="product-price fw-semibold">${{ number_format($product->price, 2) }}</span>
                          <span class="product-rating">
                            <i class="bi bi-star-fill text-warning"></i> 4.8
                          </span>
                        </div>

                        <!-- Buttons and Collapse -->
                        <div class="mt-3">
                          <button class="btn btn-outline-dark btn-sm w-100 mb-2" 
                                  type="button" 
                                  data-bs-toggle="collapse" 
                                  data-bs-target="#productDetails{{ $product->id }}" 
                                  aria-expanded="false" 
                                  aria-controls="productDetails{{ $product->id }}">
                            Description
                          </button>
                          <div class="collapse mt-2" id="productDetails{{ $product->id }}">
                            <div class="card card-body border-light text-start small">
                              <h6 class="fw-bold mb-2">Description</h6>
                              <p class="mb-0 text-muted">{{ $product->description ?? 'No description available.' }}</p>
                            </div>
                          </div>
                          <form method ='post' name='add_to_cart' id='add_to_cart' action="{{route('api.cart.add')}}">
                              <input type ='hidden' name='product_name' value='{{$product->name}}'>
                              <input type ='hidden' name='product_id' value='{{$product->id}}'>
                              <input type='hidden' name='quantity' value='1'>
                          <button class="btn btn-dark text-white btn-sm w-100 form_button">Add to Cart</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              @else
                <div class="col-12">
                  <p class="text-center text-muted">No products found in this category.</p>
                </div>
              @endif

            </div>
          </div>
        </section>
      </div>
      
    </div>
  </div>
</main>

<!-- Optional: CSS for pagination dots -->
<style>
  /* Force the swiper container to have a fixed height */
  .product-swiper {
    position: relative;
    overflow: hidden;
  }

  /* Control height of each slide container */
  .product-swiper .swiper-slide {
    height:30vh; /* Reduce height as you want */
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f9f9f9; /* Prevent white gaps */
  }

  /* Make images fit correctly */
  .product-swiper .swiper-slide img {
    width: 100%;
    height: 50%;
    object-fit: contain; /* or "contain" if you prefer full visibility */
    border-radius: 8px 8px 0 0;
  }

  /* Pagination styling */
  .product-swiper .swiper-pagination {
    bottom: 10px !important;
  }
  .product-swiper .swiper-pagination-bullet {
    width: 8px;
    height: 8px;
    background: rgba(255, 255, 255, 0.8);
    opacity: 0.9;
  }
  .product-swiper .swiper-pagination-bullet-active {
    background: #007bff;
    opacity: 1;
  }
</style>

@endsection
<!--- the image div is too long and when it is reduced, the image does'nt show at all  Kindly correct-->