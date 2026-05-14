{{-- resources/views/admin/dashboard/pages/store/index.blade.php --}}
@extends('admin.dashboard.home')

@section('admin-dashboard-content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Store Information</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Settings</a></li>
                    <li class="breadcrumb-item active">My Store</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="card-title mb-1">{{ $store->store_name ?? 'My Store' }}</h4>
                        <p class="text-muted mb-0">Your store's public information</p>
                    </div>
                    <div>
                        @if($store)
                            <a href="{{ route('admin.store_details.edit', $store->id) }}" class="btn btn-warning btn-sm">Edit Store</a>
                        @else
                            <a href="{{ route('admin.store_details.create') }}" class="btn btn-primary btn-sm">Create Store</a>
                        @endif
                    </div>
                </div>

                @if($store)
                    @php
                        $social_links =  $store->social_links ?? [];
                        $social_icons =  $store->social_icons ?? [];
                        $products =      $store->products ?? [];

                        $iconMap = [
                            'facebook' => 'bxl-facebook',
                            'instagram' => 'bxl-instagram',
                            'twitter' => 'bxl-twitter',
                            'linkedin' => 'bxl-linkedin',
                            'tiktok' => 'bxl-tiktok',
                            'youtube' => 'bxl-youtube',
                            'globe' => 'bx-globe',
                        ];
                    @endphp

                    <div class="row g-4">
                        <!-- Logo & Basic Info -->
                        <div class="col-lg-4 text-center text-lg-start">
                            <div class="position-relative d-inline-block">
                                @if($store->logo_path)
                                    <img src="{{ asset('storage/' . $store->logo_path) }}" 
                                         alt="{{ $store->store_name }}" 
                                         class="rounded-circle shadow" 
                                         width="140" height="140">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow" 
                                         style="width: 140px; height: 140px;">
                                        <i class="bx bx-store font-size-48 text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <h5 class="mt-3 mb-1">{{ $store->store_name }}</h5>
                            <p class="text-muted small">{{ $store->tagline ?: 'No tagline set' }}</p>
                        </div>

                        <!-- Details -->
                        <div class="col-lg-8">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <i class="bx bx-map text-primary me-3 fs-4"></i>
                                        <div>
                                            <small class="text-muted">Address</small>
                                            <p class="mb-0">{{ $store->address ?? 'Not set' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <i class="bx bx-phone text-success me-3 fs-4"></i>
                                        <div>
                                            <small class="text-muted">Phone</small>
                                            <p class="mb-0">
                                                @if($store->phone)
                                                    <a href="tel:{{ $store->phone }}">{{ $store->phone }}</a>
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <i class="bx bx-envelope text-info me-3 fs-4"></i>
                                        <div>
                                            <small class="text-muted">Email</small>
                                            <p class="mb-0">
                                                @if($store->email)
                                                    <a href="mailto:{{ $store->email }}">{{ $store->email }}</a>
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <i class="bx bx-time text-warning me-3 fs-4"></i>
                                        <div>
                                            <small class="text-muted">Last Updated</small>
                                            <p class="mb-0">{{ $store->updated_at?->diffForHumans() ?? 'Never' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Links -->
                            @if(count($social_links) > 0)
                                <hr class="my-4">
                                <div>
                                    <h6 class="mb-3">Social Media</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($social_links as $index => $link)
                                            @if(!empty($link))
                                                @php
                                                    $iconName = strtolower($social_icons[$index] ?? 'globe');
                                                    $iconClass = $iconMap[$iconName] ?? 'bx-globe';
                                                @endphp
                                                <a href="{{ $link }}" target="_blank" 
                                                   class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                                                    <i class="bx {{ $iconClass }} me-1"></i>
                                                    {{ ucfirst($iconName) }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            

                        </div>
                    </div>

                @else
                    <!-- No Store Yet -->
                    <div class="text-center py-5">
                        <i class="bx bx-store-alt display-1 text-muted opacity-50"></i>
                        <h4 class="mt-4 text-muted">No store created yet</h4>
                        <p class="text-muted mb-4">
                            Set up your store name, logo, contact info, and front page products.
                        </p>
                        <a href="{{ route('admin.store_details.create') }}" class="btn btn-primary px-4">
                            Create My Store
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection
