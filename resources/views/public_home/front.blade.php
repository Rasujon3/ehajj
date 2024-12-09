<!DOCTYPE html>
<!--[if IE 8]>
<html lang="bn" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="bn" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="bn" class="no-js">
<!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="HV4D6RvK2fcKhVShcH8JlT7TZZex1QhSWgVvVKQj">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title>Bangladesh ehaj Portal| Home</title>

    <!-- Fav icon -->
    <link rel="shortcut icon" type="image/ico" href="favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets')}}/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('assets')}}/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('assets')}}/plugins/toastr/toastr.min.css" />
    <link rel="stylesheet" href="{{ asset('assets')}}/plugins/bootstrap/css/bootstrap4.min.css">

    <link rel="stylesheet" href="{{ asset('assets')}}/custom/css/datatables.min.css">
    <link rel="stylesheet" href="{{ asset('assets')}}/custom/css/custom-style.css?v=20230321.98">
    <link rel="stylesheet" href="{{ asset('assets')}}/custom/css/custom-responsive.css?v=20230321.98">
    <!--start  updated footer css & js file linkup -->
    <link rel="stylesheet" href="{{ asset('assets/public_page/custom/css/custom-footer-style.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/public_page/plugins/fontawesome-free/css/all.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/public_page/custom/css/custom-style.css?v=1') }}">
    <link rel="stylesheet" href="{{ asset('assets/public_page/custom/css/custom-responsive.css') }}">
    <!--end updated footer css & js file linkup -->
    @yield('header-resources')
</head>

<body>
<div class="site-main body-pattern-bg">
    {{ csrf_field() }}

    @include('public_home.header')
    @yield('body')
    @include('public_home.footer')

</div><!-- /site-main-content -->

<script type="text/javascript" src="{{ asset('assets')}}/plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset('assets')}}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="{{ asset('assets')}}/dist/js/adminlte.min.js"></script>
<script type="text/javascript" src="{{ asset('assets')}}/plugins/toastr/toastr.min.js"></script>
<script type="text/javascript" src="{{ asset('assets')}}/scripts/jquery.validate.min.js"></script>
<script type="text/javascript" src="{{ asset('assets')}}/custom/js/jquery.countdown.min.js"></script>
<script type="text/javascript" src="{{ asset('assets')}}/custom/js/datatables.min.js"></script>
@yield('footer-script')
<script>
    $(document).ready(function() {
        getImportantLink();
        function getImportantLink() {
            $.ajax({
                url: '{{url('get-impotant-link')}}',
                type: 'GET',
                success: function (response) {
                    $('#footer-important-link').html(response);
                }
            });
        }
    });

</script>
</body>
</html>
