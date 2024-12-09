<!doctype html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="HV4D6RvK2fcKhVShcH8JlT7TZZex1QhSWgVvVKQj">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title> Bangladesh eHaj portal | Home</title>

    <!-- Fav icon -->
    <link rel="shortcut icon" type="image/ico" href="favicon.ico">
    @yield('header-resources')
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/custom/css/datatables.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/custom/css/custom-style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/custom/css/custom-responsive.css') }}">
</head>

<body >
    {{ csrf_field() }}

    <div class="site-main body-pattern-bg">
        @include('public_home.header')
        @yield('body')
        @include('public_home.footer')
    </div>
    @yield('footer-script')
    <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/scripts/jquery.validate.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/custom/js/jquery.countdown.min.js') }}"></script>

{{--    <script type="text/javascript" src="{{asset('assets/custom/js/mdb.min.js')}}"></script>--}}
    <script type="text/javascript" src="{{asset('assets/custom/js/datatables.min.js')}}"></script>


    {{-- <script>
        jQuery(document).ready(function($){
            $('#hajjDateCounter').countdown({
                date: '03/20/2023 23:59:59',
            });
        });
    </script> --}}
</body>
</html>
