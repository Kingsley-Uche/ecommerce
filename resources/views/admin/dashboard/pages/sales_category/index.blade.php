@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Sales Categories</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Sales Categories</a></li>
                    <li class="breadcrumb-item active">View all</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Sales Categories</h4>
                <p class="card-title-desc">
                    Manage sales categories, edit or delete any record.
                </p>

                <a href="{{ route('admin.sales.category.create') }}" class="btn btn-primary mb-3">
                    + Add New Sales Category
                </a>

                <table id="datatable-buttons" 
                       class="table table-striped table-bordered dt-responsive nowrap" 
                       style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->category_name }}</td>
                            <td>{{ $category->description }}</td>
                            <td>
                                @if($category->is_active == true)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $category->priority ?? '-' }}</td>

                            <td>
                                @if($category->image_path)
                                    <img src="{{ asset('storage/'.$category->image_path) }}" 
                                        width="50" height="50" class="rounded">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('admin.sales.category.edit', $category->id) }}" 
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.sales.category.destroy', $category->id) }}" 
                                      method="POST" 
                                      style="display:inline-block;"
                                      onsubmit="return confirm('Are you sure you want to delete this sales category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>
    </div>
</div>

@endsection
