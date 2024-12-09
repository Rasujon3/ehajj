@extends('layouts.admin')

@section('header-resources')
@endsection

@section('content')
    @include('partials.messages')

    @if (Auth::user()->is_approved != 1 or Auth::user()->is_approved != true)
        @include('message.un-approved')
    @else
            @if (isset($user_multiple_company) == 1 && in_array(Auth::user()->user_type, ['5x505', '6x606']))
                @include('CompanyAssociation::working-company-modal')
            @else
                @if(Auth::user()->user_type == '19x191' && Auth::user()->working_company_id == 0)
                    @include("REUSELicenseIssue::MedicineIssue.select-pharmacy")
                @else
                    @include('Dashboard::dashboard')
                @endif
            @endif
    @endif
@endsection

@section('footer-script')
    @yield('chart_script')
@endsection
