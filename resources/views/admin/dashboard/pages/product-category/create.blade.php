@extends('admin.dashboard.home')

@section('admin-dashboard-content')
    <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Product Category</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Product Category</a></li>
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

                <h4 class="card-title">Create Product Category</h4>
                <p class="card-title-desc">Fill the form to create a new product category.</p>

                <form action="{{ route('admin.product-category.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="name" value="{{ old('name') }}" placeholder="Category Name">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="description" rows="4" placeholder="Category Description">{{ old('description') }}</textarea>
                            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Excerpt</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="excerpt" value="{{ old('excerpt') }}" placeholder="Short Description">
                            @error('excerpt') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-10 d-flex align-items-center">

                        <input type="checkbox" id="status-switch" switch="primary" checked />
                        <label for="status-switch" data-on-label="Active" data-off-label="Inactive"></label>

                        <!-- Hidden field to actually submit value -->
                        <input type="hidden" name="status" id="status-value" value="active">
                    </div>
                </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Priority</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="number" name="priority" value="{{ old('priority') }}" placeholder="Priority Number">
                            @error('priority') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Image</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="file" name="image">
                            @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-10 offset-sm-2">
                            <button class="btn btn-primary" type="submit">Create Category</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>


@endsection
