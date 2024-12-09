<?php

use App\Libraries\CommonFunction;

$user_name = CommonFunction::getUserFullName();
?>
<header class="site-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="site-nav-box">
                <div class="dash-head-left">
                    <button class="navbar-toggler resView collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="site-nav-box">
                        <a class="navbar-brand" href="{{ url('/dashboard') }}"><img  src="{{ asset('assets/custom/images/logo-hajj.webp') }}" alt="Logo"></a>
                    </div>
                </div>

                {{-- @if(\Illuminate\Support\Facades\Auth::user()->user_type == '19x191' && \Illuminate\Support\Facades\Auth::user()->working_company_id != 0)
                    <h4 class="text-success">{{ CommonFunction::getPharmacyName() }}</h4>
                @endif --}}


                <div class="nav-btn-group nav-user-profile">
                    <div class="dropdown">
                        <div class="dropdown-toggle hajj-top-user" type="button" id="hajjUserInfo" data-toggle="dropdown" aria-expanded="false">
                            <div class="dash-user-info flex-center">
                                <div class="dash-user-picture"><img style="border-radius:50%" src="{{ Auth::user()->user_pic ? asset(Auth::user()->user_pic) : asset('users/user_default_pic.jpg') }}" alt="Image"></div>
                                <span class="dash-user-name deskView">
                                    <span>{{Auth::user()->user_first_name}}</span>
                                    @if(\Illuminate\Support\Facades\Auth::user()->user_type == '19x191' && \Illuminate\Support\Facades\Auth::user()->working_company_id != 0)
                                        <br>
                                        <span class="small">{{ CommonFunction::getPharmacyName() }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="hajjUserInfo">
                            <div class="user-info">
                                <div class="user-avatar">
                                    <img style="border-radius:50%" src="{{ Auth::user()->user_pic ? asset(Auth::user()->user_pic) : asset('users/user_default_pic.jpg') }}" alt="Image">
                                </div>
                                <div class="user-info-title mb-4">
                                    <h3>{{Auth::user()->user_first_name}}</h3>
                                    <span class="login-time">Last Login: 31 Dec 2023 | 02:34:54</span>

                                    <button class="btn btn-secondary mt-3"><i class="fa fa-unlock-alt mr-1" aria-hidden="true"></i> Access Log</button>
                                </div>
                                <div class="user-btn-group">
                                    <a href="{{ url('users/profileinfo') }}" class="btn btn-success">Profile</a>
                                    <a href="{{ url('osspid/logout') }}" class="btn  btn-danger">Log out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
