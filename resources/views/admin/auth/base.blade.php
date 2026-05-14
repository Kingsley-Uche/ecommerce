 @include('admin.auth.header')
 <body class="auth-body-bg">
        <div class="bg-overlay"></div>
        <div class="wrapper-page">
            <div class="container-fluid p-0">
                <div class="card">
                    
                        @yield('content')
                
                </div>
            </div>
        </div>
    </body>
    @include('admin.auth.footer')