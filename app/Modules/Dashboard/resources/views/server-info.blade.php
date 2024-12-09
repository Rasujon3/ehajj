
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="bn" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="bn" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="bn" class="no-js">
<!--<![endif]-->

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>One Stop Service platform for Ease of doing business in Bangladesh</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta name="author" content="{{ config('app.project_name') }}"/> --}}
    <meta name="keywords" content="OSS framework">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <!-- Fav icon -->
    {{-- <link rel="shortcut icon" type="image/png" href="{{ asset("assets/images/favicon.ico") }}"/> --}}
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/stylesheets/custom.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}" />
    @yield('header-resources')
</head>
<style>
    #serverInfoDiv p {
        margin-bottom: 10px;
        font-size: 16px;
        font-weight: 200;
    }
    #serverInfoDiv{
        padding:15px;
        background: #c9f7db;
    }
</style>

<body class="bg-body">

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box-div">
            <div class="row">
                <div class="col-sm-12">

                    <div class="jumbotron" id="serverInfoDiv">
                        <h2>Server Information:</h2>
                        <hr/>

                        <p><strong>RAM Usage:</strong> {{ $total_ram_usage }}%</p>
                        <p><strong>CPU Usage:</strong> {{ $cpu_load }}%</p>
                        <p><strong>Hard Disk Usage: </strong> {{ $disk_usage_percentage }}%</p>
                        {{--                            <p><strong>Established Connections: </strong> {{ $connections }}</p>--}}
                        {{--                            <p><strong>Total Connections: </strong> {{ $totalconnections }}</p>--}}

                        <hr/>

                        <p><strong>RAM Total: </strong> {{ $total_ram_size }} GB</p>
                        <p><strong>RAM Free: </strong> {{ $free_ram_size }} GB</p>
                        <p><strong>RAM Used: </strong> {{ $used_ram_size }} GB</p>
                        <p><strong>RAM Buff/cache: </strong> {{ $buffer_cache_memory_size }} GB</p>

                        <hr/>
                        <p><strong>Hard Disk Total: </strong> {{ $total_disk_size }} GB</p>
                        <p><strong>Hard Disk Used: </strong> {{ $used_disk_size }} GB</p>
                        <p><strong>Hard Disk Free: </strong> {{ $free_disk_size }} GB</p>

                        <hr/>
                        <p><strong>Server Name: </strong> {{ $_SERVER['SERVER_NAME'] }}</p>
                        <p><strong>Server Address: </strong> {{ $_SERVER['SERVER_ADDR'] }}</p>
                        <p><strong>Server Port: </strong> {{ $_SERVER['SERVER_PORT'] }}</p>
                        <p><strong>Server Software: </strong> {{ $_SERVER['SERVER_SOFTWARE'] }}</p>
                        <p><strong>PHP Version: </strong> {{ phpversion() }}</p>
                        <p><strong>Database : </strong> {{ $db_version }}</p>
                        <p><strong>Load Time : </strong> {{ $total_time_of_loading }} sec</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <a href="/login"><input type="button" class="btn btn-lg btn-success" value="Go Back to Login"/></a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

