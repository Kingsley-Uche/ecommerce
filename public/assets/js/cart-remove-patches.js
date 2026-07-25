/**
 * cart-remove-patches.js
 * --------------------------------------------------------------
 * Centralized Event Dispatcher & Handlers for Cart Actions:
 * 1. Removing single items (.remove-item)
 * 2. Updating quantities (Form submit / buttons)
 * 3. Clearing the entire cart (#clear_cart, .cart_update[data-info="clear"])
 * --------------------------------------------------------------
 */

document.addEventListener('click', async function (evt) {
    // 1. Handle Single Item Removal
    const removeBtn = evt.target.closest('.remove-item');
    if (removeBtn) {
        evt.preventDefault();
        if (typeof Swal === 'function' || window.Swal) {
        const result = await Swal.fire({
            title: 'Remove Item?',
            text: 'Are you sure you want to remove  item from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#151718',
            confirmButtonText: 'Yes, remove it!'
        });
        if (!result.isConfirmed) return;
    } else {
        if (!confirm('Are you sure you want to remove?')) return;
    }
        await handleRemoveItem(removeBtn);
        return;
    }

    // 2. Handle Cart Quantity Update
    const updateBtn = evt.target.closest('button[type="submit"], .cart_update[data-info="update"]');
    if (updateBtn) {
        const form = updateBtn.closest('form');
        if (form && !form.id?.includes('checkout')) {
            evt.preventDefault();
            evt.stopImmediatePropagation();
            await handleUpdateCart(form);
            return;
        }
    }

    // 3. Handle Clear Cart Action
    const clearBtn = evt.target.closest('#clear_cart, .cart_update[data-info="clear"], #clear_cart_btn');
    if (clearBtn) {
        evt.preventDefault();
        evt.stopImmediatePropagation();
        await handleClearCart(clearBtn);
        return;
    }
}, true);


/* --------------------------------------------------------------------------
   HANDLER METHODS
-------------------------------------------------------------------------- */
async function handleRemoveItem(btn) {
    const productId = btn.dataset.productId;
    if (!productId) {
        console.error('remove-item button missing data-product-id');
        return;
    }

    const cartItemEl = btn.closest('.cart-item');
    const cartToken = typeof getStoredValue === 'function' ? getStoredValue('cart_token') : null;
    const removeUrl = window.CART_REMOVE_URL || '/api/cart/remove';

    const formData = new FormData();
    formData.append("product_id", productId);
    
    // Optional: If your backend route expects a DELETE method spoof
    formData.append("_method", "POST"); 

    if (cartToken) formData.append("cart_token", cartToken);

    if (typeof sendFormData === 'function') {
        // Send as POST so the FormData body isn't dropped by the browser
        const json = await sendFormData(formData, removeUrl, 'POST');
        if (json) {
            if (cartItemEl) cartItemEl.remove();
            checkEmptyCartContainer();
        }
    }
}

async function handleUpdateCart(form) {
    const url = form.action || window.CART_UPDATE_URL;
    const method = form.method || 'POST';
    const formData = new FormData(form);
    console.log(form);


    const cartToken = typeof getStoredValue === 'function' ? getStoredValue('cart_token') : null;
    if (cartToken && !formData.has('cart_token')) {
        formData.append('cart_token', cartToken);
    }

    if (typeof sendFormData === 'function') {
        await sendFormData(formData, url, method);
    } else {
        console.error('sendFormData is not defined.');
    }
}

async function handleClearCart(clearBtn) {
    const cartToken = typeof getStoredValue === 'function' ? getStoredValue('cart_token') : null;
    if (!cartToken) {
        if (typeof toast === 'function') toast("Your cart is already empty.", "warning");
        return;
    }

    // Optional SweetAlert2 confirmation
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
    } else {
        if (!confirm('Are you sure you want to clear your cart?')) return;
    }

    const form = clearBtn.closest('form');
    const clearUrl = clearBtn.dataset.url || window.CART_CART_CLEAR_URL || window.CART_CLEAR_URL || "/api/cart/clear";
    const formData = new FormData();
    formData.append("cart_token", cartToken);

    // Track loading state on button if desired
    const originalText = clearBtn.innerHTML;
    clearBtn.disabled = true;
    clearBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Clearing...';

    const spinner = document.getElementById('loadingSpinner');
    if (spinner) spinner.style.display = 'flex';

    try {
        let json = null;
        if (typeof sendFormData === 'function') {
            json = await sendFormData(formData, clearUrl, 'DELETE');
        } else {
            // Fallback fetch if sendFormData is unavailable
            const response = await fetch(clearUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ cart_token: cartToken })
            });
            json = await response.json();
        }

        if (json && (json.status === 'success' || json.success)) {
            // Clear client cookies / storage
            document.cookie = "cart_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            localStorage.setItem('cart_count', 0);

            const resetData = {
                count: json.count ?? 0,
                cart_token: null,
                data: [],
                subtotal: 0,
                tax: 0,
                discount: 0,
                total: 0
            };

            if (typeof syncCartState === 'function') {
                syncCartState(resetData);
            }

            // Reset UI fields directly
            updateCartUIOnClear(json.count ?? 0);
        } else {
            alert(json?.message || 'Failed to clear cart.');
        }
    } catch (error) {
        console.error('Clear cart error:', error);
        alert('An unexpected error occurred.');
    } finally {
        if (spinner) spinner.style.display = 'none';
        clearBtn.disabled = false;
        clearBtn.innerHTML = originalText;
    }
}


/* --------------------------------------------------------------------------
   UI HELPER METHODS
-------------------------------------------------------------------------- */

function updateCartUIOnClear(count = 0) {
    const container = document.getElementById('cart_items_container');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="bi bi-cart-x" style="font-size: 2.5rem;"></i>
                <p class="mt-3 mb-0">Your cart is empty.</p>
            </div>
        `;
    }

    const subtotalEl = document.getElementById('cart_subtotal');
    const totalEl = document.getElementById('cart_total');
    if (subtotalEl) subtotalEl.innerText = '₦0.00';
    if (totalEl) totalEl.innerText = '₦0.00';

    const cartBadge = document.querySelector('.cart-badge-count');
    if (cartBadge) cartBadge.innerText = count;
}

function checkEmptyCartContainer() {
    const container = document.getElementById('cart_items_container');
    if (container && container.querySelectorAll('.cart-item').length === 0) {
        updateCartUIOnClear(0);
    }
}