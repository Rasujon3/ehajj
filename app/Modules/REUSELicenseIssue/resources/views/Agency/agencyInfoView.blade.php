
<style>
    /**
     * Hajj Agency Info
     */
    .form-searchbar .inline-src-group .btn{
        min-width: 110px;
        font-weight: normal;
        display: flex;
        column-gap: 8px;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .form-searchbar .inline-src-group .btn,
    .form-searchbar .inline-src-group .form-control{
        height: 40px;
    }

    .border-card-block.inner-card{
        border-color: #D9EDF7;
        box-shadow: none;
    }
    .border-card-block.inner-card .bd-card-head{
        background-color: #D9EDF7;
        color: #000000;
    }

    .row-gap-1{
        row-gap: 10px;
    }

    .hajj-agency-info .ehajj-list-table .table td{
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .hajj-agency-info .ehajj-list-table .table{
        min-width: 100%;
    }

    @media (max-width: 767px){
        .hajj-agency-info .ehajj-list-table .table{
            min-width: 100%;
        }
        .hajj-agency-info .ehajj-list-table .table > thead{
            display: none;
        }
        .hajj-agency-info .ehajj-list-table .table > tbody > tr > td{
            display: block;
            width: 100%;
            text-align: left;
        }
        .table-striped tbody tr:nth-of-type(2n+1){
            background-color: #f2f2f2;
        }
    }
    @media (max-width: 575px){
        .hajj-agency-info .flight-info-lists{
            margin: 0;
        }
        .hajj-agency-info .flight-info-lists ul li{
            flex-direction: column;
        }
        .hajj-agency-info .flight-info-lists ul li .flight-info-list-title{
            width: 100%;
            max-width: 100%;
            -ms-flex: 0 0 100%;
            flex: 0 0 100%;
        }

    }

</style>
<div class="border-card-block hajj-agency-info">
    <div class="bd-card-head" style="background: #17a2b8 !important;">
        <div class="bd-card-title">
            <span class="title-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18" fill="none">
                    <path d="M23.1018 3.3276L22.8657 1.98857C22.675 0.909681 21.5203 0.211331 20.2859 0.428854L4.22559 3.26095H22.418C22.6527 3.26095 22.8812 3.2846 23.1018 3.3276Z" fill="white"/>
                    <path d="M22.4183 4.93359H1.57999C0.70825 4.93359 -0.000976562 5.50649 -0.000976562 6.2104V16.3343C-0.000976562 17.0382 0.70825 17.6111 1.57999 17.6111H22.4183C23.2898 17.6111 23.999 17.0382 23.999 16.3343V6.2104C23.9989 5.50624 23.2898 4.93359 22.4183 4.93359ZM7.43186 16.3109H5.75921V14.8714H7.43186V16.3109ZM7.43186 13.4317H5.75921V11.9921H7.43186V13.4317ZM7.43186 10.5525H5.75921V9.11282H7.43186V10.5525ZM7.43186 7.67325H5.75921V6.23355H7.43186V7.67325ZM19.7217 9.40635L18.4345 10.6104L19.5942 14.4841L18.9446 15.1337C18.8733 15.2047 18.7579 15.2047 18.6865 15.1337L16.9579 11.9918L15.3679 13.56L15.6211 14.7959L15.2165 15.2007C15.1628 15.2544 15.0755 15.2544 15.0223 15.2009L12.7321 12.9107C12.6783 12.857 12.6783 12.77 12.7321 12.7165L13.1367 12.3114L14.3499 12.5589L15.9191 10.9629L12.7992 9.24561C12.7279 9.17479 12.7279 9.05932 12.7987 8.988L13.4489 8.33846L17.2969 9.49032L18.5096 8.19404C18.864 7.83968 19.4226 7.82412 19.7572 8.15888C20.0913 8.49338 20.0754 9.05211 19.7217 9.40635Z" fill="white"/>
                </svg>
            </span>
            <h3>Haj Agency Information</h3>
        </div>
    </div>
    <div class="bd-card-content">
        <div class="border-card-block inner-card mt-4 info">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <h3>Agency Information</h3>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="flight-info-lists">
                            <ul>
                                <li>
                                    <span class="flight-info-list-title">Haj License No</span>
                                    <span class="flight-info-list-desc" id="haj_license_no">
                                        {{ isset($json_object->haj_license_no) ? $json_object->haj_license_no : 'N/A' }}
                                    </span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">User Name</span>
                                    <span class="flight-info-list-desc " id="user_name">
                                        {{ isset($currentData->agency_user_info->user_full_name) ? $currentData->agency_user_info->user_full_name : 'N/A' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="flight-info-lists">
                            <ul>
                                <li>
                                    <span class="flight-info-list-title">Agency</span>
                                    <span class="flight-info-list-desc " id="agency_name">
                                        {{ isset($json_object->agency_name) ? $json_object->agency_name : 'N/A' }}
                                    </span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">User email</span>
                                    <span class="flight-info-list-desc " id="user_email">
                                        {{ isset($currentData->agency_user_info->user_email) ? $currentData->agency_user_info->user_email : 'N/A' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-card-block inner-card mb-0 info">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <h3>Change Information</h3>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="ehajj-list-table">
                    <div class="table-responsive ">
                        <table class="table table-striped table-bordered" style="border: 1px solid #D9D9D9;">
                            <thead>
                            <tr>
                                <th class="text-left">Label Name</th>
                                <th class="text-left">Current Information</th>
                                <th class="text-left">Change Information Request</th>
                            </tr>
                            </thead>
                            <tbody>

                            @if(isset($json_object->agency_section) && $json_object->agency_section == 1 )
                                <tr>
                                    <td class="text-left">Agency Status</td>
                                    <td>
                                        <div class="text-left">
                                            <span class="current-status"> Status: @if(isset($json_object->chk_current_agency_status) && $json_object->chk_current_agency_status == 1) Active @else Inactive @endif </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-left">
                                            <span class="current-status"> Status: @if(isset($json_object->agency_status) && $json_object->agency_status == 1) Active @else Inactive @endif </span><br>
                                            @if(isset($json_object->agency_status) && $json_object->agency_status == 0)
                                                <span class="current-status"> Inactive reason: {{$json_object->inactive_reason ?? 'N/A' }} </span><br>
                                                <span class="current-status"> Inactive Date: {{$json_object->inactive_date ?? 'N/A' }} </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            @if(isset($json_object->pre_reg_section) && $json_object->pre_reg_section == 1 )
                                <tr>
                                    <td class="text-left">Pre-Registration Status</td>
                                    <td>
                                        <div class="text-left">
                                            <span class="current-status "> Status:  @if(isset($json_object->chk_current_pre_reg_agency_status) && $json_object->chk_current_pre_reg_agency_status == 1) Active @else Inactive @endif </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-left">
                                            <span class="current-status "> Status:  @if(isset($json_object->pre_reg_status) && $json_object->pre_reg_status == 1) Active @else Inactive @endif </span>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            @if(isset($json_object->agency_user_section) && $json_object->agency_user_section == 1 )
                                <tr>
                                    <td class="text-left">User Status</td>
                                    <td>
                                        <div class="text-left">
                                            <span class="current-status "> Status:  @if(isset($json_object->chk_current_user_status) && $json_object->chk_current_user_status == 1) Active @else Inactive @endif </span>
                                            <br>
                                            <span class="current-status"> User Email: {{ $json_object->chk_current_user_email ?? 'N/A' }} </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-left">
                                            <span class="current-status "> Status:  @if(isset($json_object->user_status) && $json_object->user_status == 1) Active @else Inactive @endif </span>
                                            <br>
                                            <span class="current-status "> User Email: {{$json_object->user_email ?? 'N/A' }} </span>
                                        </div>
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

    <div class="bd-card-footer info">
        <div class="flex-center flex-btn-group">
            <a class="btn btn-info btn-sm" href="{{ url('/process/list/') }}">বন্ধ করুন</a>
        </div>
    </div>
</div>



