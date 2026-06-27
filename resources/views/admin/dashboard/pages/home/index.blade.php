@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Dashboard</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row">

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title rounded-circle bg-primary">
                            <i class="ri-shopping-bag-3-line font-size-20"></i>
                        </span>
                    </div>

                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Products</p>
                        <h4 class="mb-0">{{ number_format($totalProducts) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title rounded-circle bg-success">
                            <i class="ri-price-tag-3-line font-size-20"></i>
                        </span>
                    </div>

                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Categories</p>
                        <h4 class="mb-0">{{ $categories->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title rounded-circle bg-warning">
                            <i class="ri-money-dollar-circle-line font-size-20"></i>
                        </span>
                    </div>

                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Revenue (YTD)</p>
                        <h4 class="mb-0">₦{{ number_format($revenue,2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title rounded-circle bg-info">
                            <i class="ri-line-chart-line font-size-20"></i>
                        </span>
                    </div>

                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Products in Report</p>
                        <h4 class="mb-0">{{ $productBreakdown->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <!-- Inventory -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Inventory Overview</h4>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Sold</th>
                                <th>Stock/Sold Ratio</th>
                            </tr>
                        </thead>

                        <tbody>

                        @forelse($productBreakdown as $product)

                            <tr>

                                <td>{{ $product['name'] }}</td>

                                <td>
                                    <span class="badge bg-success">
                                        {{ number_format($product['stock']) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-primary">
                                        {{ number_format($product['sold']) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $product['product_status'] ?? 'N/A' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No products available.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="col-lg-4">

        <div class="card">

            <div class="card-header">
                <h4 class="card-title mb-0">Categories</h4>
            </div>

            <div class="card-body">

                <ul class="list-group list-group-flush">

                    @forelse($categories as $category)

                        <li class="list-group-item d-flex justify-content-between align-items-center">

                            {{ $category->name }}

                            <span class="badge bg-primary rounded-pill">
                                {{ $loop->iteration }}
                            </span>

                        </li>

                    @empty

                        <li class="list-group-item text-center">
                            No Categories Found
                        </li>

                    @endforelse

                </ul>

            </div>

        </div>

    </div>

</div>

@endsection