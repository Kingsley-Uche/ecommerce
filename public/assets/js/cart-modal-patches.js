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

    // 1. Extract count safely
    const count = cartData.count !== undefined ? parseInt(cartData.count) : null;
   
    

    
    // 2. Resolve token cleanly (check response payload first, fall back to existing cookies/globals)
   // 1. Extract the token safely from the payload
let token = cartData.cart_token || cartData.cartToken || cartData.data?.cart_token || cartData.data?.cartToken;

if (!token) {
    token = typeof getStoredValue === 'function' ? getStoredValue('cart_token') : window.CART_TOKEN || null;
}

// 2. Save it using your storage function if a valid token exists
if (token) {
    setStoredValue('cart_token', token, 30);
}

    // 3. Handle Cookie State
    if (count === 0 || !token) {
        document.cookie = "cart_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        token = null;
    } else {
        document.cookie = `cart_token=${token}; path=/; max-age=${30 * 24 * 60 * 60}`;
    }

    // 4. Update LocalStorage & DOM Badge
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

    // 5. Update Cart Icon Link href dynamically
    const cartIcon = document.getElementById('cart_icon');
    if (cartIcon && cartIcon.dataset.cartUrl && token) {
        const separator = cartIcon.dataset.cartUrl.includes('?') ? '&' : '?';
        cartIcon.href = `${cartIcon.dataset.cartUrl}${separator}cart_token=${encodeURIComponent(token)}`;
    }

    // 6. Update Modal Content if items list exists
    const items = Array.isArray(cartData.data) ? cartData.data 
                : Array.isArray(cartData.items) ? cartData.items 
                : cartData.data?.items;
                
    if (items && typeof CheckoutContent === 'function') {
        CheckoutContent(items);
    }
}


function setStoredValue(name, value, days = 30) {
    const expiryTime = new Date().getTime() + (days * 24 * 60 * 60 * 1000);
    
    // Save to localStorage with expiry
    const itemData = { value: value, expiry: expiryTime };
    localStorage.setItem(name, JSON.stringify(itemData));
    
    // Also set it as a cookie so backend requests pick it up natively
    document.cookie = `${name}=${value}; path=/; max-age=${days * 24 * 60 * 60}`;
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
        const cartToken = typeof getStoredValue === 'function' ? getStoredValue('cart_token') : null;
        

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
    const cartToken = typeof getStoredValue === 'function' ? getStoredValue('cart_token') : null;
    const el = document.getElementById('cart_icon');
    if (el && cartToken && typeof get_general_data === 'function') {
        const formData = new FormData();
        formData.append("cart_token", cartToken);
        
        get_general_data("/api/cart", "POST", formData).then(json => {
            if (json) syncCartState(json);
        });
    }
});