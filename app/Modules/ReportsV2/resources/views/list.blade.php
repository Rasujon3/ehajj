<?php $accessMode = ACL::getAccsessRight('reportv2');
if (!ACL::isAllowed($accessMode, 'V')) die('no access right!');
?>

@extends('layouts.admin')

@section('header-resources')
    @include('partials.datatable-css')
    <style>
        .small-box {
            margin-bottom: 0;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')

    @include('partials.messages')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-magenta border border-magenta">

                <div class="card-header">
                    <div class="float-left p-2">
                        <div class="bd-card-head">
                            <div class="bd-card-title">
                                    <span class="title-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.83 4.17 22 7.81 22H16.18C19.82 22 21.99 19.83 21.99 16.19V7.81C22 4.17 19.83 2 16.19 2ZM7.63 18.15C7.63 18.56 7.29 18.9 6.88 18.9C6.47 18.9 6.13 18.56 6.13 18.15V16.08C6.13 15.67 6.47 15.33 6.88 15.33C7.29 15.33 7.63 15.67 7.63 16.08V18.15ZM12.75 18.15C12.75 18.56 12.41 18.9 12 18.9C11.59 18.9 11.25 18.56 11.25 18.15V14C11.25 13.59 11.59 13.25 12 13.25C12.41 13.25 12.75 13.59 12.75 14V18.15ZM17.87 18.15C17.87 18.56 17.53 18.9 17.12 18.9C16.71 18.9 16.37 18.56 16.37 18.15V11.93C16.37 11.52 16.71 11.18 17.12 11.18C17.53 11.18 17.87 11.52 17.87 11.93V18.15ZM17.87 8.77C17.87 9.18 17.53 9.52 17.12 9.52C16.71 9.52 16.37 9.18 16.37 8.77V7.8C13.82 10.42 10.63 12.27 7.06 13.16C7 13.18 6.94 13.18 6.88 13.18C6.54 13.18 6.24 12.95 6.15 12.61C6.05 12.21 6.29 11.8 6.7 11.7C10.07 10.86 13.07 9.09 15.45 6.59H14.2C13.79 6.59 13.45 6.25 13.45 5.84C13.45 5.43 13.79 5.09 14.2 5.09H17.13C17.17 5.09 17.2 5.11 17.24 5.11C17.29 5.12 17.34 5.12 17.39 5.14C17.44 5.16 17.48 5.19 17.53 5.22C17.56 5.24 17.59 5.25 17.62 5.27C17.63 5.28 17.63 5.29 17.64 5.29C17.68 5.33 17.71 5.37 17.74 5.41C17.77 5.45 17.8 5.48 17.81 5.52C17.83 5.56 17.83 5.6 17.84 5.65C17.85 5.7 17.87 5.75 17.87 5.81C17.87 5.82 17.88 5.83 17.88 5.84V8.77H17.87Z" fill="white"/>
                                        </svg>
                                    </span>
                                <h3>List of Report</h3>
                            </div>
                        </div>
                    </div>
                    <div class="float-right">
                        @if(Auth::user()->user_type == '1x101' || Auth::user()->user_type == '15x151' || Auth::user()->user_type == '7x707')
                            @if(ACL::getAccsessRight('reportv2','A'))
                                <a class="" href="{{ url('/reportv2/create') }}">
                                    {!! Form::button('<i class="fa fa-plus"></i> <b>Add New Report</b>', array('type' => 'button', 'class' => 'btn btn-info')) !!}
                                </a>
                            @endif
                        @endif
                    </div>
                    <div class="clearfix"></div>
                </div>
                <!-- /.panel-heading -->
                <div class="card-body">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a data-toggle="tab" class="nav-link active" href="#list_1" aria-expanded="true">
                                    <b>Recent</b>
                                </a>
                            </li>
                            {{--                            <li class="nav-item">--}}
                            {{--                                <a data-toggle="tab" href="#list_2" aria-expanded="true">--}}
                            {{--                                    <b>My Favourite</b>--}}
                            {{--                                </a>--}}
                            {{--                            </li>--}}
                            <li class="nav-item all_reports">
                                <a class="nav-link" data-toggle="tab" href="#list_3" aria-expanded="false">
                                    <b>All Reports</b>
                                </a>
                            </li>
                            <li class="nav-item unpublished_reports nav-link">
                                <a data-toggle="tab" href="#list_4" aria-expanded="false">
                                    <b>Unpublished</b>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div id="list_1" class="tab-pane active">
                            <div class="card card-default">
                                <div class="card-header">
                                    <label class="card-title" style="font-size: 18px;">Last 4 report</label>
                                </div>
                                <div class="card-body">

                                    @foreach($getLast4Reports as $item)
                                        <a href="{!! url('reportv2/view/'. Encryption::encode($item->report_id."/Published" )) !!}">
                                            <div class="form-group col-lg-3 col-md-3 col-xs-6">
                                                <div class="small-box"
                                                     +
                                                     style="color: #fff; border-radius: 10px; padding: 15px; background-image: linear-gradient(to right, #7C5CF5, #9B8BF7);">
                                                    <div class=" text-center">
                                                        <i class="fa fa-file fa-3x"></i>
                                                    </div>
                                                    <br>
                                                    <div class="row text-center">
                                                        <label for="">{{ substr($item->report_title, 0,  30) }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach

                                </div>
                            </div>


                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <label style="font-size: 18px;">Favourites</label>
                                </div>
                                <div class="panel-body">

                                    @foreach($getFavouriteList['fav_report'] as $favourite)
                                        <a href="{!! url('reportv2/view/'. Encryption::encode($favourite->report_id."/Favourites" )) !!}">
                                            <div class="form-group col-lg-3 col-md-3 col-xs-6">
                                                <div class="small-box"
                                                     style="color: #fff; border-radius: 10px; padding: 15px; background-image: linear-gradient(to right, #7C5CF5, #9B8BF7);">
                                                    <div class="row text-center">
                                                        <i class="fa fa-files-o fa-3x"></i>
                                                    </div>
                                                    <br>
                                                    <div class="row text-center">
                                                        <label for="">{{ substr($favourite->report_title, 0,  30) }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach

                                </div>
                            </div>

                            @foreach($Categories as $row)
                                <?php
                                $singleData = explode('@', $row->groupData);
                                ?>
                                <div class="card card-default">
                                    <div class="card-header">
                                        <label style="font-size: 18px;"
                                               class="card-title">{{$row->category_name}}</label>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($singleData as $singleRow)
                                                <?php
                                                $value = explode('=', $singleRow);
                                                ?>

                                                <div class=" col-lg-3 col-md-3 col-xs-6 ">
                                                    <a href="{!! url('reportv2/view/'. Encryption::encode($value[0]."/Published" )) !!}">
                                                        <div class="small-box"
                                                             style="color: #fff; border-radius: 10px; padding: 15px; background-image: linear-gradient(to right, #69D4D4, #6CD2D5);">
                                                            <div class=" text-center">
                                                                <i class="fa fa-file fa-3x"></i>
                                                            </div>
                                                            <br>
                                                            <div class=" text-center">
                                                               <label for="">{{ substr($value[1], 0,  30) ??""}}</label>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            @endforeach

                            <div class="card panel-default">
                                <div class="card-header">
                                    <label class="card-title" style="font-size: 18px;">Uncategorized</label>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                    @foreach($uncategorized as $item)
                                        <div class=" col-lg-3 col-md-3 col-xs-6">
                                            <a href="{!! url('reportv2/view/'. Encryption::encode($item->report_id."/Published" )) !!}">
                                                <div class="small-box"
                                                     style="color: #fff; border-radius: 10px; padding: 15px; background-image: linear-gradient(to right, #EC6060, #FC8170);">
                                                    <div class=" text-center">
                                                        <i class="fa fa-file fa-3x"></i>
                                                    </div>
                                                    <br>
                                                    <div class="text-center">
                                                        <label for="">{{ substr($item->report_title, 0,  30) }}</label>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            </div>

                            {{--                            <div class="card panel-default">--}}
                            {{--                                <div class="panel-heading">--}}
                            {{--                                    <label style="font-size: 18px;">Published</label>--}}
                            {{--                                </div>--}}
                            {{--                                <div class="card-body">--}}

                            {{--                                    @foreach($publishedReports as $published)--}}
                            {{--                                        <a href="{!! url('reportv2/view/'. Encryption::encode($published->report_id."/Published" )) !!}">--}}
                            {{--                                            <div class="form-group col-lg-3 col-md-3 col-xs-6">--}}
                            {{--                                                <div class="small-box"--}}
                            {{--                                                     style="color: #fff; border-radius: 10px; padding: 15px; background-image: linear-gradient(to right, #5373DF, #458DDD);">--}}
                            {{--                                                    <div class="row text-center">--}}
                            {{--                                                        <i class="fa fa-files-o fa-3x"></i>--}}
                            {{--                                                    </div>--}}
                            {{--                                                    <br>--}}
                            {{--                                                    <div class="row text-center">--}}
                            {{--                                                        <label for="">{{$published->report_title}}</label>--}}
                            {{--                                                    </div>--}}
                            {{--                                                </div>--}}
                            {{--                                            </div>--}}
                            {{--                                        </a>--}}
                            {{--                                    @endforeach--}}

                            {{--                                </div>--}}
                            {{--                            </div>--}}

                        </div>
                        {{--                        <div id="list_2" class="tab-pane">--}}
                        {{--                            <div class="panel-body">--}}
                        {{--                                <div class="table-responsive">--}}
                        {{--                                    <table id="fav_list" class="table table-striped table-bordered dt-responsive nowrap"--}}
                        {{--                                           cellspacing="0" width="100%">--}}
                        {{--                                        <thead>--}}
                        {{--                                        <tr>--}}
                        {{--                                            <th>Title</th>--}}
                        {{--                                            <th>Category</th>--}}
                        {{--                                            <th>Last Modified</th>--}}
                        {{--                                            <th>Action</th>--}}
                        {{--                                        </tr>--}}
                        {{--                                        </thead>--}}
                        {{--                                        <tbody>--}}
                        {{--                                        @foreach($getFavouriteList['fav_report'] as $row)--}}
                        {{--                                            <tr>--}}
                        {{--                                                <td>{!! $row->report_title !!}</td>--}}
                        {{--                                                <td>{!! $row->category_name !!}</td>--}}
                        {{--                                                <td>{!! date('d-m-Y', strtotime($row->updated_at)) !!}</td>--}}
                        {{--                                                <td>--}}
                        {{--                                                    @if(\App\Libraries\UtilFunction::isAllowedToViewFvrtReport($row->report_id))--}}
                        {{--                                                        @if(ACL::getAccsessRight('reportv2','V'))--}}
                        {{--                                                            <a href="{!! url('reportv2/view/'. Encryption::encode($row->report_id."/Favourites" )) !!}"--}}
                        {{--                                                               class="btn btn-xs btn-primary">--}}
                        {{--                                                                <i class="fa fa-folder-open-o"></i> Open--}}
                        {{--                                                            </a>--}}
                        {{--                                                        @endif--}}
                        {{--                                                        @if(ACL::getAccsessRight('report','E'))--}}
                        {{--                                                            {!! link_to('reportv2/edit/'. Encryption::encodeId($row->report_id),'Edit',['class' => 'btn btn-default btn-xs']) !!}--}}
                        {{--                                                        @endif--}}
                        {{--                                                    @endif--}}
                        {{--                                                </td>--}}
                        {{--                                            </tr>--}}
                        {{--                                        @endforeach--}}
                        {{--                                        </tbody>--}}
                        {{--                                    </table>--}}
                        {{--                                </div>--}}
                        {{--                                <!-- /.table-responsive -->--}}
                        {{--                            </div>--}}
                        {{--                            <!-- /.panel-body -->--}}
                        {{--                        </div>--}}
                        <div id="list_3" class="tab-pane all_reports">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="list" class="table table-striped table-bordered dt-responsive nowrap"
                                           cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Last Modified</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($getList['result'] as $row)
                                            <tr>
                                                <td>{!! $row->report_title !!}</td>
                                                <td>{!! $row->category_name !!}</td>
                                                <td>{!! date('d-m-Y', strtotime($row->updated_at)) !!}</td>
                                                <td>
                                                    @if(ACL::getAccsessRight('reportv2','V'))
                                                        <?php
                                                        $status = $row->status == 1 ? "Published" : "unpublished"
                                                        ?>
                                                        <a href="{!! url('reportv2/view/'. Encryption::encode($row->report_id."/".$status )) !!}"
                                                           class="btn btn-xs btn-primary">
                                                            <i class="fa fa-folder-open-o"></i> Open
                                                        </a>
                                                    @endif
                                                    @if(ACL::getAccsessRight('reportv2','E'))
                                                        {!! link_to('reportv2/edit/'. Encryption::encodeId($row->report_id),'Edit',['class' => 'btn btn-info btn-xs']) !!}

                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.panel-body -->
                        </div>
                        <div id="list_4" class="tab-pane unpublished_reports">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table cellspacing="0" width="100%"
                                           class="table table-responsive table-striped table-bordered nowrap">
                                        <thead>
                                        <tr>
                                            <td>Title</td>
                                            <th>Category</th>
                                            <th>Last Modified</th>
                                            <td>Action</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($getUnpublishedList as $row)
                                            <tr>
                                                <td>{!! $row->report_title !!}</td>
                                                <td>{!! $row->category_name !!}</td>
                                                <td>{!! date('d-m-Y', strtotime($row->updated_at)) !!}</td>
                                                <td>
                                                    @if(Auth::user()->user_type == '1x101' || Auth::user()->user_type == '15x151' || Auth::user()->user_type == '7x707')
                                                        <?php
                                                        $status = $row->status == 1 ? "Published" : "unpublished"
                                                        ?>
                                                        <a href="{!! url('reportv2/view/'.  Encryption::encode($row->report_id."/".$status )) !!}"
                                                           class="btn btn-xs btn-primary">
                                                            <i class="fa fa-folder-open-o"></i> Open
                                                        </a>
                                                        {!! link_to('reportv2/edit/'. Encryption::encodeId($row->report_id),'Edit',['class' => 'btn btn-info btn-xs']) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel -->
        </div>
    </div>
    <!-- /.col-lg-12 -->

@endsection

@section('footer-script')

    @include('partials.datatable-js')

    <script>

        $(function () {
            $('#list').DataTable({
                "paging": true,
                "lengthChange": true,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "iDisplayLength": 25
            });
        });

        $(function () {
            $('#fav_list').DataTable({
                "paging": true,
                "lengthChange": true,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "iDisplayLength": 25
            });
        });

    </script>
@endsection
