/**
 * cart-remove-patches.js
 * --------------------------------------------------------------
 * Handles:
 * 1. Removing single items via .remove-item elements using sendFormData
 * 2. Clearing the entire cart via #clear_cart using sendFormData (DELETE)
 * 3. Updating cart quantities via [type="submit"] / Update Cart buttons using sendFormData
 * --------------------------------------------------------------
 */

/* --------------------------------------------------------------------------
   REMOVE SINGLE ITEM LISTENER
-------------------------------------------------------------------------- */
document.addEventListener('click', async function (evt) {
    const btn = evt.target.closest('.remove-item');
    if (!btn) return;

    evt.preventDefault();

    const productId = btn.dataset.productId;
    if (!productId) {
        console.error('remove-item button missing data-product-id');
        return;
    }

    const cartItemEl = btn.closest('.cart-item');
    const cartToken = typeof getCookie === 'function' ? getCookie('cart_token') : null;
    const removeUrl = window.CART_REMOVE_URL || '/api/cart/remove';

    const formData = new FormData();
    formData.append("product_id", productId);
    if (cartToken) formData.append("cart_token", cartToken);

    let json = null;
    if (typeof sendFormData === 'function') {
        json = await sendFormData(formData, removeUrl, 'DELETE');
    } else {
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) spinner.style.display = 'flex';
        try {
            const response = await fetch(removeUrl, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: formData,
            });
            json = await response.json().catch(() => null);
            if (response.ok && json && typeof syncCartState === 'function') {
                syncCartState(json);
            }
        } catch (err) {
            console.error('Remove item failed:', err);
        } finally {
            const spinner = document.getElementById('loadingSpinner');
            if (spinner) spinner.style.display = 'none';
        }
    }

    if (json) {
        if (cartItemEl) cartItemEl.remove();

        const container = document.getElementById('cart_items_container');
        if (container && container.children.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-cart-x" style="font-size: 2.5rem;"></i>
                    <p class="mt-3 mb-0">Your cart is empty.</p>
                </div>
            `;
        }
    }
});


/* --------------------------------------------------------------------------
   UPDATE CART EVENT LISTENER (Captured for Update Cart form submissions via sendFormData)
-------------------------------------------------------------------------- */
document.addEventListener('click', async function (evt) {
    const updateBtn = evt.target.closest('button[type="submit"], .cart_update[data-info="update"]');
    if (!updateBtn) return;

    const form = updateBtn.closest('form');
    if (!form) return;

    // Ensure we only handle update actions (skip standard checkout forms if separate)
    if (form.id && form.id.includes('checkout')) return;

    evt.preventDefault();
    evt.stopImmediatePropagation();

    const url = form.action || window.CART_UPDATE_URL;
    const method = form.method || 'POST';
    const formData = new FormData(form);

    const cartToken = typeof getCookie === 'function' ? getCookie('cart_token') : null;
    if (cartToken && !formData.has('cart_token')) {
        formData.append('cart_token', cartToken);
    }

    if (typeof sendFormData === 'function') {
        await sendFormData(formData, url, method);
    } else {
        console.error('sendFormData is not defined.');
    }
}, true);


/* --------------------------------------------------------------------------
   CLEAR CART EVENT LISTENER (Captured for #clear_cart & Modal Triggers via sendFormData)
-------------------------------------------------------------------------- */
document.addEventListener('click', async function (evt) {
    const clearBtn = evt.target.closest('#clear_cart, .cart_update[data-info="clear"], #clear_cart_btn');

    if (!clearBtn) return;

    evt.preventDefault();
    evt.stopImmediatePropagation();

    const cartToken = typeof getCookie === 'function' ? getCookie('cart_token') : null;
    if (!cartToken) {
        if (typeof toast === 'function') toast("Your cart is already empty.", "warning");
        return;
    }

    // Optional confirmation using SweetAlert2 if available
    if (typeof Swal === 'function' || window.Swal) {
        const result = await Swal.fire({
            title: 'Clear Cart?',
            text: 'Are you sure you want to remove all items from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear it!'
        });

        if (!result.isConfirmed) return;
    }

    const form = clearBtn.closest('form');
    const clearUrl = clearBtn.dataset.url || (form ? form.action : null) || window.CART_CLEAR_URL || "/api/cart/clear";
    
    const formData = new FormData();
    formData.append("cart_token", cartToken);

    let json = null;
    if (typeof sendFormData === 'function') {
        json = await sendFormData(formData, clearUrl, 'DELETE');
    } else {
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) spinner.style.display = 'flex';
        try {
            const response = await fetch(clearUrl, {
                method: "DELETE",
                body: formData,
                headers: { 
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
                }
            });
            json = await response.json().catch(() => null);
        } catch (error) {
            console.error("Clear cart error:", error);
        } finally {
            const spinner = document.getElementById('loadingSpinner');
            if (spinner) spinner.style.display = 'none';
        }
    }

    if (json) {
        const resetData = {
            count: 0,
            cart_token: json?.cart_token || cartToken,
            data: [],
            subtotal: 0,
            tax: 0,
            discount: 0,
            total: 0
        };

        if (typeof syncCartState === 'function') {
            syncCartState(resetData);
        }
        
        localStorage.setItem('cart_count', 0);

        const container = document.getElementById('cart_items_container');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-cart-x" style="font-size: 2.5rem;"></i>
                    <p class="mt-3 mb-0">Your cart is empty.</p>
                </div>
            `;
        }
    }
}, true);