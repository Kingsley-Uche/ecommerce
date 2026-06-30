/**
 * cart-remove-patches.js
 * --------------------------------------------------------------
 * main.js's ecommerceCartTools() binds .remove-item click handlers
 * that only do `btn.closest('.cart-item').remove()` — a DOM-only
 * removal with no call to api.cart.remove. The item stays in the
 * database; it just disappears from the screen until next reload.
 *
 * This adds a real API call before removing the row, for both the
 * server-rendered cart partial (cart.blade.php) and the JS-rendered
 * modal content (CheckoutContent() in inner.js) — both use the same
 * .remove-item class.
 *
 * Load this AFTER main.js (so .remove-item nodes that existed at
 * page-load are already there) and it also re-applies on dynamically
 * inserted nodes via event delegation, so it keeps working after
 * inner.js rebuilds #cart_items_container.
 * --------------------------------------------------------------
 */
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
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) spinner.style.display = 'flex';

    // window.CART_REMOVE_URL must be defined in a <script> block before this
    // file loads — see the snippet to add near CART_ADD_URL in the footer.
    const removeUrl = window.CART_REMOVE_URL || '/api/cart/remove';

    try {
        const response = await fetch(removeUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({ product_id: productId }),
        });

        const json = await response.json().catch(() => null);

        if (!response.ok) {
            toast(json?.message || 'Could not remove item.', 'error');
            return;
        }

        if (cartItemEl) cartItemEl.remove();

        if (typeof window.updateCartBadge === 'function' && json.count !== undefined) {
            window.updateCartBadge(json.count);
        }

        const itemNumberEl = document.getElementById('item_number');
        if (itemNumberEl && json.count !== undefined) {
            itemNumberEl.innerHTML = json.count;
        }

        toast(json.message || 'Item removed.', json.status || 'success');

        // If the cart is now empty, show the empty state instead of a blank container
        const container = document.getElementById('cart_items_container');
        if (container && container.children.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-cart-x" style="font-size: 2.5rem;"></i>
                    <p class="mt-3 mb-0">Your cart is empty.</p>
                </div>
            `;
        }
    } catch (err) {
        console.error('Remove item failed:', err);
        toast('Something went wrong removing this item.', 'error');
    } finally {
        if (spinner) spinner.style.display = 'none';
    }
});
