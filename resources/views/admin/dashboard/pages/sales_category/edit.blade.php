@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Sales Category</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Sales Category</a></li>
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
                <h4 class="card-title">Edit Sales Category</h4>
                <p class="card-title-desc">Update the category details.</p>

                <form action="{{ route('admin.sales.category.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Category Name --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Category Name</label>
                        <div class="col-sm-10">
                            <input class="form-control" 
                                type="text" 
                                name="category_name" 
                                value="{{ old('category_name', $category->category_name) }}" 
                                placeholder="Enter Sales Category Name">
                            @error('category_name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" 
                                name="description" 
                                rows="4" 
                                placeholder="Enter Category Description">{{ old('description', $category->description) }}</textarea>
                            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            @php $active = old('is_active', $category->is_active); @endphp

                            <input type="checkbox" id="status-switch" switch="primary" 
                                {{ $active ? 'checked' : '' }}>
                            <label for="status-switch" data-on-label="Active" data-off-label="Inactive"></label>

                            <input type="hidden" name="is_active" id="status-value" value="{{ $active }}">
                        </div>
                    </div>

                    {{-- Priority --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Priority</label>
                        <div class="col-sm-10">
                            <input class="form-control" 
                                type="number" 
                                name="priority" 
                                value="{{ old('priority', $category->priority) }}" 
                                placeholder="Priority Level (e.g. 1)">
                            @error('priority') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="row mb-3">
                        <div class="col-sm-10 offset-sm-2">
                            <button class="btn btn-success" type="submit">Update Category</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('status-switch').addEventListener('change', function () {
    document.getElementById('status-value').value = this.checked ? 1 : 0;
});
</script>

@endsection
