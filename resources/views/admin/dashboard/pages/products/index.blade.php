@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Products</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="#">Products</a></li>
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

        <h4 class="card-title">Products</h4>
        <p class="card-title-desc">Manage products, edit or delete any record.</p>

        <table class="table table-striped table-bordered dt-responsive nowrap" width="100%">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Sales Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        @php
                            $img = $product->images->first()->image_path ?? null;
                        @endphp

                        @if($img)
                            <img src="{{ asset('storage/'.$img) }}" width="60" height="60" class="rounded">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td>

                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ $product->sales_category->category_name ?? '-' }}</td>
                    <td>₦{{ number_format($product->price,2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->created_at->format('d M, Y') }}</td>

                    <td>
                        <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <form action="{{ route('admin.product.destroy', $product->id) }}" 
                              method="POST" style="display:inline-block;"
                              onsubmit="return confirm('Delete product?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- PAGINATION --}}
        <div class="mt-3">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>
</div>
</div>

@endsection

