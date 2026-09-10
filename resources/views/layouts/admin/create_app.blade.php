<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ @$admin_setting->title }}</title>
    <link rel="shortcut icon"
        href="{{ file_exists(@$admin_setting->favicon) ? asset(@$admin_setting->favicon) : asset('backend/images/logo/favicon.png') }}"
        type="image/x-icon">
    @include('layouts.admin.partial.styles')
    @include('layouts.admin.partial.alert')
    <style>
        :root {
            --bs-primary: {{ @$admin_setting->primary_color ?? '#3753e9' }};
            --bs-secondary: {{ @$admin_setting->secondary_color ?? '#415FFF' }};
        }
    </style>
</head>

<body class="bg-light">
    <div class="overflow-hidden site-wrapper @if (Session::has('sidebar-collapse')) session-sidebar @endif">
        @include('layouts.admin.partial.sidebar')

        <div class="content-wrapper">
            @include('layouts.admin.partial.header')
            <div class="content">
                <div class="p-sm-4 p-3">
                    <div class="row g-3">
                        <div class="col-12">
                            @php
                                $currentRouteName = \Request::route()->getName();
                                $store_link = str_replace('create', 'store', $currentRouteName);
                                $back_link = str_replace('create', 'index', $currentRouteName);
                            @endphp
                            <form action="{{ Route($store_link) }}" method="POST" enctype="multipart/form-data"
                                @isset($target_blank) target="_blank" @endisset id="store_form">
                                @csrf
                                <div class="card">
                                    <div class="card-header pe-2 py-2">
                                        <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center">
                                            <h6 class="h6 mb-0 text-uppercase text-nowrap flex-grow-1">
                                                {{ @$title ?? 'Please Set Title' }}</h6>
                                            <div class="d-flex gap-2">
                                                <a href="{{ Route($back_link) }}" class="btn btn-primary btn-sm">Go
                                                    Back</a>
                                                <button type="submit" class="btn btn-primary btn-sm submit_btn"><span
                                                        class="btn-spiner spinner-border spinner-border-sm"
                                                        style="display: none;" aria-hidden="true"></span>Save</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @yield('content')
                                    </div>
                                    <div class="card-footer text-end pe-2 py-2">
                                        <button type="submit" class="btn btn-primary btn-sm submit_btn"><span
                                                class="btn-spiner spinner-border spinner-border-sm"
                                                style="display: none;" aria-hidden="true"></span>Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.admin.partial.footer')
        </div>
    </div>
    <!-- End Site Wrapper -->

    @include('sweetalert::alert')
    @include('layouts.admin.partial.scripts')

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @stack('js')
</body>

</html>
