<script>
document.addEventListener('DOMContentLoaded', function () {
    const socialContainer = document.getElementById('social-fields');
    const productContainer = document.getElementById('product-fields');

    // Add Social Link
    document.querySelector('.add-social')?.addEventListener('click', () => {
        socialContainer.insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2 social-row">
                <input type="text" name="social_icons[]" class="form-control" placeholder="Instagram" style="max-width: 140px;">
                <input type="url" name="social_links[]" class="form-control" placeholder="https://instagram.com/...">
                <button type="button" class="btn btn-danger remove-social">Remove</button>
            </div>
        `);
    });

    // Add Product
    document.querySelector('.add-product')?.addEventListener('click', () => {
        productContainer.insertAdjacentHTML('beforeend', `
            <div class="border rounded p-4 mb-3 product-row bg-light">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="product_images[]" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-9">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Name</label>
                                <input type="text" name="product_names[]" class="form-control" placeholder="Product Name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Price (₦)</label>
                                <input type="number" name="product_prices[]" class="form-control" placeholder="0.00" step="0.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Short Description</label>
                                <textarea name="product_descriptions[]" rows="2" class="form-control" placeholder="Brief description..."></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Stock</label>
                                <input type="number" name="product_stock[]" class="form-control" placeholder="0" step="1" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-end mt-3">
                        <button type="button" class="btn btn-danger remove-product">Remove Product</button>
                    </div>
                </div>
            </div>
        `);
    });

    // Remove Dynamic Rows
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-social')) e.target.closest('.social-row').remove();
        if (e.target.classList.contains('remove-product')) e.target.closest('.product-row').remove();
    });
});
</script>