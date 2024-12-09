<style>
    label {
        font-weight: normal;
        font-size: 14px;
    }

    span {
        font-size: 14px;
    }

    .section_head {
        font-size: 24px;
        font-weight: 400;
        margin-top: 25px;
    }

    @media (min-width: 767px) {
        .addressField {
            width: 79.5%;
            float: right
        }
    }

    @media (max-width: 480px) {
        .section_head {
            font-size: 20px;
            font-weight: 400;
            margin-top: 5px;
        }

        label {
            font-weight: normal;
            font-size: 13px;
        }

        span {
            font-size: 13px;
        }

        .panel-body {
            padding: 10px 0 !important;
        }

        .form-group {
            margin: 0;
        }

        .image_mobile {
            width: 100%;
        }
    }
</style>


<div id="paymentPanel"></div>

<div class="card" style="border-radius: 10px;" id="applicationForm">
    <div style="padding: 10px 15px">
        <span class="section_head">{!! trans('CompanyProfile::messages.industry_reg') !!}</span>
    </div>
    <div class="card-body" style="padding: 0 15px"><br>

        {{-- General info --}}
        <div class="card card-magenta border border-magenta">
            <div  class="card-header">
                    {!! trans('CompanyProfile::messages.general_information') !!}
            </div>
            <div class="card-body">

                <div class="form-group">
                    <div class="row">
                        <div class="col-6 row">
                            {!! Form::label('company_name_bangla', trans('CompanyProfile::messages.company_name_bangla'), [
                                'class' => 'col-5',
                            ]) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->org_nm_bn }}</span>
                            </div>
                        </div>
                        <div class="col-6 row">
                            {!! Form::label('company_name_english', trans('CompanyProfile::messages.company_name_english'), [
                                'class' => 'col-5',
                            ]) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->org_nm }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-6 row">
                            {!! Form::label('project_name', trans('CompanyProfile::messages.project_name'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->project_nm }}</span>
                            </div>
                        </div>
                        <div class="col-6 row">
                            {!! Form::label('reg_type_id', trans('CompanyProfile::messages.reg_type'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->regist_name_bn }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-6 row">
                            {!! Form::label('company_type_id', trans('CompanyProfile::messages.company_type'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->company_type_bn }}</span>
                            </div>
                        </div>
                        <div class="col-6 row">
                            {!! Form::label('business_category_id', trans('CompanyProfile::messages.business_category'), [
                                'class' => 'col-5',
                            ]) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->business_category }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-6 row">
                            {!! Form::label('investment_type_id', trans('CompanyProfile::messages.invest_type'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->investment_type_bn }}</span>
                            </div>
                        </div>
                        <div class="col-6 row">
                            {!! Form::label('investing_country_id', trans('CompanyProfile::messages.investing_country'), [
                                'class' => 'col-5',
                            ]) !!}
                            <div class="col-7">
                                <span>:
                                    @foreach ($investing_country as $investing_countrys)
                                        {{ $investing_countrys->country_name }} ,
                                    @endforeach
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-6 row">
                            {!! Form::label('total_investment', trans('CompanyProfile::messages.total_investment'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>:</span><span class="input_ban"> {{ $appInfo->investment_limit }}</span>
                            </div>
                        </div>
                        <div class="col-6 row">
                            {!! Form::label('industrial_category_id', trans('CompanyProfile::messages.industrial_class'), [
                                'class' => 'col-5',
                            ]) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->ind_category_bn }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-6 row">
                            {!! Form::label('industrial_sector_id', trans('CompanyProfile::messages.industrial_sector'), [
                                'class' => 'col-5',
                            ]) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->ind_sector_bn }}</span>
                            </div>
                        </div>
                        <div class="col-6 row">
                            {!! Form::label('industrial_sub_sector_id', trans('CompanyProfile::messages.industrial_sub_sector'), [
                                'class' => 'col-5',
                            ]) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->ind_sub_sector_bn }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reg office info --}}
        <div class="card card-magenta border border-magenta">
            <div  class="card-header">
                    {!! trans('CompanyProfile::messages.pref_reg_office') !!}
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 row">
                            {!! Form::label('pref_reg_office', trans('CompanyProfile::messages.pref_reg_office'), ['class' => 'col-4']) !!}
                            <div class="col-8">
                                <span>: {{ $appInfo->reg_office_name_bn }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="card-header section_head">{!! trans('CompanyProfile::messages.reg_information') !!}</p>

        {{-- Activities info --}}
        <div class="card card-magenta border border-magenta">
            <div class="card-header">ক.
                    {!! trans('CompanyProfile::messages.company_main_works_info') !!}
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 row">
                            {!! Form::label('company_main_works', trans('CompanyProfile::messages.company_main_works'), [
                                'class' => 'col-md-2 col-xs-5',
                            ]) !!}
                            <div class="col-md-10 col-xs-7 addressField">
                                <span>: {{ $appInfo->main_activities }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Company annual production --}}
        <div class="card card-magenta border border-magenta">
            <div  class="card-header">খ.
                    {!! trans('CompanyProfile::messages.company_annual_production_capacity') !!}
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>{!! trans('CompanyProfile::messages.service_name') !!}</th>
                        <th>{!! trans('CompanyProfile::messages.amount') !!}</th>
                        <th>{!! trans('CompanyProfile::messages.amount_unit') !!}</th>
                        <th>{!! trans('CompanyProfile::messages.price_lak_taka') !!}</th>
                    </tr>
                    <tbody>
                        @foreach ($annualProductionCapacity as $item)
                            <tr>
                                <td>{{ $item->service_name }}</td>
                                <td class="input_ban">{{ $item->quantity }}</td>
                                <td class="input_ban">{{ $item->name_bn }}</td>
                                <td class="input_ban">{{ $item->amount_bdt }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-center">
                                {!! trans('CompanyProfile::messages.grand_total') !!}
                            </td>
                            <td class="text-center input_ban">
                                {{ $appInfo->apc_price_total }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sell_parcent --}}
        <div class="card card-magenta border border-magenta">
            <div  class="card-header">গ.
                    {!! trans('CompanyProfile::messages.sell_parcent') !!}
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-6 row">
                            {!! Form::label('local_sales_per', trans('CompanyProfile::messages.local'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>:</span><span class="input_ban"> {{ $appInfo->sales_local }}</span>
                            </div>
                        </div>
                        <div class="col-6 row">
                            {!! Form::label('foreign_sales_per', trans('CompanyProfile::messages.foreign'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>:</span><span class="input_ban"> {{ $appInfo->sales_foreign }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Company manpower --}}
        <div class="card card-magenta border border-magenta">
            <div class="card-header">ঘ. {!! trans('CompanyProfile::messages.company_man_power') !!}</div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="" class="table table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr class="section_heading1">
                                <th class="text-center" colspan="3">{!! trans('CompanyProfile::messages.local_bangladeshi') !!}</th>
                                <th class="text-center" colspan="3">{!! trans('CompanyProfile::messages.foreign') !!}</th>
                                <th class="text-center" colspan="3">{!! trans('CompanyProfile::messages.total') !!}</th>
                            </tr>
                            <tr class="text-center section_heading2">
                                <th>{!! trans('CompanyProfile::messages.men') !!}</th>
                                <th>{!! trans('CompanyProfile::messages.women') !!}</th>
                                <th>{!! trans('CompanyProfile::messages.total') !!}</th>
                                <th>{!! trans('CompanyProfile::messages.men') !!}</th>
                                <th>{!! trans('CompanyProfile::messages.women') !!}</th>
                                <th>{!! trans('CompanyProfile::messages.total') !!}</th>
                                <th>{!! trans('CompanyProfile::messages.grand_total') !!}</th>
                                <th>{!! trans('CompanyProfile::messages.local_rate') !!}</th>
                                <th>{!! trans('CompanyProfile::messages.foreign_rate') !!}</th>
                            </tr>
                        </thead>

                        <tbody id="manpower">
                            <tr class="text-center">
                                <td class="input_ban">
                                    {{ $appInfo->local_male }}
                                </td>
                                <td class="input_ban">
                                    {{ $appInfo->local_female }}
                                </td>
                                <td class="input_ban">
                                    {{ $appInfo->local_total }}
                                </td>
                                <td class="input_ban">
                                    {{ $appInfo->foreign_male }}
                                </td>
                                <td class="input_ban">
                                    {{ $appInfo->foreign_female }}
                                </td>
                                <td class="input_ban">
                                    {{ $appInfo->foreign_total }}
                                </td>
                                <td class="input_ban">
                                    {{ $appInfo->manpower_total }}
                                </td>
                                <td class="input_ban">
                                    {{ $appInfo->manpower_local_ratio }}
                                </td>
                                <td class="input_ban">
                                    {{ $appInfo->manpower_foreign_ratio }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        {{-- Necessary service description --}}
        <div class="card card-magenta border border-magenta">
            <div class="card-header">ঙ.
                    {!! trans('CompanyProfile::messages.necessary_services_details') !!}
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="section_heading1">
                        <th class="text-center">{!! trans('CompanyProfile::messages.necessary_services_name') !!}</th>
                        <th class="text-center">{!! trans('CompanyProfile::messages.has_connectivity_advantage') !!}</th>
                        <th class="text-center">{!! trans('CompanyProfile::messages.possible_distance_from_connection') !!}</th>
                    </thead>

                    <tbody id="">
                        @foreach ($utilityService as $item)
                            <tr>
                                <td>
                                    {{ $item->name_bn }}
                                </td>
                                <td class="text-center">
                                    @if ($item->services_availability == 1)
                                        হ্যাঁ
                                    @else
                                        না
                                    @endif
                                </td>
                                <td class="text-center">
                                    <?php

                                    $distance = '';
                                    if ($item->distance_unit == 'Meter') {
                                        $distance = trans('IndustryNew::messages.Meter');
                                    } elseif ($item->distance_unit == 'Kilometer') {
                                        $distance = trans('IndustryNew::messages.Kilometer');
                                    }

                                    ?>
                                    <span class="input_ban">{{ $item->utility_distance }}</span>
                                    {{ $distance }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Investment --}}
        <div class="card card-magenta border border-magenta">
            <div class="card-header">চ.
                    {!! trans('CompanyProfile::messages.investment') !!}
            </div>
            <div class="card-body">
                <table id="" class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead class="section_heading1">
                        <tr>
                            <th class="text-center" colspan="4">{!! trans('CompanyProfile::messages.investment_source') !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center section_heading2">
                            <td>{!! trans('CompanyProfile::messages.source') !!}</td>
                            <td>{!! trans('CompanyProfile::messages.taka') !!}</td>
                            <td>{!! trans('CompanyProfile::messages.dollar') !!}</td>
                            <td>{!! trans('CompanyProfile::messages.loan_org_country') !!}</td>
                        </tr>
                        <tr class="text-center" style="background: #F9F9F9">
                            <td>
                                {!! trans('CompanyProfile::messages.ceo_same_invest') !!}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->ceo_taka_invest }}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->ceo_dollar_invest }}
                            </td>
                            <td>
                                {{ $appInfo->ceo_loan_org_country }}
                            </td>
                        </tr>
                        <tr class="text-center" style="background: #FFFFFF">
                            <td>
                                {!! trans('CompanyProfile::messages.local_loan') !!}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->local_loan_taka }}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->local_loan_dollar }}
                            </td>
                            <td>
                                {{ $appInfo->local_loan_org_country }}
                            </td>

                        </tr>
                        <tr class="text-center" style="background: #F9F9F9">
                            <td>
                                {!! trans('CompanyProfile::messages.foreign_loan') !!}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->foreign_loan_taka }}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->foreign_loan_dollar }}
                            </td>
                            <td>
                                {{ $appInfo->foreign_loan_org_country }}
                            </td>

                        </tr>
                        <tr class="text-center" style="background: #FFFFFF">
                            <td class="text-right">
                                {!! trans('CompanyProfile::messages.grand_total') !!}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->total_inv_taka }}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->total_inv_dollar }}
                            </td>
                            <td>

                            </td>

                        </tr>
                    </tbody>
                </table>

                <div class="card">
                    <div style="background-color: #F7F7F7; border: 1px solid #DDDDDD; border-bottom: none;">
                        <h4 class="card-header" class="text-center"
                            style="font-size: 16px; margin-top: 5px; font-weight: bold">{!! trans('CompanyProfile::messages.country_wise_loan_source') !!}</h4>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <table id="loanSourceTable" class="table table-bordered dt-responsive" cellspacing="0"
                            width="100%">
                            <thead class="text-center section_heading1">
                                <tr style="font-size: 16px;">
                                    <th style="font-weight: normal;" class="text-center">{!! trans('CompanyProfile::messages.country_name') !!}</th>
                                    <th style="font-weight: normal;" class="text-center">{!! trans('CompanyProfile::messages.org_name') !!}</th>
                                    <th style="font-weight: normal;" class="text-center">{!! trans('CompanyProfile::messages.loan_amount') !!}</th>
                                    <th style="font-weight: normal;" class="text-center">{!! trans('CompanyProfile::messages.loan_taking_date') !!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loanSrcCountry as $item)
                                    <tr class="text-center" id="loanSourceRow" data-number="1">
                                        <td>
                                            {{ $item->country_name }}
                                        </td>
                                        <td>
                                            {{ $item->loan_org_nm }}
                                        </td>
                                        <td class="input_ban">
                                            {{ $item->loan_amount }}
                                        </td>
                                        <td class="input_ban">
                                            {{ date('d-m-Y', strtotime($item->loan_receive_date)) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Raw material details --}}
        <div class="card card-magenta border border-magenta">
            <div class="card-header">
                    {!! trans('CompanyProfile::messages.raw_material_package_details') !!}
            </div>
            <div class="card-body">
                <div class="card card-magenta border border-magenta">
                    <div  class="card-header" >
                            ক. {!! trans('CompanyProfile::messages.locally_collected') !!}
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr class="text-center">
                                <th class="text-center">{!! trans('CompanyProfile::messages.raw_material_package_name') !!}</th>
                                <th class="text-center">{!! trans('CompanyProfile::messages.amount') !!}</th>
                                <th class="text-center">{!! trans('CompanyProfile::messages.amount_unit') !!}</th>
                                <th class="text-center">{!! trans('CompanyProfile::messages.price_lak_taka') !!}</th>
                            </tr>
                            <tbody>
                                @foreach ($localRawMaterial as $item)
                                    <tr class="text-center">
                                        <td>{{ $item->local_raw_material_name }}</td>
                                        <td class="input_ban">{{ $item->local_raw_material_quantity }}</td>
                                        <td class="input_ban">{{ $item->name_bn }}</td>
                                        <td class="input_ban">{{ $item->local_raw_material_amount_bdt }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-center">
                                        {!! trans('CompanyProfile::messages.grand_total') !!}
                                    </td>
                                    <td class="text-center input_ban">
                                        {{ $appInfo->local_raw_price_total }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                <table id="" class="table table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr class="section_heading1">
                            <th class="text-center">{!! trans('CompanyProfile::messages.raw_material_package_source') !!}</th>
                            <th class="text-center">{!! trans('CompanyProfile::messages.n_number') !!}</th>
                            <th class="text-center">{!! trans('CompanyProfile::messages.price_taka') !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                স্থানীয়
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->raw_local_number }}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->raw_local_price }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                আমদানিযোগ্য
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->raw_imported_number }}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->raw_imported_price }}

                            </td>
                        </tr>
                        <tr>
                            <td>
                                সর্বমোট
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->raw_total_number }}
                            </td>
                            <td class="input_ban">
                                {{ $appInfo->raw_total_price }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <p class="section_head">{!! trans('CompanyProfile::messages.attachments') !!}</p>
        {{-- Applicant info --}}
        <div class="card card-magenta border border-magenta">
            <div class="card-header">
                    {!! trans('CompanyProfile::messages.approved_applicant_info') !!}
            </div>

            <div class="card-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-6 row">
                            {!! Form::label('auth_person_nm', trans('CompanyProfile::messages.name'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->auth_person_nm }}</span>
                            </div>
                        </div>
                        <div class="col-6 row">
                            {!! Form::label('auth_person_desig', trans('CompanyProfile::messages.designation'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->auth_person_desig }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 row">
                            {!! Form::label('auth_person_address', trans('CompanyProfile::messages.address'), [
                                'class' => 'col-md-2 col-xs-5',
                            ]) !!}
                            <div class="col-md-10 col-xs-7 addressField">
                                <span>: {{ $appInfo->auth_person_address }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-6 row">
                            {!! Form::label('auth_person_mobile', trans('CompanyProfile::messages.mobile_no'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>:</span><span class="input_ban"> {{ $appInfo->auth_person_mobile }}</span>
                            </div>
                        </div>
                        <div class="col-6 row">
                            {!! Form::label('auth_person_email', trans('CompanyProfile::messages.email'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span>: {{ $appInfo->auth_person_email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-6 row">
                            {!! Form::label('authorization_letter', trans('CompanyProfile::messages.approved_permission_letter'), [
                                'class' => 'col-5',
                            ]) !!}
                            <div class="col-7 align-items-start">
                                <span>:</span>
                                @isset($appInfo->authorization_letter)
                                    <a href="{{ '/uploads/' . $appInfo->authorization_letter }}" target="_blank"
                                        class="btn btn-info btn-sm">Open</a>
                                @endisset
                            </div>
                        </div>
                        <div class="col-6 row">
                            {!! Form::label('correspondent_photo', trans('CompanyProfile::messages.image'), ['class' => 'col-5']) !!}
                            <div class="col-7">
                                <span><img src="{{ '/' . $appInfo->auth_person_pic }}" alt="No image"
                                        width="75px"></span>
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
            </div>
        </div>

    </div>
</div>


<script src="{{ asset('assets/scripts/custom.min.js') }}"></script>
<script>
    $(document).on('click', '.cancelcounterpayment', function() {
        return confirm('Are you sure?');
    });

    var reg_type_id = "{{ $appInfo->regist_type }}";
    var company_type_id = "{{ $appInfo->org_type }}";
    var industrial_category_id = "{{ $appInfo->ind_category_id }}";
    var investment_type_id = "{{ $appInfo->invest_type }}";

    var key = reg_type_id + '-' + company_type_id + '-' + industrial_category_id + '-' + investment_type_id;

    loadApplicationDocs('docListDiv', key);


    @if (in_array(Auth::user()->user_type, ['5x505']) && in_array($appInfo->status_id, [15, 32]))

        var unfixed_amounts = <?php echo json_encode($unfixed_amounts); ?>

        loadPaymentPanel('{{ $appInfo->id }}', '{{ $process_type_id }}', '{{ $payment_step_id }}',
            'paymentPanel', "{{ CommonFunction::getUserFullName() }}",
                        "{{ Auth::user()->user_email }}",
                        "{{ Auth::user()->user_mobile }}",
                        "{{ Auth::user()->contact_address }}", unfixed_amounts);
    @endif
</script>
