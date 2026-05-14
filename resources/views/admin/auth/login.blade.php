@extends('admin.auth.base')

@section('content')
<div class="card-body">

    <div class="text-center mt-4">
        <div class="mb-3">
            <a href="{{ route('admin.login') }}" class="auth-logo">
                <img src="{{ asset('admin-assets/images/logo-dark.png') }}" height="30" class="logo-dark mx-auto" alt="">
                <img src="{{ asset('admin-assets/images/logo-light.png') }}" height="30" class="logo-light mx-auto" alt="">
            </a>
        </div>
    </div>

    <h4 class="text-muted text-center font-size-18"><b>Sign In</b></h4>

    <div class="p-3">
        <form class="form-horizontal mt-3" action="{{ route('admin.login') }}" method="POST">
            @csrf

            <div class="form-group mb-3">
                <input class="form-control @error('email') is-invalid @enderror"
                       type="email"
                       name="email"
                       id="email"
                       value="{{ old('email') }}"
                       required
                       placeholder="Email">
                @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <input class="form-control @error('password') is-invalid @enderror"
                       type="password"
                       name="password"
                       id="password"
                       required
                       placeholder="Password">
                @error('password')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group mb-3 d-flex align-items-center">
                <input type="checkbox" name="remember" id="remember" class="me-2">
                <label for="remember" class="form-label ms-1">Remember me</label>
            </div>

            <div class="form-group mb-3 text-center">
                <button class="btn btn-info w-100 waves-effect waves-light" type="submit">
                    Log In
                </button>
            </div>

            <div class="form-group row mt-2">
                <div class="col-sm-7 mt-3">
                    <a href="{{ route('password.request') }}" class="text-muted">
                        <i class="mdi mdi-lock"></i> Forgot your password?
                    </a>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
