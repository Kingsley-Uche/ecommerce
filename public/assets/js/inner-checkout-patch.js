/**
 * inner-checkout-patch.js
 * ---------------------------------------------------------------
 * Replaces CheckoutContent() from inner.js with a version that
 * matches cart_items.blade.php exactly:
 *
 *   Product col-4 | Size col-2 | Price col-2 | Qty col-2 | Total col-1 | Remove col-1
 *
 * Also reads item.size from the API response so the size dropdown
 * is pre-selected to whatever is stored in the cart row.
 *
 * Load this AFTER inner.js — it overwrites the original function.
 * ---------------------------------------------------------------
 */
async function CheckoutContent(data) {
    const container = document.getElementById('cart_items_container');
    if (!container) return;

    container.innerHTML = '';

    if (!data || data.length === 0) {
        container.innerHTML =
            '<div class="text-center py-5 text-muted">' +
                '<i class="bi bi-cart-x" style="font-size:2.5rem;"></i>' +
                '<p class="mt-3 mb-0">Your cart is empty.</p>' +
            '</div>';
        updateCartSummary({ subtotal: 0, tax: 0, discount: 0, total: 0 });
        return;
    }

    const SIZES = ['', 'XS', 'S', 'M', 'L', 'XL', 'XXL'];

    let subtotal = 0;

    data.forEach(function (item) {
        const p            = item.product;
        const price        = parseFloat(p.price  ?? 0);
        const qty          = parseInt(item.quantity ?? 1, 10);
        const itemTotal    = price * qty;
        const selectedSize = item.size ?? '';
        subtotal          += itemTotal;

        const image = (p.images && p.images.length)
            ? '/storage/' + p.images[0].image_path
            : '/assets/img/default-product.png';

        /* Build size <option> list — pre-select whatever is stored on the cart row */
        const sizeOptions = SIZES.map(function (s) {
            const label    = s === '' ? '&mdash; Size &mdash;' : s;
            const selected = selectedSize === s ? ' selected' : '';
            return '<option value="' + s + '"' + selected + '>' + label + '</option>';
        }).join('');

        /* Format numbers as Nigerian naira with 2 dp */
        function fmt(n) {
            return '&#x20A6;' + n.toLocaleString('en-NG', { minimumFractionDigits: 2 });
        }

        const html =
            /* cart-item wrapper */
            '<div class="cart-item border-bottom py-3" data-product-id="' + p.id + '">' +
              '<div class="row align-items-center g-1">' +

                /* Product — col-4 */
                '<div class="col-lg-4 col-12 mb-2 mb-lg-0">' +
                  '<div class="product-info d-flex align-items-center gap-2">' +
                    '<img src="' + image + '"' +
                         ' alt="' + (p.name || 'Product') + '"' +
                         ' width="60"' +
                         ' class="img-fluid rounded flex-shrink-0"' +
                         ' style="object-fit:contain;height:60px;">' +
                    '<div>' +
                      '<h6 class="mb-0 small fw-semibold">' + (p.name || 'Unknown product') + '</h6>' +
                      (p.description
                          ? '<small class="text-muted">' + p.description.substring(0, 50) + '</small>'
                          : '') +
                    '</div>' +
                  '</div>' +
                '</div>' +

                /* Size — col-2 */
                '<div class="col-lg-2 col-4 text-center mb-2 mb-lg-0">' +
                  '<label class="form-label small text-muted d-block d-lg-none mb-0">Size</label>' +
                  '<select name="size[]"' +
                          ' class="form-select form-select-sm size-selector"' +
                          ' data-product-id="' + p.id + '"' +
                          ' data-cart-item-id="' + (item.id || '') + '">' +
                    sizeOptions +
                  '</select>' +
                '</div>' +

                /* Price — col-2 */
                '<div class="col-lg-2 col-4 text-center mb-2 mb-lg-0">' +
                  '<label class="form-label small text-muted d-block d-lg-none mb-0">Price</label>' +
                  '<span class="current-price"><strong>' + fmt(price) + '</strong></span>' +
                '</div>' +

                /* Quantity — col-2 */
                '<div class="col-lg-2 col-4 text-center mb-2 mb-lg-0">' +
                  '<label class="form-label small text-muted d-block d-lg-none mb-0">Qty</label>' +
                  '<div class="quantity-selector d-flex align-items-center justify-content-center gap-1">' +
                    '<button type="button"' +
                            ' class="quantity-btn decrease btn btn-sm btn-outline-dark"' +
                            ' data-price="' + price + '">' +
                      '<i class="bi bi-dash"></i>' +
                    '</button>' +
                    '<input type="number"' +
                           ' class="quantity-input form-control form-control-sm text-center"' +
                           ' value="' + qty + '"' +
                           ' min="1"' +
                           ' name="quantity[]"' +
                           ' style="width:52px;">' +
                    '<input type="hidden" name="initial_quantity[]" value="' + qty + '">' +
                    '<input type="hidden" name="product_id[]"       value="' + p.id + '">' +
                    '<input type="hidden" name="total"              value="' + itemTotal.toFixed(2) + '">' +
                    '<button type="button"' +
                            ' class="quantity-btn increase btn btn-sm btn-outline-dark"' +
                            ' data-price="' + price + '">' +
                      '<i class="bi bi-plus"></i>' +
                    '</button>' +
                  '</div>' +
                '</div>' +

                /* Line total — col-1 */
                '<div class="col-lg-1 col-2 text-center item-total">' +
                  '<label class="form-label small text-muted d-block d-lg-none mb-0">Total</label>' +
                  '<strong>' + fmt(itemTotal) + '</strong>' +
                '</div>' +

                /* Remove — col-1 */
                '<div class="col-lg-1 col-2 text-center">' +
                  '<button type="button"' +
                          ' class="remove-item btn btn-sm btn-link text-danger p-0 mt-lg-0 mt-2"' +
                          ' data-product-id="' + p.id + '"' +
                          ' aria-label="Remove ' + (p.name || 'item') + '">' +
                    '<i class="bi bi-trash"></i>' +
                  '</button>' +
                '</div>' +

              '</div>' +
            '</div>';

        container.insertAdjacentHTML('beforeend', html);
    });

    /* Re-attach quantity +/- listeners to the newly inserted nodes */
    attachQuantityListeners();

    /* Update the order summary counters */
    updateCartSummary({
        subtotal: subtotal,
        tax:      subtotal * 0.1,
        discount: 0,
        total:    subtotal * 1.1,
    });
}