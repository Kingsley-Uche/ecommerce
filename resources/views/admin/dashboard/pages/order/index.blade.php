@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Orders</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Summary cards --}}
<div class="row mb-4">
    @php
        $statusCounts = [
            'pending'    => $orders->where('order_status', 'pending')->count(),
            'processing' => $orders->where('order_status', 'processing')->count(),
            'shipped'    => $orders->where('order_status', 'shipped')->count(),
            'delivered'  => $orders->where('order_status', 'delivered')->count(),
            'cancelled'  => $orders->where('order_status', 'cancelled')->count(),
        ];
        $cardConfig = [
            'pending'    => ['color' => 'warning',   'icon' => 'mdi-clock-outline'],
            'processing' => ['color' => 'info',      'icon' => 'mdi-cog-outline'],
            'shipped'    => ['color' => 'primary',   'icon' => 'mdi-truck-outline'],
            'delivered'  => ['color' => 'success',   'icon' => 'mdi-check-circle-outline'],
            'cancelled'  => ['color' => 'danger',    'icon' => 'mdi-close-circle-outline'],
        ];
    @endphp

    @foreach($statusCounts as $status => $count)
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar-sm bg-{{ $cardConfig[$status]['color'] }} bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;">
                        <i class="mdi {{ $cardConfig[$status]['icon'] }} text-{{ $cardConfig[$status]['color'] }} fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 text-uppercase fw-semibold" style="font-size:.7rem;letter-spacing:.05em;">
                            {{ $status }}
                        </p>
                        <h4 class="mb-0 fw-bold">{{ number_format($count) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Total revenue card --}}
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="avatar-sm bg-success bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;">
                    <i class="mdi mdi-currency-ngn text-success fs-4"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 text-uppercase fw-semibold" style="font-size:.7rem;letter-spacing:.05em;">
                        Revenue
                    </p>
                    <h4 class="mb-0 fw-bold" style="font-size:1rem;">
                        ₦{{ number_format($orders->sum('total_paid'), 0) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="card-title mb-1">All Orders</h4>
                        <p class="card-title-desc mb-0">
                            Click any order to view details and update its status.
                        </p>
                    </div>

                    {{-- Quick status filter --}}
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach(['all','pending','processing','shipped','delivered','cancelled'] as $f)
                            <a href="{{ request()->fullUrlWithQuery(['status' => $f]) }}"
                               class="btn btn-sm {{ request('status', 'all') === $f ? 'btn-primary' : 'btn-outline-secondary' }}">
                                {{ ucfirst($f) }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <table class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ref</th>
                            <th>Customer</th>
                            <th>City</th>
                            <th>Total Cost</th>
                            <th>Total Paid</th>
                            <th>Order Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="align-middle text-muted small">{{ $order->id }}</td>

                                <td class="align-middle">
                                    <span class="font-monospace small fw-semibold">
                                        {{ $order->payment_ref ?? '—' }}
                                    </span>
                                </td>

                                <td class="align-middle">
                                    <div class="fw-semibold">{{ $order->user_name }}</div>
                                    <small class="text-muted">{{ $order->email_address }}</small>
                                </td>

                                <td class="align-middle small">{{ $order->delivery_city }}</td>

                                <td class="align-middle">
                                    ₦{{ number_format($order->total_cost, 2) }}
                                </td>

                                <td class="align-middle">
                                    ₦{{ number_format($order->total_paid, 2) }}
                                    @if($order->total_paid < $order->total_cost)
                                        <span class="badge bg-warning ms-1">Part</span>
                                    @endif
                                </td>

                                <td class="align-middle">
                                    @php
                                        $osBadge = match($order->order_status) {
                                            'pending'    => 'warning',
                                            'processing' => 'info',
                                            'shipped'    => 'primary',
                                            'delivered'  => 'success',
                                            'cancelled'  => 'danger',
                                            default      => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $osBadge }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>

                                <td class="align-middle">
                                    @php
                                        $psBadge = match($order->payment_status) {
                                            'paid'     => 'success',
                                            'pending'  => 'warning',
                                            'failed'   => 'danger',
                                            'refunded' => 'secondary',
                                            default    => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $psBadge }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>

                                <td class="align-middle small">
                                    {{ $order->created_at->format('d M, Y') }}
                                </td>

                                <td class="align-middle">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="mdi mdi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    No orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
</div>

@endsection