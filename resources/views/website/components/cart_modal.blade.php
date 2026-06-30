<div class="modal fade" id="cart_modal" tabindex="-1" aria-labelledby="cart_modal_label" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="cart_modal_label">Cart Contents</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">

        <section id="cart" class="cart section">
          <div class="container-fluid">

            <div class="row">

              <!-- Items -->
              <div class="col-lg-8">

                <div class="cart-items">

                  <!-- Header -->
                  <div class="cart-header d-none d-lg-block mb-3">
                    <div class="row fw-bold">
                      <div class="col-lg-5">Product</div>
                      <div class="col-lg-2 text-center">Price</div>
                      <div class="col-lg-1 text-center">Quantity</div>
                      <div class="col-lg-2 text-center">Total</div>
                    </div>
                  </div>

                  <form method="post" name="update_cart" id="update_cart_form" action="{{ route('api.cart.update') }}">
                    @csrf

                    {{-- Server-rendered on first paint via the cart_items partial
                         (same one CartController::loadCartView() uses), so there's
                         no "Loading cart…" flash. inner.js's CheckoutContent()
                         takes over and rewrites this container after any
                         add/update/remove action, since those happen without a
                         full page reload. $cartItems is supplied by the
                         ComposeCartModal view composer — see AppServiceProvider. --}}
                    <div id="cart_items_container">
                        @include('website.main.partials.cart_items', ['cartItems' => $cartItems ?? collect()])
                    </div>
                  </form>

                </div>
              </div>

              <!-- Summary -->
              <div class="col-lg-4">
                <div class="cart-summary p-3 border rounded">

                  <h4 class="summary-title mb-3">Order Summary</h4>

                  <div class="summary-item d-flex justify-content-between mb-2">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value purecounter"
                        id="cart_subtotal"
                        data-purecounter-start="0"
                        data-purecounter-end="0"
                        data-purecounter-duration="1">
                    </span>
                  </div>

                  <div class="summary-item d-flex justify-content-between mb-2">
                    <span class="summary-label">Tax</span>
                    <span class="summary-value purecounter"
                        id="cart_tax"
                        data-purecounter-start="0"
                        data-purecounter-end="0"
                        data-purecounter-duration="1">
                    </span>
                  </div>

                  <div class="summary-item d-flex justify-content-between mb-2">
                    <span class="summary-label">Discount</span>
                    <span class="summary-value purecounter"
                        id="cart_discount"
                        data-purecounter-start="0"
                        data-purecounter-end="0"
                        data-purecounter-duration="1">
                    </span>
                  </div>

                  <hr>

                  <div class="summary-total d-flex justify-content-between mb-3">
                    <span class="summary-label fw-bold">Total</span>
                    <span class="summary-value fw-bold purecounter"
                        id="cart_total"
                        data-purecounter-start="0"
                        data-purecounter-end="0"
                        data-purecounter-duration="1">
                    </span>
                  </div>

                  <div id="remove_pay">
                    <a href="#" class="btn btn-dark w-100 mb-2 cart_update" data-info="checkout">Proceed to Checkout</a>
                    <a href="#" class="btn btn-outline-dark w-100 cart_update" data-info="continue">
                      <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                  </div>

                </div>
              </div>

            </div>

          </div>
        </section>

      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>