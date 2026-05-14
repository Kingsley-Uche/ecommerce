@extends('website.main.landpage')

@section('content')

<main class="main">

  <!-- Page Title -->
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Checkout</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="/">Home</a></li>
          <li class="current">Checkout</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- Checkout Section -->
  <section id="checkout" class="checkout section">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="row">

        <!-- ================================
             LEFT SIDE - CHECKOUT FORM
        =================================== -->
        <div class="col-lg-7">

          <div class="checkout-container" data-aos="fade-up">

            <form class="checkout-form" method="POST" action="{{ route('payment.initiate') }}" id="checkout_form" >
              @csrf

              <!-- 1. CUSTOMER INFORMATION -->
              <div class="checkout-section" id="customer-info">
                <div class="section-header">
                  <div class="section-number">1</div>
                  <h3>Customer Information</h3>
                </div>

                <div class="section-content">

                  <div class="form-group">
                    <label for="customer_name">Full Name</label>
                    <input type="text" class="form-control"
                           name="customer_name" id="customer_name"
                           placeholder="Enter your full name"
                           required>
                  </div>

                  <div class="form-group">
                    <label for="customer_email">Email Address</label>
                    <input type="email" class="form-control"
                           name="customer_email" id="customer_email"
                           placeholder="Enter your email"
                           required>
                  </div>

                  <div class="form-group">
                    <label for="customer_phone">Phone Number</label>
                    <input type="tel" class="form-control"
                           name="customer_phone" id="customer_phone"
                           placeholder="Enter your phone number"
                           required>
                  </div>

                </div>
              </div>

              <!-- 2. SHIPPING ADDRESS -->
              <div class="checkout-section" id="shipping-address">
                <div class="section-header">
                  <div class="section-number">2</div>
                  <h3>Shipping Address</h3>
                </div>

                <div class="section-content">

                  <div class="form-group">
                    <label for="delivery_address">Street Address</label>
                    <input type="text" class="form-control"
                           name="delivery_address" id="delivery_address"
                           placeholder="House number / street name"
                           required>
                  </div>

                  <div class="form-group">
                    <label for="delivery_location">Delivery Location (City & Country)</label>
                    <input type="text" class="form-control"
                           name="delivery_location" id="delivery_location"
                           placeholder="e.g Abuja, Nigeria"
                           required>
                  </div>

                </div>
              </div>

              <!-- 3. REVIEW & PLACE ORDER -->
              <div class="checkout-section" id="order-review">
                <div class="section-header">
                  <div class="section-number">3</div>
                  <h3>Review &amp; Place Order</h3>
                </div>

                <div class="section-content">

                  <div class="form-check terms-check mb-3">
                    <input class="form-check-input" type="checkbox"
                           id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                      I agree to the
                      <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a>
                      and
                      <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                    </label>
                  </div>

                  <button type="submit" class="btn btn-dark w-100 py-3" id ="place_order_btn">
                    Place Order
                  </button>

                </div>
              </div>

              <!-- Hidden cart items -->
              @foreach ($cartItems as $item)
                <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                <input type="hidden" name="quantity[]" value="{{ $item->quantity }}">
              @endforeach

            </form>

          </div>
        </div>

        <!-- ================================
             RIGHT SIDE - SUMMARY
        =================================== -->
        <div class="col-lg-5">
          <div class="order-summary" data-aos="fade-left" data-aos-delay="200">

            <div class="order-summary-header">
              <h3>Order Summary</h3>
              <span class="item-count">{{ count($cartItems) }} Items</span>
            </div>

            <div class="order-summary-content">

              <div class="order-items">
                <?php
                $total = (int)0;
                ?>

                @foreach ($cartItems as $item)
                <div class="order-item">
                  <div class="order-item-image">

                    {{-- FIX: Your product table does not have image field --}}
                    <?php
                    $image = $item->product->images->first()->image_path ?? 'images/default.png';
                    ?>
                    <img src="{{ asset('storage/'.$image) }}" alt="{{ $item->product->name }}"  class="img-fluid">
                  </div>

                  <div class="order-item-details">
                    <h4>{{ $item->product->name }}</h4>
                    <?php
                    $sub_total = $item->product->price*$item->quantity;
                    $total = +$sub_total;
                    ?>

                    <div class="order-item-price">
                      <span class="quantity">{{ $item->quantity }} ×</span>
                      <span class="price">₦{{ number_format((float)$item->product->price, 2) }}</span>
                    </div>
                  </div>
                </div>
                @endforeach

              </div>

              <div class="order-totals mt-3">

              

                <div class="order-total d-flex justify-content-between fw-bold">
                  <span>Total</span>
                  <span>₦{{ number_format($total, 2) }}</span>
                </div>

              </div>

            </div>

          </div>
        </div>

      </div>

    </div>

  </section>

</main>

@endsection
