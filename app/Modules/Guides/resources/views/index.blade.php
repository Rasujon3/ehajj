@extends('layouts.admin')

@section('page_heading',trans('messages.bank_list'))

@section('header-resources')
    <link rel="stylesheet" href="{{ asset("assets/plugins/select2.min.css") }}">
@endsection
@section('content')

    <div class="col-md-12 col-lg-12">
        {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
        {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}
    </div>
    <?php
    $accessMode = ACL::getAccsessRight('guides');
    if (!ACL::isAllowed($accessMode, 'V')) {
        die('You have no access right! Please contact with system admin for more information.');
    }
    ?>
    <div id="app">
        <router-view></router-view>
    </div>


@endsection

@section('footer-script')
    <script src="{{asset('assets/js/select2.min.js')}}"></script>
@endsection
