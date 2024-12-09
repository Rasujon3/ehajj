@extends('layouts.admin')
@section('header-resources')
    <link rel="stylesheet" href="{{ asset("assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css") }}"/>
    <link rel="stylesheet" href="{{ asset("assets/plugins/select2.min.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/plugins/intlTelInput/css/intlTelInput.min.css") }}"/>
    @include('partials.datatable-css')
    <style>
        label {
            color: gray;
            font-size: 16px;
            font-weight: 400;
        }

        p {
            font-size: 16px;
            font-weight: 400;
        }

        div button.editBtn:hover {
            background-color: #F5F5F5;
        }

        .col-centered {
            float: none;
            margin: 0 auto;
        }

        .signature-upload-input {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            height: 100%;
            width: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 5;
            background-color: blue;
        }

        .sign_div {
            position: relative;
        }

        .signature-upload {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: center;
            -webkit-justify-content: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            cursor: pointer;
            overflow: hidden;
            height: 160px;
            width: 100%;
            max-width: 100%;
            padding: 5px 10px;
            font-size: 1rem;
            text-align: center;
            color: #000;
            background-color: #F5FAFE;
            border-radius: 0 !important;
            border: 0;
            /*box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);*/
            font-weight: 400;
            outline: 1px dashed #ccc;
            margin-bottom: 5px;
        }

        .image-upload figure {
            position: relative;
            cursor: pointer;
        }

        .image-upload figure figcaption {
            position: absolute;
            bottom: 0;
            color: #fff;
            width: 100%;
            padding-left: 9px;
            padding-bottom: 5px;
            text-shadow: 0 0 10px #000;
        }

        .panel-heading .accordion-toggle:after {
            /* symbol for "opening" panels */
            font-family: 'Glyphicons Halflings'; /* essential for enabling glyphicon */
            content: "\e114"; /* adjust as needed, taken from bootstrap.css */
            float: right; /* adjust as needed */
            color: grey; /* adjust as needed */
        }

        .card-header .accordion-toggle:after {
            /* symbol for "opening" panels */
            font-family: 'Glyphicons Halflings'; /* essential for enabling glyphicon */
            content: "\e114"; /* adjust as needed, taken from bootstrap.css */
            float: right; /* adjust as needed */
            color: grey; /* adjust as needed */
        }

        .panel-heading .accordion-toggle.collapsed:after {
            /* symbol for "collapsed" panels */
            content: "\e080"; /* adjust as needed, taken from bootstrap.css */
        }

        .card-header .accordion-toggle.collapsed:after {
            /* symbol for "collapsed" panels */
            content: "\e080"; /* adjust as needed, taken from bootstrap.css */
        }

        @media (min-width: 767px) {
            .addressField {
                width: 79.5%;
                float: right
            }
        }

        @media (max-width: 480px) {
            .panel-body {
                padding: 10px 10px !important;
            }

            .card-body {
                padding: 10px 10px !important;
            }

            .form-group {
                margin: 0;
            }

            .section_head {
                font-size: 15px !important;
                font-weight: 400;
                /*margin-top: 5px;*/
            }

            label {
                font-weight: normal;
                font-size: 14px;
                margin-top: 5px;
            }

            .mg-lr {
                padding-left: 15px;
                padding-right: 15px;
            }

            .title {
                font-size: 22px !important;
            }

            .sm_font {
                font-size: 14px;
            }

            .col-centered-xs {
                display: flex;
                justify-content: center;
            }

            .img_sm {
                width: 75px;
            }

            .ceoInfoDirector {
                margin-right: 15px;
            }

            .ceoInfoDirector2 {
                margin-right: -15px;
            }
        }

        .email {
            font-family: Arial !important;
        }
    </style>
