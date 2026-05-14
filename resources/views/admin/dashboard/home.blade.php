 @include('admin.dashboard.components.header')
 @include('admin.dashboard.components.nav')
 @include('admin.dashboard.components.sidebar')
    <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                            @yield('admin-dashboard-content')
                        </div>
                    </div>
                </div>
    @include('admin.dashboard.components.footer')

