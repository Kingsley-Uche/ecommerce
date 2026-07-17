<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                         @if(!empty($shop_data['logo_path']))
              <img src="{{ asset('storage/' . $shop_data['logo_path']) }}" alt="{{ $shop_data['store_name'] ?? 'Store' }}" class="logo-img me-2">
            @endif
                    <span class="logo-lg">
                       @if(!empty($shop_data['logo_path']))
              <img src="{{ asset('storage/' . $shop_data['logo_path']) }}" alt="{{ $shop_data['store_name'] ?? 'Store' }}" class="logo-img me-2">
            @endif
                    </span>
                </a>

                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        @if(!empty($shop_data['logo_path']))
              <img src="{{ asset('storage/' . $shop_data['logo_path']) }}" alt="{{ $shop_data['store_name'] ?? 'Store' }}" class="logo-img me-2">
            @endif
                    </span>
                    <span class="logo-lg">
                         @if(!empty($shop_data['logo_path']))
              <img src="{{ asset('storage/' . $shop_data['logo_path']) }}" alt="{{ $shop_data['store_name'] ?? 'Store' }}" class="logo-img me-2">
            @endif
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect"
                id="vertical-menu-btn">
                <i class="ri-menu-2-line align-middle"></i>
            </button>

            <!-- App Search-->
            <form class="app-search d-none d-lg-block">
                <div class="position-relative">
                    <input type="text" class="form-control" placeholder="Search...">
                    <span class="ri-search-line"></span>
                </div>
            </form>

        </div>
    </div>
</header>
