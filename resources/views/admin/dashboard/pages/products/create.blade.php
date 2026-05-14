@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Create Product</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Products</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
<div class="col-12">
    <div class="card">
        <div class="card-body">

            <h4 class="card-title">Create Product</h4>
            <p class="card-title-desc">Fill the form to create a product.</p>

            <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <div class="col-lg-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input class="form-control" type="text" name="name" value="{{ old('name') }}" placeholder="Enter Product Name">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-lg-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input class="form-control" type="number" step="0.01" name="price"
                                   value="{{ old('price') }}" placeholder="Product Price">
                            @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-lg-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-control" name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Sales Category</label>
                            <select class="form-control" name="sales_category_models_id">
                                <option value="">Select Sales Category</option>
                                @foreach($sales_category as $cat)
                                    <option value="{{ $cat->id }}" {{ old('sales_category_models_id') == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                            @error('sales_category_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-lg-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Available Stock</label>
                            <input class="form-control" type="number" name="stock" value="{{ old('stock') }}" placeholder="Enter Stock Quantity">
                            @error('stock') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Dynamic Image Inputs --}}
                    <div class="col-lg-6 col-12">
                        <label class="form-label">Product Images</label>

                        <div id="image-wrapper">
                            <div class="input-group mb-2">
                                <input type="file" name="images[]" class="form-control" accept="image/*" required>
                                <button type="button" class="btn btn-success add-image">+</button>
                            </div>
                        </div>

                        @error('images') <span class="text-danger">{{ $message }}</span> @enderror
                        @error('images.*') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Product Description">{{ old('description') }}</textarea>
                            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                </div>

                <button class="btn btn-primary" type="submit">Create Product</button>

            </form>

        </div>
    </div>
</div>
</div>

{{-- Dynamic Image Script --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const wrapper = document.getElementById('image-wrapper');

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('add-image')) {
            let field = `
                <div class="input-group mb-2">
                    <input type="file" name="images[]" class="form-control" accept="image/*" required>
                    <button type="button" class="btn btn-danger remove-image">−</button>
                </div>`;
            wrapper.insertAdjacentHTML('beforeend', field);
        }

        if (e.target.classList.contains('remove-image')) {
            e.target.closest('.input-group').remove();
        }
    });
});
</script>

@endsection