@endsection
@section('content')

    <?php
    //        $a = 0;
    //        dd(empty($a));
    //        dd($companyProfile);

    //        if(empty($companyProfile->org_nm))
    //    if ($companyProfile->user_first_name == '' || Auth::user()->user_last_name == '' || Auth::user()->user_mobile == '' || Auth::user()->user_pic == '') {
    //
    //    }
    if ($companyProfile->org_nm)
        $company_id = \App\Libraries\Encryption::encodeId($companyProfile->id);
    $is_complete = $companyProfile->is_complete;
    ?>

    @include('partials.messages')
    <div class="modal fade" id="ImageUploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="myModalLabel"> Photo resize</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="upload_crop_area" class="center-block" style="padding-bottom: 25px"></div>
                </div>
                <div class="modal-footer" id="modal-footer">
                    <div id="cropping_msg" class="alert alert-info text-center hidden">
                        <i class="fa fa-spinner fa-pulse"></i> Please wait, Face detecting
                    </div>
                    <div id="button_area">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">{{trans('CompanyProfile::messages.close')}}</button>
                        <button type="button" id="cropImageBtn" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content load_modal"></div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-12">

                {{--Company Profile--}}
                <div class="card card-default" style="border-radius: 10px;">
                    <div class="card-header" style="padding: 10px 15px">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="card-title"
                                   style="font-size: 32px; font-weight: 400">{!!trans('CompanyProfile::messages.company_profile')!!}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="float-right">
                                    <button type="button" class="btn btn-default float-right" style="margin-bottom: 5px"
                                            id="expandAll" onclick="expandAll()">Full View
                                    </button>
                                    <button type="button" class="btn btn-default float-right hidden"
                                            style="margin-bottom: 5px" id="collapseAll" onclick="collapseAll()">Collapse
                                    </button>
                                    <br>
                                    <span class="float-right">সর্বশেষ আপডেটের তারিখ :  <span id="last_updated_date"
                                                                                             class="input_ban"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" style="padding: 0 15px">
                        <div class="card-grousp" id="accordion">
                            {{--General Info--}}
                            <div class="card card-default">
                                <div class="">
                                    <a href="#general_information_view" data-toggle="collapse" role="button"
                                       aria-expanded="true">
                                        <div class="card-header">
                                            <p class="section_head card-title"
                                               style="margin: 0; color: #636363; font-size: 18px; font-weight: 400">
                                                <i class="fa fa-chevron-down" style="margin-right: 15px;"></i>
                                                {!!trans('CompanyProfile::messages.general_information')!!}</p>

                                        </div>
                                    </a>
                                    <div id="general_information_view" class="collapse in show">
                                        <div class="card-body" style="padding: 15px 25px;">
                                            <div id="general_info" class="row">
                                                <div class="col-md-2">

                                                </div>
                                                <div class="col-md-8">
                                                    <h3 id="company_name_bn"
                                                        style="color: #462A73; font-weight: 400"></h3>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <label
                                                                for="">{!! trans('CompanyProfile::messages.submit_date') !!}</label>
                                                        </div>
                                                        <div class="col-5">
                                                            : <span id="submit_date" class="input_ban"></span>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-5">
                                                            <label
                                                                for="">{!! trans('CompanyProfile::messages.reg_type') !!}</label>
                                                        </div>
                                                        <div class="col-5">
                                                            <p id="reg_type"></p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <label
                                                                for="">{!! trans('CompanyProfile::messages.invest_type') !!}</label>
                                                        </div>
                                                        <div class="col-5">
                                                            <p id="invest_type"></p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <label
                                                                for="">{!! trans('CompanyProfile::messages.total_investment') !!}</label>
                                                        </div>
                                                        <div class="col-5">
                                                            : <span id="total_investment_data" class="input_ban"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <label
                                                                for="">{!! trans('CompanyProfile::messages.industrial_sector') !!}</label>
                                                        </div>
                                                        <div class="col-5">
                                                            <p id="industrial_sector"></p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <label
                                                                for="">{!! trans('CompanyProfile::messages.industrial_sub_sector') !!}</label>
                                                        </div>
                                                        <div class="col-5">
                                                            <p id="industrial_sub_sector"></p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <label
                                                                for="">{!! trans('CompanyProfile::messages.company_type') !!}</label>
                                                        </div>
                                                        <div class="col-5">
                                                            <p id="company_type"></p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <label
                                                                for="">{!! trans('CompanyProfile::messages.investing_country') !!}</label>
                                                        </div>
                                                        <div class="col-5">
                                                            <p class="sm_font" id="investing_country"></p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <label
                                                                for="">{!! trans('CompanyProfile::messages.industrial_class') !!}</label>
                                                        </div>
                                                        <div class="col-5">
                                                            <p id="industrial_class"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-right">
                                                    <button class="btn btn-lg editBtn" id="showGeneralEdit"
                                                            style="border: none; background: white; color: #462A73"
                                                            onclick="showGeneralEdit()">
                                                        <i class="fa fa-edit"></i> EDIT
                                                    </button>
                                                </div>
                                            </div>

                                            {!! Form::open(array('url' => '','method' => 'post', 'class' => 'form-horizontal', 'id' => 'generalInfoEditForm', 'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                                            <div id="general_info_edit" class="row" style="display: none">

                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group row mr-md-1 ">
                                                                {!! Form::label('company_name_bangla', trans('CompanyProfile::messages.company_name_bangla'),
                                                                ['class'=>'col-md-5 required-star']) !!}
                                                                <div
                                                                    class="col-md-7 {{$errors->has('company_name_bangla') ? 'has-error': ''}}">
                                                                    {!! Form::text('company_name_bangla', '', ['placeholder' => trans("CompanyProfile::messages.write_company_name_bangla"),
                                                                   'class' => 'form-control input-md required bnEng','id'=>'company_name_bangla']) !!}
                                                                    {!! $errors->first('company_name_bangla','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group row  ">
                                                                {!! Form::label('company_name_english', trans('CompanyProfile::messages.company_name_english'),
                                                                ['class'=>'col-md-5 required-star']) !!}
                                                                <div
                                                                    class="col-md-7 {{$errors->has('company_name_english') ? 'has-error': ''}}">
                                                                    {!! Form::text('company_name_english','', ['placeholder' => trans("CompanyProfile::messages.write_company_name_english"),
                                                                   'class' => 'form-control input-md required bnEng','id'=>'company_name_english']) !!}
                                                                    {!! $errors->first('company_name_english','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group row mr-md-1 ">
                                                                {!! Form::label('reg_type_id', trans('CompanyProfile::messages.reg_type'),
                                                                ['class'=>'col-md-5 required-star']) !!}
                                                                <div
                                                                    class="col-md-7 {{$errors->has('reg_type_id') ? 'has-error': ''}}">
                                                                    {!! Form::select('reg_type_id', $regType, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'reg_type_id']) !!}
                                                                    {!! $errors->first('reg_type_id','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group row ">
                                                                {!! Form::label('company_type_id', trans('CompanyProfile::messages.company_type'),
                                                                ['class'=>'col-md-5 required-star']) !!}
                                                                <div
                                                                    class="col-md-7 {{$errors->has('company_type') ? 'has-error': ''}}">
                                                                    {!! Form::select('company_type_id', $companyType, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'company_type_id']) !!}
                                                                    {!! $errors->first('company_type_id','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">

                                                        <div class="col-md-6">
                                                            <div class="form-group row mr-md-1 ">
                                                                {!! Form::label('investment_type_id', trans('CompanyProfile::messages.invest_type'),
                                                                ['class'=>'col-md-5 required-star']) !!}
                                                                <div
                                                                    class="col-md-7 {{$errors->has('company_type') ? 'has-error': ''}}">
                                                                    {!! Form::select('investment_type_id', $investmentType, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'investment_type_id']) !!}
                                                                    {!! $errors->first('investment_type_id','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group row  ">
                                                                {!! Form::label('investing_country_id', trans('CompanyProfile::messages.investing_country'),
                                                                ['class'=>'col-md-5 required-star']) !!}
                                                                <div
                                                                    class="col-md-7 {{$errors->has('investing_country_id') ? 'has-error': ''}}">
                                                                    {!! Form::select('investing_country_id[]', $nationality, '', ['class' =>'form-control input-md investing_country_id required', 'placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'investing_country_id']) !!}
                                                                    {!! $errors->first('investing_country_id','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group row mr-md-1 ">
                                                                {!! Form::label('total_investment', trans('CompanyProfile::messages.total_investment'),
                                                                ['class'=>'col-md-5 required-star']) !!}
                                                                <div
                                                                    class="col-md-7 {{$errors->has('total_investment') ? 'has-error': ''}}">
                                                                    {!! Form::text('total_investment','', ['placeholder' => trans("CompanyProfile::messages.write_total_investment"),
                                                                   'class' => 'form-control input-md onlyNumber input_ban required','id'=>'total_investment']) !!}
                                                                    {!! $errors->first('total_investment','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group row  ">
                                                                {!! Form::label('industrial_category_id', trans('CompanyProfile::messages.industrial_class'),
                                                                ['class'=>'col-md-5 required-star']) !!}
                                                                <div
                                                                    class="col-md-7 {{$errors->has('industrial_category_id') ? 'has-error': ''}}">
                                                                    {!! Form::select('industrial_category_id', $industrialCategory, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'industrial_category_id']) !!}
                                                                    {!! $errors->first('industrial_category_id','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class=" form-group row mr-md-1 ">
                                                                {!! Form::label('industrial_sector_id', trans('CompanyProfile::messages.industrial_sector'),
                                                                ['class'=>'col-md-5 required-star']) !!}
                                                                <div
                                                                    class="col-md-7 {{$errors->has('industrial_sector_id') ? 'has-error': ''}}">
                                                                    {!! Form::select('industrial_sector_id', $industrialSector, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'industrial_sector_id']) !!}
                                                                    {!! $errors->first('industrial_sector_id','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group row ">
                                                                {!! Form::label('industrial_sub_sector_id', trans('CompanyProfile::messages.industrial_sub_sector'),
                                                                ['class'=>'col-md-5 required-star']) !!}
                                                                <div
                                                                    class="col-md-7 {{$errors->has('industrial_sub_sector_id') ? 'has-error': ''}}">
                                                                    {!! Form::select('industrial_sub_sector_id', $industrialSubSector, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'industrial_sub_sector_id']) !!}
                                                                    {!! $errors->first('industrial_sub_sector_id','<span class="help-block">:message</span>') !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <br>
                                                    <div class="form-group">
                                                        <div class="row col-centered-xs">
                                                            <div class="col-md-12 text-center "
                                                                 style="display: inline-block;">
                                                                <button type="button" id="hideGeneralEdit"
                                                                        class="btn btn-default"
                                                                        style="margin-right: 5px;">{{trans('CompanyProfile::messages.close')}}</button>
                                                                <button id="updateGeneralInfoBtn"
                                                                        class="btn btn-primary"
                                                                        onclick="updateGeneralInfo()"
                                                                        style="margin-left: 5px;">{{trans('CompanyProfile::messages.update')}}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>


                                            {!! Form::close() !!}
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--Company office address--}}
                        <div class="card card-default">
                            <a
                                href="#company_office_address_view" data-toggle="collapse"
                                role="button" aria-expanded="false">
                                <div class="card-header">
                                    <p class="section_head card-title"
                                       style="margin: 0; color: #636363; font-size: 18px; font-weight: 400">
                                        <i class="fa fa-chevron-down"
                                           style="margin-right: 15px;"></i> {!!trans('CompanyProfile::messages.company_office_address')!!}
                                    </p>
                                </div>
                            </a>
                            <div id="company_office_address_view" class="expand collapse">
                                <div class="card-body">
                                    <br>
                                    <div id="office_info" class="row">
                                        <div class="col-md-2">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.division') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    <p id="office_division"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.district') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    <p id="office_district"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.thana') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    <p id="office_thana"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.post_code') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    : <span id="office_postcode" class="input_ban"> </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.address') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    <p class="sm_font" id="office_address"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.email') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    <p class="sm_font" id="office_email"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.mobile') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    : <span id="office_mobile" class="input_ban"> </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-right">
                                            <button id="showOfficeEdit" class="btn btn-lg editBtn"
                                                    style="border: none; background: white; color: #462A73"
                                                    onclick="showOfficeEdit()">
                                                <i class="fa fa-edit"></i> EDIT
                                            </button>
                                        </div>
                                    </div>

                                    {!! Form::open(array('url' => '','method' => '', 'class' => 'form-horizontal', 'id' => 'officeInfoEditForm', 'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                                    <div id="office_info_edit" class="row" style="display: none">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_office_division_id', trans('CompanyProfile::messages.division'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_office_division_id') ? 'has-error': ''}}">
                                                            {!! Form::select('company_office_division_id', $divisions, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select_division"),
            'id'=>'company_office_division_id', 'onchange'=>"getDistrictByDivisionId('company_office_division_id', this.value, 'company_office_district_id')"]) !!}
                                                            {!! $errors->first('company_office_division_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row ">
                                                        {!! Form::label('company_office_district_id', trans('CompanyProfile::messages.district'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_office_district_id') ? 'has-error': ''}}">
                                                            {!! Form::select('company_office_district_id', [], '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select_district"),
            'id'=>'company_office_district_id', 'onchange'=>"getThanaByDistrictId('company_office_district_id', this.value, 'company_office_thana_id')" ]) !!}
                                                            {!! $errors->first('company_office_district_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_office_thana_id', trans('CompanyProfile::messages.thana'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_office_thana_id') ? 'has-error': ''}}">
                                                            {!! Form::select('company_office_thana_id', [], '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select_thana"),'id'=>'company_office_thana_id']) !!}
                                                            {!! $errors->first('company_office_thana_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row ">
                                                        {!! Form::label('company_office_postCode', trans('CompanyProfile::messages.post_code'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_office_postCode') ? 'has-error': ''}}">
                                                            {!! Form::text('company_office_postCode','', ['placeholder' => trans("CompanyProfile::messages.write_post_code"),
                                                           'class' => 'form-control input-md onlyNumber input_ban required','id'=>'company_office_postCode']) !!}
                                                            {!! $errors->first('company_office_postCode','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row ">
                                                        {!! Form::label('company_office_address', trans('CompanyProfile::messages.address'),
                                                        ['class'=>'col-md-2 required-star']) !!}
                                                        <div
                                                            class="col-md-10 addressField {{$errors->has('company_office_address') ? 'has-error': ''}}">
                                                            {!! Form::text('company_office_address','', ['placeholder' => trans("CompanyProfile::messages.write_address"),
                                                           'class' => 'form-control input-md required bnEng','id'=>'company_office_address']) !!}
                                                            {!! $errors->first('company_office_address','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_office_email', trans('CompanyProfile::messages.email'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_office_email') ? 'has-error': ''}}">
                                                            {!! Form::text('company_office_email','', ['placeholder' => trans("CompanyProfile::messages.write_email"),
                                                           'class' => 'form-control input-md email required','id'=>'company_office_email']) !!}
                                                            {!! $errors->first('company_office_email','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        {!! Form::label('company_office_mobile', trans('CompanyProfile::messages.mobile_no'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_office_mobile') ? 'has-error': ''}}">
                                                            {!! Form::text('company_office_mobile','', ['placeholder' => trans("CompanyProfile::messages.write_mobile_no"),
                                                           'class' => 'form-control input-md input_ban required','id'=>'company_office_mobile', 'onkeyup' => 'mobile_no_validation(this.id)']) !!}
                                                            {!! $errors->first('company_office_mobile','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <div class="row col-centered-xs">
                                                    <div class="col-md-12 text-center" style="display: inline-block;">
                                                        <button type="button" id="hideOfficeEdit"
                                                                class="btn btn-default"
                                                                style="margin-right: 5px;">{{trans('CompanyProfile::messages.close')}}</button>
                                                        <button id="updateOfficeInfoBtn" class="btn btn-primary"
                                                                onclick="updateOfficeInfo()"
                                                                style="margin-left: 5px;">{{trans('CompanyProfile::messages.update')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                {!! Form::close() !!}
                                <br>
                            </div>
                        </div>
                        {{--Company factory address--}}
                        <div class="card card-default">
                            <a
                                href="#company_factory_address_view" data-toggle="collapse"
                                role="button" aria-expanded="false">
                                <div class="card-header"><p class="section_head card-title" style="margin: 0;
                                 color: #636363; font-size: 18px; font-weight: 400">
                                        <i class="fa fa-chevron-down"
                                           style="margin-right: 15px;"></i> {!!trans('CompanyProfile::messages.company_factory_address_view')!!}
                                    </p></div>
                            </a>
                            <div id="company_factory_address_view" class="expand collapse">
                                <div class="card-body" style="padding: 15px 25px;">
                                    <br>
                                    <div id="factory_info" class="row">
                                        <div class="col-md-2">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.division') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    <p id="factory_division"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.district') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    <p id="factory_district"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.thana') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    <p id="factory_thana"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.post_code') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    : <span id="factory_postcode" class="input_ban"> </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.address') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    <p class="sm_font" id="factory_address"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.email') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    <p class="sm_font" id="factory_email"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-xs-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.mobile') !!}</label>
                                                </div>
                                                <div class="col-md-8 col-xs-7">
                                                    : <span id="factory_mobile" class="input_ban"> </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-right">
                                            <button id="showFactoryEdit" class="btn btn-lg editBtn"
                                                    style="border: none; background: white; color: #462A73"
                                                    onclick="showFactoryEdit()">
                                                <i class="fa fa-edit"></i> EDIT
                                            </button>
                                        </div>
                                    </div>

                                    {!! Form::open(array('url' => '','method' => '', 'class' => 'form-horizontal', 'id' => 'factoryInfoEditForm', 'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                                    <div id="factory_info_edit" class="row" style="display: none">
                                        <div class="col-md-12">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_factory_division_id', trans('CompanyProfile::messages.division'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_factory_division_id') ? 'has-error': ''}}">
                                                            {!! Form::select('company_factory_division_id', $divisions, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select_division"),
            'id'=>'company_factory_division_id', 'onchange'=>"getDistrictByDivisionId('company_factory_division_id', this.value, 'company_factory_district_id')"]) !!}
                                                            {!! $errors->first('company_factory_division_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        {!! Form::label('company_factory_district_id', trans('CompanyProfile::messages.district'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_factory_district_id') ? 'has-error': ''}}">
                                                            {!! Form::select('company_factory_district_id', $districts, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select_district"),
            'id'=>'company_factory_district_id', 'onchange'=>"getThanaByDistrictId('company_factory_district_id', this.value, 'company_factory_thana_id')"]) !!}
                                                            {!! $errors->first('company_factory_district_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_factory_thana_id', trans('CompanyProfile::messages.thana'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_factory_thana_id') ? 'has-error': ''}}">
                                                            {!! Form::select('company_factory_thana_id', $thanas, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select_thana"), 'id'=>'company_factory_thana_id']) !!}
                                                            {!! $errors->first('company_factory_thana_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        {!! Form::label('company_factory_postCode', trans('CompanyProfile::messages.post_code'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_office_postCode') ? 'has-error': ''}}">
                                                            {!! Form::text('company_factory_postCode','', ['placeholder' => trans("CompanyProfile::messages.write_post_code"),
                                                           'class' => 'form-control input-md onlyNumber input_ban required','id'=>'company_factory_postCode']) !!}
                                                            {!! $errors->first('company_factory_postCode','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row ">
                                                        {!! Form::label('company_factory_address', trans('CompanyProfile::messages.address'),
                                                        ['class'=>'col-md-2 required-star']) !!}
                                                        <div
                                                            class="col-md-10 addressField {{$errors->has('company_office_address') ? 'has-error': ''}}">
                                                            {!! Form::text('company_factory_address','', ['placeholder' => trans("CompanyProfile::messages.write_address"),
                                                           'class' => 'form-control input-md required bnEng','id'=>'company_factory_address']) !!}
                                                            {!! $errors->first('company_factory_address','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_factory_email', trans('CompanyProfile::messages.email'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_factory_email') ? 'has-error': ''}}">
                                                            {!! Form::text('company_factory_email','', ['placeholder' => trans("CompanyProfile::messages.write_email"),
                                                           'class' => 'form-control input-md email required','id'=>'company_factory_email']) !!}
                                                            {!! $errors->first('company_factory_email','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        {!! Form::label('company_factory_mobile', trans('CompanyProfile::messages.mobile_no'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_factory_mobile') ? 'has-error': ''}}">
                                                            {!! Form::text('company_factory_mobile','', ['placeholder' => trans("CompanyProfile::messages.write_mobile_no"),
                                                           'class' => 'form-control input-md input_ban required','id'=>'company_factory_mobile', 'onkeyup' => 'mobile_no_validation(this.id)']) !!}
                                                            {!! $errors->first('company_factory_mobile','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>
                                            <div class="form-group">
                                                <div class="row col-centered-xs">
                                                    <div class="col-md-12 text-center" style="display: inline-block;">
                                                        <button type="button" id="hideFactoryEdit"
                                                                class="btn btn-default"
                                                                style="margin-right: 5px;">{{trans('CompanyProfile::messages.close')}}</button>
                                                        <button id="updateFactoryInfoBtn" class="btn btn-primary"
                                                                onclick="updateFactoryInfo()"
                                                                style="margin-left: 5px;">{{trans('CompanyProfile::messages.update')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    {!! Form::close() !!}
                                    <br>
                                </div>
                            </div>
                        </div>


                        {{--Company Directors--}}
                        <div class="card card-default">
                            <a
                                href="#company_director_view" data-toggle="collapse"
                                role="button" aria-expanded="false">
                                <div class="card-header"><p
                                        class="section_head card-title"
                                        style="margin: 0; color: #636363; font-size: 18px; font-weight: 400"><i
                                            class="fa fa-chevron-down"
                                            style="margin-right: 15px;"></i> {!!trans('CompanyProfile::messages.company_director_info')!!}
                                    </p></div>
                            </a>

                            <div id="company_director_view" class="expand collapse">
                                <div class="card-body" style="padding: 15px 25px;">
                                    <br>
                                    <div id="company_director_info" class="row">
                                        <div class="col-md-2">
                                        </div>
                                        <div class="col-md-8 table-responsive">
                                            <table class="table table-bordered">
                                                <thead style="background-color: #F7F7F7">
                                                <tr>
                                                    <th class="text-center"
                                                        scope="col">{!!trans('CompanyProfile::messages.number')!!}</th>
                                                    <th class="text-center"
                                                        scope="col">{!!trans('CompanyProfile::messages.name')!!}</th>
                                                    <th class="text-center"
                                                        scope="col">{!!trans('CompanyProfile::messages.designation')!!}</th>
                                                    <th class="text-center" scope="col">NID/TIN/Passport</th>
                                                    <th class="text-center"
                                                        scope="col">{!!trans('CompanyProfile::messages.nationality')!!}</th>
                                                </tr>
                                                </thead>
                                                <tbody id="investor_table">

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-2 text-right">
                                            <button id="showCompanyDirectorEdit" class="btn btn-lg editBtn"
                                                    style="border: none; background: white; color: #462A73;"
                                                    onclick="showCompanyDirectorEdit()">
                                                <i class="fa fa-edit"></i> EDIT
                                            </button>
                                        </div>
                                    </div>

                                    {!! Form::open(array('url' => '','method' => '', 'class' => 'form-horizontal', 'id' => 'companyDirectorEditForm', 'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                                    <div id="companyDirector_edit" class="row" style="display: none">
                                        <div class="col-md-12">
                                            <div class="card card-default">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="card-title"
                                                               style="margin: 0; color: #452A73; font-size: 18px; font-weight: 400">{!!trans('CompanyProfile::messages.company_director_info')!!}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            {{--                                        <div class="pull-left">--}}
                                                            {{--                                            <button type="button" class="btn btn-success" id="refreshDirectors" onclick="LoadListOfDirectors()">Refresh Director List</button>--}}
                                                            {{--                                        </div>--}}
                                                            <div class="float-right">
                                                                <a data-toggle="modal" data-target="#directorModel"
                                                                   onclick="openModal(this)"
                                                                   data-action="{{ url('client/company-profile/create-director') }}">
                                                                    <button type="button"
                                                                            class="btn btn-primary pull-right ceoInfoDirector2"
                                                                            style="margin-bottom: 5px"
                                                                            id="addMoreDirector">{!!trans('CompanyProfile::messages.add_directors')!!}</button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-body ">
                                                    <table class="table table-bordered" id="directorList">
                                                        <thead style="background-color: #F7F7F7">
                                                        <tr>
                                                            <th class="text-center"
                                                                scope="col">{!!trans('CompanyProfile::messages.number')!!}</th>
                                                            <th class="text-center"
                                                                scope="col">{!!trans('CompanyProfile::messages.name')!!}</th>
                                                            <th class="text-center"
                                                                scope="col">{!!trans('CompanyProfile::messages.designation')!!}</th>
                                                            <th class="text-center" scope="col">NID/TIN/Passport</th>
                                                            <th class="text-center"
                                                                scope="col">{!!trans('CompanyProfile::messages.nationality')!!}</th>
                                                            <th class="text-center"
                                                                scope="col">{!!trans('CompanyProfile::messages.action')!!}</th>

                                                        </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>

                                                    <br>
                                                    <div class="form-group">
                                                        <div class="row col-centered-xs">
                                                            <div class="col-md-12 text-center"
                                                                 style="display: inline-block;">
                                                                <button type="button" id="hideCompanyDirectorEdit"
                                                                        class="btn btn-default"
                                                                        style="margin-right: 5px;">{{trans('CompanyProfile::messages.close')}}</button>
                                                                <button type="button" id="de" class="btn btn-primary"
                                                                        onclick="updateCompanyDirectorInfo()"
                                                                        style="margin-left: 5px;">{{trans('CompanyProfile::messages.update')}}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                    <br>
                                </div>
                            </div>
                        </div>
                        {{--Company ceo--}}
                        <div class="card card-default">
                            <a
                                href="#company_ceo_view" data-toggle="collapse"
                                role="button" aria-expanded="false">
                                <div class="card-header"><p
                                        class="section_head card-title"
                                        style="margin: 0; color: #636363; font-size: 18px; font-weight: 400"><i
                                            class="fa fa-chevron-down"
                                            style="margin-right: 15px;"></i> {!!trans('CompanyProfile::messages.company_ceo')!!}
                                    </p></div>
                            </a>

                            <div id="company_ceo_view" class="expand  collapse">
                                <div class="card-body">
                                    <br>
                                    <div id="ceo_info" class="row">
                                        <div class="col-md-2">
                                        </div>
                                        <div class="col-md-8">

                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.select_directors') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    <p id="director_type_name"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.name') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    <p id="company_ceo_name_data"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.father_name') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    <p id="company_ceo_father_name"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.nationality') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    <p id="company_ceo_nationality_data"></p>
                                                </div>
                                            </div>

                                            <div class="row hidden" id="passport">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.passport_no') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    <p id="company_ceo_passport_data"></p>
                                                </div>
                                            </div>

                                            <div class="row hidden" id="nid">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.nid') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    : <span id="company_ceo_nid_data" class="input_ban"> </span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.dob') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    : <span id="company_ceo_dob_data" class="input_ban"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.designation') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    <p id="company_ceo_designation"></p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.email') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    <p class="sm_font" id="company_ceo_email_data"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.mobile') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    : <span id="company_ceo_mobile_data" class="input_ban"> </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.signature') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    <img class="img_sm" id="ceo_signature" src="" alt=""
                                                         style="max-width: 150px; max-height: 45px;">
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                        <div class="col-md-2 text-right">
                                            <button id="showCeoEdit" class="btn btn-lg editBtn"
                                                    style="border: none; background: white; color: #462A73"
                                                    onclick="showCeoEdit()">
                                                <i class="fa fa-edit"></i> EDIT
                                            </button>
                                        </div>
                                    </div>

                                    {!! Form::open(array('url' => '','method' => '', 'class' => 'form-horizontal', 'id' => 'ceoInfoEditForm', 'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                                    <div id="ceo_info_edit" class="row" style="display: none">

                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class=" text-right float-right">
                                                        <a style="margin-left: 23px" data-toggle="modal"
                                                           data-target="#directorModel"
                                                           onclick="openModal(this)"
                                                           data-action="{{ url("client/company-profile/create-info/$company_id") }}">
                                                            <button type="button"
                                                                    class="btn btn-primary ceoInfoDirector"
                                                                    id="addMoreDirector">{!!trans('CompanyProfile::messages.select_identity')!!}</button>
                                                            <br>
                                                            <br>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('select_directors', trans('CompanyProfile::messages.select_directors'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('select_directors') ? 'has-error': ''}}">
                                                            {!! Form::select('select_directors', $companyDirector, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'select_directors']) !!}
                                                            {!! $errors->first('select_directors','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_ceo_name', trans('CompanyProfile::messages.name'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_ceo_name') ? 'has-error': ''}}">
                                                            {!! Form::text('company_ceo_name','', ['placeholder' => trans("CompanyProfile::messages.write_name"),
                                                               'class' => 'form-control input-md required bnEng','id'=>'company_ceo_name', 'readonly'=>true]) !!}
                                                            {!! $errors->first('company_ceo_name','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row ">
                                                        {!! Form::label('company_ceo_fatherName', trans('CompanyProfile::messages.father_name'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_ceo_fatherName') ? 'has-error': ''}}">
                                                            {!! Form::text('company_ceo_fatherName','', ['placeholder' => trans("CompanyProfile::messages.write_father_name"),
                                                               'class' => 'form-control input-md required bnEng','id'=>'company_ceo_fatherName']) !!}
                                                            {!! $errors->first('company_ceo_fatherName','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_ceo_nationality', trans('CompanyProfile::messages.nationality'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('ceo_nationality') ? 'has-error': ''}}">
                                                            {!! Form::select('company_ceo_nationality', $nationality, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'company_ceo_nationality']) !!}
                                                            {!! $errors->first('company_ceo_nationality','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 hidden" id="company_ceo_nid_section">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_ceo_nid', trans('CompanyProfile::messages.nid'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('ceo_nid') ? 'has-error': ''}}">
                                                            {!! Form::text('company_ceo_nid','', ['placeholder' => trans("CompanyProfile::messages.write_nid"),
                                                           'class' => 'form-control input-md onlyNumber required','id'=>'company_ceo_nid', 'readonly'=>true]) !!}
                                                            {!! $errors->first('company_ceo_nid','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 hidden" id="company_ceo_passport_section">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_ceo_passport', trans('CompanyProfile::messages.passport'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_ceo_passport') ? 'has-error': ''}}">
                                                            {!! Form::text('company_ceo_passport','', ['placeholder' => trans("CompanyProfile::messages.write_passport"),
                                                           'class' => 'form-control input-md required','id'=>'company_ceo_passport', 'readonly'=>true]) !!}
                                                            {!! $errors->first('company_ceo_passport','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div
                                                    class="col-md-6 {{$errors->has('company_ceo_dob') ? 'has-error': ''}}">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_ceo_dob',trans('CompanyProfile::messages.dob'),['class'=>'col-md-5']) !!}
                                                        <div class=" col-md-7">
                                                            <div class="ceoDP input-group date" data-date="12-03-2015"
                                                                 data-date-format="dd-mm-yyyy">
                                                                {!! Form::text('company_ceo_dob', '', ['class'=>'form-control input_ban required', 'placeholder' => trans("CompanyProfile::messages.select"), 'data-rule-maxlength'=>'40', 'id'=>'company_ceo_dob', 'readonly'=>true]) !!}
                                                                <span class="input-group-addon">
                                                                <span class="fa fa-calendar"></span>
                                                            </span>
                                                                {!! $errors->first('company_ceo_dob','<span class="help-block">:message</span>') !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        {!! Form::label('company_ceo_designation_id', trans('CompanyProfile::messages.designation'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_ceo_designation_id') ? 'has-error': ''}}">
                                                            {!! Form::text('company_ceo_designation_id', '', ['class'=>'form-control required bnEng', 'placeholder' => trans("CompanyProfile::messages.write_designation"), 'data-rule-maxlength'=>'40', 'id'=>'company_ceo_designation_id', 'readonly'=>true]) !!}
                                                            {{--                                                            {!! Form::select('company_ceo_designation_id', $designation, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select_designation"),'id'=>'company_ceo_designation_id']) !!}--}}
                                                            {!! $errors->first('company_ceo_designation_id','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row mr-md-1 ">
                                                        {!! Form::label('company_ceo_email', trans('CompanyProfile::messages.email'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('ceo_email') ? 'has-error': ''}}">
                                                            {!! Form::text('company_ceo_email','', ['placeholder' => trans("CompanyProfile::messages.write_email"),
                                                           'class' => 'form-control input-md email required','id'=>'company_ceo_email']) !!}
                                                            {!! $errors->first('company_ceo_email','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row  ">
                                                        {!! Form::label('company_ceo_mobile', trans('CompanyProfile::messages.mobile_no'),
                                                        ['class'=>'col-md-5 required-star']) !!}
                                                        <div
                                                            class="col-md-7 {{$errors->has('company_ceo_mobile') ? 'has-error': ''}}">
                                                            {!! Form::text('company_ceo_mobile','', ['placeholder' => trans("CompanyProfile::messages.write_mobile_no"),
                                                           'class' => 'form-control input-md input_ban required','id'=>'company_ceo_mobile', 'onkeyup' => 'mobile_no_validation(this.id)']) !!}
                                                            {!! $errors->first('company_ceo_mobile','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>
                                            <div class="col-md-7 col-centered">
                                                <div class="form-group mg-lr">
                                                    <div class="row">
                                                        <div class="signature-upload">
                                                            <div class="col-md-12">
                                                                <label class="center-block image-upload"
                                                                       for="correspondent_signature">
                                                                    <figure>
                                                                        <img src=""
                                                                             class="img-responsive img-thumbnail"
                                                                             id="correspondent_signature_preview"
                                                                             width="75px"/>
                                                                    </figure>
                                                                    <input type="hidden"
                                                                           id="correspondent_signature_base64"
                                                                           name="correspondent_signature_base64"/>
                                                                </label>
                                                                <p style="font-size: 16px;">
                                                                    আপনার স্ক্যান স্বাক্ষরটি এখানে আপলোড করুন বা <strong
                                                                        style="color: #259BFF">ব্রাউজ করুন</strong>
                                                                </p>
                                                                <span
                                                                    style="font-size: 10px; font-weight: bold; display: block; color: #A6A6A6">
                                                                [File Format: *.jpg/ .jpeg .png | Maximum 5 MB]
                                                                </span>
                                                                <input type="file"
                                                                       class="form-control signature-upload-input input-sm required"
                                                                       name="correspondent_signature"
                                                                       id="correspondent_signature"
                                                                       onchange="imageUploadWithCropping(this, 'correspondent_signature_preview', 'correspondent_signature_base64')"
                                                                       size="300x80"/>
                                                            </div>
                                                        </div>
                                                        <div class="text-center">
                                                            <p style="font-size: 16px;">
                                                                <a target="_blank" href="https://picresize.com/">আপনার
                                                                    ইমেজ মডিফাই করার জন্য এখানে ক্লিক করুন </a>
                                                            </p>
                                                            <span style="color: #FF7272">প্রয়োজনীয় সকল কাগজপত্র এই স্বাক্ষরের মাধ্যমে স্বাক্ষরিত হতে হবে</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <div class="row col-centered-xs">
                                                    <div class="col-md-12 text-center" style="display: inline-block;">
                                                        <button type="button" id="hideCeoEdit" class="btn btn-default"
                                                                style="margin-right: 5px;">{{trans('CompanyProfile::messages.close')}}</button>
                                                        <button id="updateCeoInfoBtn" class="btn btn-primary"
                                                                onclick="updateCeoInfo()"
                                                                style="margin-left: 5px;">{{trans('CompanyProfile::messages.update')}}</button>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>


                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>


                        {{--Company main works--}}
                        <div class="card card-default">
                            <a
                                href="#company_main_work_view" data-toggle="collapse"
                                role="button" aria-expanded="false">
                                <div class="card-header">
                                    <p
                                        class="section_head card-title"
                                        style="margin: 0; color: #636363; font-size: 18px; font-weight: 400"><i
                                            class="fa fa-chevron-down"
                                            style="margin-right: 15px;"></i> {!!trans('CompanyProfile::messages.company_main_works')!!}
                                    </p>
                                </div>
                            </a>
                            <div id="company_main_work_view" class="expand  collapse">
                                <div class="card-body">
                                    <br>
                                    <div id="company_main_work_info" class="row">
                                        <div class="col-md-2">
                                        </div>
                                        <div class="col-md-8">

                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.pref_reg_office') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    <p id="pref_reg_office_data"></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5">
                                                    <label
                                                        for="">{!! trans('CompanyProfile::messages.company_main_works') !!}</label>
                                                </div>
                                                <div class="col-5">
                                                    <p id="company_main_activities"></p>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-2 text-right">
                                            <button id="showCompanyMainWorkEdit" class="btn btn-lg editBtn"
                                                    style="border: none; background: white; color: #462A73;"
                                                    onclick="showCompanyMainWorkEdit()">
                                                <i class="fa fa-edit"></i> EDIT
                                            </button>
                                        </div>
                                    </div>

                                    {!! Form::open(array('url' => '','method' => '', 'class' => 'form-horizontal', 'id' => 'companyActivitiesEditForm', 'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                                    <div id="companyMainWork_info_edit" class="row" style="display: none">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row  ">
                                                        {!! Form::label('pref_reg_office', trans('CompanyProfile::messages.pref_reg_office'),
                                                        ['class'=>'col-md-4 required-star']) !!}
                                                        <div
                                                            class="col-md-8 addressField {{$errors->has('pref_reg_office') ? 'has-error': ''}}">
                                                            {!! Form::select('pref_reg_office', $regOffice, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'pref_reg_office']) !!}
                                                            {!! $errors->first('pref_reg_office','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row ">
                                                        {!! Form::label('company_main_works', trans('CompanyProfile::messages.company_main_works'),
                                                        ['class'=>'col-md-4 required-star']) !!}
                                                        <div
                                                            class="col-md-8 addressField {{$errors->has('company_main_works') ? 'has-error': ''}}">
                                                            {!! Form::text('company_main_works','', ['placeholder' => trans("CompanyProfile::messages.company_main_works"),
                                                           'class' => 'form-control input-md required bnEng','id'=>'company_main_works']) !!}
                                                            {!! $errors->first('company_main_works','<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <br>
                                            <div class="form-group">
                                                <div class="row col-centered-xs">
                                                    <div class="col-md-12 text-center" style="display: inline-block;">
                                                        <button type="button" id="hideCompanyWorkEdit"
                                                                class="btn btn-default"
                                                                style="margin-right: 5px;">{{trans('CompanyProfile::messages.close')}}</button>
                                                        <button id="updateCompanyWorkInfo" class="btn btn-primary"
                                                                onclick="updateCompanyActivitiesInfo()"
                                                                style="margin-left: 5px;">{{trans('CompanyProfile::messages.update')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    {!! Form::close() !!}
                                    <br>
                                </div>
                            </div>
                        </div>

                        <div class="card card-default">
                            <a
                                href="#company_main_document_list" data-toggle="collapse"
                                role="button" aria-expanded="false">
                                <div class="card-header"><p class="section_head card-title">
                                        <i class="fa fa-chevron-down" style="margin-right: 15px;"></i> <span>প্রতিষ্ঠানের প্রয়োজনীয় সংযুক্তি সমুহ।</span>
                                    </p></div>
                            </a>
                            <div id="company_main_document_list"
                                 class=" company_assciation expand  collapse">
                                <div class="card-body" >
                                    <br>
                                    <div id="company_main_document_info">
                                        <div class="card card-default" >
                                            <div class="card-body">


                                                                                                    @include("Documents::index")
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>
                        {{--Company user list--}}
                        <div class="card card-default">
                            <a
                                href="#company_main_association_list" data-toggle="collapse"
                                role="button" aria-expanded="false">
                            <div class="card-header"><p class="section_head card-title"
                                                               style="margin: 0; color: #636363; font-size: 18px; font-weight: 400">
                                        <i class="fa fa-chevron-down"
                                           style="margin-right: 15px;"></i> {!!trans('CompanyProfile::messages.user')!!}
                                    </p></div></a>
                            <div id="company_main_association_list"
                                 class=" company_assciation expand  collapse">
                                <div class="card-body" >
                                    <br>
                                    <div id="company_main_work_info">
                                        <div class="card card-default" style="border-radius: 10px;">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="companyUserList"
                                                           style="width: 100%">
                                                        <thead>
                                                        <tr>
                                                            <th>{{trans('CompanyProfile::messages.user_email')}}</th>
                                                            <th>{{trans('CompanyProfile::messages.last_login')}}</th>
                                                            <th>{{trans('CompanyProfile::messages.action')}}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>

                                                    </table>
                                                </div>
                                                <div id="load_content">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Company Profile End--}}

@endsection

@section('footer-script')
    @include('partials.image-upload')
    @include('partials.datatable-js')
    <script src="{{ asset("assets/scripts/moment.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js") }}"></script>
    <script src="{{ asset("assets/scripts/jquery.validate.min.js") }}"></script>
    <script src="{{ asset("assets/scripts/sweetalert2.all.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/select2.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/intlTelInput/js/intlTelInput.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/intlTelInput/js/utils.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/scripts/custom.min.js") }}"></script>

    <script>

        function getCompanyUserList() {
            /**
             * table desk script
             * @type {jQuery}
             */
            $('#companyUserList').DataTable({
                processing: true,
                // serverSide: true,
                searching: false,

                ajax: {
                    url: '{{url("/client/company-association/get-user-list")}}',
                    method: 'get',
                },
                columns: [
                    {data: 'user_email', name: 'user_email'},
                    {data: 'last_login', name: 'last_login'},
                    {data: 'action', name: 'action'},

                ],
                "aaSorting": []
            });
        }

        function activateDeactiveUser(e, key) {
            var r = confirm("Are you sure?");
            if (r !== true) {
                return false;
            }
            const button_text = e.innerText;
            const loading_sign = '...<i class="fa fa-spinner fa-spin"></i>';

            var companyAssocId = e.value;
            $.ajax({
                url: "{{ url('client/company-association/approve-reject') }}",
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    companyAssocId: companyAssocId,
                    key: key
                },
                beforeSend: function () {
                    e.innerHTML = button_text + loading_sign;
                },
                success: function (response) {
                    toastr.success('Activated successfully!');
                    $('#companyUserList').DataTable().destroy();
                    getCompanyUserList()
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    toastr.warning('Activation not successful!');
                    console.log(errorThrown);
                },
                // complete: function () {
                //
                // }
            });
        }

        var selectCountry = '';
        $(document).ready(function () {


            loadCompanyProfile();
            getCompanyUserList();

            var today = new Date();
            var yyyy = today.getFullYear();
            $('.ceoDP').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY',
                maxDate: 'now',
                minDate: '01/01/' + (yyyy - 110)
            });

            $('.manufacture_starting_dateDP').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY',
                // maxDate: 'now',
                minDate: '01/01/' + (yyyy - 110)
            });

            $('.project_deadlineDP').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY',
                // maxDate: '01/01/\' + (yyyy + 110)',
                minDate: '01/01/' + (yyyy - 110)
            });

            // LoadListOfDirectors();

            {{--initail -input mobile plugin script start--}}
            // $("#company_office_mobile").intlTelInput({
            //     hiddenInput: "company_office_mobile",
            //     initialCountry: "BD",
            //     placeholderNumberType: "MOBILE",
            //     separateDialCode: true
            // });
            // $("#company_factory_mobile").intlTelInput({
            //     hiddenInput: "company_factory_mobile",
            //     initialCountry: "BD",
            //     placeholderNumberType: "MOBILE",
            //     separateDialCode: true
            // });
            // $("#company_ceo_mobile").intlTelInput({
            //     hiddenInput: "company_ceo_mobile",
            //     initialCountry: "BD",
            //     placeholderNumberType: "MOBILE",
            //     separateDialCode: true
            // });
            {{--initail -input mobile plugin script end--}}
        })

        function mobile_no_validation(id) {
            var id = id;
            $("#" + id).on('keyup', function () {
                var countryCode = $("#" + id).intlTelInput("getSelectedCountryData").dialCode;

                if (countryCode === "880") {
                    var mobile = $("#" + id).val();
                    var reg = /^0/;
                    if (reg.test(mobile)) {
                        $("#" + id).val("");
                    }
                }
            });
        }


        function expandAll() {
            $(".expand").addClass("in show");
            $("#expandAll").addClass("hidden");
            $("#collapseAll").removeClass("hidden");
        }

        function collapseAll() {
            $(".expand").removeClass("in show");
            $("#expandAll").removeClass("hidden");
            $("#collapseAll").addClass("hidden");
        }

        // Show general info edit data
        function showGeneralEdit() {
            $("#general_info").hide();

            $.ajax({
                type: "GET",
                url: '/client/company-profile/get-edit-info',
                dataType: "json",
                data: {
                    company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                },
                success: function (editInfo) {
                    var response = editInfo.companyProfile;
                    var countryInfo = editInfo.countryInfo;
                    $('#company_name_bangla').val(response.org_nm_bn);
                    $('#company_name_english').val(response.org_nm);
                    $('#reg_type_id').val(response.regist_type);
                    $('#company_type_id').val(response.org_type);
                    $('#investment_type_id').val(response.invest_type);
                    $('#total_investment').val(response.investment_limit);
                    $('#industrial_category_id').val(response.ind_category_id);
                    $('#industrial_sector_id').val(response.ins_sector_id);
                    selectCountry = countryInfo.country_id;
                    $("#investment_type_id").trigger('change');
                    // $("#industrial_sector_id").trigger('change', [response.ins_sub_sector_id]);
                    $('#industrial_sub_sector_id').val(response.ins_sub_sector_id);


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Unknown error occurred during attachment loading. Please, try again after reload');
                },
            });
            $("#general_info_edit").fadeIn();
        }


        //  update general info
        function updateGeneralInfo() {

            $('#generalInfoEditForm').validate({
                errorPlacement: function () {
                    return true;
                },

                submitHandler: function (form) {
                    var company_name_bangla = $('#company_name_bangla').val();
                    var company_name_english = $('#company_name_english').val();
                    var reg_type_id = $('#reg_type_id').val();
                    var company_type_id = $('#company_type_id').val();
                    var investment_type_id = $('#investment_type_id').val();
                    var total_investment = $('#total_investment').val();
                    var industrial_category_id = $('#industrial_category_id').val();
                    var industrial_sector_id = $('#industrial_sector_id').val();
                    var industrial_sub_sector_id = $('#industrial_sub_sector_id').val();
                    var investing_country_id = $('#investing_country_id').val();

                    $.ajax({
                        type: "PATCH",
                        url: '/client/company-profile/update-general-info',
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                            company_name_english: company_name_english,
                            company_name_bangla: company_name_bangla,
                            reg_type_id: reg_type_id,
                            company_type_id: company_type_id,
                            investment_type_id: investment_type_id,
                            total_investment: total_investment,
                            industrial_category_id: industrial_category_id,
                            industrial_sector_id: industrial_sector_id,
                            industrial_sub_sector_id: industrial_sub_sector_id,
                            investing_country_id: investing_country_id,

                        },
                        success: function (response) {
                            loadCompanyProfile();
                            toastr.success('Information Updated Successfully!');
                            $("#general_info_edit").hide();
                            $("#general_info").fadeIn();

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error('Information Update Error!');
                        },
                    });
                }
            });
        };

        $('#hideGeneralEdit').click(function () {
            $("#general_info_edit").hide();
            $("#general_info").fadeIn();
        })


        function showOfficeEdit() {
            $("#office_info").hide();
            $.ajax({
                type: "GET",
                url: '/client/company-profile/get-edit-info',
                dataType: "json",
                data: {
                    company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                },
                success: function (editInfo) {
                    var response = editInfo.companyProfile;
                    $('#company_office_division_id').val(response.office_division);
                    if (response.office_division !== '') {
                        getDistrictByDivisionId('company_office_division_id', response.office_division, 'company_office_district_id', response.office_district);
                    }
                    if (response.office_district !== '') {
                        getThanaByDistrictId('company_office_district_id', response.office_district, 'company_office_thana_id', response.office_thana);
                    }
                    // $('#company_office_district_id').val(response.office_district);
                    // $('#company_office_thana_id').val(response.office_thana);
                    $('#company_office_postCode').val(response.office_postcode);
                    $('#company_office_address').val(response.office_location);
                    $('#company_office_email').val(response.office_email);

                    $('#company_office_mobile').intlTelInput('destroy');
                    $('#company_office_mobile').val(response.office_mobile);

                    $("#company_office_mobile").intlTelInput({
                        // hiddenInput: "company_office_mobile",
                        initialCountry: "BD",
                        placeholderNumberType: "MOBILE",
                        separateDialCode: true
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Unknown error occurred during attachment loading. Please, try again after reload');
                },
            });
            $("#office_info_edit").fadeIn();
        }

        //  update office info
        function updateOfficeInfo() {
            $('#officeInfoEditForm').validate({
                errorPlacement: function () {
                    return true;
                },
                submitHandler: function (form) {
                    var company_office_division_id = $('#company_office_division_id').val();
                    var company_office_district_id = $('#company_office_district_id').val();
                    var company_office_thana_id = $('#company_office_thana_id').val();
                    var company_office_postCode = $('#company_office_postCode').val();
                    var company_office_address = $('#company_office_address').val();
                    var company_office_email = $('#company_office_email').val();
                    var company_office_mobile = $("#company_office_mobile").intlTelInput('getNumber');

                    $.ajax({
                        type: "PATCH",
                        url: '/client/company-profile/update-office-info',
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                            company_office_division_id: company_office_division_id,
                            company_office_district_id: company_office_district_id,
                            company_office_thana_id: company_office_thana_id,
                            company_office_postCode: company_office_postCode,
                            company_office_address: company_office_address,
                            company_office_email: company_office_email,
                            company_office_mobile: company_office_mobile,

                        },
                        success: function (response) {
                            loadCompanyProfile();
                            toastr.success('Information Updated Successfully!');
                            $("#office_info_edit").hide();
                            $("#office_info").fadeIn();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error('Information Update Error!');
                        },
                    });
                }
            });
        };

        $('#hideOfficeEdit').click(function () {
            $("#office_info_edit").hide();
            $("#office_info").fadeIn();
        })

        function showFactoryEdit() {
            $("#factory_info").hide();
            $.ajax({
                type: "GET",
                url: '/client/company-profile/get-edit-info',
                dataType: "json",
                data: {
                    company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                },
                success: function (editInfo) {
                    var response = editInfo.companyProfile;
                    $('#company_factory_division_id').val(response.factory_division);

                    if (response.factory_division !== "") {
                        getDistrictByDivisionId('company_factory_division_id', response.factory_division, 'company_factory_district_id', response.factory_district);
                    }
                    if (response.factory_district !== "") {
                        getThanaByDistrictId('company_factory_district_id', response.factory_district, 'company_factory_thana_id', response.factory_thana);
                    }

                    // $('#company_factory_district_id').val(response.factory_district);
                    // $('#company_factory_thana_id').val(response.factory_thana);
                    $('#company_factory_postCode').val(response.factory_postcode);
                    $('#company_factory_address').val(response.factory_location);
                    $('#company_factory_email').val(response.factory_email);

                    $('#company_factory_mobile').intlTelInput('destroy');
                    $('#company_factory_mobile').val(response.factory_mobile);
                    $("#company_factory_mobile").intlTelInput({
                        // hiddenInput: "company_factory_mobile",
                        initialCountry: "BD",
                        placeholderNumberType: "MOBILE",
                        separateDialCode: true
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Unknown error occurred during attachment loading. Please, try again after reload');
                },
            });
            $("#factory_info_edit").fadeIn();
        }

        //  update office info
        function updateFactoryInfo() {
            $('#factoryInfoEditForm').validate({
                errorPlacement: function () {
                    return true;
                },
                submitHandler: function (form) {
                    var company_factory_division_id = $('#company_factory_division_id').val();
                    var company_factory_district_id = $('#company_factory_district_id').val();
                    var company_factory_thana_id = $('#company_factory_thana_id').val();
                    var company_factory_postCode = $('#company_factory_postCode').val();
                    var company_factory_address = $('#company_factory_address').val();
                    var company_factory_email = $('#company_factory_email').val();
                    var company_factory_mobile = $("#company_factory_mobile").intlTelInput('getNumber');

                    $.ajax({
                        type: "PATCH",
                        url: '/client/company-profile/update-factory-info',
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                            company_factory_division_id: company_factory_division_id,
                            company_factory_district_id: company_factory_district_id,
                            company_factory_thana_id: company_factory_thana_id,
                            company_factory_postCode: company_factory_postCode,
                            company_factory_address: company_factory_address,
                            company_factory_email: company_factory_email,
                            company_factory_mobile: company_factory_mobile,

                        },
                        success: function (response) {
                            loadCompanyProfile();
                            toastr.success('Information Updated Successfully!');
                            $("#factory_info_edit").hide();
                            $("#factory_info").fadeIn();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error('Information Update Error!');
                        },
                    });
                }
            });
        };

        $('#hideFactoryEdit').click(function () {
            $("#factory_info_edit").hide();
            $("#factory_info").fadeIn();
        })

        function showCeoEdit() {
            $("#ceo_info").hide();
            $.ajax({
                type: "GET",
                url: '/client/company-profile/get-edit-info',
                dataType: "json",
                data: {
                    company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                },
                success: function (editInfo) {
                    var response = editInfo.companyProfile;

                    // $('#company_ceo_division_id').val(response.ceo_division);
                    // $('#company_ceo_district_id').val(response.ceo_district);
                    // $('#company_ceo_thana_id').val(response.ceo_thana);
                    // if(response.ceo_division !==""){
                    //     getDistrictByDivisionId('company_ceo_division_id', response.ceo_division, 'company_ceo_district_id', response.ceo_district);
                    // }
                    // if(response.ceo_district !==""){
                    //     getThanaByDistrictId('company_ceo_district_id', response.ceo_district, 'company_ceo_thana_id', response.ceo_thana);
                    // }

                    $('#company_ceo_name').val(response.ceo_name);
                    $('#company_ceo_fatherName').val(response.ceo_father_nm);

                    if (response.nationality == '18') {
                        // $("#company_ceo_nationality").attr('readonly', true);
                        $('#company_ceo_nid').val(response.nid);
                        $("#company_ceo_nid_section").removeClass('hidden');
                        $("#company_ceo_passport_section").addClass('hidden');
                    }
                    if (response.passport != null) {
                        // $("#company_ceo_nationality").attr('readonly', true);
                        $("#company_ceo_passport_section").removeClass('hidden');
                        $("#company_ceo_nid_section").addClass('hidden');
                        $('#company_ceo_passport').val(response.passport);
                    }

                    $('#company_ceo_nationality').val(response.nationality);

                    $('#company_ceo_dob').val(response.dob);
                    $('#company_ceo_designation_id').val(response.designation);
                    $('#company_ceo_postCode').val(response.ceo_postcode);
                    $('#company_ceo_address').val(response.ceo_location);
                    $('#company_ceo_email').val(response.ceo_email);

                    $('#company_ceo_mobile').intlTelInput('destroy');
                    $('#company_ceo_mobile').val(response.ceo_mobile);
                    $("#company_ceo_mobile").intlTelInput({
                        // hiddenInput: "company_ceo_mobile",
                        initialCountry: "BD",
                        placeholderNumberType: "MOBILE",
                        separateDialCode: true
                    });
                    $('#correspondent_signature_base64').val('/' + response.entrepreneur_signature);
                    $('#correspondent_signature_preview').attr('src', '/' + response.entrepreneur_signature);
                    if (response.entrepreneur_signature == null) {
                        $('#correspondent_signature').addClass('required');
                    } else {
                        $('#correspondent_signature').removeClass('required');
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Unknown error occurred during attachment loading. Please, try again after reload');
                },
            });
            $("#ceo_info_edit").fadeIn();
        }

        //  update ceo info
        function updateCeoInfo() {
            $('#ceoInfoEditForm').validate({
                errorPlacement: function () {
                    return true;
                },
                submitHandler: function (form) {
                    var company_ceo_name = $('#company_ceo_name').val();
                    var select_directors = $('#select_directors').val();
                    var company_ceo_fatherName = $('#company_ceo_fatherName').val();
                    var company_ceo_nationality = $('#company_ceo_nationality').val();
                    var company_ceo_passport = $('#company_ceo_passport').val();
                    var company_ceo_nid = $('#company_ceo_nid').val();
                    var company_ceo_dob = $('#company_ceo_dob').val();
                    var company_ceo_designation_id = $('#company_ceo_designation_id').val();
                    // var company_ceo_division_id = $('#company_ceo_division_id').val();
                    // var company_ceo_district_id = $('#company_ceo_district_id').val();
                    // var company_ceo_thana_id = $('#company_ceo_thana_id').val();
                    // var company_ceo_postCode = $('#company_ceo_postCode').val();
                    // var company_ceo_address = $('#company_ceo_address').val();
                    var company_ceo_email = $('#company_ceo_email').val();
                    var company_ceo_mobile = $("#company_ceo_mobile").intlTelInput('getNumber');
                    var correspondent_signature_base64 = $('#correspondent_signature_base64').val();

                    $.ajax({
                        type: "PATCH",
                        url: '/client/company-profile/update-ceo-info',
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                            select_directors: select_directors,
                            company_ceo_name: company_ceo_name,
                            company_ceo_fatherName: company_ceo_fatherName,
                            company_ceo_nationality: company_ceo_nationality,
                            company_ceo_passport: company_ceo_passport,
                            company_ceo_nid: company_ceo_nid,
                            company_ceo_dob: company_ceo_dob,
                            company_ceo_designation_id: company_ceo_designation_id,
                            // company_ceo_division_id: company_ceo_division_id,
                            // company_ceo_district_id: company_ceo_district_id,
                            // company_ceo_thana_id: company_ceo_thana_id,
                            // company_ceo_postCode: company_ceo_postCode,
                            // company_ceo_address: company_ceo_address,
                            company_ceo_email: company_ceo_email,
                            company_ceo_mobile: company_ceo_mobile,
                            correspondent_signature_base64: correspondent_signature_base64,

                        },
                        success: function (response) {
                            loadCompanyProfile();
                            toastr.success('Information Updated Successfully!');
                            $("#ceo_info_edit").hide();
                            $("#ceo_info").fadeIn();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error('Information Update Error!');
                        },
                    });
                }
            });
        };

        $('#hideCeoEdit').click(function () {
            $("#ceo_info_edit").hide();
            $("#ceo_info").fadeIn();
        })

        function showCompanyMainWorkEdit() {
            $("#company_main_work_info").hide();
            $.ajax({
                type: "GET",
                url: '/client/company-profile/get-edit-info',
                dataType: "json",
                data: {
                    company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                },
                success: function (editInfo) {
                    var response = editInfo.companyProfile;
                    $('#pref_reg_office').val(response.bscic_office_id);
                    $('#company_main_works').val(response.main_activities);
                    $('#manufacture_starting_date').val(response.commercial_operation_dt);
                    $('#project_deadline').val(response.project_deadline);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Unknown error occurred during attachment loading. Please, try again after reload');
                },
            });
            $("#companyMainWork_info_edit").fadeIn();
        }

        //  update activities info
        function updateCompanyActivitiesInfo() {

            $('#companyActivitiesEditForm').validate({
                errorPlacement: function () {
                    return true;
                },
                submitHandler: function (form) {
                    var company_main_works = $('#company_main_works').val();
                    var manufacture_starting_date = $('#manufacture_starting_date').val();
                    var project_deadline = $('#project_deadline').val();
                    var pref_reg_office = $('#pref_reg_office').val();

                    $.ajax({
                        type: "PATCH",
                        url: '/client/company-profile/update-activities-info',
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                            company_main_works: company_main_works,
                            manufacture_starting_date: manufacture_starting_date,
                            project_deadline: project_deadline,
                            pref_reg_office: pref_reg_office,

                        },
                        success: function (response) {
                            loadCompanyProfile();
                            toastr.success('Information Updated Successfully!');
                            $("#companyMainWork_info_edit").hide();
                            $("#company_main_work_info").fadeIn();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error('Information Update Error!');
                        },
                    });
                }
            });
        };


        //  update company info
        function updateCompanyDirectorInfo() {

            $.ajax({
                type: "PATCH",
                url: '/client/company-profile/update-company-director-info',
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",

                },
                success: function (response) {
                    // loadCompanyProfile();
                    toastr.success('Information Updated Successfully!');
                    loadCompanyProfile();

                    $('#companyDirector_edit').find('input:text').val('');
                    $('#companyDirector_edit').find('input:file').val('');
                    $("#companyDirector_edit").find('select').prop('selectedIndex', 0);
                    $("#companyDirector_edit").hide();
                    $("#company_director_info").fadeIn();

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    toastr.error('Information Update Error!');
                },
            });
        };

        $('#hideCompanyWorkEdit').click(function () {
            $("#companyMainWork_info_edit").hide();
            $("#company_main_work_info").fadeIn();
        })

        function showCompanyDirectorEdit() {
            $("#company_director_info").hide();
            $.ajax({
                type: "GET",
                url: '/client/company-profile/get-edit-info',
                dataType: "json",
                data: {
                    company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                },
                success: function (response) {
                    {{--                    var edit_url = "{{url('/client/company-profile/edit-director')}}";--}}
                    var delete_url = "{{url('/client/company-profile/delete-director-db')}}";
                    $('#directorList tbody').html("");
                    $.each(response.investorInfo, function (index, value) {
                        index = index + 1;
                        var html = '<tr>' +
                            ' <td>' + index + '</td> ' +
                            ' <td>' + value.investor_nm + '</td> ' +
                            ' <td>' + value.designation + '</td> ' +
                            '<td>' + value.identity_no + '</td> ' +
                            '<td>' + value.nationality + '</td>' +
                            '<td>';
                        if (index != 1) {
                            html += '<a data-action="' + delete_url + '/' + value.id + '" onclick="ConfirmDelete(this)" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>';

                        }
                        html += '</td></tr>';

                        $('#directorList tbody').append(html);

                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Unknown error occurred during attachment loading. Please, try again after reload');
                },
            });
            $("#companyDirector_edit").fadeIn();
        }

        $('#hideCompanyDirectorEdit').click(function () {
            $('#companyDirector_edit').find('input:text').val('');
            $('#companyDirector_edit').find('input:file').val('');
            $("#companyDirector_edit").find('select').prop('selectedIndex', 0);
            $("#companyDirector_edit").hide();
            $("#company_director_info").fadeIn();
        })

        function loadCompanyProfile() {
            $.ajax({
                type: "GET",
                url: '/client/company-profile/get-company-profile',
                dataType: "json",
                data: {
                    company_id: "{{\App\Libraries\Encryption::encodeId($companyProfile->id) }}",
                },
                success: function (profileInfo) {
                    
                    // General Info
                    var investor = profileInfo.investorInfo;
                    var countryInfo = profileInfo.countryInfo;

                    var response = profileInfo.companyProfile;

                    var companyUserType = profileInfo.companyUserType;
                    if (companyUserType !== 'Employee') {
                        $('#company_name_bangla').attr('readonly', true);
                        $('#company_name_english').attr('readonly', true);
                        $('#company_type_id').attr('disabled', true);
                        $('#company_office_division_id').attr('disabled', true);
                        $('#company_office_district_id').attr('disabled', true);
                        $('#company_office_thana_id').attr('disabled', true);
                    }

                    if (response.org_nm == null || response.org_nm_bn == null || response.regist_type == null ||
                        response.org_type == null || response.invest_type == null || response.ins_sector_id == null || response.investment_limit == null ||
                        response.office_division == null || response.office_email == null || response.office_postcode == null ||
                        response.director_type == null || response.ceo_name == null || response.ceo_mobile == null ||
                        response.bscic_office_id == null || response.main_activities == null ||
                        investor.length === 0
                    ) {
                        expandAll()
                        showGeneralEdit()
                        showOfficeEdit()
                        showFactoryEdit()
                        showCompanyDirectorEdit()
                        showCeoEdit()
                        showCompanyMainWorkEdit()
                        $(".company_assciation").removeClass("in");

                    }

                    $('#company_name_bn').html(response.org_nm_bn);
                    var date = response.created_at;
                    var created_at = moment(date).format("DD-MM-YYYY");
                    $('#submit_date').html(created_at);

                    var last_updated_date = response.updated_at;
                    var last_updated = moment(last_updated_date).format("DD-MM-YYYY");
                    $('#last_updated_date').html(last_updated);

                    $('#reg_type').html(': ' + response.reg_type_bn);
                    $('#invest_type').html(': ' + response.inv_type_bn);
                    if (countryInfo) {
                        $('#investing_country').html(': ' + countryInfo.con_name);
                    }
                    
                    $('#total_investment_data').html(response.investment_limit);
                    $('#industrial_sector').html(': ' + response.ind_sect_bn);
                    $('#industrial_sub_sector').html(': ' + response.ind_s_sect_bn);
                    $('#company_type').html(': ' + response.com_type_bn);
                    $('#industrial_class').html(': ' + response.ind_cat_bn);
                    $('#office_division').html(': ' + response.office_division_bn);
                    // Office Info
                    $('#office_district').html(': ' + response.office_district_bn);
                    $('#office_thana').html(': ' + response.office_thana_bn);
                    $('#office_postcode').html(response.office_postcode);
                    $('#office_address').html(': ' + response.office_location);
                    $('#office_email').html(': ' + response.office_email);
                    $('#office_mobile').html(response.office_mobile);
                    // Factory Info
                    $('#factory_division').html(': ' + response.factory_division_bn);
                    $('#factory_district').html(': ' + response.factory_district_bn);
                    $('#factory_thana').html(': ' + response.factory_thana_bn);
                    $('#factory_postcode').html(response.factory_postcode);
                    $('#factory_address').html(': ' + response.factory_location);
                    $('#factory_email').html(': ' + response.factory_email);
                    $('#factory_mobile').html(response.factory_mobile);
                    // Cew Info
                    $('#director_type_name').html(': ' + response.director_type_name);
                    $('#director_type_name').html(': ' + response.director_type_name);
                    $('#select_directors').val(response.director_type);

                    $('#company_ceo_name_data').html(': ' + response.ceo_name);
                    $('#company_ceo_father_name').html(': ' + response.ceo_father_nm);
                    $('#company_ceo_nationality_data').html(': ' + response.ceo_nationality);

                    if (response.nid != null) {
                        $('#nid').removeClass("hidden");
                        $('#company_ceo_nid_data').html(response.nid);
                        $('#passport').addClass("hidden");
                    }

                    if (response.passport != null) {
                        $('#company_ceo_passport_data').html(': ' + response.passport);
                        $('#passport').removeClass("hidden");
                        $('#nid').addClass("hidden");
                    }

                    var dob_date = response.dob;
                    var ceo_dob = moment(dob_date).format("DD-MM-YYYY");

                    $('#company_ceo_dob_data').html(ceo_dob);
                    $('#company_ceo_designation').html(': ' + response.designation);

                    // $('#company_ceo_division').html(': ' + response.ceo_division_bn);
                    // $('#company_ceo_district').html(': ' + response.ceo_district_bn);
                    // $('#company_ceo_thana').html(': ' + response.ceo_thana_bn);
                    // $('#company_ceo_post_code').html(response.ceo_postcode);
                    // $('#company_ceo_address_data').html(': ' + response.ceo_location);
                    $('#company_ceo_email_data').html(': ' + response.ceo_email);
                    $('#company_ceo_mobile_data').html(response.ceo_mobile);
                    // Ceo Name and Signature
                    // $('#ceo_name').html(': ' + response.entrepreneur_name);
                    // $('#ceo_designation_data').html(': ' + response.entrepreneur_designation);
                    $('#ceo_signature').attr('src', '/' + response.entrepreneur_signature);
                    // Company Activities
                    $('#pref_reg_office_data').html(': ' + response.bscic_office_nm_bn);
                    $('#company_main_activities').html(': ' + response.main_activities);
                    // var m_date = response.commercial_operation_dt;
                    // var manufacture_starting_date =moment(m_date).format("DD- MM-YYYY");
                    // $('#manufacture_starting_date_data').html(manufacture_starting_date);
                    // var deadline_date = response.project_deadline;
                    // var project_deadline =moment(deadline_date).format("DD-MM-YYYY");
                    // $('#project_deadline_data').html(project_deadline);

                    $('#investor_table').html("");
                    $.each(investor, function (index, value) {
                        index = index + 1;
                        $('#investor_table').append('<tr>' +
                            ' <td>' + index + '</td> ' +
                            ' <td>' + value.investor_nm + '</td> ' +
                            ' <td>' + value.designation + '</td> ' +
                            '<td>' + value.identity_no + '</td> ' +
                            '<td>' + value.nationality + '</td></tr>');

                    });

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Unknown error occurred');
                },
            });
        }


    </script>

    <script>
        $("#investment_type_id").change(function (e, values = '', investment_type = '') {
            var investment_type_id = $('#investment_type_id').val();
            $(this).after('<span class="loading_data">Loading...</span>');
            var self = $(this);
            $.ajax({
                type: "GET",
                url: "{{ url('client/company-profile/get-country-by-investment-type') }}",
                data: {
                    investment_type_id: investment_type_id
                },
                success: function (response) {
                    if (investment_type_id == 1) {
                        $('#investing_country_id').attr('multiple', 'multiple');
                        //Select2
                        $("#investing_country_id").select2();
                    } else {
                        if ($("#investing_country_id").data('select2')) {
                            $("#investing_country_id").select2('destroy');
                        }
                        $('#investing_country_id').removeAttr('multiple');
                    }

                    if (investment_type_id == 3) {
                        var option = "";
                    } else {
                        var option = '<option value="">{{trans("CompanyProfile::messages.select")}}</option>';
                    }
                    if (response.responseCode == 1) {
                        $.each(response.data, function (id, value) {
                            var repId = (id.replace(' ', ''))
                            if (selectCountry != null) {
                                if ($.inArray(repId, selectCountry.split(',')) != -1) {
                                    option += '<option value="' + repId + '" selected>' + value + '</option>';
                                } else {
                                    option += '<option value="' + repId + '">' + value + '</option>';
                                }
                            } else {
                                option += '<option value="' + repId + '">' + value + '</option>';
                            }
                        });
                    }
                    $("#investing_country_id").html(option);

                    $(self).next().hide();
                }
            });
        });
    </script>

    <script>
        function openModal(btn) {
            var this_action = btn.getAttribute('data-action');
            if (this_action != '') {
                $.get(this_action, function (data, success) {
                    if (success === 'success') {
                        $('#myModal .load_modal').html(data);
                    } else {
                        $('#myModal .load_modal').html('Unknown Error!');
                    }
                    $('#myModal').modal('show', {backdrop: 'static'});
                });
            }
        }
    </script>

    <script>
        //Load list of directors
        function LoadListOfDirectors() {
            $.ajax({
                url: "{{ url("client/company-profile/load-listof-directors") }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    app_id: "{{ Encryption::encodeId($companyProfile->id) }}",
                    {{--process_type_id: "{{ Encryption::encodeId($appInfo->process_type_id) }}",--}}
                    _token: $('input[name="_token"]').val()
                },
                success: function (response) {
                    var html = '';
                    if (response.responseCode == 1) {

                        {{--var edit_url = "{{url('/client/company-profile/edit-director-db')}}";--}}
                        var delete_url = "{{url('/client/company-profile/delete-director-db')}}";

                        var count = 1;
                        $.each(response.data, function (id, value) {
                            if (typeof value.id !== 'undefined') {
                                id = value.id
                            } else {
                                id = id + '-key';
                            }
                            var sl = count++;

                            html += '<tr>';
                            html += '<td>' + sl + '</td>';
                            html += '<td>' + value.l_director_name + '</td>';
                            html += '<td>' + value.designation + '</td>';
                            html += '<td>' + value.nid_etin_passport + '</td>';
                            html += '<td>' + value.nationality + '</td>';
                            html += '<td>' +
                                '<a data-action="' + delete_url + '/' + id + '" onclick="ConfirmDelete(this)" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>' +
                                '</td>';
                            html += '</tr>';
                        });

                        if (response.ceoInfo != null) {
                            $("#ceoInfoDIV").removeClass('hidden');

                            $("#company_ceo_designation_id").val(response.ceoInfo.designation);
                            var date_of_birth = moment(response.ceoInfo.date_of_birth).format("DD-MM-YYYY");
                            $("#company_ceo_dob").val(date_of_birth);
                            $("#company_ceo_name").val(response.ceoInfo.l_director_name);
                            $("#company_ceo_fatherName").val(response.ceoInfo.l_father_name);


                            if (response.ceoInfo.nationality == 'Bangladeshi') {
                                // $("#company_ceo_nationality").attr('readonly', true);
                                $("#company_ceo_nationality").val(18);

                            } else {
                                $("#company_ceo_nationality").val();
                                // $("#company_ceo_nationality").attr('readonly', '');
                            }

                            if (response.ceoInfo.identity_type == 'passport') {
                                $("#company_ceo_passport_section").removeClass("hidden");
                                $("#company_ceo_nid_section").addClass("hidden");
                                $("#company_ceo_passport").val(response.ceoInfo.nid_etin_passport);
                                $("#company_ceo_nid").val(' ');
                            } else {
                                $("#company_ceo_passport_section").addClass("hidden");
                                $("#company_ceo_nid_section").removeClass("hidden");
                                $("#company_ceo_nid").val(response.ceoInfo.nid_etin_passport);
                                $("#company_ceo_passport").val(' ');
                            }
                        } else {
                            $(".ceoInfoDirector").removeClass('hidden');
                            $("#ceoInfoDIV").addClass('hidden');
                        }

                    } else {
                        html += '<tr>';
                        html += '<td colspan="5" class="text-center">' + '<span class="text-danger">No data available!</span>' + '</td>';
                        html += '</tr>';
                    }
                    $('#directorList tbody').html(html);
                }
            });
        }

        //confirm delete alert
        function ConfirmDelete(btn) {
            var sure_delete = confirm("Are you sure you want to delete?");
            if (sure_delete) {
                var url = btn.getAttribute('data-action');
                $.ajax({
                    url: url,
                    type: "get",
                    success: function (response) {
                        if (response.responseCode == 1) {
                            toastr.success(response.msg);
                        }
                    }
                });
                LoadListOfDirectors();
            } else {
                return false;
            }
        }
    </script>

    <script>
        $("#total_investment").change(function () {
            var total_investment = $('#total_investment').val();
            $.ajax({
                type: "GET",
                url: "{{ url('client/company-profile/get-industry-type-by-investment') }}",
                data: {
                    total_investment: total_investment
                },
                success: function (response) {
                    $("#industrial_category_id").val(response.data);
                    if (response.data !== "") {
                        $("#industrial_category_id").find("[value!='" + response.data + "']").prop("disabled", true);
                        $("#industrial_category_id").find("[value='" + response.data + "']").prop("disabled", false);
                    }
                }
            });
        });
    </script>

    <script>
        $("#industrial_sector_id").change(function (e, value) {
            var industrial_sector_id = $('#industrial_sector_id').val();
            $(this).after('<span class="loading_data">Loading...</span>');
            var self = $(this);
            $.ajax({
                type: "GET",
                url: "{{ url('client/company-profile/get-sub-sector-by-sector') }}",
                data: {
                    industrial_sector_id: industrial_sector_id
                },
                success: function (response) {

                    var option = '<option value="">{{trans("CompanyProfile::messages.select")}}</option>';
                    if (response.responseCode == 1) {
                        $.each(response.data, function (id, value) {
                            option += '<option value="' + id + '">' + value + '</option>';
                        });
                    }
                    $("#industrial_sub_sector_id").html(option).val(value).change();
                    $(self).next().hide();
                }
            });
        });
    </script>
@endsection
