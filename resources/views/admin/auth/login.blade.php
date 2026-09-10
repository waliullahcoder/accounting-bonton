<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $admin_setting ? $admin_setting->title : '' }}</title>
    <link rel="shortcut icon"
        href="{{ $admin_setting && file_exists($admin_setting->favicon) ? asset($admin_setting->favicon) : asset('backend/images/logo/favicon.png') }}"
        type="image/x-icon">
    @include('layouts.admin.partial.styles')

    @if (session()->has('errors'))
        @foreach (session('errors')->all() as $error)
            @php
                RealRashid\SweetAlert\Facades\Alert::toast($error, 'error');
            @endphp
        @endforeach
    @endif

    @if (\Session::has('error'))
        @php
            RealRashid\SweetAlert\Facades\Alert::toast(\Session::get('error'), 'error');
        @endphp
    @endif
    <style>
        :root {
            --bs-primary: {{ $admin_setting && $admin_setting->primary_color ? $admin_setting->primary_color : '#3753e9' }};
            --bs-secondary: {{ $admin_setting && $admin_setting->secondary_color ? $admin_setting->secondary_color : '#415FFF' }};
        }
    </style>
</head>

<body class="bg-light">
    <div class="site-wrapper">
        <div class="place-items-center p-3" style="height: 100vh">
            <div class="login-wrapper p-sm-5 p-4">
                <div class="text-center pb-2 mb-4">
                    <img src="{{ $admin_setting && file_exists($admin_setting->logo) ? asset($admin_setting->logo) : asset('backend/images/logo/logo.png') }}"
                        height="60" alt="Logo">
                    <br>
                </div>
                <form action="{{ route('admin.login') }}" class="login-form" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" class="form-control" name="user_name" placeholder="Username"
                                    value="{{ old('user_name') }}" required autofocus autocomplete="user_name">
                                <span class="input-group-text"><i class="fal fa-user-alt"></i></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group">
                                <input type="password" class="form-control" id="toggle_password" name="password"
                                    placeholder="Password" required autocomplete="password">
                                <button type="button" class="input-group-text password-toggler"><i
                                        class="fas fa-eye-slash fs-18"></i></button>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check fs-15">
                                <input class="form-check-input c-pointer" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label c-pointer" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-primary">Log in</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Site Wrapper -->
    @include('layouts.admin.partial.scripts')
    @include('sweetalert::alert')
</body>

</html>
