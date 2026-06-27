@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Products</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.product.index') }}">Products</a>
                    </li>
                    <li class="breadcrumb-item active">View All</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">

        <div class="card">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Products</h4>
                        <p class="card-title-desc mb-0">
                            Manage products, edit or delete any record.
                        </p>
                    </div>

                    <div>
                        <a href="{{ route('admin.product.create.load') }}"
                           class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> Create Product
                        </a>
                    </div>
                </div>

                <table class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Categories</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Sold</th>
                            <th>Created</th>
                            <th width="170">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($products as $product)

                            <tr>

                                <td class="align-middle">

                                    @php
                                        $img = optional($product->images->first())->image_path;
                                    @endphp

                                    @if($img)
                                        <img src="{{ asset('storage/'.$img) }}"
                                             width="60"
                                             height="60"
                                             class="rounded border"
                                             alt="{{ $product->name }}">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif

                                </td>

                                <td class="align-middle">
                                    {{ $product->name }}
                                </td>

                                <td class="align-middle">

                                    @forelse($product->categories as $category)

                                        <span class="badge bg-primary me-1 mb-1">
                                            {{ $category->name }}
                                        </span>

                                    @empty

                                        <span class="text-muted">
                                            No Category
                                        </span>

                                    @endforelse

                                </td>

                                <td class="align-middle">
                                    ₦{{ number_format($product->price, 2) }}
                                </td>

                                <td class="align-middle">
                                    {{ number_format($product->stock) }}
                                </td>

                                <td class="align-middle">
                                    {{ number_format($product->num_sold) }}
                                </td>

                                <td class="align-middle">
                                    {{ $product->created_at->format('d M, Y') }}
                                </td>

                                <td class="align-middle">

                                    <a href="{{ route('admin.product.edit', $product->id) }}"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.product.destroy', $product->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this product?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-sm">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="text-center text-muted">
                                    No products found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

                <div class="mt-3">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>

            </div>

        </div>

    </div>
</div>

@endsection