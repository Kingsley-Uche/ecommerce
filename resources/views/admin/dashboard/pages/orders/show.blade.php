@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Order #{{ $order->id }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active">#{{ $order->id }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">

    {{-- ── Left column: order details ──────────────────────── --}}
    <div class="col-lg-8">

        {{-- Customer & delivery --}}
        <div class="card mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="mb-0"><i class="mdi mdi-account-outline me-2"></i>Customer & Delivery</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Customer Name</label>
                        <p class="mb-0 fw-semibold">{{ $order->user_name }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Email</label>
                        <p class="mb-0">
                            <a href="mailto:{{ $order->email_address }}">{{ $order->email_address }}</a>
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Phone</label>
                        <p class="mb-0">
                            <a href="tel:{{ $order->phone }}">{{ $order->phone }}</a>
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">City</label>
                        <p class="mb-0">{{ $order->delivery_city }}</p>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Delivery Address</label>
                        <p class="mb-0">{{ $order->delivery_address }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment info --}}
        <div class="card mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="mb-0"><i class="mdi mdi-cash-multiple me-2"></i>Payment Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Payment Reference</label>
                        <p class="mb-0 font-monospace">{{ $order->payment_ref ?? '—' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Cart Token</label>
                        <p class="mb-0 font-monospace text-truncate" style="max-width:220px;"
                           title="{{ $order->cart_token }}">
                            {{ $order->cart_token ?? '—' }}
                        </p>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Total Cost</label>
                        <p class="mb-0 fw-bold fs-5">₦{{ number_format($order->total_cost, 2) }}</p>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Total Paid</label>
                        <p class="mb-0 fw-bold fs-5 text-success">₦{{ number_format($order->total_paid, 2) }}</p>
                    </div>
                  <div class="col-sm-4">
    <label class="text-muted small text-uppercase fw-semibold"
           style="letter-spacing:.05em;">
        Balance
    </label>

    @php
        $balance = max(
            (float) ($order->total_cost ?? 0) - (float) ($order->total_paid ?? 0),
            0
        );
    @endphp

    <p class="mb-0 fw-bold fs-5 {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
        ₦{{ number_format($balance, 2) }}
    </p>
</div>
                </div>
            </div>
        </div>

        {{-- Order meta --}}
        <div class="card">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="mb-0"><i class="mdi mdi-information-outline me-2"></i>Order Meta</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Order ID</label>
                        <p class="mb-0 font-monospace">#{{ $order->id }}</p>
                    </div>
                <div class="col-12">
    <label class="text-muted small text-uppercase fw-semibold"
           style="letter-spacing:.05em;">Ordered Products</label>

    @if($products->isNotEmpty())
        <div class="table-responsive mt-2">
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td class="text-end">
                                ₦{{ number_format($product->price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="mb-0 text-muted">No products found.</p>
    @endif
</div>
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Placed On</label>
                        <p class="mb-0">{{ $order->created_at->format('d M, Y — H:i') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-semibold"
                               style="letter-spacing:.05em;">Last Updated</label>
                        <p class="mb-0">{{ $order->updated_at->format('d M, Y — H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Right column: status panel ──────────────────────── --}}
    <div class="col-lg-4">

        {{-- Current status --}}
        <div class="card mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="mb-0"><i class="mdi mdi-tag-outline me-2"></i>Current Status</h5>
            </div>
            <div class="card-body">
                @php
                    $osBadge = match($order->order_status) {
                        'pending'    => 'warning',
                        'processing' => 'info',
                        'shipped'    => 'primary',
                        'delivered'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'secondary',
                    };
                    $psBadge = match($order->payment_status) {
                        'paid'     => 'success',
                        'pending'  => 'warning',
                        'failed'   => 'danger',
                        'refunded' => 'secondary',
                        default    => 'secondary',
                    };
                @endphp

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small fw-semibold text-uppercase"
                          style="letter-spacing:.05em;">Order</span>
                    <span class="badge bg-{{ $osBadge }} fs-6 px-3 py-2">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-semibold text-uppercase"
                          style="letter-spacing:.05em;">Payment</span>
                    <span class="badge bg-{{ $psBadge }} fs-6 px-3 py-2">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Status update form --}}
        <div class="card">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="mb-0"><i class="mdi mdi-pencil-outline me-2"></i>Update Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.status', $order->id) }}"
                      method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="order_status" class="form-label fw-semibold">
                            Order Status
                        </label>
                        <select name="order_status"
                                id="order_status"
                                class="form-select @error('order_status') is-invalid @enderror">
                            @foreach(['pending','processing','shipped','delivered','cancelled'] as $status)
                                <option value="{{ $status }}"
                                        {{ $order->order_status === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('order_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="payment_status" class="form-label fw-semibold">
                            Payment Status
                        </label>
                        <select name="payment_status"
                                id="payment_status"
                                class="form-select @error('payment_status') is-invalid @enderror">
                            @foreach(['pending','paid','failed','refunded'] as $status)
                                <option value="{{ $status }}"
                                        {{ $order->payment_status === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save-outline me-1"></i>
                            Save Changes
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- Back button --}}
        <div class="mt-3">
            <a href="{{ route('admin.orders.index') }}"
               class="btn btn-outline-secondary w-100">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Orders
            </a>
        </div>

    </div>

</div>

@endsection