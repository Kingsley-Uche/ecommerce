@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Product Category</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Product Category</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
<div class="col-12">
<div class="card">
<div class="card-body">

<h4 class="card-title">Edit Product Category</h4>
<p class="card-title-desc">Update product category details.</p>

<form action="{{ route('admin.product-category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
            <input class="form-control" type="text" name="name" 
                   value="{{ old('name', $category->name) }}" placeholder="Category Name">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
            <textarea class="form-control" name="description" rows="4"
                      placeholder="Category Description">{{ old('description', $category->description) }}</textarea>
            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Excerpt</label>
        <div class="col-sm-10">
            <input class="form-control" type="text" name="excerpt" 
                   value="{{ old('excerpt', $category->excerpt) }}" placeholder="Short Description">
            @error('excerpt') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- Status Switch --}}
    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Status</label>
        <div class="col-sm-10 d-flex align-items-center">

            <input type="checkbox" id="status-switch" switch="primary" 
                   {{ $category->status == 'active' ? 'checked' : '' }} />
            <label for="status-switch" data-on-label="Active" data-off-label="Inactive"></label>

            <input type="hidden" name="status" id="status-value" value="{{ $category->status }}">
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Priority</label>
        <div class="col-sm-10">
            <input class="form-control" type="number" name="priority" 
                   value="{{ old('priority', $category->priority) }}" placeholder="Priority Number">
            @error('priority') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- Image Preview --}}
    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Current Image</label>
        <div class="col-sm-10">
            @if($category->image_path)
                <img src="{{ asset('storage/'.$category->image_path) }}" width="90" class="mb-2 rounded border">
            @else
                <p class="text-muted">No image uploaded</p>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Change Image</label>
        <div class="col-sm-10">
            <input class="form-control" type="file" name="image">
            @error('image') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-10 offset-sm-2">
            <button class="btn btn-primary" type="submit">Update Category</button>
        </div>
    </div>

</form>

</div>
</div>
</div>
</div>

@endsection

{{-- JS for switch to update hidden field --}}
<script>
document.querySelector('#status-switch').addEventListener('change', function () {
    document.querySelector('#status-value').value = this.checked ? 'active' : 'inactive';
});
</script>
