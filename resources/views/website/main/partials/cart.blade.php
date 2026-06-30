{{--
    website/main/partials/cart.blade.php

    Full standalone cart view — used by:
      - CartController::loadCartView() (route: cart.view, GET /cart/load/{cart_id})

    This wraps the shared cart_items partial in the #cart_items_container
    div. The modal (cart_modal.blade.php) includes the SAME cart_items
    partial directly, so both surfaces render identical row markup from
    one source of truth.

    Expects:
      $cartItems → Collection of CartModel, each with ->product eager loaded
      $count     → int, total quantity across the cart
--}}

<div id="cart_items_container">
    @include('website.main.partials.cart_items', ['cartItems' => $cartItems])
</div>