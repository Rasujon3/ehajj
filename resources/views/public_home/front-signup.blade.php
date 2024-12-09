<?php echo
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // HTTP 1.1.
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
header('Content-Type: text/html');
header("X-Frame-Options: SAMEORIGIN");
header('X-Content-Type-Options: nosniff');
?>
        <!DOCTYPE html>
<!--[if IE 8]>
<html lang="bn" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="bn" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="bn" class="no-js">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BIDA One Stop Service platform for Ease of doing business in Bangladesh</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="{{ config('app.project_name') }}"/>
    <meta name="keywords" content="OSS framework">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Expires" content="0"/>

    <!-- Fav icon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset("assets/images/favicon.ico") }}"/>

    <link rel="stylesheet" type="text/css" href="{{ mix('css/front.css') }}" media="all"/>
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/plugins/newsTicker/ticker-style.min.css") }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/plugins/toastr/toastr.min.css") }}"/>
    <link rel="stylesheet" href="{{ asset('assets/stylesheets/homepage_custom.css') }}"/>

{{--    <link rel="stylesheet" type="text/css" href="{{ asset("assets/stylesheets/kalpurush.css") }}"/>--}}
    <script async src="{{ asset('assets/scripts/html5shiv.min.js') }}"></script>
    <script async src="{{ asset('assets/scripts/respond.min.js') }}"></script>
    <![endif]-->

    @yield('header-resources')
</head>

<body class="">
{{ csrf_field() }}
@include('public_home.header')

<div class="home-wrapper mt-0 body-patern">
    @yield('body')
</div>
@include('public_home.footer')


    <!-- jQuery -->
    {{--    <script type="text/javascript" src="{{ mix("assets/plugins/jquery/jquery.min.js") }}"></script>--}}
    <script type="text/javascript" src="{{ asset("assets/scripts/jquery_v3.5.1.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("assets/plugins/toastr/toastr.min.js") }}"></script>




<script defer type="text/javascript" src="{{ asset("assets/plugins/newsTicker/jquery.ticker.min.js") }}"></script>
@if(Request::is('/') || Request::is('login'))
    {{--    <script defer src="{{ mix("assets/scripts/home_page.min.js") }}" type="text/javascript"></script>--}}
    <script defer src="{{ asset("assets/scripts/home_page.min.js") }}" type="text/javascript"></script>
@endif


@include('public_home.footer_script')

@yield('footer-script')

<script>
    {{--function languageSwitch(status) {--}}
    {{--    console.log(status);--}}
    {{--    let lang = 'bn';--}}
    {{--    if (status) {--}}
    {{--        lang = 'bn'--}}
    {{--    } else {--}}
    {{--        lang = 'en';--}}
    {{--    }--}}
    {{--    const lang_url = '{{ url('lang') }}' + '/' + lang;--}}
    {{--    $(location).attr('href', lang_url)--}}
    {{--}--}}
</script>
</body>
</html>

