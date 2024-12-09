<link rel="stylesheet"
    href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}" />
<link rel="stylesheet" href="{{ asset("assets/plugins/jquery-steps/jquery.steps.css") }}">

<style>
    .wizard>.steps>ul>li {
        width: 25% !important;
    }

    .wizard {
        overflow: visible;
    }

    .wizard>.content {
        overflow: visible;
    }

    .wizard>.actions {
        width: 70% !important;
    }
    .wizard > .steps .current a, .wizard > .steps .current a:hover, .wizard > .steps .current a:active {
        background: #563590;
        color: #fff;
        cursor: default;
    }
    .wizard > .steps .done a, .wizard > .steps .done a:hover, .wizard > .steps .done a:active {
        background: #7a5eab;
        color: #fff;
    }
    .wizard > .steps .disabled a, .wizard > .steps .disabled a:hover, .wizard > .steps .disabled a:active {
        background:  #7a5eab;
        color: #fff;
        cursor: default;
    }

    #total_fixed_ivst_million {
        pointer-events: none;
    }

    /*.wizard > .actions {*/
    /*top: -15px;*/
    /*}*/

    .col-centered {
        float: none;
        margin: 0 auto;
    }

    .radio {
        cursor: pointer;
    }

    /*form label {*/
    /*    font-weight: normal;*/
    /*    font-size: 16px;*/
    /*}*/

    /*.table>thead:first-child>tr:first-child>th {*/
    /*    font-weight: normal;*/
    /*    font-size: 16px;*/
    /*}*/

    /*td {*/
    /*    font-size: 16px;*/
    /*}*/

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
        width: 100%;
        max-width: 100%;
        padding: 5px 10px;
        font-size: 1rem;
        text-align: center;
        color: #000;
        background-color: #F5FAFE;
        border-radius: 0 !important;
        border: 0;
        height: 160px;
        /*box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);*/
        font-weight: 400;
        outline: 1px dashed #ccc;
        margin-bottom: 5px;
    }

    .custom_error {
        outline: 1px dashed red;
    }

    .email {
        font-family: Arial !important;
    }

    @media (min-width: 481px) {
        .panel-body {
            padding: 0 15px;
        }
    }

    @media (max-width: 480px) {
        .wizard>.actions {
            width: 55% !important;
            position: inherit;
        }

        .panel-body {
            padding: 0;
        }

        .form-group {
            margin-bottom: 0;
        }

        .wizard>.content>.body label {
            margin-top: .5em;
            margin-bottom: 0;
        }


        .tabInput {
            width: 120px;
        }

        .tabInput_sm {
            width: 75px;
        }

        .tabInputDate {
            width: 150px;
        }

        .table_responsive {
            overflow-x: auto;
        }

        /*.bootstrap-datetimepicker-widget {*/
        /*    position: relative !important;*/
        /*    top: 0 !important;*/
        /*}*/

        .prevMob {
            margin-top: 45px;
        }

        /*.wizard > .actions {*/
        /*display: block !important;*/
        /*width: 100% !important;*/
        /*text-align: center;*/
        /*}*/
    }
</style>

@include('partials.datatable-css')

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content load_modal"></div>
    </div>
</div>
@if (in_array($appInfo->status_id, [5, 6, 17, 22]))
    @include('ProcessPath::remarks-modal')
@endif

