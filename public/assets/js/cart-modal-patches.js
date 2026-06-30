/**
 * cart-modal-patches.js
 * --------------------------------------------------------------
 * payment.js's existing .cart_update click handler submits
 * update_cart_form for EVERY .cart_update button, including
 * "Continue Shopping" (data-info="continue"). That's wrong —
 * continue shopping should just close the modal without firing
 * an unnecessary PATCH to /cart/update.
 *
 * This intercepts clicks on [data-info="continue"] in the capture
 * phase, before payment.js's listener runs, and stops propagation
 * so only the modal-close behavior happens.
 *
 * Load this AFTER payment.js.
 * --------------------------------------------------------------
 */
document.addEventListener('click', function (evt) {
    const btn = evt.target.closest('.cart_update[data-info="continue"]');
    if (!btn) return;

    evt.preventDefault();
    evt.stopImmediatePropagation(); // blocks payment.js's handler on this element

    const modalEl = document.getElementById('cart_modal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.hide();
    }
}, true); // capture phase — runs before payment.js's bubble-phase listener
