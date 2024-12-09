<?php

if (!empty($process_info)) {

    $accessMode = ACL::getAccsessRight($process_info->acl_name);
    if (!ACL::isAllowed($accessMode, '-V-')) {
        die('no access right!');
    }
}

$moduleName = Request::segment(1);
$user_type = CommonFunction::getUserType();
$desk_id_array = explode(',', \Session::get('user_desk_ids'));
$delegatedUserDeskOfficeIds = CommonFunction::getDelegatedUserDeskOfficeIds();


$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);

$prefix = '';
if ($type[0] == 5) {
    //$prefix = 'client';
    $prefix = 'client';
}

?>
@extends('layouts.admin')

@section('header-resources')
    <style>
        .unreadMessage td {
            font-weight: bold;
        }

        .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f1f1f1;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        width: 100%;
        border-radius: 3px;
    }

    .dropdown-content a {
        color: black;
        text-decoration: none;
        display: block;
        font-weight: bold;
        border: 1px solid green;
        border-radius: 3px;
        background-color: rgb(168, 158, 158);
    }

    .dropdown-content a:hover {
        background-color: #3e8e41;
        color: white;
        border-color: grey;
    }

    .form-check :hover {
        background-color: #3e8e41 !important;
        color: white;
        border-color: grey;
    }

    .dropdown:hover .dropdown-content {display: block;}

    .dropdown:hover .dropbtn {background-color: #ddd;}

    .serviceBoxes {
        float: left;
        width: 110px;
        margin: 5px 3px;
        height: 80px;
        cursor: pointer;
    }

    .serviceBoxes-inner {
        padding: 3px !important;
        font-weight: bold !important;
        height: 100%;
    }

    .bg-aqua {
        background-color: #00c0ef !important;
    }
    .danger {
        background-color: #ff0000 !important;
    }
    .info {
        background-color: #62dbf1 !important;
    }

    </style>
    @include('partials.datatable-css')
    <link rel="stylesheet"
        href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}" />
@endsection

