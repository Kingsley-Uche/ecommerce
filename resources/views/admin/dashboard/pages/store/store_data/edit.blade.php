@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Store Details</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.store_details.index') }}">Store Info</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@php
    $social_icons = $store->social_icons ?? [];
    $social_links = $store->social_links ?? [];
@endphp

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.store_details.update', $store->id) }}" 
                      method="POST" 
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        {{-- Store Name --}}
                        <div class="col-lg-6">
                            <label class="form-label">Store Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="store_name" 
                                   class="form-control" 
                                   value="{{ old('store_name', $store->store_name) }}" 
                                   required>
                        </div>

                        {{-- Email --}}
                        <div class="col-lg-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control"
                                   value="{{ old('email', $store->email) }}" 
                                   required>
                        </div>

                        {{-- Phone --}}
                        <div class="col-lg-6">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="phone" 
                                   class="form-control"
                                   value="{{ old('phone', $store->phone) }}" 
                                   required>
                        </div>

                        {{-- Tagline --}}
                        <div class="col-lg-6">
                            <label class="form-label">Tagline</label>
                            <input type="text" 
                                   name="tagline" 
                                   class="form-control"
                                   value="{{ old('tagline', $store->tagline) }}">
                        </div>

                        {{-- Address --}}
                        <div class="col-12">
                            <label class="form-label">Store Address <span class="text-danger">*</span></label>
                            <textarea name="address" 
                                      rows="3" 
                                      class="form-control"
                                      required>{{ old('address', $store->address) }}</textarea>
                        </div>

                        {{-- Logo --}}
                        <div class="col-lg-6">
                            <label class="form-label">Store Logo</label>
                            <input type="file" 
                                   name="logo" 
                                   class="form-control"
                                   accept="image/*">

                            @if($store->logo_path)
                                <div class="mt-2">
                                    <p class="text-muted mb-1">Current Logo:</p>
                                    <img src="{{ asset('storage/' . $store->logo_path) }}"
                                         class="rounded shadow-sm"
                                         width="120">
                                </div>
                            @endif
                        </div>

                        {{-- Social Media --}}
                        <div class="col-12 mt-4">
                            <label class="form-label d-block">
                                Social Media Links
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success ms-2" 
                                        id="add-social">
                                    Add Link
                                </button>
                            </label>

                            <div id="social-fields">
                                @php
                                    $socialCount = max(
                                        count(old('social_links', [])),
                                        count($social_links),
                                        1
                                    );
                                @endphp

                                @for($i = 0; $i < $socialCount; $i++)
                                <div class="input-group mb-2 social-row">
                                    <input type="text" 
                                           name="social_icons[]" 
                                           class="form-control"
                                           placeholder="Icon (e.g Facebook)"
                                           value="{{ old("social_icons.$i", $social_icons[$i] ?? '') }}">

                                    <input type="url" 
                                           name="social_links[]" 
                                           class="form-control"
                                           placeholder="https://facebook.com/page"
                                           value="{{ old("social_links.$i", $social_links[$i] ?? '') }}">

                                    <button type="button" class="btn btn-danger remove-social">Remove</button>
                                </div>
                                @endfor
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="col-12 text-end mt-3">
                            <a href="{{ route('admin.store_details.index') }}" 
                               class="btn btn-secondary me-2">Cancel</a>

                            <button type="submit" 
                                    class="btn btn-success">
                                Update Store
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Add social row
    document.getElementById('add-social')?.addEventListener('click', () => {
        document.getElementById('social-fields').insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2 social-row">
                <input type="text" name="social_icons[]" class="form-control" placeholder="Icon">
                <input type="url" name="social_links[]" class="form-control" placeholder="https://example.com/">
                <button type="button" class="btn btn-danger remove-social">Remove</button>
            </div>
        `);
    });

    // Remove row
    document.addEventListener('click', e => {
        if (e.target.classList.contains('remove-social')) {
            e.target.closest('.social-row').remove();
        }
    });
});
</script>