{{-- Industry registration --}}
<div class="card" id="inputForm" style="border-radius: 10px;">
    <div style="padding: 10px 15px">
        <h4 class="card-header" style="font-size: 24px; font-weight: 400">{!! trans('CompanyProfile::messages.industry_reg') !!}</h4>
    </div>
    <div class="clearfix">


        @if (in_array($appInfo->status_id, [5]))

            <div  class="col-md-12">
                <div class="float-right" style="margin-right: 1%;">
                    <a data-toggle="modal" data-target="#remarksModal">
                        {!! Form::button('<i class="fa fa-eye"></i>Reason of ' . $appInfo->status_name . '', ['type' => 'button', 'class' => 'btn btn-md btn-danger']) !!}
                    </a>
                </div>
            </div>

        @endif

    </div>

    <div class="card-body">
        {!! Form::open(['url' => url('industry-new/store'), 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'application_form', 'enctype' => 'multipart/form-data', 'files' => 'true', 'onSubmit' => 'enablePath()']) !!}
        <h3>{!! trans('CompanyProfile::messages.general_information') !!}</h3>
        <br>

        <input type="hidden" id="openMode" name="openMode" value="edit">
        {!! Form::hidden('app_id', Encryption::encodeId($appInfo->id), ['class' => 'form-control input-md required', 'id' => 'app_id']) !!}

        <fieldset>
            {{-- General Info --}}
            <div class="card card-magenta border border-magenta">
                <div  class="card-header">
                        {!! trans('CompanyProfile::messages.general_information') !!}
                </div>
                <div class="card-body" style="padding: 15px 25px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('company_name_bangla', trans('CompanyProfile::messages.company_name_bangla'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('org_nm') ? 'has-error' : '' }}">
                                    {!! Form::text('company_name_bangla', $appInfo->org_nm, ['placeholder' => trans('CompanyProfile::messages.write_company_name_bangla'), 'class' => 'form-control input-md bnEng', 'id' => 'company_name_bangla', 'readonly' => $companyUserType != 'Employee' ? true : false]) !!}
                                    {!! $errors->first('company_name_bangla', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('company_name_english', trans('CompanyProfile::messages.company_name_english'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('org_nm_bn') ? 'has-error' : '' }}">
                                    {!! Form::text('company_name_english', $appInfo->org_nm_bn, ['placeholder' => trans('CompanyProfile::messages.write_company_name_english'), 'class' => 'form-control input-md', 'id' => 'company_name_english', 'readonly' => $companyUserType != 'Employee' ? true : false]) !!}
                                    {!! $errors->first('company_name_english', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('project_name', trans('CompanyProfile::messages.project_name'), ['class' => 'col-md-4 ']) !!}
                                <div class="col-md-8 {{ $errors->has('project_nm') ? 'has-error' : '' }}">
                                    {!! Form::text('project_name', $appInfo->project_nm, ['placeholder' => trans('CompanyProfile::messages.project_name'), 'class' => 'form-control input-md', 'id' => 'project_name']) !!}
                                    {!! $errors->first('project_name', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('reg_type_id', trans('CompanyProfile::messages.reg_type'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('reg_type_id') ? 'has-error' : '' }}">
                                    {!! Form::select('reg_type_id', $regType, $appInfo->regist_type, ['class' => 'form-control input-md', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'reg_type_id']) !!}
                                    {!! $errors->first('reg_type_id', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('company_type_id', trans('CompanyProfile::messages.company_type'), ['class' => 'col-md-4 ']) !!}
                                <div class="col-md-8 {{ $errors->has('org_type') ? 'has-error' : '' }}">
                                    {!! Form::select('company_type_id', $companyType, $appInfo->org_type, ['class' => 'form-control input-md', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'company_type_id', 'disabled' => $companyUserType != 'Employee' ? true : false]) !!}
                                    {!! $errors->first('company_type_id', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <input type="hidden" id="company_id_for_business"
                                    value="{{ isset($companyInfo->id) ? \App\Libraries\Encryption::encodeId($companyInfo->id) : '' }}">
                                {!! Form::label('business_category_id', trans('CompanyProfile::messages.business_category'), ['class' => 'col-md-4 required-star']) !!}
                                <div class="col-md-8 {{ $errors->has('business_category_id') ? 'has-error' : '' }}">
                                    {!! Form::select('business_category_id', $businessCategory, $appInfo->business_category_id, ['class' => 'form-control input-md required', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'business_category_id']) !!}
                                    {!! $errors->first('business_category_id', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('investment_type_id', trans('CompanyProfile::messages.invest_type'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('company_type') ? 'has-error' : '' }}">
                                    {!! Form::select('investment_type_id', $investmentType, $appInfo->invest_type, ['class' => 'form-control input-md', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'investment_type_id']) !!}
                                    {!! $errors->first('investment_type_id', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('investing_country_id', trans('CompanyProfile::messages.investing_country'), ['class' => 'col-md-4 ']) !!}
                                <div class="col-md-8 {{ $errors->has('investing_country_id') ? 'has-error' : '' }}">
                                    {!! Form::select('investing_country_id[]', $country, $investing_country->country_id, ['class' => 'form-control input-md investing_country_id', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'investing_country_id']) !!}
                                    {!! $errors->first('investing_country_id', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('total_investment', trans('CompanyProfile::messages.total_investment'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('total_investment') ? 'has-error' : '' }}">
                                    {!! Form::text('total_investment', $appInfo->investment_limit, ['placeholder' => trans('CompanyProfile::messages.write_total_investment'), 'class' => 'form-control input-md input_ban onlyNumber', 'id' => 'total_investment']) !!}
                                    {!! $errors->first('total_investment', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('industrial_category_id', trans('CompanyProfile::messages.industrial_class'), ['class' => 'col-md-3 ']) !!} <div class="col-md-1 "><i class="fa fa-cog" data-toggle="modal"
                                        data-target="#feeModal"
                                        title="শিল্পের শ্রেণীকরণ সহকারে সরকারি ফি এর বিষয়ে জানতে ক্লিক করুন"></i></div>
                                <div
                                    class="col-md-8 {{ $errors->has('industrial_category_id') ? 'has-error' : '' }}">
                                    {!! Form::select('industrial_category_id', $industrialCategory, $appInfo->ind_category_id, ['class' => 'form-control input-md', 'readonly', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'industrial_category_id']) !!}
                                    {!! $errors->first('industrial_category_id', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('industrial_sector_id', trans('CompanyProfile::messages.industrial_sector'), ['class' => 'col-md-4 ']) !!}
                                <div class="col-md-8 {{ $errors->has('industrial_sector_id') ? 'has-error' : '' }}">
                                    {!! Form::select('industrial_sector_id', $industrialSector, $appInfo->ins_sector_id, ['class' => 'form-control input-md', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'industrial_sector_id']) !!}
                                    {!! $errors->first('industrial_sector_id', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('industrial_sub_sector_id', trans('CompanyProfile::messages.industrial_sub_sector'), ['class' => 'col-md-4']) !!}
                                <div
                                    class="col-md-8 {{ $errors->has('industrial_sub_sector_id') ? 'has-error' : '' }}">
                                    {!! Form::select('industrial_sub_sector_id', $industrialSubSector, $appInfo->ins_sub_sector_id, ['class' => 'form-control input-md', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'industrial_sub_sector_id']) !!}
                                    {!! $errors->first('industrial_sub_sector_id', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reg Office name --}}
            <div class="card card-magenta border border-magenta">
                <div  class="card-header">
                        {!! trans('CompanyProfile::messages.pref_reg_office') !!}
                </div>

                <div class="card-body" style="padding: 15px 25px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('pref_reg_office', trans('CompanyProfile::messages.pref_reg_office'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('pref_reg_office') ? 'has-error' : '' }}">
                                    {!! Form::select('pref_reg_office', $regOffice, $appInfo->bscic_office_id, ['class' => 'form-control input-md', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'pref_reg_office']) !!}
                                    {!! $errors->first('pref_reg_office', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="height: 45px; margin: 0 -25px; background: #F2F3F5;"></div>

            {{-- Reg info --}}
            <p style="font-size: 24px; font-weight: 400; margin-top: 25px;">{!! trans('CompanyProfile::messages.reg_information') !!}</p>

            {{-- Company main work --}}
            <div class="card card-magenta border border-magenta">
                <div  class="card-header">
                        ক. {!! trans('CompanyProfile::messages.company_main_works_info') !!}
                </div>

                <div class="card-body" style="padding: 15px 25px;">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::label('company_main_works', trans('CompanyProfile::messages.company_main_works'), ['class' => 'col-md-2']) !!}
                                <div class="col-md-10 {{ $errors->has('company_main_works') ? 'has-error' : '' }}"
                                    style="width: 79.5%; float: right">
                                    {!! Form::text('company_main_works', $appInfo->main_activities, ['placeholder' => trans('CompanyProfile::messages.company_main_works'), 'class' => 'form-control input-md', 'id' => 'company_main_works']) !!}
                                    {!! $errors->first('company_main_works', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Company annual production --}}
            <div class="card card-magenta border border-magenta">
                <div  class="card-header">
                        খ. {!! trans('CompanyProfile::messages.company_annual_production_capacity') !!}
                </div>

                <div class="card-body table-responsive" style="padding: 15px 25px;">
                    <table id="annualProductionTable" class="table table-bordered dt-responsive" cellspacing="0"
                        width="100%">
                        <thead>
                            <tr class="section_heading1">
                                <th class="text-center">{!! trans('CompanyProfile::messages.service_name') !!}</th>
                                <th class="text-center">{!! trans('CompanyProfile::messages.amount') !!}</th>
                                <th class="text-center">{!! trans('CompanyProfile::messages.amount_unit') !!}</th>
                                <th class="text-center">{!! trans('CompanyProfile::messages.price') !!}</th>
                                <th class="text-center">{!! trans('CompanyProfile::messages.action') !!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($annualProduction->isNotEmpty())
                                <?php $inc = 0; ?>
                                @foreach ($annualProduction as $annualProduction)
                                    <tr id="annualProductionRow{{ $inc }}" data-number="1">
                                        <td>
                                            {!! Form::hidden("apc_id[$inc]", $annualProduction->id) !!}

                                            {!! Form::text("apc_service_name[$inc]", $annualProduction->service_name, ['class' => 'form-control input-md text-center', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'apc_service_name']) !!}
                                        </td>
                                        <td>
                                            {!! Form::text("apc_quantity[$inc]", $annualProduction->quantity, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => trans('CompanyProfile::messages.write_amount'), 'id' => 'apc_quantity']) !!}
                                        </td>
                                        <td>
                                            {!! Form::select("apc_unit[$inc]", $apc_unit, $annualProduction->unit, ['class' => 'form-control input-md tabInput', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'apc_unit']) !!}
                                        </td>
                                        <td>
                                            {!! Form::text("apc_amount_bdt[$inc]", $annualProduction->amount_bdt, ['class' => 'form-control input-md text-center onlyNumber input_ban apc_amount_bdt tabInput', 'onkeyup' => "calculateMachineryTotal('apc_amount_bdt', 'apc_price_total')", 'placeholder' => trans('CompanyProfile::messages.price_lak_taka'), 'id' => 'apc_amount_bdt']) !!}
                                        </td>
                                        <td class="text-center">
                                            <?php if ($inc == 0) { ?>
                                            <a class="btn btn-sm btn-info addTableRows" title="Add more"
                                                onclick="addTableRow('annualProductionTable', 'annualProductionRow0');"><i
                                                    class="fa fa-plus"></i></a>
                                            <?php } else { ?>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-danger removeRow"
                                                onclick="removeTableRow('annualProductionTable','annualProductionRow{{ $inc }}');">
                                                <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php $inc++; ?>
                                @endforeach
                            @else
                                <tr id="annualProductionRow" data-number="1">
                                    <td>
                                        {!! Form::text('apc_service_name[0]', '', ['class' => 'form-control input-md text-center', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'apc_service_name']) !!}
                                    </td>
                                    <td>
                                        {!! Form::text('apc_quantity[0]', '', ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => trans('CompanyProfile::messages.write_amount'), 'id' => 'apc_quantity']) !!}
                                    </td>
                                    <td>
                                        {!! Form::select('apc_unit[0]', $apc_unit, null, ['class' => 'form-control input-md tabInput', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'apc_unit']) !!}
                                    </td>
                                    <td>
                                        {!! Form::text('apc_amount_bdt[0]', '', ['class' => 'form-control input-md text-center onlyNumber input_ban apc_amount_bdt tabInput', 'onkeyup' => "calculateMachineryTotal('apc_amount_bdt', 'apc_price_total')", 'placeholder' => trans('CompanyProfile::messages.price_lak_taka'), 'id' => 'apc_amount_bdt']) !!}
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-sm btn-info addTableRows" title="Add more"
                                            onclick="addTableRow('annualProductionTable', 'annualProductionRow');"><i
                                                class="fa fa-plus"></i></a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tr>
                            <td colspan="3" class="text-right">
                                {!! trans('CompanyProfile::messages.grand_total') !!}
                            </td>
                            <td colspan="2">
                                {!! Form::text('apc_price_total', isset($appInfo->apc_price_total) ? $appInfo->apc_price_total : '', ['class' => 'form-control input-md text-center onlyNumber input_ban', 'readonly', 'placeholder' => trans('CompanyProfile::messages.price_taka'), 'id' => 'apc_price_total']) !!}
                                {!! $errors->first('apc_price_total', '<span class="help-block">:message</span>') !!}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Sell parcent --}}
            <div class="card card-magenta border border-magenta">
                <div class="card-header" >
                        গ. {!! trans('CompanyProfile::messages.sell_parcent') !!}
                </div>

                <div class="card-body" style="padding: 15px 25px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('local_sales_per', trans('CompanyProfile::messages.local'), ['class' => 'col-md-4 ']) !!}
                                <div class="col-md-8 {{ $errors->has('local_sales') ? 'has-error' : '' }}">
                                    {!! Form::text('local_sales', $appInfo->sales_local, ['placeholder' => '0%', 'class' => 'form-control input-md onlyNumber input_ban', 'id' => 'local_sales_per']) !!}
                                    {!! $errors->first('local_sales', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('foreign_sales_per', trans('CompanyProfile::messages.foreign'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('foreign') ? 'has-error' : '' }}">
                                    {!! Form::text('foreign_sales', $appInfo->sales_foreign, ['placeholder' => '0%', 'class' => 'form-control input-md onlyNumber input_ban', 'id' => 'foreign_sales_per']) !!}
                                    {!! $errors->first('foreign_sales', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Company manpower --}}
            <div class="card card-magenta border border-magenta">
                <div  class="card-header">
                        ঘ. {!! trans('CompanyProfile::messages.company_man_power') !!}
                </div>

                <div class="card-body table-responsive" style="padding: 15px 25px;">
                    <table id="" class="table table-bordered" cellspacing="0" width="100%">
                        <thead class="section_heading1">
                            <tr>
                                <th class="text-center" colspan="3">{!! trans('CompanyProfile::messages.local_bangladeshi') !!}</th>
                                <th class="text-center" colspan="3">{!! trans('CompanyProfile::messages.foreign') !!}</th>
                                <th class="text-center" colspan="3">{!! trans('CompanyProfile::messages.total') !!}</th>
                            </tr>
                        </thead>
                        <tbody id="manpower">
                            <tr class="text-center section_heading2">
                                <td>{!! trans('CompanyProfile::messages.men') !!}</td>
                                <td>{!! trans('CompanyProfile::messages.women') !!}</td>
                                <td>{!! trans('CompanyProfile::messages.total') !!}</td>
                                <td>{!! trans('CompanyProfile::messages.men') !!}</td>
                                <td>{!! trans('CompanyProfile::messages.women') !!}</td>
                                <td>{!! trans('CompanyProfile::messages.total') !!}</td>
                                <td>{!! trans('CompanyProfile::messages.grand_total') !!}</td>
                                <td>{!! trans('CompanyProfile::messages.local_rate') !!}</td>
                                <td>{!! trans('CompanyProfile::messages.foreign_rate') !!}</td>
                            </tr>
                            <tr>
                                <td>
                                    {!! Form::text('local_male', $appInfo->local_male, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput_sm', 'placeholder' => trans('CompanyProfile::messages.n_number'), 'id' => 'local_male']) !!}
                                    {!! $errors->first('local_male', '<span class="help-block">:message</span>') !!}
                                </td>
                                <td>
                                    {!! Form::text('local_female', $appInfo->local_female, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput_sm', 'placeholder' => trans('CompanyProfile::messages.n_number'), 'id' => 'local_female']) !!}
                                    {!! $errors->first('local_female', '<span class="help-block">:message</span>') !!}
                                </td>
                                <td>
                                    {!! Form::text('local_total', $appInfo->local_total, ['class' => 'form-control input-md text-center input_ban tabInput_sm', 'readonly', 'placeholder' => trans('CompanyProfile::messages.total'), 'id' => 'local_total']) !!}
                                    {!! $errors->first('local_total', '<span class="help-block">:message</span>') !!}
                                </td>
                                <td>
                                    {!! Form::text('foreign_male', $appInfo->foreign_male, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput_sm', 'placeholder' => trans('CompanyProfile::messages.n_number'), 'id' => 'foreign_male']) !!}
                                    {!! $errors->first('foreign_male', '<span class="help-block">:message</span>') !!}
                                </td>
                                <td>
                                    {!! Form::text('foreign_female', $appInfo->foreign_female, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput_sm', 'placeholder' => trans('CompanyProfile::messages.n_number'), 'id' => 'foreign_female']) !!}
                                    {!! $errors->first('foreign_female', '<span class="help-block">:message</span>') !!}
                                </td>
                                <td>
                                    {!! Form::text('foreign_total', $appInfo->foreign_total, ['class' => 'form-control input-md text-center input_ban tabInput_sm', 'readonly', 'placeholder' => trans('CompanyProfile::messages.total'), 'id' => 'foreign_total']) !!}
                                    {!! $errors->first('foreign_total', '<span class="help-block">:message</span>') !!}
                                </td>
                                <td>
                                    {!! Form::text('manpower_total', $appInfo->manpower_total, ['class' => 'form-control input-md text-center input_ban tabInput_sm', 'readonly', 'placeholder' => trans('CompanyProfile::messages.grand_total'), 'id' => 'mp_total']) !!}
                                    {!! $errors->first('manpower_total', '<span class="help-block">:message</span>') !!}
                                </td>
                                <td>
                                    {!! Form::text('manpower_local_ratio', $appInfo->manpower_local_ratio, ['class' => 'form-control input-md text-center input_ban tabInput_sm', 'readonly', 'placeholder' => trans('CompanyProfile::messages.local_rate'), 'id' => 'mp_ratio_local']) !!}
                                    {!! $errors->first('manpower_local_ratio', '<span class="help-block">:message</span>') !!}
                                </td>
                                <td>
                                    {!! Form::text('manpower_foreign_ratio', $appInfo->manpower_foreign_ratio, ['class' => 'form-control input-md text-center input_ban tabInput_sm', 'readonly', 'placeholder' => trans('CompanyProfile::messages.foreign_rate'), 'id' => 'mp_ratio_foreign']) !!}
                                    {!! $errors->first('manpower_foreign_ratio', '<span class="help-block">:message</span>') !!}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Necessary service description --}}
            <div class="card card-magenta border border-magenta">
                <div  class="card-header">
                        ঙ. {!! trans('CompanyProfile::messages.necessary_services_details') !!}
                </div>

                <div class="card-body" style="padding: 15px 25px;">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead class="section_heading2">
                                <th class="text-center">{!! trans('CompanyProfile::messages.necessary_services_name') !!}</th>
                                <th class="text-center">{!! trans('CompanyProfile::messages.has_connectivity_advantage') !!}</th>
                                <th class="text-center">{!! trans('CompanyProfile::messages.possible_distance_from_connection') !!}</th>
                            </thead>

                            <tbody id="">
                                @foreach ($public_utility as $key => $value)
                                    <tr>
                                        <td>
                                            <span class="helpTextCom" id="">{{ $value->name_bn }}</span>

                                            {!! Form::hidden("ind_reg_utility_id[$key]", $value->id, []) !!}
                                            {!! Form::hidden("utility_id[$key]", $value->utility_id, []) !!}
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <label class="radio control-label" for=""
                                                    style="margin-right: 15px">
                                                    {!! Form::radio("services_availability[$key]", 1, $value->services_availability == 1 ? true : false, ['class' => '', 'id' => '']) !!}
                                                    {!! trans('CompanyProfile::messages.yes') !!}
                                                </label>

                                                <label class="radio control-label" for=""
                                                    style="margin-left: 15px">
                                                    {!! Form::radio("services_availability[$key]", 0, $value->services_availability == 0 ? true : false, ['class' => '', 'id' => '']) !!}
                                                    {!! trans('CompanyProfile::messages.no') !!}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <table style="width:100%;">
                                                <tr>
                                                    <td>
                                                        {!! Form::text('utility_distance[]', $value->utility_distance, ['class' => 'form-control input-md onlyNumber input_ban text-center', 'id' => 'utility_distance', 'placeholder' => trans('CompanyProfile::messages.distance')]) !!}
                                                        {!! $errors->first('utility_distance[]', '<span class="help-block">:message</span>') !!}
                                                    </td>
                                                    <td>
                                                        {!! Form::select('distance_unit[]', ['Meter' => trans('IndustryNew::messages.Meter'), 'Kilometer' => trans('IndustryNew::messages.Kilometer')], $value->distance_unit, ['id' => 'distance_unit', 'class' => 'form-control input-md', 'placeholder' => trans('CompanyProfile::messages.select')]) !!}
                                                        {!! $errors->first('distance_unit', '<span class="help-block">:message</span>') !!}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Investment --}}
            <div class="card card-magenta border border-magenta">
                <div class="card-header">
                        চ. {!! trans('CompanyProfile::messages.investment') !!}
                </div>

                <div class="card-body" style="padding: 15px 25px;">
                    <div class="table-responsive">
                        <table id="" class="table table-bordered">
                            <thead class="section_heading2">
                                <tr>
                                    <th class="text-center required-star" colspan="4">{!! trans('CompanyProfile::messages.investment_source') !!}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center" style="background: #FFFFFF">
                                    <td>{!! trans('CompanyProfile::messages.source') !!}</td>
                                    <td>{!! trans('CompanyProfile::messages.taka') !!}</td>
                                    <td>{!! trans('CompanyProfile::messages.dollar') !!}</td>
                                    <td>{!! trans('CompanyProfile::messages.loan_org_country') !!}</td>
                                </tr>
                                <tr style="background: #F9F9F9">
                                    <td>
                                        {!! trans('CompanyProfile::messages.ceo_same_invest') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('ceo_taka_invest', $appInfo->ceo_taka_invest, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => '0', 'id' => 'ceo_taka_invest', 'onkeyup' => 'calculateInvSourceTaka()']) !!}
                                        {!! $errors->first('ceo_taka_invest', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('ceo_dollar_invest', $appInfo->ceo_dollar_invest, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => '0', 'id' => 'ceo_dollar_invest', 'onkeyup' => 'calculateInvSourceDollar()']) !!}
                                        {!! $errors->first('ceo_dollar_invest', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('ceo_loan_org_country', $appInfo->ceo_loan_org_country, ['class' => 'form-control input-md text-center', 'placeholder' => trans('CompanyProfile::messages.loan_org_country'), 'id' => 'ceo_loan_country']) !!}
                                        {!! $errors->first('ceo_loan_org_country', '<span class="help-block">:message</span>') !!}
                                    </td>
                                </tr>
                                <tr style="background: #FFFFFF">
                                    <td>
                                        {!! trans('CompanyProfile::messages.local_loan') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('local_loan_taka', $appInfo->local_loan_taka, ['class' => 'form-control input-md text-center onlyNumber input_ban', 'placeholder' => '0', 'id' => 'local_loan_taka', 'onkeyup' => 'calculateInvSourceTaka()']) !!}
                                        {!! $errors->first('local_loan_taka', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('local_loan_dollar', $appInfo->local_loan_dollar, ['class' => 'form-control input-md text-center onlyNumber input_ban', 'placeholder' => '0', 'id' => 'local_loan_dollar', 'onkeyup' => 'calculateInvSourceDollar()']) !!}
                                        {!! $errors->first('local_loan_dollar', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('local_loan_org_country', $appInfo->local_loan_org_country, ['class' => 'form-control input-md text-center', 'placeholder' => trans('CompanyProfile::messages.loan_org_country'), 'id' => 'local_loan_country']) !!}
                                        {!! $errors->first('local_loan_org_country', '<span class="help-block">:message</span>') !!}
                                    </td>

                                </tr>
                                <tr style="background: #F9F9F9">
                                    <td>
                                        {!! trans('CompanyProfile::messages.foreign_loan') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('foreign_loan_taka', $appInfo->foreign_loan_taka, ['class' => 'form-control input-md text-center onlyNumber input_ban', 'placeholder' => '0', 'id' => 'foreign_loan_taka', 'onkeyup' => 'calculateInvSourceTaka()']) !!}
                                        {!! $errors->first('foreign_loan_taka', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('foreign_loan_dollar', $appInfo->foreign_loan_dollar, ['class' => 'form-control input-md text-center onlyNumber input_ban', 'placeholder' => '0', 'id' => 'foreign_loan_dollar', 'onkeyup' => 'calculateInvSourceDollar()']) !!}
                                        {!! $errors->first('foreign_loan_dollar', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('foreign_loan_org_country', $appInfo->foreign_loan_org_country, ['class' => 'form-control input-md text-center', 'placeholder' => trans('CompanyProfile::messages.loan_org_country'), 'id' => 'foreign_loan_country']) !!}
                                        {!! $errors->first('foreign_loan_org_country', '<span class="help-block">:message</span>') !!}
                                    </td>

                                </tr>
                                <tr style="background: #FFFFFF">
                                    <td class="text-right">
                                        {!! trans('CompanyProfile::messages.grand_total') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('total_inv_taka', $appInfo->total_inv_taka, ['class' => 'form-control input-md input_ban', 'readonly', 'placeholder' => '0', 'id' => 'total_inv_taka']) !!}
                                        {!! $errors->first('total_inv_taka', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('total_inv_dollar', $appInfo->total_inv_dollar, ['class' => 'form-control input-md input_ban', 'readonly', 'placeholder' => '0', 'id' => 'total_inv_dollar']) !!}
                                        {!! $errors->first('total_inv_dollar', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>

                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- <div class="table-responsive"> --}}
                    {{--  --}}
                    {{-- </div> --}}

                    <div class="card card-magenta border border-magenta">
                        <div  class="card-header">
                                {!! trans('CompanyProfile::messages.country_wise_loan_source') !!}
                        </div>
                        <div class="card-body" style="padding: 0">
                            <div class="table_responsive">
                                <table id="loanSourceTable" class="table table-bordered">
                                    <thead>
                                        <tr class="section_heading2" style="font-size: 16px;">
                                            <th style="font-weight: normal;" class="text-center">
                                                {!! trans('CompanyProfile::messages.country_name') !!}</th>
                                            <th style="font-weight: normal;" class="text-center">
                                                {!! trans('CompanyProfile::messages.org_name') !!}</th>
                                            <th style="font-weight: normal;" class="text-center">
                                                {!! trans('CompanyProfile::messages.loan_amount') !!}</th>
                                            <th style="font-weight: normal;" class="text-center">
                                                {!! trans('CompanyProfile::messages.loan_taking_date') !!}</th>
                                            <th style="font-weight: normal;" class="text-center">
                                                {!! trans('CompanyProfile::messages.action') !!}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($loanSource->isNotEmpty())
                                            <?php $inc = 0; ?>
                                            @foreach ($loanSource as $loanSource)
                                                <tr id="loanSourceRow{{ $inc }}" data-number="1">
                                                    <td class="text-center">
                                                        {!! Form::hidden("loan_source_id[$inc]", $loanSource->id) !!}

                                                        {!! Form::select("loan_country_id[$inc]", $country, $loanSource->loan_country_id, ['class' => 'form-control input-md tabInput', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'loan_country_id']) !!}
                                                    </td>
                                                    <td>
                                                        {!! Form::text("loan_org_name[$inc]", $loanSource->loan_org_nm, ['class' => 'form-control input-md text-center tabInput', 'placeholder' => trans('CompanyProfile::messages.org_name'), 'id' => 'loan_org_name']) !!}
                                                    </td>
                                                    <td>
                                                        {!! Form::text("loan_amount[$inc]", $loanSource->loan_amount, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => trans('CompanyProfile::messages.loan_amount'), 'id' => 'loan_amount']) !!}
                                                    </td>
                                                    <td>
                                                        <div class="input-group date datetimepicker4"
                                                            id="datepicker{{$inc}}" data-target-input="nearest">
                                                            {!! Form::text("loan_receive_date[$inc]", date('d-m-Y', strtotime($loanSource->loan_receive_date)), ['class' => 'form-control input_ban', 'id' => 'loan_receive_date']) !!}
                                                            <div class="input-group-append"
                                                                data-target="#datepicker{{$inc}}"
                                                                data-toggle="datetimepicker">
                                                                <div class="input-group-text"><i
                                                                        class="fa fa-calendar"></i></div>
                                                            </div>
                                                            {!! $errors->first('loan_receive_date', '<span class="help-block">:message</span>') !!}
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($inc == 0) { ?>
                                                        <a class="btn btn-sm btn-info addTableRows"
                                                            title="Add more"
                                                            onclick="addTableRow('loanSourceTable', 'loanSourceRow0');"><i
                                                                class="fa fa-plus"></i></a>
                                                        <?php } else { ?>
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-sm btn-danger removeRow"
                                                            onclick="removeTableRow('loanSourceTable','loanSourceRow{{ $inc }}');">
                                                            <i class="fa fa-times" aria-hidden="true"></i></a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php $inc++; ?>
                                            @endforeach
                                        @else
                                            <tr id="loanSourceRow" data-number="1">
                                                <td class="text-center">
                                                    {!! Form::select('loan_country_id[0]', $country, '', ['class' => 'form-control input-md tabInput', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'loan_country_id']) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('loan_org_name[0]', '', ['class' => 'form-control input-md text-center tabInput', 'placeholder' => trans('CompanyProfile::messages.org_name'), 'id' => 'loan_org_name']) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('loan_amount[0]', '', ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => trans('CompanyProfile::messages.loan_amount'), 'id' => 'loan_amount']) !!}
                                                </td>
                                                <td>
                                                    <div class="input-group date datetimepicker4"
                                                         id="datepicker0" data-target-input="nearest">
                                                        {!! Form::text('loan_receive_date[0]', '', ['class' => 'form-control input_ban', 'id' => 'loan_receive_date']) !!}
                                                        <div class="input-group-append"
                                                             data-target="#datepicker0"
                                                             data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i
                                                                    class="fa fa-calendar"></i></div>
                                                        </div>
                                                        {!! $errors->first('loan_receive_date', '<span class="help-block">:message</span>') !!}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a class="btn btn-sm btn-info addTableRows" title="Add more"
                                                        onclick="addTableRow('loanSourceTable', 'loanSourceRow');"><i
                                                            class="fa fa-plus"></i></a>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </fieldset>

        <h3>{!! trans('CompanyProfile::messages.general_information') !!}</h3>
        <fieldset>
            {{-- Company manpower --}}
            <div class="card card-magenta border border-magenta">
                <div  class="card-header">
                    {!! trans('CompanyProfile::messages.raw_material_package_details') !!}
                </div>
                <div class="card-body" style="padding: 15px 25px;">
                    <div class="card card-magenta border border-magenta">
                        <div class="card-header">
                                ক. {!! trans('CompanyProfile::messages.locally_collected') !!}
                        </div>

                        <div class="card-body table-responsive" style="padding: 15px 25px;">
                            <table id="localRawMaterialTable" class="table table-bordered dt-responsive" cellspacing="0"
                                width="100%">
                                <thead style="background-color: #F7F7F7">
                                    <tr>
                                        <th class="text-center">{!! trans('CompanyProfile::messages.raw_material_package_name') !!}</th>
                                        <th class="text-center">{!! trans('CompanyProfile::messages.amount') !!}</th>
                                        <th class="text-center">{!! trans('CompanyProfile::messages.amount_unit') !!}</th>
                                        <th class="text-center">{!! trans('CompanyProfile::messages.price') !!}</th>
                                        <th class="text-center">{!! trans('CompanyProfile::messages.action') !!}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($localRawMaterials->isNotEmpty())
                                        <?php $inc = 0; ?>
                                        @foreach ($localRawMaterials as $localRawMaterial)
                                            <tr id="localRawMaterialRow{{ $inc }}" data-number="1">
                                                <td>
                                                    {!! Form::hidden("local_raw_material[$inc]", $localRawMaterial->id) !!}

                                                    {!! Form::text("local_raw_material_name[$inc]", $localRawMaterial->local_raw_material_name, ['class' => 'form-control input-md text-center', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'local_raw_material_name']) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text("local_raw_material_quantity[$inc]", $localRawMaterial->local_raw_material_quantity, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => trans('CompanyProfile::messages.write_amount'), 'id' => 'local_raw_material_quantity']) !!}
                                                </td>
                                                <td>
                                                    {!! Form::select("local_raw_material_unit[$inc]", $apc_unit, $localRawMaterial->local_raw_material_unit, ['class' => 'form-control input-md tabInput', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'local_raw_material_unit']) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text("local_raw_material_amount_bdt[$inc]", $localRawMaterial->local_raw_material_amount_bdt, ['class' => 'form-control input-md text-center onlyNumber input_ban local_raw_material_amount_bdt tabInput', 'onkeyup' => "calculateMachineryTotal('local_raw_material_amount_bdt', 'local_raw_price_total')", 'placeholder' => trans('CompanyProfile::messages.price_lak_taka'), 'id' => 'local_raw_material_amount_bdt']) !!}
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($inc == 0) { ?>
                                                    <a class="btn btn-sm btn-info addTableRows" title="Add more"
                                                        onclick="addTableRow('localRawMaterialTable', 'localRawMaterialRow0');"><i
                                                            class="fa fa-plus"></i></a>
                                                    <?php } else { ?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger removeRow"
                                                        onclick="removeTableRow('localRawMaterialTable','localRawMaterialRow{{ $inc }}');">
                                                        <i class="fa fa-times" aria-hidden="true"></i></a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php $inc++; ?>
                                        @endforeach
                                    @else
                                        <tr id="localRawMaterialRow" data-number="1">
                                            <td>
                                                {!! Form::text('local_raw_material_name[0]', '', ['class' => 'form-control input-md text-center', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'local_raw_material_name']) !!}
                                            </td>
                                            <td>
                                                {!! Form::text('local_raw_material_quantity[0]', '', ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => trans('CompanyProfile::messages.write_amount'), 'id' => 'local_raw_material_quantity']) !!}
                                            </td>
                                            <td>
                                                {!! Form::select('local_raw_material_unit[0]', $apc_unit, null, ['class' => 'form-control input-md tabInput', 'placeholder' => trans('CompanyProfile::messages.select'), 'id' => 'local_raw_material_unit']) !!}
                                            </td>
                                            <td>
                                                {!! Form::text('local_raw_material_amount_bdt[0]', '', ['class' => 'form-control input-md text-center onlyNumber input_ban local_raw_material_amount_bdt tabInput', 'onkeyup' => "calculateMachineryTotal('local_raw_material_amount_bdt', 'local_raw_price_total')", 'placeholder' => trans('CompanyProfile::messages.price_lak_taka'), 'id' => 'local_raw_material_amount_bdt']) !!}
                                            </td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-info addTableRows" title="Add more"
                                                    onclick="addTableRow('localRawMaterialTable', 'localRawMaterialRow');"><i
                                                        class="fa fa-plus"></i></a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tr>
                                    <td colspan="2" class="text-right">
                                        {!! trans('CompanyProfile::messages.grand_total') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('local_raw_price_total', isset($appInfo->local_raw_price_total) ? $appInfo->local_raw_price_total : '', ['class' => 'form-control input-md text-center onlyNumber input_ban', 'readonly', 'placeholder' => trans('CompanyProfile::messages.price_taka'), 'id' => 'local_raw_price_total']) !!}
                                        {!! $errors->first('local_raw_price_total', '<span class="help-block">:message</span>') !!}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="" class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                            <thead>
                                <tr class="section_heading2">
                                    <th class="text-center">{!! trans('CompanyProfile::messages.number') !!}</th>
                                    <th class="text-center">{!! trans('CompanyProfile::messages.raw_material_package_source') !!}</th>
                                    <th class="text-center">{!! trans('CompanyProfile::messages.n_number') !!}</th>
                                    <th class="text-center">{!! trans('CompanyProfile::messages.price_taka') !!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        ১
                                    </td>
                                    <td>
                                        স্থানীয়
                                    </td>
                                    <td>
                                        {!! Form::text('local_raw_material_number', $appInfo->raw_local_number, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => trans('CompanyProfile::messages.n_number'), 'id' => 'local_raw_material_number', 'onkeyup' => 'calculateRawMaterialNumber()']) !!}
                                        {!! $errors->first('local_raw_material_number', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('local_raw_material_price', $appInfo->raw_local_price, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => trans('CompanyProfile::messages.price_taka'), 'id' => 'local_raw_material_price', 'onkeyup' => 'calculateRawMaterialPrice()']) !!}
                                        {!! $errors->first('local_raw_material_price', '<span class="help-block">:message</span>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        2
                                    </td>
                                    <td>
                                        আমদানিযোগ্য
                                    </td>
                                    <td>
                                        {!! Form::text('import_raw_material_number', $appInfo->raw_imported_number, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => trans('CompanyProfile::messages.n_number'), 'id' => 'import_raw_material_number', 'onkeyup' => 'calculateRawMaterialNumber()']) !!}
                                        {!! $errors->first('import_raw_material_number', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('import_raw_material_price', $appInfo->raw_imported_price, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'placeholder' => trans('CompanyProfile::messages.price_taka'), 'id' => 'import_raw_material_price', 'onkeyup' => 'calculateRawMaterialPrice()']) !!}
                                        {!! $errors->first('import_raw_material_price', '<span class="help-block">:message</span>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        ৩
                                    </td>
                                    <td>
                                        সর্বমোট
                                    </td>
                                    <td>
                                        {!! Form::text('raw_material_total_number', $appInfo->raw_total_number, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'readonly', 'placeholder' => trans('CompanyProfile::messages.n_number'), 'id' => 'raw_material_total_number']) !!}
                                        {!! $errors->first('raw_material_total_number', '<span class="help-block">:message</span>') !!}
                                    </td>
                                    <td>
                                        {!! Form::text('raw_material_total_price', $appInfo->raw_total_price, ['class' => 'form-control input-md text-center onlyNumber input_ban tabInput', 'readonly', 'placeholder' => trans('CompanyProfile::messages.taka'), 'id' => 'raw_material_total_price']) !!}
                                        {!! $errors->first('raw_material_total_price', '<span class="help-block">:message</span>') !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </fieldset>


        <h3>{!! trans('CompanyProfile::messages.attachments') !!}</h3>
        <fieldset>
            {{-- Applicant info --}}
            <div class="card card-magenta border border-magenta">
                <div class="card-header">
                        {!! trans('CompanyProfile::messages.approved_applicant_info') !!}
                </div>

                <div class="card-body" style="padding: 15px 25px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('auth_person_nm', trans('CompanyProfile::messages.name'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('auth_person_nm') ? 'has-error' : '' }}">
                                    {!! Form::text('auth_person_nm', $appInfo->auth_person_nm, ['placeholder' => trans('CompanyProfile::messages.write_name'), 'class' => 'form-control input-md', 'id' => 'auth_person_nm']) !!}
                                    {!! $errors->first('auth_person_nm', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('auth_person_desig', trans('CompanyProfile::messages.designation'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('auth_person_desig') ? 'has-error' : '' }}">
                                    {!! Form::text('auth_person_desig', $appInfo->auth_person_desig, ['placeholder' => trans('CompanyProfile::messages.write_designation'), 'class' => 'form-control input-md', 'id' => 'auth_person_desig']) !!}
                                    {!! $errors->first('auth_person_desig', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                {!! Form::label('auth_person_address', trans('CompanyProfile::messages.address'), ['class' => 'col-md-2']) !!}
                                <div class="col-md-10 {{ $errors->has('auth_person_address') ? 'has-error' : '' }}"
                                    style="width: 79.5%; float: right">
                                    {!! Form::text('auth_person_address', $appInfo->auth_person_address, ['placeholder' => trans('CompanyProfile::messages.write_address'), 'class' => 'form-control input-md', 'id' => 'auth_person_address']) !!}
                                    {!! $errors->first('auth_person_address', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('auth_person_mobile', trans('CompanyProfile::messages.mobile_no'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('auth_person_mobile') ? 'has-error' : '' }}">
                                    {!! Form::text('auth_person_mobile', $appInfo->auth_person_mobil, ['placeholder' => trans('CompanyProfile::messages.write_mobile_no'), 'class' => 'form-control input-md input_ban onlyNumber', 'id' => 'auth_person_mobile', 'onkeyup' => 'mobile_no_validation(this.id)']) !!}
                                    {!! $errors->first('auth_person_mobile', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('auth_person_email', trans('CompanyProfile::messages.email'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('auth_person_email') ? 'has-error' : '' }}">
                                    {!! Form::text('auth_person_email', $appInfo->auth_person_email, ['placeholder' => trans('CompanyProfile::messages.write_email'), 'class' => 'form-control input-md email', 'id' => 'auth_person_email']) !!}
                                    {!! $errors->first('auth_person_email', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('authorization_letter', trans('CompanyProfile::messages.approved_permission_letter'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8 {{ $errors->has('authorization_letter') ? 'has-error' : '' }}">
                                    {!! Form::file('authorization_letter', ['class' => 'form-control input-md', 'id' => 'authorization_letter_edit', 'flag' => 'img']) !!}
                                    {!! $errors->first('authorization_letter', '<span class="help-block">:message</span>') !!}
                                    <span class="text-success"
                                        style="font-size: 9px; font-weight: bold; display: block;">[File Format: *.pdf/
                                        || Max-file size 2MB]
                                    </span>
                                    @isset($appInfo->authorization_letter)
                                        <a href="{{ '/uploads/' . $appInfo->authorization_letter }}" target="_blank"
                                            class="btn btn-sm btn-danger" style="margin-top: 5px">Open</a>
                                    @endisset
                                    <p style="font-size: 12px;">
                                        <a target="_blank" href="/csv-upload/sample/SamplAuthLetter.pdf">স্যাম্পল
                                            ফাইল</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('correspondent_photo', trans('CompanyProfile::messages.image'), ['class' => 'col-md-4']) !!}
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-sm-9">
                                            <input type="file" class="form-control input-sm"
                                                value="{{ $appInfo->auth_person_pic }}" name="correspondent_photo"
                                                id="correspondent_photo_edit"
                                                onchange="imageUploadWithCroppingAndDetect(this, 'correspondent_photo_preview', 'correspondent_photo_base64')"
                                                size="300x300" />
                                            <span class="text-success"
                                                style="font-size: 9px; font-weight: bold; display: block;">[File
                                                Format: *.jpg/ .jpeg/ .png | Width 300PX, Height 300PX]
                                                <p style="font-size: 12px;">
                                                    <a target="_blank" href="https://picresize.com/">আপনার ইমেজ মডিফাই
                                                        করতে পারেন</a>
                                                </p>
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="center-block image-upload" for="correspondent_photo">
                                                <figure>
                                                    <img src="{{ !empty($appInfo->auth_person_pic) ? url('/' . $appInfo->auth_person_pic) : url('assets/images/photo_default.png') }}"
                                                        class="img-responsive img-thumbnail"
                                                        id="correspondent_photo_preview" />
                                                </figure>
                                                <input type="hidden" id="correspondent_photo_base64"
                                                    name="correspondent_photo_base64" />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Necessary attachment --}}
            <div class="card card-magenta border border-magenta">
                <div class="card-header">
                        {!! trans('CompanyProfile::messages.necessary_attachment') !!}
                </div>
                <div class="card-body" style="padding: 15px 25px;">
                    <input type="hidden" id="doc_type_key" name="doc_type_key">
                    <div id="docListDiv"></div>
                    <span>
                        <p>সংযুক্তির নির্দেশিকা- সার্ভিসের নাম ও প্রতিষ্ঠানের ধরণ নির্ধারণের পর সংযুক্তির তালিকা
                            প্রদর্শিত হবে।</p>
                    </span>
                </div>
            </div>

            {{-- Announcement --}}
            <div class="card card-magenta border border-magenta">
                <div class="card-header">
                   {!! trans('CompanyProfile::messages.announcement') !!}
                </div>
                <div class="card-body" style="padding: 15px 25px;">
                    <p style="color: #452A73;">
                        বাংলাদেশ ক্ষুদ্র ও কুটির শিল্প করপোরেশন (বিসিক) এর ওয়ান স্টপ সার্ভিস সিস্টেমের
                        মাধ্যমে জমাকৃত আবেদনপত্রে কোন ধরণের অসঙ্গতি পরিলক্ষিত হলে গণপ্রজাতন্ত্রী বাংলাদেশ
                        সরকারের আইসিটি আইনের আওতায় দায়বদ্ধ থাকবেন।
                    </p>
                    <br>
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('accept_terms', 1, null, ['id' => 'accept_terms', 'class' => 'required']) !!}
                            আমি এই মর্মে ঘোষণা করছি যে উপরে বর্ণিত যাবতীয় তথ্য সঠিক আছে। কোন তথ্য গোপন করা
                            হয় নাই। কোন তথ্য সঠিক পাওয়া না গেলে কর্তৃপক্ষ শিল্প প্রতিষ্ঠানটির নিবন্ধন বাতিল
                            করতে পারবেন এবং যে কোন প্রকার প্রশাসনিক ও আইনানুগ ব্যবস্থা গ্রহণ করতে পারবে। আমি
                            আরো ঘোষণা করছি যে, কর্তৃপক্ষ কর্তৃক সরেজমিনে পরিদর্শনকালে সকল প্রকার সহযোগিতা
                            প্রদান করতে বাধ্য থাকবো।
                        </label>
                    </div>
                </div>
            </div>
        </fieldset>

        <h3>{!! trans('CompanyProfile::messages.payment_and_submit') !!}</h3>
        <fieldset>

            <div id="paymentPanel"></div>

            {{-- <div class="card">
                <div style="border-bottom: none; padding: 7px 25px;">
                    <h4 class="card-header" style="margin: 0; color: #452A73; font-size: 18px; font-weight: 400">
                        {!!trans('IndustryNew::messages.service_fee_payment')!!}
                    </h4>
                </div>
                <div class="card-body" style="padding: 15px 25px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('sfp_contact_name','যোগাযোগ কারীর নাম ',['class'=>'col-md-4 text-left required-star']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('sfp_contact_name',  $appInfo->sfp_contact_name, ['class' => 'form-control input-md required']) !!}
                                    {!! $errors->first('sfp_contact_name','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('sfp_contact_email',' যোগাযোগ কারীর ইমেইল',['class'=>'col-md-4 text-left required-star']) !!}
                                <div class="col-md-8">
                                    {!! Form::email('sfp_contact_email', $appInfo->sfp_contact_email, ['class' => 'form-control input-md required email']) !!}
                                    {!! $errors->first('sfp_contact_email','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('sfp_contact_phone','যোগাযোগকারীর মোবাইল নং ',['class'=>'col-md-4 text-left required-star']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('sfp_contact_phone', Auth::user()->user_mobile, ['class' => 'form-control input-md sfp_contact_phone required helpText15 phone_or_mobile onlyNumber input_ban' , 'onkeyup' => 'mobile_no_validation(this.id)']) !!}
                                    {!! $errors->first('sfp_contact_phone','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('sfp_contact_address','যোগাযোগের ঠিকানা',['class'=>'col-md-4 text-left required-star']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('sfp_contact_address', $appInfo->sfp_contact_address, ['class' => 'form-control input-md required']) !!}
                                    {!! $errors->first('sfp_contact_address','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('sfp_pay_amount','পে এমাউন্ট',['class'=>'col-md-3 text-left']) !!}
                                <div class="col-md-1 "><i class="fa fa-cog" data-toggle="modal" data-target="#feeModal" title="সার্ভিস ফি এর বিষয়ে জানতে ক্লিক করুন"></i></div>
                                <div class="col-md-8">
                                    {!! Form::text('sfp_pay_amount', $appInfo->sfp_pay_amount, ['class' => 'form-control input-md', 'readonly', 'id'=>'sfp_pay_amount']) !!}
                                    {!! $errors->first('sfp_pay_amount','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('sfp_vat_on_pay_amount','পে এমাউন্টের উপর  ভ্যাট ',['class'=>'col-md-4 text-left']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('sfp_vat_on_pay_amount', $appInfo->sfp_vat_tax, ['class' => 'form-control input-md', 'readonly']) !!}
                                    {!! $errors->first('sfp_vat_on_pay_amount','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('sfp_total_amount','মোট টাকা',['class'=>'col-md-4 text-left']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('sfp_total_amount', number_format($appInfo->sfp_total_amount, 2), ['class' => 'form-control input-md', 'readonly']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('sfp_status','পেমেন্টের অবস্থা',['class'=>'col-md-4 text-left']) !!}
                                <div class="col-md-8">
                                    @if ($appInfo->sfp_payment_status == 0)
                                        <span class="label label-warning">Pending</span>
                                    @elseif($appInfo->sfp_payment_status == -1)
                                        <span class="label label-info">In-Progress</span>
                                    @elseif($appInfo->sfp_payment_status == 1)
                                        <span class="label label-success">Paid</span>
                                    @elseif($appInfo->sfp_payment_status == 2)
                                        <span class="label label-danger">-Exception</span>
                                    @elseif($appInfo->sfp_payment_status == 3)
                                        <span class="label label-warning">Waiting for Payment Confirmation</span>
                                    @else
                                        <span class="label label-warning">invalid status</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger" role="alert">
                                    <strong>ভ্যাট / ট্যাক্স </strong> এবং <strong>লেনদেনের চার্জ </strong> আনুমানিক পরিমাণ, সোনালী ব্যাংকের পেমেন্ট সিস্টেমের উপর ভিত্তি করে এগুলি পরিবর্তিত হতে পারে এবং অর্থ প্রদানের পরে তা এখানে দৃশ্যমান হবে।
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </fieldset>

        @if(ACL::getAccsessRight('IndustryNew','-E-') && $appInfo->status_id != 6 && Auth::user()->user_type == '5x505')

            @if(!in_array($appInfo->status_id,[5]))
        <div class="float-left">
            <button type="submit" class="btn btn-info btn-md cancel" value="draft" name="actionBtn"
                id="save_as_draft">Save as Draft
            </button>
        </div>
        <div class="float-left" style="padding-left: 1em;">
            <button type="submit" id="submitForm" style="cursor: pointer;" class="btn btn-success btn-md"
                value="Submit" name="actionBtn">Pay Now
                <i class="fa fa-question-circle" style="cursor: pointer" data-toggle="tooltip" title=""
                    data-original-title="After clicking this button will take you in the payment portal for providing the required payment. After payment, the application will be automatically submitted and you will get a confirmation email with necessary info."
                    aria-describedby="tooltip"></i>
            </button>
        </div>
                @endif


                @if($appInfo->status_id == 5)
                    <div class="float-left">
                        <span style="display: block; height: 34px">&nbsp;</span>
                    </div>
                    <div class="float-left" style="padding-left: 1em;">
                        <button type="submit" id="submitForm" style="cursor: pointer;"
                                class="btn btn-info btn-md"
                                value="Re-submit" name="actionBtn">Re-submit
                        </button>
                    </div>
                @endif

        @endif



        {!! Form::close() !!}

    </div>

</div>{{-- Industry registration --}}

@include('partials.datatable-js')
<script src="{{ asset("assets/plugins/jquery-steps/jquery.steps.js") }}"></script>

@include('partials.image-upload')
<script>
    function enablePath() {
        document.getElementById('company_type_id').disabled = "";
        document.getElementById('company_office_division_id').disabled = "";
        document.getElementById('company_office_district_id').disabled = "";
        document.getElementById('company_office_thana_id').disabled = "";
    }

    var selectCountry = '';

    $(document).ready(function() {

        @isset($companyInfo->office_division)
            getDistrictByDivisionId('company_office_division_id', {{ $companyInfo->office_division ?? '' }},
                'company_office_district_id', {{ $companyInfo->office_district ?? '' }});
        @endisset
        @isset($companyInfo->office_district)
            getThanaByDistrictId('company_office_district_id', {{ $companyInfo->office_district ?? '' }},
                'company_office_thana_id', {{ $companyInfo->office_thana ?? '' }});
        @endisset


        @isset($companyInfo->factory_division)
            getDistrictByDivisionId('company_factory_division_id',
                {{ $companyInfo->factory_division ?? '' }},
                'company_factory_district_id', {{ $companyInfo->factory_district ?? '' }});
        @endisset
        @isset($companyInfo->factory_district)
            getThanaByDistrictId('company_factory_district_id', {{ $companyInfo->factory_district ?? '' }},
                'company_factory_thana_id', {{ $companyInfo->factory_thana ?? '' }});
        @endisset

        // ceo section
        @isset($companyInfo->ceo_division)
            getDistrictByDivisionId('company_ceo_division_id', {{ $companyInfo->ceo_division ?? '' }},
                'company_ceo_district_id', {{ $companyInfo->ceo_district ?? '' }});
        @endisset
        @isset($companyInfo->ceo_district)
            getThanaByDistrictId('company_ceo_district_id', {{ $companyInfo->ceo_district ?? '' }},
                'company_ceo_thana_id', {{ $companyInfo->ceo_thana ?? '' }});
        @endisset

        // loadApplicationDocs('docListDiv', 'stable_small_forgen');

        var form = $("#application_form").show();
        // form.find('#save_as_draft').css('display','none');
        form.find('#submitForm').css('display', 'none');
        form.find('.next').addClass('btn-primary');
        form.steps({
            headerTag: "h3",
            bodyTag: "fieldset",
            transitionEffect: "slideLeft",
            onStepChanging: function (event, currentIndex, newIndex) {
                return true;
                if (newIndex == 1) {
                    return true;
                    var imagePath =
                        "{{ !empty($companyInfo->entrepreneur_signature) ? $companyInfo->entrepreneur_signature : '' }}";

                    if (imagePath == "") {
                        if ($("#correspondent_signature").hasClass("error") || $(
                            "#correspondent_signature").val() == "") {
                            $('.signature-upload').addClass('custom_error')
                        } else {
                            $('.signature-upload').removeClass('custom_error')
                        }
                    }
                    // retuauth_person_mobilern true;
                    // Allways allow previous action even if the current form is not valid!
                    if (currentIndex > newIndex) {
                        return true;
                    }

                }

                if (newIndex == 2) {
                    local_machinery_ivst = $('#local_machinery_ivst').val();

                    local_machinery_total = parseInt($('#local_machinery_total').val());
                    imported_machinery_total = parseInt($('#imported_machinery_total').val());
                    totalsum = local_machinery_total + imported_machinery_total;
                    // alert(local_machinery_ivst);
                    // alert(totalsum);

                    if (local_machinery_ivst != totalsum) {
                        new swal({
                            type: 'error',
                            text: 'নোটিশঃ আবেদন এর প্রথম ধাপের "চ. বিনিয়োগ" সেকশনের মধ্যে "স্থায়ী বিনিয়োগ" সাব-সেকশনের এর "যন্ত্রপাতি ও সরঞ্জামাদি" এর জন্য যত টাকা দেয়া হবে, আবেদনের দ্বিতীয় ধাপে যন্ত্রপাতি ও সরঞ্জামাদির তথ্য তে স্থানীয়ভাবে সংগৃহীত/সংগৃহীতব্য + আমদানিকৃত/আমদানিতব্য মোট টাকার পরিমান সমান হতে হবে। অন্যথায় আবেদন গ্রহণ করা যাবেনা।',
                        });
                        return false;
                    }
                    // local_machinery_ivst == local_machinery_total+imported_machinery_total
                }

                if (currentIndex > newIndex) {
                    return true;
                }
                // Forbid next action on "Warning" step if the user is to young
                if (newIndex === 3 && Number($("#age-2").val()) < 18) {
                    return false;
                }
                // Needed in some cases if the user went back (clean up)
                if (currentIndex < newIndex) {
                    // To remove error styles
                    form.find(".body:eq(" + newIndex + ") label.error").remove();
                    form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
                }
                form.validate().settings.ignore = ":disabled,:hidden";
                return form.valid();
            },
            onStepChanged: function (event, currentIndex, priorIndex) {

                if (currentIndex > 0) {
                    $('.actions > ul > li:first-child').attr('style', '');
                } else {
                    $('.actions > ul > li:first-child').attr('style', 'display:none');
                }
                if (currentIndex === 1) {
                    attachmentLoad();
                }
                // Used to skip the "Warning" step if the user is old enough.
                if (currentIndex === 2 && Number($("#age-2").val()) >= 18) {
                    form.steps("next");
                }
                if (currentIndex === 3) {
                    form.find('#submitForm').css('display', 'block');
                    form.find('.previous').addClass('prevMob');

                } else {
                    $('ul[aria-label=Pagination] input[class="btn"]').remove();
                    form.find('#submitForm').css('display', 'none');
                    form.find('.previous').removeClass('prevMob');
                }
            },
            onFinishing: function (event, currentIndex) {
                form.validate().settings.ignore = ":disabled";
                return form.valid();
            },
            onFinished: function (event, currentIndex) {
                errorPlacement: function errorPlacement(error, element) {
                    element.before(error);
                }
            }
        }).validate({
            errorPlacement: function errorPlacement(error, element) {
                element.before(error);
            },
            rules: {
                confirm: {
                    equalTo: "#password-2"
                }
            }
        });
        var popupWindow = null;
        $('.finish').on('click', function (e) {

            if ($('#accept_terms').is(":checked")){
                if ($("#investing_country_id").data('select2')) {
                    $("#investing_country_id").select2('destroy');
                }
                $('#investing_country_id').removeAttr('multiple');
                $('#accaccept_termseptTerms').removeClass('error');
                $('#accept_terms').next('label').css('color', 'black');
                $('body').css({"display": "none"});
                popupWindow = window.open('<?php echo URL::to('/industry-new/preview'); ?>', 'Sample', '');
            } else {
                $('#acceptTerms').addClass('error');
                return false;
            }
        });

        function attachmentLoad() {
            var reg_type_id = parseInt($("#reg_type_id").val()); //order 1
            var company_type_id = parseInt($("#company_type_id").val()); //order 2
            var industrial_category_id = parseInt($("#industrial_category_id").val()); //order 3
            var investment_type_id = parseInt($("#investment_type_id").val()); //order 4

            var key = reg_type_id + '-' + company_type_id + '-' + industrial_category_id + '-' +
                investment_type_id

            $("#doc_type_key").val(key);
            console.log(key)

            loadApplicationDocs('docListDiv', key);
        }
        // loadApplicationDocs('docListDiv', '1-1-1-1');
    });

    $(document).ready(function() {

        var today = new Date();
        var yyyy = today.getFullYear();

        $('.datepicker').datetimepicker({
            viewMode: 'years',
            format: 'DD-MM-YYYY',
            extraFormats: ['DD.MM.YY', 'DD.MM.YYYY'],
            maxDate: 'now',
            minDate: '01/01/1905'
        });

        $('.datepickerProject').datetimepicker({
            viewMode: 'years',
            format: 'DD-MM-YYYY',
            extraFormats: ['DD.MM.YY', 'DD.MM.YYYY'],
            maxDate: '01/01/' + (yyyy + 20),
            minDate: '01/01/1905'
        })

        // Bangla step number
        $(".wizard>.steps .number").addClass('input_ban');

        {{-- initail -input mobile plugin script start --}}
        $("#company_office_mobile").intlTelInput({
            hiddenInput: "company_office_mobile",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $("#company_factory_mobile").intlTelInput({
            hiddenInput: "company_factory_mobile",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });
        $("#company_ceo_mobile").intlTelInput({
            hiddenInput: "company_ceo_mobile",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });
        $("#auth_person_mobile").intlTelInput({
            hiddenInput: "auth_person_mobile",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });
        $("#sfp_contact_phone").intlTelInput({
            hiddenInput: "sfp_contact_phone",
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });
        {{-- initail -input mobile plugin script end --}}

    })

    function mobile_no_validation(id) {
        var id = id;
        $("#" + id).on('keyup', function() {
            var countryCode = $("#" + id).intlTelInput("getSelectedCountryData").dialCode;

            if (countryCode === "880") {
                var mobile = $("#" + id).val();
                var reg = /^0/;
                if (reg.test(mobile)) {
                    $("#" + id).val("");
                }
                if (mobile.length != 10) {
                    $("#" + id).addClass('error')
                }
            }
        });
    }

    function calculateTotalDollar() {
        var total_fixed_ivst_million = $('#total_fixed_ivst_million').val();
        var usd_exchange_rate = $('#usd_exchange_rate').val();
        var usd_amount = (total_fixed_ivst_million / usd_exchange_rate).toFixed(2);
        document.getElementById('total_invt_dollar').value = usd_amount;
    }

    function calculateInvSourceTaka() {
        var ceo_taka_invest = parseFloat(document.getElementById('ceo_taka_invest').value);
        var local_loan_taka = parseFloat(document.getElementById('local_loan_taka').value);
        var foreign_loan_taka = parseFloat(document.getElementById('foreign_loan_taka').value);
        var total_inv_taka = ((isNaN(ceo_taka_invest) ? 0 : ceo_taka_invest) +
            (isNaN(local_loan_taka) ? 0 : local_loan_taka) +
            (isNaN(foreign_loan_taka) ? 0 : foreign_loan_taka)).toFixed(2);
        document.getElementById('total_inv_taka').value = total_inv_taka;
    }

    function calculateInvSourceDollar() {
        var ceo_dollar_invest = parseFloat(document.getElementById('ceo_dollar_invest').value);
        var local_loan_dollar = parseFloat(document.getElementById('local_loan_dollar').value);
        var foreign_loan_dollar = parseFloat(document.getElementById('foreign_loan_dollar').value);
        var total_inv_dollar = ((isNaN(ceo_dollar_invest) ? 0 : ceo_dollar_invest) +
            (isNaN(local_loan_dollar) ? 0 : local_loan_dollar) +
            (isNaN(foreign_loan_dollar) ? 0 : foreign_loan_dollar)).toFixed(2);
        document.getElementById('total_inv_dollar').value = total_inv_dollar;
    }

    function calculateRawMaterialNumber() {
        var local_raw_material_number = parseFloat(document.getElementById('local_raw_material_number').value);
        var import_raw_material_number = parseFloat(document.getElementById('import_raw_material_number').value);
        var raw_material_total_number = ((isNaN(local_raw_material_number) ? 0 : local_raw_material_number) +
            (isNaN(import_raw_material_number) ? 0 : import_raw_material_number)).toFixed(2);
        document.getElementById('raw_material_total_number').value = raw_material_total_number;
    }

    function calculateRawMaterialPrice() {
        var local_raw_material_price = parseFloat(document.getElementById('local_raw_material_price').value);
        var import_raw_material_price = parseFloat(document.getElementById('import_raw_material_price').value);
        var raw_material_total_price = ((isNaN(local_raw_material_price) ? 0 : local_raw_material_price) +
            (isNaN(import_raw_material_price) ? 0 : import_raw_material_price)).toFixed(2);
        document.getElementById('raw_material_total_price').value = raw_material_total_price;
    }
</script>

<script>
    var selectCountry = '';
    $(document).on('keydown', '#local_wc_ivst_ccy', function(e) {
        if (e.which == 9) {
            e.preventDefault();
            $('#usd_exchange_rate').focus();
        }
    })
    $(document).on('keydown', '#usd_exchange_rate', function(e) {
        if (e.which == 9) {
            e.preventDefault();
            $('#ceo_taka_invest').focus();
        }
    })
    $(document).on('change', '.companyInfoChange', function(e) {
        $('#same_address').trigger('change');
    })
    $(document).on('blur', '.companyInfoInput', function(e) {
        $('#same_address').trigger('change');
    })
    $(document).ready(function() {

        var check = $('#same_address').prop('checked');
        if ("{{ isset($companyInfo) && $companyInfo->is_same_address === 0 }}") {
            $('#company_factory_div').removeClass('hidden');
        }
        if (check == false) {
            $('#company_factory_div').removeClass('hidden');
        }

        $('#same_address').change(function() {

            if (this.checked === false) {
                $('#company_factory_div').removeClass('hidden');
                this.checked = false;
                // $('#company_factory_division_id').val($('#company_office_division_id').val())
                // getDistrictByDivisionId('company_factory_division_id', $('#company_office_division_id').val(), 'company_factory_district_id',$('#company_office_district_id').val());
                // getThanaByDistrictId('company_factory_district_id', $('#company_office_district_id').val(), 'company_factory_thana_id', $('#company_office_thana_id').val());
                // // $('#company_factory_thana_id').val($('#company_office_thana_id').val())
                // $('#company_factory_postCode').val($('#company_office_postCode').val())
                // $('#company_factory_address').val($('#company_office_address').val())
                // $('#company_factory_email').val($('#company_office_email').val())
                // $('#company_factory_mobile').val($('#company_office_mobile').val())
                //
                $("#company_factory_division_id").val("");
                $("#company_factory_district_id").val("");
                $("#company_factory_thana_id").val("");
                $("#company_factory_postCode").val("");
                $("#company_factory_address").val("");
                $("#company_factory_email").val("");
                $("#company_factory_mobile").val("");
            } else {
                this.checked = true;
                $('#company_factory_div').addClass('hidden');
                $("#company_factory_division_id").val("").removeClass('error');
                $("#company_factory_district_id").val("").removeClass('error');
                $("#company_factory_thana_id").val("").removeClass('error');
                $("#company_factory_postCode").val("").removeClass('error');
                $("#company_factory_address").val("").removeClass('error');
                $("#company_factory_email").val("").removeClass('error');
                $("#company_factory_mobile").val("").removeClass('error');
            }

        });

        // $("#same_address").trigger('change');

        $("#investment_type_id").change(function() {
            var investment_type_id = $('#investment_type_id').val();
            $(this).after('<span class="loading_data">Loading...</span>');
            var self = $(this);
            $.ajax({
                type: "GET",
                url: "{{ url('client/company-profile/get-country-by-investment-type') }}",
                data: {
                    investment_type_id: investment_type_id
                },
                success: function(response) {
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
                        var option =
                            '<option value="">{{ trans('CompanyProfile::messages.select') }}</option>';
                    }
                    selectCountry = "{{ $investing_country->country_id ?? '' }}";
                    if (response.responseCode == 1) {
                        $.each(response.data, function(id, value) {
                            var repId = (id.replace(' ', ''))
                            if ($.inArray(repId, selectCountry.split(',')) != -1) {
                                option += '<option value="' + repId +
                                    '" selected>' + value + '</option>';
                            } else {
                                option += '<option value="' + repId + '">' + value +
                                    '</option>';
                            }

                        });
                    }
                    $("#investing_country_id").html(option);
                    $(self).next().hide();


                }
            });
        });
        $("#investment_type_id").trigger('change');

        $("#total_investment").change(function() {
            var total_investment = $('#total_investment').val();
            var vat_percentage = parseFloat('{{ $vat_percentage }}');

            let oss_fee = 0;
            let vat = 0;

            $.ajax({
                type: "GET",
                url: "{{ url('client/company-profile/get-industry-type-by-investment') }}",
                data: {
                    total_investment: total_investment
                },
                success: function(response) {
                    $("#industrial_category_id").val(response.data);
                    if (response.data !== "") {
                        $("#industrial_category_id").find("[value!='" + response.data +
                            "']").prop("disabled", true);
                        $("#industrial_category_id").find("[value='" + response.data + "']")
                            .prop("disabled", false);
                    }

                    oss_fee = parseFloat(response.oss_fee);
                    vat = (oss_fee / 100) * vat_percentage;
                },
                complete: function() {
                    var unfixed_amounts = {
                        1: oss_fee,
                        2: 0,
                        3: 0,
                        4: vat,
                        5: 0,
                        6: 0
                    };
                    loadPaymentPanel('', '{{ $process_type_id }}', '1',
                        'paymentPanel', "{{ $appInfo->sfp_contact_name }}", "{{ $appInfo->sfp_contact_email }}", "{{ $appInfo->sfp_contact_phone }}",
                        "{{ $appInfo->sfp_contact_address }}", unfixed_amounts);
                }
            });
        });

        $("#total_investment").trigger('change');

        $("#industrial_sector_id").change(function() {
            var industrial_sector_id = $('#industrial_sector_id').val();
            $(this).after('<span class="loading_data">Loading...</span>');
            var self = $(this);
            $.ajax({
                type: "GET",
                url: "{{ url('client/company-profile/get-sub-sector-by-sector') }}",
                data: {
                    industrial_sector_id: industrial_sector_id
                },
                success: function(response) {

                    var option =
                        '<option value="">{{ trans('CompanyProfile::messages.select') }}</option>';
                    if (response.responseCode == 1) {
                        $.each(response.data, function(id, value) {

                            option += '<option value="' + id + '">' + value +
                                '</option>';
                        });
                    }
                    $("#industrial_sub_sector_id").html(option);
                    @if (isset($companyInfo->ins_sub_sector_id))
                        $("#industrial_sub_sector_id").val(
                            "{{ $companyInfo->ins_sub_sector_id }}").change();
                    @endif
                    $(self).next().hide();
                }
            });
        });

        // $("#industrial_sector_id").trigger('change');

        // Sales (in 100%)
        $("#local_sales_per").on('keyup', function() {
            var local_sales_per = this.value;
            if (local_sales_per <= 100 && local_sales_per >= 0) {
                var cal = 100 - local_sales_per;
                $('#foreign_sales_per').val(cal);
                // $("#total_sales").val(100);
            } else {
                alert("Please select a value between 0 & 100");
                $('#local_sales_per').val(0);
                $('#foreign_sales_per').val(0);
                // $("#total_sales").val(0);
            }
        });

        $("#foreign_sales_per").on('keyup', function() {
            var foreign_sales_per = this.value;
            if (foreign_sales_per <= 100 && foreign_sales_per >= 0) {
                var cal = 100 - foreign_sales_per;
                $('#local_sales_per').val(cal);
                // $("#total_sales").val(100);
            } else {
                alert("Please select a value between 0 & 100");
                $('#local_sales_per').val(0);
                $('#foreign_sales_per').val(0);
                // $("#total_sales").val(0);
            }
        });

        //------- Manpower start -------//
        $('#manpower').find('input').keyup(function() {
            var local_male = $('#local_male').val() ? parseFloat($('#local_male').val()) : 0;
            var local_female = $('#local_female').val() ? parseFloat($('#local_female').val()) : 0;
            var local_total = parseInt(local_male + local_female);
            $('#local_total').val(local_total);


            var foreign_male = $('#foreign_male').val() ? parseFloat($('#foreign_male').val()) : 0;
            var foreign_female = $('#foreign_female').val() ? parseFloat($('#foreign_female').val()) :
                0;
            var foreign_total = parseInt(foreign_male + foreign_female);
            $('#foreign_total').val(foreign_total);

            var mp_total = parseInt(local_total + foreign_total);
            $('#mp_total').val(mp_total);

            var mp_ratio_local = parseFloat(local_total / mp_total);
            var mp_ratio_foreign = parseFloat(foreign_total / mp_total);

            //            mp_ratio_local = Number((mp_ratio_local).toFixed(3));
            //            mp_ratio_foreign = Number((mp_ratio_foreign).toFixed(3));

            //---------- code from bida old
            mp_ratio_local = ((local_total / mp_total) * 100).toFixed(2);
            mp_ratio_foreign = ((foreign_total / mp_total) * 100).toFixed(2);
            // if (foreign_total == 0) {
            //     mp_ratio_local = local_total;
            // } else {
            //     mp_ratio_local = Math.round(parseFloat(local_total / foreign_total) * 100) / 100;
            // }
            // mp_ratio_foreign = (foreign_total != 0) ? 1 : 0;
            // End of code from bida old -------------

            $('#mp_ratio_local').val(mp_ratio_local);
            $('#mp_ratio_foreign').val(mp_ratio_foreign);

        });

        LoadListOfDirectors();
    })
</script>
<script>
    function openModal(btn) {
        //e.preventDefault();
        var this_action = btn.getAttribute('data-action');
        if (this_action != '') {
            $.get(this_action, function(data, success) {
                if (success === 'success') {
                    $('#myModal .load_modal').html(data);
                } else {
                    $('#myModal .load_modal').html('Unknown Error!');
                }
                $('#myModal').modal('show', {
                    backdrop: 'static'
                });
            });
        }
    }

    $(document).ready(function() {
        if ("{{ $companyInfo->nid ?? '' }}") {
            $("#company_ceo_passport_section").addClass("hidden");
            $("#company_ceo_nid_section").removeClass("hidden");
        } else {
            $("#company_ceo_passport_section").removeClass("hidden");
            $("#company_ceo_nid_section").addClass("hidden");
        }
    })

    //Load list of directors
    function LoadListOfDirectors() {
        $.ajax({
            url: "{{ url('client/company-profile/load-listof-directors-session') }}",
            type: "POST",
            data: {
                {{-- app_id: "{{ Encryption::encodeId($appInfo->id) }}", --}}
                {{-- process_type_id: "{{ Encryption::encodeId($appInfo->process_type_id) }}", --}}
                _token: $('input[name="_token"]').val()
            },
            success: function(response) {
                var html = '';
                if (response.responseCode == 1) {

                    var edit_url = "{{ url('/client/company-profile/edit-director') }}";
                    var delete_url = "{{ url('/client/company-profile/delete-director-session') }}";

                    var count = 1;
                    $.each(response.data, function(id, value) {
                        var sl = count++;
                        html += '<tr>';
                        html += '<td>' + sl + '</td>';
                        html += '<td>' + value.l_director_name + '</td>';
                        html += '<td>' + value.l_director_designation + '</td>';
                        html += '<td>' + value.nationality + '</td>';
                        html += '<td>' + value.nid_etin_passport + '</td>';
                        html += '<td>' +
                            '<a data-toggle="modal" data-target="#directorModel" onclick="openModal(this)" data-action="' +
                            edit_url + '/' + id +
                            '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a>';
                        if (sl != 1) {
                            html += '<a data-action="' + delete_url + '/' + id +
                                '" onclick="ConfirmDelete(this)" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>';
                        }
                        html += '</td>';
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
                            $("#company_ceo_nationality").attr('readonly', true);
                            $("#company_ceo_nationality").val(18);
                        }

                        if (response.ceoInfo.identity_type == 'passport') {
                            $("#company_ceo_passport_section").removeClass("hidden");
                            $("#company_ceo_nid_section").addClass("hidden");
                            $("#company_ceo_passport").val(response.ceoInfo.nid_etin_passport);
                            $("#company_ceo_nationality").attr('readonly', false);
                            $("#company_ceo_nationality").val('');
                            $("#company_ceo_nid").val('');
                        } else {
                            $("#company_ceo_passport_section").addClass("hidden");
                            $("#company_ceo_nid_section").removeClass("hidden");
                            $("#company_ceo_nid").val(response.ceoInfo.nid_etin_passport);
                            $("#company_ceo_passport").val('');
                        }
                    } else {
                        $(".ceoInfoDirector").removeClass('hidden');
                        // $("#ceoInfoDIV").addClass('hidden');
                    }

                } else {
                    html += '<tr>';
                    html += '<td colspan="5" class="text-center">' +
                        '<span class="text-danger">No data available!</span>' + '</td>';
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
                success: function(response) {
                    if (response.responseCode == 1) {
                        toastr.success(response.msg);
                    }

                    LoadListOfDirectors();
                }
            });

        } else {
            return false;
        }
    }
</script>

<script>
    function calculateMachineryTotal(className, totalShowFieldId) {
        var total = 0.00;
        $("." + className).each(function() {
            total = total + (this.value ? parseFloat(this.value) : 0.00);
        })
        $("#" + totalShowFieldId).val(total.toFixed(2));
    }
</script>
<script>
    $(document).ready(function() {
        $('#business_category_id').on('change', function() {
            var businessCategoryId = $('#business_category_id').val();
            var oldBusinessCategoryId =
                '{{ isset($companyInfo->business_category_id) ? $companyInfo->business_category_id : '' }}';

            if (businessCategoryId != oldBusinessCategoryId) {
                $('#company_ceo_designation_id').val('');
            } else {
                $('#company_ceo_designation_id').val(
                    '{{ isset($companyInfo->designation) ? $companyInfo->designation : '' }}');
            }
        })
    })
</script>

<script type="text/javascript">
    $(function() {
        var today = new Date();
        var yyyy = today.getFullYear();

        $('.datetimepicker4').datetimepicker({
            format: 'DD-MMM-YYYY',
            maxDate: 'now',
            minDate: '01/01/' + (yyyy - 110),
            ignoreReadonly: true,
        });
    });
</script>