@section('content')

    @include('partials.messages')

    @if (empty($delegated_desk))
        <div class="modal fade" id="ProjectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="frmAddProject"></div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-magenta border border-magenta" style="">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-10">
                            <h5><i class="fa fa-list"></i> <b>{!! trans('ProcessPath::messages.application_list') !!}
                                    <span class="list_name"></span>
                                    <!-- @if (isset($process_info->name) && !empty($process_info->name))
                                        for
                                        ({{ $process_info->name }}) -->
                                </b>
                                <!-- @endif -->
                            </h5>

                        </div>
                        @if ( count($process_type_data_arr) > 0 && $user_type != '17x171' )
                    <button type="button" class="btn btn-default" data-toggle="modal" style="text-align: right" data-target="#processTypeModal">
                            <i class="fa fa-plus"></i><b>  New Application </b>
                        </button>
                        @endif

                        <div class="modal fade" id="processTypeModal" tabindex="-1" aria-labelledby="processTypeModalLabel" aria-hidden="true" style="margin-top: 0px;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body" style="color: #000;">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <div class="mt-2" style="padding: 30px;">
                                            <p class="rounded-pill" style="font-size: 18px; padding: 5px; background-color: #558e9f;text-align: center !important;"> একটি সার্ভিস ক্লিক করুন। </p>

                                            @if(count($process_type_data_arr) > 0)
                                                @foreach($process_type_data_arr as $processIndex => $process)
                                                    @php
                                                         if(!isset($process['id']) || empty($process['id'])){
                                                             continue;
                                                         }
                                                    @endphp
                                                <div class="form-check p-0">
                                                    <label class="form-check-label px-3 py-2 border w-100 mb-2" for="exampleRadios3">
                                                        @if(\Illuminate\Support\Facades\Auth::user()->working_user_type != 'Pilgrim')
                                                        <a href="{{URL::to('/client/process/'.$process['form_url'].'/add/'.\App\Libraries\Encryption::encodeId($process['id']))}}"
                                                           class="p-2 mr-2"
                                                           style="font-size: 16px;padding: 5px; cursor: pointer; color: #0a0e14;"> {{ $process['name_bn'] }} </a>
                                                        @else
                                                            <a @if($process['id']==3) onclick="checkGuide()" @else href="{{URL::to('/client/process/'.$process['form_url'].'/add/'.\App\Libraries\Encryption::encodeId($process['id']))}}" @endif
                                                               class="p-2 mr-2"
                                                               style="font-size: 16px;padding: 5px; cursor: pointer; color: #0a0e14;" > {{ $process['name_bn'] }} </a>
                                                        @endif
                                                    </label>
                                                </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                @if ( $user_type != '5x505' )
                    <div class="card-body">
                        <div class="clearfix">
                            @if(count($process_type_data_arr) > 0)
                                @foreach($process_type_data_arr as $key=>$val)
                                    <div class="serviceBoxes" id="{{$val['id']}}">
                                        <div class="card serviceBoxes-inner {{ $val['panel'] }}"
                                            style="background: {{ $val['panel'] }}; border: 1px solid {{ $val['panel'] }} !important;">
                                            <a href="#" class="statusWiseList" data-id="{{$key}}"
                                               style="background: {{ $val['panel'] }}">
                                                <div class="{{ $val['panel'] }}"
                                                     style="background: {{ $val['panel'] }};color: white; padding: 10px 5px !important; alignment-adjust: central;height: 100%"
                                                     title="{{ !empty($val['name']) ? $val['name'] :'N/A'}}">

                                                    <div class="row" style=" text-decoration: none !important">
                                                        <div class="col-12 text-center">
                                                            <div class="h3"
                                                                 style="margin-top:0;margin-bottom:0;font-size:12px; font-weight: bold">
                                                                {{ !empty($val['name']) ? $val['name'] :'N/A'}}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12 text-center">
                                                            <div class="h3" style="margin-top:0;margin-bottom:0;font-size:18px;font-weight: bold"
                                                                 id="{{ !empty($val['name']) ? $val['name'] :'N/A'}}">
                                                                {{$val['total']}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                          @endforeach
                        </div>
                    </div>
                @endif

                    <div class="clearfix">
                        <div class="card-body" id="statuswiseAppsDiv" style="display: none">

                        </div>

                    </div>


                    <div class="nav-tabs-custom" style="margin-top: 15px;padding: 0px 5px;">
                        <nav class="navbar navbar-expand-mdjustify-content-center">

                            <ul class="nav nav-tabs">
                                @if ($user_type != '1x101')
                                    @if(\Illuminate\Support\Facades\Auth::user()->desk_id != 0)
                                        <li id="tab1" class="nav-item ">
                                            <a data-toggle="tab" href="#list_desk" class="mydesk nav-link active"
                                               aria-expanded="true">
                                                <b>{!! trans('ProcessPath::messages.my_desk') !!}</b>
                                            </a>
                                        </li>
                                        <li id="tab1" class="nav-item ">
                                            <a data-toggle="tab" href="#my_list_desk" class="myapplication nav-link "
                                               aria-expanded="true">
                                                <b>My Application</b>
                                            </a>
                                        </li>
                                    @else
                                        <li id="tab1" class="nav-item ">
                                            <a data-toggle="tab" href="#my_list_desk" class="myapplication nav-link active"
                                               aria-expanded="true">
                                                <b>My Application</b>
                                            </a>
                                        </li>
                                    @endif
                                @else
                                    <li id="tab1" class="nav-item active">
                                        <a data-toggle="tab" href="#list_desk" class="mydesk nav-link active"
                                            aria-expanded="true">
                                            <b>{!! trans('ProcessPath::messages.list') !!}</b>
                                        </a>
                                    </li>
                                @endif

                                <li id="tab4" class="nav-item">
                                    <a data-toggle="tab" href="#favoriteList" class="favorite_list nav-link"
                                        aria-expanded="true">
                                        <b>{!! trans('ProcessPath::messages.favourite') !!}</b>
                                    </a>
                                </li>

                                <li id="tab3" class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#list_search" id="search_by_keyword"
                                        aria-expanded="false">
                                        <b>{!! trans('ProcessPath::messages.search') !!}</b>
                                    </a>
                                </li>
                            </ul>

                            @if ( $user_type != '5x505' )
                                <ul class="navbar-nav ml-auto">
                                    <div class="row">
                                        <li class="process_type_tab nav-item" id="processDropdown">
                                            {!! Form::select('ProcessType', ['0' => 'সকল তথ্য'] + $ProcessType, $process_type_id, [
                                                'class' => 'form-control ProcessType',
                                            ]) !!}
                                        </li>
                                    </div>
                                </ul>
                            @endif
                        </nav>
                        <div id="reyad" class="tab-content">
                            <div id="list_desk" class="tab-pane active" style="margin-top: 20px">
                                <table id="table_desk" class="table table-striped table-bordered display"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%;">{!! trans('ProcessPath::messages.tracking_no') !!}</th>
                                            <th>{!! trans('ProcessPath::messages.current_desk') !!}</th>
                                            <th>{!! trans('ProcessPath::messages.process_type') !!}</th>
                                            <th style="width: 35%">{!! trans('ProcessPath::messages.reference_data') !!}</th>
                                            <th>{!! trans('ProcessPath::messages.status_') !!}</th>
                                            <th>{!! trans('ProcessPath::messages.modified') !!}</th>
                                            <th>{!! trans('ProcessPath::messages.action') !!}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                            <div id="my_list_desk" class="tab-pane" style="margin-top: 20px">
                                <table id="my_application_desk" class="table table-striped table-bordered display"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%;">{!! trans('ProcessPath::messages.tracking_no') !!}</th>
                                            <th>{!! trans('ProcessPath::messages.current_desk') !!}</th>
                                            <th>{!! trans('ProcessPath::messages.process_type') !!}</th>
                                            <th style="width: 35%">{!! trans('ProcessPath::messages.reference_data') !!}</th>
                                            <th>{!! trans('ProcessPath::messages.status_') !!}</th>
                                            <th>{!! trans('ProcessPath::messages.modified') !!}</th>
                                            <th>{!! trans('ProcessPath::messages.action') !!}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                            <div id="list_search" class="tab-pane" style="margin-top: 20px">
                                @include('ProcessPath::search')
                            </div>
                            <div id="list_delg_desk" class="tab-pane" style="margin-top: 20px">
                                <div class="table-responsive">
                                    <table id="table_delg_desk" class="table table-striped table-bordered display"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 15%;">{!! trans('ProcessPath::messages.tracking_no') !!}</th>
                                                <th>{!! trans('ProcessPath::messages.current_desk') !!}</th>
                                                <th>{!! trans('ProcessPath::messages.process_type') !!}</th>
                                                <th style="width: 35%">{!! trans('ProcessPath::messages.reference_data') !!}</th>
                                                <th>{!! trans('ProcessPath::messages.status_') !!}</th>
                                                <th>{!! trans('ProcessPath::messages.modified') !!}</th>
                                                <th>{!! trans('ProcessPath::messages.action') !!}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="favoriteList" class="tab-pane" style="margin-top: 20px">
                                <div class="table-responsive">
                                    <table id="favorite_list" class="table table-striped table-bordered display"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 15%;">{!! trans('ProcessPath::messages.tracking_no') !!}</th>
                                                <th>{!! trans('ProcessPath::messages.current_desk') !!}</th>
                                                <th>{!! trans('ProcessPath::messages.process_type') !!}</th>
                                                <th style="width: 35%">{!! trans('ProcessPath::messages.reference_data') !!}</th>
                                                <th>{!! trans('ProcessPath::messages.status_') !!}</th>
                                                <th>{!! trans('ProcessPath::messages.modified') !!}</th>
                                                <th>{!! trans('ProcessPath::messages.action') !!}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

@endsection
@section('footer-script')
    @include('partials.datatable-js')
    <script src="{{ asset('assets/scripts/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />

    <script language="javascript">
        $('.mydesk').click(function() {
            $('#processDropdown').show();
        });

        $('.favorite_list').click(function() {
            $('#processDropdown').hide();
        });

        $('.search_by_keyword').click(function() {
            $('#processDropdown').hide();
        });

        $(function() {
            // Global search or dashboard search option
            @if (isset($search_by_keyword) && !empty($search_by_keyword))
                $('#search_by_keyword').trigger('click');
                return false;
            @endif

            var table = [];

            /**
             * set selected ProcessType in session
             * load data by ProcessType, on change ProcessType select box
             * @type {jQuery}
             */
            $('.ProcessType').change(function() {
                var process_type_id = $(this).val();
                sessionStorage.setItem("process_type_id", process_type_id);

                table_desk = $('#table_desk').DataTable({
                iDisplayLength: '{{ $number_of_rows }}',
                processing: true,
                serverSide: true,
                searching: true,
                responsive: true,
                "bDestroy": true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: '{{ route('process.getList', ['-1000', 'my-desk']) }}',
                    method: 'get',
                    data: function(d) {
                        d.process_type_id = "{{ Encryption::encodeId($process_type_id) }}";
                        d.process_type_id_app = process_type_id;
                    }
                },
                columns: [{
                        data: 'tracking_no',
                        name: 'tracking_no',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'user_desk.desk_name',
                        name: 'user_desk.desk_name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'process_name',
                        name: 'process_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'json_object',
                        name: 'json_object',
                        orderable: false,
                    },
                    {
                        data: 'process_status.status_name',
                        name: 'process_status.status_name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                "aaSorting": []
            });

            });
            $('.ProcessType').trigger('change');

            /**
             * table desk script
             * @type {jQuery}
             */
            table_desk = $('#table_desk').DataTable({
                iDisplayLength: '{{ $number_of_rows }}',
                processing: true,
                serverSide: true,
                searching: true,
                responsive: true,
                "bDestroy": true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: '{{  route('process.getList', (Auth::user()->desk_id != 0)? ['-1000', 'my-desk'] : ['-1000', 'my-application'])   }}',
                    method: 'get',
                    data: function(d) {
                        // d.process_type_id = parseInt(sessionStorage.getItem("process_type_id"));
                        d.process_type_id = "{{ Encryption::encodeId($process_type_id) }}";
                    }
                },
                columns: [{
                        data: 'tracking_no',
                        name: 'tracking_no',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'user_desk.desk_name',
                        name: 'user_desk.desk_name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'process_name',
                        name: 'process_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'json_object',
                        name: 'json_object',
                        orderable: false,
                    },
                    {
                        data: 'process_status.status_name',
                        name: 'process_status.status_name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                "aaSorting": []
            });


            /**
             * on click Delegation Desk load table with delegated application list
             * @type {jQuery}
             */
            var deleg_list_flag = 0;
            $('.delgDesk').click(function() {
                /**
                 * delegated application list table script
                 * @type {jQuery}
                 */
                if (deleg_list_flag == 0) {
                    deleg_list_flag = 1;
                    $('#table_delg_desk').DataTable({
                        iDisplayLength: '{{ $number_of_rows }}',
                        processing: true,
                        serverSide: true,
                        searching: true,
                        responsive: true,
                        ajax: {
                            url: '{{ route('process.getList', ['-1000', 'my-delg-desk']) }}',
                            method: 'get',
                            data: function(d) {
                                d._token = $('input[name="_token"]').val();
                                // d.process_type_id = parseInt(sessionStorage.getItem("process_type_id"));
                                d.process_type_id = "{{ Encryption::encodeId($process_type_id) }}";
                            }
                        },
                        columns: [{
                                data: 'tracking_no',
                                name: 'tracking_no',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'user_desk.desk_name',
                                name: 'user_desk.desk_name',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'process_name',
                                name: 'process_name',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'json_object',
                                name: 'json_object',
                                orderable: false,
                            },
                            {
                                data: 'process_status.status_name',
                                name: 'process_status.status_name',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'updated_at',
                                name: 'updated_at',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ],
                        "aaSorting": []
                    });
                }

            });


            /**
             * on click favourite Desk load table with favourite application list
             * @type {jQuery}
             */
            var fav_list_flag = 0;
            $('.favorite_list').click(function() {
                /**
                 * delegated application list table script
                 * @type {jQuery}
                 */
                if (fav_list_flag == 0) {
                    fav_list_flag = 1;
                    $('#favorite_list').DataTable({
                        iDisplayLength: '{{ $number_of_rows }}',
                        processing: true,
                        serverSide: true,
                        searching: true,
                        responsive: true,
                        ajax: {
                            url: '{{ route('process.getList', ['-1000', 'favorite_list']) }}',
                            method: 'get',
                            data: function(d) {
                                d._token = $('input[name="_token"]').val();
                                // d.process_type_id = parseInt(sessionStorage.getItem("process_type_id"));
                                d.process_type_id = "{{ Encryption::encodeId($process_type_id) }}"
                            }
                        },
                        columns: [{
                                data: 'tracking_no',
                                name: 'tracking_no',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'user_desk.desk_name',
                                name: 'user_desk.desk_name',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'process_name',
                                name: 'process_name',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'json_object',
                                name: 'json_object',
                                orderable: false,
                            },
                            {
                                data: 'process_status.status_name',
                                name: 'process_status.status_name',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'updated_at',
                                name: 'updated_at',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ],
                        "aaSorting": []
                    });
                }
            });


            $('.myapplication').click(function() {
                /**
                 * delegated application list table script
                 * @type {jQuery}
                 */

                    $('#my_application_desk').DataTable({
                        iDisplayLength: '{{ $number_of_rows }}',
                        processing: true,
                        serverSide: true,
                        searching: true,
                        responsive: true,
                        bDestroy: true,
                        ajax: {
                            url: '{{ route('process.getList', ['-1000', 'my-application']) }}',
                            method: 'get',
                            data: function(d) {
                                d._token = $('input[name="_token"]').val();
                                d.process_type_id = "{{ Encryption::encodeId($process_type_id) }}";
                                d.is_my_application = "1";
                            }
                        },
                        columns: [{
                                data: 'tracking_no',
                                name: 'tracking_no',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'user_desk.desk_name',
                                name: 'user_desk.desk_name',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'process_name',
                                name: 'process_name',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'json_object',
                                name: 'json_object',
                                orderable: false,
                            },
                            {
                                data: 'process_status.status_name',
                                name: 'process_status.status_name',
                                orderable: false,
                                searchable: true
                            },
                            {
                                data: 'updated_at',
                                name: 'updated_at',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ],
                        "aaSorting": []
                    });


            });


        });

        $('body').on('click', '.favorite_process', function() {

            var process_list_id = $(this).attr('id');
            $(this).css({
                "color": "#f0ad4e"
            }).removeClass('fa-star-o favorite_process').addClass('fa fa-star remove_favorite_process');
            $(this).attr("title", "Added to your favorite list");
            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "POST",
                url: "<?php echo url('/process/favorite-data-store'); ?>",
                data: {
                    _token: _token,
                    process_list_id: process_list_id
                },
                success: function(response) {
                    if (response.responseCode == 1) {
                    }
                }
            });
        });

        $('body').on('click', '.remove_favorite_process', function() {

            var process_list_id = $(this).attr('id');
            $(this).css({
                "color": ""
            }).removeClass('fa fa-star remove_favorite_process').addClass('fa fa-star-o favorite_process');
            $(this).attr("title", "Add to your favorite list");


            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "POST",
                url: "<?php echo url('/process/favorite-data-remove'); ?>",
                data: {
                    _token: _token,
                    process_list_id: process_list_id
                },
                success: function(response) {
                    btn.html(btn_content);
                    if (response.responseCode == 1) {
                    }
                }
            });
        });

        @if (in_array(\App\Libraries\CommonFunction::getUserType(), ['4x404', '17x171']))

            //current used the code for update batch
            $('body').on('click', '.is_delegation', function() {
                var is_blank_page = $(this).attr('target');
                var _token = $('input[name="_token"]').val();
                var current_process_id = $(this).parent().parent().find('.batchInputStatus').val();

                $.ajax({
                    type: "get",
                    url: "<?php echo url('/'); ?>/process/batch-process-set",
                    async: false,
                    data: {
                        _token: _token,
                        is_delegation: true,
                        current_process_id: current_process_id,
                    },
                    success: function(response) {

                        if (response.responseType == 'single') {
                            // window.location.href = response.url;
                            if (is_blank_page === undefined) {
                                window.location.href = response.url;
                            }
                            window.open(response.url, '_blank');
                        }
                        if (response.responseType == false) {
                            toastr.error('did not found any data for search list!');
                        }
                    }

                });
                return false;
            });

                {{--$('body').on('click', '.common_batch_update', function() {--}}
                {{--    var current_process_id = $(this).parent().find('.batchInput').val();--}}
                {{--    process_id_array = [];--}}
                {{--    $('.batchInput').each(function(i, obj) {--}}
                {{--        process_id_array.push(this.value);--}}
                {{--    });--}}
                {{--    process_id_array = process_id_array.filter(onlyUnique);--}}
                {{--    var _token = $('input[name="_token"]').val();--}}
                {{--    $.ajax({--}}
                {{--        type: "get",--}}
                {{--        url: "<?php echo url('/'); ?>/process/batch-process-set",--}}
                {{--        async: false,--}}
                {{--        data: {--}}
                {{--            _token: _token,--}}
                {{--            process_id_array: process_id_array,--}}
                {{--            current_process_id: current_process_id,--}}
                {{--        },--}}
                {{--        success: function(response) {--}}
                {{--            if (response.responseType == 'single') {--}}
                {{--                // return false--}}
                {{--                window.location.href = response.url;--}}
                {{--            }--}}
                {{--            if (response.responseType == false) {--}}
                {{--                toastr.error('did not found any data for search list!');--}}
                {{--            }--}}
                {{--        }--}}

                {{--    });--}}
                {{--    return false;--}}
                {{--});--}}


            function onlyUnique(value, index, self) {
                return self.indexOf(value) === index;
            }
        @endif
        @if(\App\Libraries\CommonFunction::getUserType() != '5x505')
        $('body').on('click', '.ProcessType', function() {
            var current_process_id = $(this).val();
            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "post",
                url: "<?php echo url('/'); ?>/process/get-servicewise-count",
                async: false,
                data: {
                    _token: _token,
                    current_process_id: current_process_id,
                },
                success: function(response) {
                    if (response) {
                        $("#statuswiseAppsDiv").html(response).show();
                    }
                }

            });

        });
        @endif

        function checkGuide(){
            @if(isset($process['id']) && !empty($process['id']))

            let url = '<?php echo URL::to(env('APP_URL').'/client/process/'.$process['form_url'].'/add/'.\App\Libraries\Encryption::encodeId($process['id']));  ?>';
            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "POST",
                url: "<?php echo url(env('APP_URL').'/process/check-guide'); ?>",
                data: {
                    _token: _token,
                },
                success: function(response) {
                    // btn.html(btn_content);
                    if (response.responseCode == 1) {
                        if(response.data.data.pilgrim_type_id == 6){
                            window.location.href =url;
                        }else{
                            alert("Only Guide Can set Travel Plan");
                        }
                    }
                }
            });
            @endif


        }

        // Service Boxes
        $('body').on('click', '.serviceBoxes', function() {
            var current_process_id = $(this).attr('id');
            var _token = $('input[name="_token"]').val();
            $.ajax({
                type: "post",
                url: "<?php echo url('/'); ?>/process/get-servicewise-count",
                async: false,
                data: {
                    _token: _token,
                    current_process_id: current_process_id,
                },
                success: function(response) {
                    if (response) {
                        $("#statuswiseAppsDiv").html(response).show();
                        $('select[name="ProcessType"]').val(current_process_id);
                        var processTypeValue = $('select[name="ProcessType"]').val(); // Verify the change
                        $('#search_process').click();
                    }
                }
            });

        });

    </script>
    @yield('footer-script2')
@endsection
