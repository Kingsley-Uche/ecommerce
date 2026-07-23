/**
 * cart-modal-patches.js
 * --------------------------------------------------------------
 * Handles:
 * 1. Continue Shopping button
 * 2. Lazy loading of cart modal
 * 3. Centralized cart state updates & DOM initialization
 * --------------------------------------------------------------
 */

/* --------------------------------------------------------------------------
   CENTRALIZED CART STATE UPDATER (SINGLE SOURCE OF TRUTH)
-------------------------------------------------------------------------- */
function syncCartState(cartData) {
    if (!cartData) return;

    // 1. Extract count and token safely from response variations (nested or flat)
    const count = cartData.count !== undefined ? parseInt(cartData.count) : null;
    
    // Check all potential keys your backend might use for the cart token
    const token = cartData.cart_token 
               || cartData.cartToken 
               || cartData.data?.cart_token 
               || cartData.data?.cartToken;

    // 2. Update Cookie if token is found anywhere in the response
    if (token) {
        document.cookie = `cart_token=${token}; path=/; max-age=${30 * 24 * 60 * 60}`;
    }

    // 3. Update LocalStorage & DOM Badge
    if (count !== null) {
        localStorage.setItem('cart_count', count);
        const countEl = document.getElementById('item_number');
        if (countEl) {
            if (count > 0) {
                countEl.classList.add('bg-warning');
                countEl.innerHTML = count;
                countEl.style.display = 'inline-block';
            } else {
                countEl.innerHTML = '';
                countEl.style.display = 'none';
            }
        }
    }

    // 4. Update Cart Icon Link href dynamically
    const cartIcon = document.getElementById('cart_icon');
    const currentToken = typeof getCookie === 'function' ? getCookie('cart_token') : token;
    if (cartIcon && cartIcon.dataset.cartUrl && currentToken) {
        const separator = cartIcon.dataset.cartUrl.includes('?') ? '&' : '?';
        cartIcon.href = `${cartIcon.dataset.cartUrl}${separator}cart_token=${encodeURIComponent(currentToken)}`;
    }

    // 5. Update Modal Content if items list exists
    const items = Array.isArray(cartData.data) ? cartData.data 
                : Array.isArray(cartData.items) ? cartData.items 
                : cartData.data?.items;
                
    if (items && typeof CheckoutContent === 'function') {
        CheckoutContent(items);
    }
}


/* --------------------------------------------------------------------------
   GLOBAL CLICK EVENT LISTENER (Continue Shopping & Cart Icon / Lazy Load)
-------------------------------------------------------------------------- */
document.addEventListener('click', async function (evt) {

    /*
    |--------------------------------------------------------------------------
    | Continue Shopping Button
    |--------------------------------------------------------------------------
    */
    const continueBtn = evt.target.closest('.cart_update[data-info="continue"]');

    if (continueBtn) {
        evt.preventDefault();
        evt.stopImmediatePropagation();

        const modalEl = document.getElementById('cart_modal');

        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();
        }

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Cart Icon (Lazy Load Modal)
    |--------------------------------------------------------------------------
    */
    const cartBtn = evt.target.closest('#cart_icon');

    if (!cartBtn) {
        return;
    }

    evt.preventDefault();

    const spinner = document.getElementById('loadingSpinner');

    try {
        if (spinner) {
            spinner.style.display = 'flex';
        }

        // 1. Get the token safely
        const cartToken = typeof getCookie === 'function' ? getCookie('cart_token') : null;

        // 2. Append token to the URL as a query parameter if it exists
        let targetUrl = cartBtn.dataset.cartUrl;
        if (cartToken) {
            const separator = targetUrl.includes('?') ? '&' : '?';
            targetUrl = `${targetUrl}${separator}cart_token=${encodeURIComponent(cartToken)}`;
        }

        const response = await fetch(targetUrl, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "text/html"
            }
        });

        if (!response.ok) {
            throw new Error("Unable to load cart.");
        }

        const html = await response.text();
        document.getElementById('cartModalContainer').innerHTML = html;

        const modalEl = document.getElementById('cart_modal');

        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }

    } catch (error) {
        console.error(error);

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Unable to load your cart.'
            });
        }
    } finally {
        if (spinner) {
            spinner.style.display = 'none';
        }
    }
}, true);


/* --------------------------------------------------------------------------
   PAGE LOAD INITIALIZATION
-------------------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    // Instant load from cache so it never looks empty/inconsistent on load
    const cachedCount = localStorage.getItem('cart_count');
    const countEl = document.getElementById('item_number');
    if (cachedCount !== null && countEl) {
        const count = parseInt(cachedCount) || 0;
        if (count > 0) {
            countEl.innerHTML = count;
            countEl.classList.add('bg-warning');
            countEl.style.display = 'inline-block';
        } else {
            countEl.innerHTML = '';
            countEl.style.display = 'none';
        }
    }

    // Fetch fresh cart state silently in the background
    const cartToken = typeof getCookie === 'function' ? getCookie('cart_token') : null;
    const el = document.getElementById('cart_icon');
    if (el && cartToken && typeof get_general_data === 'function') {
        const formData = new FormData();
        formData.append("cart_token", cartToken);
        
        get_general_data("/api/cart", "POST", formData).then(json => {
            if (json) syncCartState(json);
        });
    }
});