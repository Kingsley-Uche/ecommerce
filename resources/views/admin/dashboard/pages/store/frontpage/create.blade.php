{{-- resources/views/admin/dashboard/pages/store/create.blade.php --}}
@extends('admin.dashboard.home')

@section('admin-dashboard-content')
<!-- Featured Products -->
<div class="col-12 card" >
    <hr class="my-4">

    <div class="d-flex justify-content-between align-items-center mb-3 p-2">
        <h5 class="mb-0 m-2">Front Page Products</h5>
        <button type="button" class="btn btn-sm btn-success add-product">Add Product</button>
    </div>

    <div id="product-fields" class="card-body">

        @php
            $oldProducts = old('product_names', []);
        @endphp

        {{-- If old data exists --}}
        @if(count($oldProducts) > 0)

            @foreach($oldProducts as $i => $product)
                <div class="border rounded p-4 mb-3 product-row bg-white">
                    <div class="row g-3">

                        <div class="col-md-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="product_images[]" class="form-control" accept="image/*">
                        </div>

                        <div class="col-md-9">
                            <div class="row g-3">

                                <div class="col-md-5">
                                    <label class="form-label">Name</label>
                                    <input type="text"
                                           name="product_names[]"
                                           class="form-control"
                                           value="{{ old('product_names.'.$i) }}"
                                           placeholder="Product Name">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Price (₦)</label>
                                    <input type="number"
                                           name="product_prices[]"
                                           class="form-control"
                                           value="{{ old('product_prices.'.$i) }}"
                                           placeholder="0.00"
                                           step="0.01">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number"
                                           name="product_stock[]"
                                           class="form-control"
                                           value="{{ old('product_stock.'.$i) }}"
                                           placeholder="0"
                                           min="0"
                                           step="1">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Short Description</label>
                                    <textarea name="product_descriptions[]"
                                              rows="2"
                                              class="form-control"
                                              placeholder="Brief description...">{{ old('product_descriptions.'.$i) }}</textarea>
                                </div>

                            </div>
                        </div>

                        <div class="col-12 text-end mt-3">
                            <button type="button" class="btn btn-danger remove-product">Remove Product</button>
                        </div>

                    </div>
                </div>
            @endforeach

        @else

            {{-- Default product row --}}
            <div class="border rounded p-4 mb-3 product-row bg-white">
                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="product_images[]" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3">

                            <div class="col-md-5">
                                <label class="form-label">Name</label>
                                <input type="text"
                                       name="product_names[]"
                                       class="form-control"
                                       placeholder="Product Name">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Price (₦)</label>
                                <input type="number"
                                       name="product_prices[]"
                                       class="form-control"
                                       placeholder="0.00"
                                       step="0.01">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Stock</label>
                                <input type="number"
                                       name="product_stock[]"
                                       class="form-control"
                                       placeholder="0"
                                       min="0"
                                       step="1">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Short Description</label>
                                <textarea name="product_descriptions[]"
                                          rows="2"
                                          class="form-control"
                                          placeholder="Brief description..."></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="col-12 text-end mt-3">
                        <button type="button" class="btn btn-danger remove-product">Remove Product</button>
                    </div>

                </div>
            </div>

        @endif
    </div>

    {{-- Validation Errors --}}
    @error('product_images.*') <small class="text-danger d-block">{{ $message }}</small> @enderror
    @error('product_names.*') <small class="text-danger d-block">{{ $message }}</small> @enderror
    @error('product_prices.*') <small class="text-danger d-block">{{ $message }}</small> @enderror
    @error('product_stock.*') <small class="text-danger d-block">{{ $message }}</small> @enderror
    @error('product_descriptions.*') <small class="text-danger d-block">{{ $message }}</small> @enderror

</div>


<script>
document.addEventListener("DOMContentLoaded", function () {

    // ADD PRODUCT ROW
    document.querySelector(".add-product").addEventListener("click", function () {
        let container = document.getElementById("product-fields");

        let html = `
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
                            <input type="text"
                                   name="product_names[]"
                                   class="form-control"
                                   placeholder="Product Name">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Price (₦)</label>
                            <input type="number"
                                   name="product_prices[]"
                                   class="form-control"
                                   placeholder="0.00"
                                   step="0.01">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Stock</label>
                            <input type="number"
                                   name="product_stock[]"
                                   class="form-control"
                                   placeholder="0"
                                   min="0"
                                   step="1">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Short Description</label>
                            <textarea name="product_descriptions[]"
                                      rows="2"
                                      class="form-control"
                                      placeholder="Brief description..."></textarea>
                        </div>

                    </div>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="button" class="btn btn-danger remove-product">Remove Product</button>
                </div>

            </div>
        </div>
        `;

        container.insertAdjacentHTML("beforeend", html);
    });

    // REMOVE PRODUCT ROW
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("remove-product")) {
            event.target.closest(".product-row").remove();
        }
    });

});
</script>
@endsection
