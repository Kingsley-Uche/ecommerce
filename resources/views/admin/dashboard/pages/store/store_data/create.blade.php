{{-- resources/views/admin/dashboard/pages/store/create.blade.php --}}
@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Create Store Profile</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.store_details.index') }}">Store</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <form action="{{ route('admin.store_details.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        <!-- Store Info -->
                        <div class="col-lg-6">
                            <label class="form-label">Store Name <span class="text-danger">*</span></label>
                            <input type="text" name="store_name" class="form-control" value="{{ old('store_name') }}" placeholder="e.g. TechMart" required>
                            @error('store_name') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="contact@techmart.com" required>
                            @error('email') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08012345678" required>
                            @error('phone') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="tagline" class="form-control" value="{{ old('tagline') }}" placeholder="Best deals in town">
                            @error('tagline') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Store Address <span class="text-danger">*</span></label>
                            <textarea name="address" rows="3" class="form-control" required>{{ old('address') }}</textarea>
                            @error('address') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Store Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <small class="text-muted">Max 2MB. PNG, JPG, or SVG</small>
                            @error('logo') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <!-- Social Links -->
                        <div class="col-12">
                            <hr class="my-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Social Media Links</h5>
                                <button type="button" class="btn btn-sm btn-outline-success add-social">Add Link</button>
                            </div>

                            <div id="social-fields">
                                @if(old('social_links'))
                                    @foreach(old('social_links') as $i => $link)
                                        <div class="input-group mb-2 social-row">
                                            <input type="text" name="social_icons[]" class="form-control" value="{{ old("social_icons.$i") }}" placeholder="Facebook" style="max-width: 140px;">
                                            <input type="url" name="social_links[]" class="form-control" value="{{ $link }}" placeholder="https://facebook.com/yourpage">
                                            <button type="button" class="btn btn-danger remove-social">Remove</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 social-row">
                                        <input type="text" name="social_icons[]" class="form-control" placeholder="Facebook" style="max-width: 140px;">
                                        <input type="url" name="social_links[]" class="form-control" placeholder="https://facebook.com/yourpage">
                                        <button type="button" class="btn btn-danger remove-social">Remove</button>
                                    </div>
                                @endif
                            </div>
                            @error('social_links.*') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Featured Products -->
                        <div class="col-12">
                            <hr class="my-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Front Page Products</h5>
                                <button type="button" class="btn btn-sm btn-primary add-product">Add Product</button>
                            </div>

                            <div id="product-fields">
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
                                                <div class="col-md-3">
                                                    <label class="form-label">Stock</label>
                                                    <input type="number" name="product_stock[]" class="form-control" placeholder="0" step="1" min="0">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Short Description</label>
                                                    <textarea name="product_descriptions[]" rows="2" class="form-control" placeholder="Brief description..."></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 text-end mt-3">
                                            <button type="button" class="btn btn-danger remove-product">Remove Product</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('product_images.*') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <!-- Submit -->
                        <div class="col-12 text-end mt-4">
                            <a href="{{ route('admin.store_details.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-success px-5">Create Store</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection

<!-- JAVASCRIPT -->