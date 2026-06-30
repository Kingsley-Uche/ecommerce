<footer id="footer" class="footer dark-background">
    <div class="footer-main">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-4 col-md-6">
            <div class="footer-widget footer-about">
              <a href="{{ route('home') }}" class="logo">
                <span class="sitename">{{ $shop_data['store_name'] ?? 'Our Shop' }}</span>
              </a>
              @if(!empty($shop_data['tagline']))
                <p>{{ $shop_data['tagline'] }}</p>
              @endif

              @if(!empty($shop_data['social_links']) && is_array($shop_data['social_links']))
                <div class="social-links mt-4">
                  <h5>Connect With Us</h5>
                  <div class="social-icons">
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
                </div>
              @endif
            </div>
          </div>

          <div class="col-lg-2 col-md-6 col-sm-6">
            <div class="footer-widget">
              <h4>Shop</h4>
              <ul class="footer-links">
                @if(isset($product_categ) && count($product_categ))
                  @foreach(collect($product_categ)->take(6) as $catId => $cat)
                    <li>
                      <a href="{{ route('category.products', ['category_id' => encrypt($catId)]) }}">
                        {{ $cat['category_name'] }}
                      </a>
                    </li>
                  @endforeach
                @else
                  <li class="text-muted">No categories yet</li>
                @endif
              </ul>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 col-sm-6">
            <div class="footer-widget">
              <h4>Support</h4>
              <ul class="footer-links">
                {{-- TODO: these support/policy pages have no routes yet --}}
                <li><a href="#">Help Center</a></li>
                <li><a href="#">Order Status</a></li>
                <li><a href="#">Shipping Info</a></li>
                <li><a href="#">Returns &amp; Exchanges</a></li>
                <li><a href="#">Size Guide</a></li>
                <li><a href="#">Contact Us</a></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="footer-widget">
              <h4>Contact Information</h4>
              <div class="footer-contact">
                @if(!empty($shop_data['address']))
                  <div class="contact-item">
                    <i class="bi bi-geo-alt"></i>
                    <span>{{ $shop_data['address'] }}</span>
                  </div>
                @endif
                @if(!empty($shop_data['phone']))
                  <div class="contact-item">
                    <i class="bi bi-telephone"></i>
                    <span>{{ $shop_data['phone'] }}</span>
                  </div>
                @endif
                @if(!empty($shop_data['email']))
                  <div class="contact-item">
                    <i class="bi bi-envelope"></i>
                    <span>{{ $shop_data['email'] }}</span>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <div class="row gy-3 align-items-center">
          <div class="col-lg-6 col-md-12">
            <div class="copyright">
              <p>&copy; <span>Copyright</span> <strong class="sitename">{{ $shop_data['store_name'] ?? 'Our Shop' }}</strong>. All Rights Reserved.</p>
            </div>
            <div class="credits mt-1">
              <!-- All the links in the footer should remain intact. -->
              <!-- You can delete the links only if you've purchased the pro version. -->
              <!-- Licensing information: https://bootstrapmade.com/license/ -->
              Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
            </div>
          </div>

          <div class="col-lg-6 col-md-12">
            <div class="d-flex flex-wrap justify-content-lg-end justify-content-center align-items-center gap-4">
              <div class="payment-methods">
                <div class="payment-icons">
                  <i class="bi bi-credit-card" aria-label="Credit Card"></i>
                  <i class="bi bi-cash" aria-label="Cash on Delivery"></i>
                </div>
              </div>

              {{-- TODO: terms/privacy/cookies pages have no routes yet --}}
              <div class="legal-links">
                <a href="#">Terms</a>
                <a href="#">Privacy</a>
                <a href="#">Cookies</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>


  <!-- Scroll Top, Preloader -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="preloader"></div>

  {{-- Cart modal — now contains #cart_items_container and order summary
       so inner.js's CheckoutContent() / updateCartSummary() have somewhere
       real to write to --}}
  @include('website.components.cart_modal')

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/drift-zoom/Drift.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>

  <!-- SweetAlert2 — required by toast() in swal-toast.js. Must load BEFORE
       main.js / inner.js / payment.js since they call toast() on events. -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('assets/js/swal-toast.js') }}"></script>

  <!-- Globals needed by cart-remove-patches.js — must be defined before it loads -->
  <script>
    const CART_ADD_URL = "{{ route('api.cart.add') }}";
    window.CART_REMOVE_URL = "{{ route('api.cart.remove') }}";
  </script>

  <!-- Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script src="{{ asset('assets/js/inner.js') }}"></script>
  <script src="{{ asset('assets/js/inner-patches.js') }}"></script>
  <script src="{{ asset('assets/js/payment.js') }}"></script>
  <script src="{{ asset('assets/js/cart-modal-patches.js') }}"></script>
  <script src="{{ asset('assets/js/cart-remove-patches.js') }}"></script>

  <div id="loadingSpinner">
    <div class="spinner"></div>
  </div>
