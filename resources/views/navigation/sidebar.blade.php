<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);
$Segment = Request::segment(3);
$is_eligibility = 0;
if ($user_type == '5x505') {
    $is_eligibility = \App\Libraries\CommonFunction::checkEligibility();
}
$prefix = '';
if ($type[0] == 5) {
    $prefix = 'client';
}
?>
<aside id="navbarCollapse" class="dash-sidebar collapse">
    <div class="dash-navbar">
        @if ( !in_array($user_type, ['21x101', '18x415']))
            <div class="dash-menu-item {{ Request::is('dashboard') || Request::is('dashboard/*') ? 'active' : '' }}">
                <a class="dash-menu-text" href="{{ url('/dashboard') }}"><i class="fas fa-th-large dash-menu-icon"></i> ড্যাশবোর্ড</a>
            </div>
        @endif

        <div class="dash-menu-item {{ Request::is('my-desk') || Request::is('my-desk/*') ? 'active' : '' }}">
            <a class="dash-menu-text" href="{{ url('/my-desk') }}"><i class="fas fa-th-large dash-menu-icon"></i>
                @if(in_array($user_type, ['21x101', '18x415']))
                    ড্যাশবোর্ড
                @else
                    My Desk
                @endif
            </a>
        </div>

        @if(in_array($user_type, ['18x415']))
            <div class="dash-menu-item">
                <div class="dash-nested-menu {{ Request::is('my-profile')  ? 'active' : '' }}">
                    <a class="dash-menu-text" href="{{url('/my-profile')}}">
                        <div class="w-100 d-flex justify-content-between">
                            <i class="fa fa-user dash-menu-icon" aria-hidden="true"></i>আমার প্রোফাইল
                        </div>
                    </a>
                </div>
                <div class="dash-nested-menu {{ (Request::is('pilgrim/*') || Request::is('pilgrim'))  ? 'active' : '' }}">
                    <a class="dash-menu-text toggle-nested-menu2" href="javascript:void(0)">
                        <div class="w-100 d-flex justify-content-between">
                            <i class="fas fa-grip-vertical dash-menu-icon" aria-hidden="true"></i>প্রাক নিবন্ধন
                            <i class="fa fa-chevron-left chevron-icon2"  aria-hidden="true"></i>
                        </div>
                    </a>
                </div>

                <!-- Nested Menu -->
                <div class="nested-menu" style="display: {{(Request::is('pilgrim/*') || Request::is('pilgrim'))  ? 'block' : 'none'}}; padding-top: 3px;">
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("pilgrim/pre-registration/index#/pilgrims-list") }}">
                            <i class="fa  fa-space-shuttle fa-fw"></i>&nbsp; আবেদন
                        </a>
                    </div>
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("pilgrim/voucher/index#/voucher-list") }}">
                            <i class="fa fa-save"></i>&nbsp; ভাউচার
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if(in_array($user_type, ['18x415']))
            <div class="dash-menu-item">
                <div class="dash-nested-menu {{ (Request::is('registration/*') || Request::is('registration'))  ? 'active' : '' }}">
                    <a class="dash-menu-text toggle-nested-menu2" href="javascript:void(0)">
                        <div class="w-100 d-flex justify-content-between">
                            <i class="fas fa-grip-vertical dash-menu-icon" aria-hidden="true"></i> নিবন্ধন
                            <i class="fa fa-chevron-left chevron-icon2"  aria-hidden="true"></i>
                        </div>
                    </a>
                </div>

                <!-- Nested Menu -->
                <div class="nested-menu" style="display: {{(Request::is('registration/*') || Request::is('registration'))  ? 'block' : 'none'}}; padding-top: 3px;">
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("/registration/reg/index#/reg-pilgrims-list") }}">
                            <i class="fa  fa-space-shuttle fa-fw"></i>&nbsp; নিবন্ধন আবেদন
                        </a>
                    </div>
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("registration/reg/voucher/index#/reg-voucher-list") }}">
                            <i class="fa fa-save"></i>&nbsp; নিবন্ধন ভাউচার
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @php
        $access_users = \App\Modules\Settings\Models\Configuration::where('caption','bulletin_permission')->first();
        @endphp
        @if(!empty($access_users) && $access_users->value && in_array(\Illuminate\Support\Facades\Auth::user()->user_email,json_decode($access_users->value2)))
         <div class="dash-menu-item {{ Request::is('bulletin') || Request::is('bulletin/*') ? 'active' : '' }}">
                <a class="dash-menu-text" href="{{ url('/bulletin') }}">
                <span class="dash-menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                        <path d="M15.8002 2.68671C15.3902 2.27671 14.6802 2.55671 14.6802 3.12671V6.61671C14.6802 8.07671 15.9202 9.28671 17.4302 9.28671C18.3802 9.29671 19.7002 9.29671 20.8302 9.29671C21.4002 9.29671 21.7002 8.62671 21.3002 8.22671C19.8602 6.77671 17.2802 4.16671 15.8002 2.68671Z" fill="#474D49"/>
                        <path d="M20.5 10.6667H17.61C15.24 10.6667 13.31 8.73671 13.31 6.36671V3.47672C13.31 2.92672 12.86 2.47672 12.31 2.47672H8.07C4.99 2.47672 2.5 4.47672 2.5 8.04672V16.9067C2.5 20.4767 4.99 22.4767 8.07 22.4767H15.93C19.01 22.4767 21.5 20.4767 21.5 16.9067V11.6667C21.5 11.1167 21.05 10.6667 20.5 10.6667ZM13 16.2267H8.81L9.53 16.9467C9.82 17.2367 9.82 17.7167 9.53 18.0067C9.38 18.1567 9.19 18.2267 9 18.2267C8.81 18.2267 8.62 18.1567 8.47 18.0067L6.47 16.0067C6.4 15.9367 6.36 15.8667 6.32 15.7867C6.31 15.7667 6.3 15.7367 6.29 15.7167C6.27 15.6567 6.26 15.5967 6.25 15.5367C6.25 15.5067 6.25 15.4867 6.25 15.4567C6.25 15.3767 6.27 15.2967 6.3 15.2167C6.3 15.2067 6.3 15.2067 6.31 15.1967C6.34 15.1167 6.4 15.0367 6.46 14.9767C6.47 14.9667 6.47 14.9567 6.48 14.9567L8.48 12.9567C8.77 12.6667 9.25 12.6667 9.54 12.9567C9.83 13.2467 9.83 13.7267 9.54 14.0167L8.82 14.7367H13C13.41 14.7367 13.75 15.0767 13.75 15.4867C13.75 15.8967 13.41 16.2267 13 16.2267Z" fill="#474D49"/>
                    </svg>
                </span>
                    <span class="dm-text">বুলেটিন</span>
                </a>
            </div>
        @endif
        @if ( in_array($type[0], [1, 2, 3, 4, 12, 17, 21]))
        <div class="dash-menu-item {{ Request::is('process/list') || Request::is('client/process/details/*') || Request::is('client/process/industry-new/*') || Request::is('process/industry-new/*') || Request::is('industry-new/list/*') || Request::is('client/industry-new/list/*') || Request::is('process/list') || Request::is('process/list/*') ? 'active' : '' }}">
            <a class="dash-menu-text" href="{{ url("/process/list") }}">
                <i class="fas fa-grip-vertical dash-menu-icon"></i> সেবা সমূহ
            </a>
        </div>
        @endif
        @if ( in_array($type[0], [17,2]))
            <div class="dash-menu-item {{ Request::is('medicine-store') || Request::is('medicine-store/*') ? 'active' : '' }}">
                <a class="dash-menu-text" href="{{ url('/medicine-store') }}"><i class="fa fa-hospital dash-menu-icon"></i> মেডিসিন স্টোর</a>
            </div>
        @endif
        @if ( in_array($user_type, ['4x401','4x402','4x404','2x202','2x203']))
        <div class="dash-menu-item {{ Request::is('room-allocation') || Request::is('room-allocation/*') ? 'active' : '' }}">
            <a class="dash-menu-text" href="{{ url('/room-allocation/') }}"><i class="fa fa-hotel dash-menu-icon"></i> রুম বরাদ্দ</a>
        </div>
        @endif
        @if ( $user_type == '19x191' && Auth::user()->first_login != 0)
            <div class="dash-menu-item {{ Request::is('medicine-issue') || Request::is('medicine-issue/*') ? 'active' : '' }}">
                <a class="dash-menu-text" href="{{ url('/medicine-issue') }}"><i class="fa fa-hospital dash-menu-icon"></i> ঔষধ বিতরণ</a>
            </div>
            <div class="dash-menu-item {{ Request::is('medicine-draft') || Request::is('medicine-draft/*') ? 'active' : '' }}">
                <a class="dash-menu-text" href="{{ url('/medicine-draft') }}"><i class="fa fa-hospital dash-menu-icon"></i> ড্রাফট ঔষধ বিতরণ</a>
            </div>
        @endif
        @if ( $user_type == '19x192')
            <div class="dash-menu-item {{ Request::is('medicine-receive') || Request::is('medicine-receive/*') ? 'active' : '' }}">
                <a class="dash-menu-text" href="{{ url('/medicine-receive') }}"><i class="fa fa-hospital dash-menu-icon"></i> ঔষধ বিতরনের তথ্য অনুসন্ধান </a>
            </div>
        @endif
        @if (in_array($type[0], [1]))
        <div class="dash-menu-item {{ Request::is('users/lists') || Request::is('users/view/*') ? 'active' : '' }}">
            <a class="dash-menu-text" href="{{ url('/users/lists') }}">
                <i class="fas fa-grip-vertical dash-menu-icon"></i> ব্যবহারকারী
            </a>
        </div>
        @endif
        @if(in_array($user_type, ['4x401','6x606','6x607']))
            <div class="dash-menu-item {{ Request::is('flight') || Request::is('flight/*') ? 'active' : '' }}">
                <a class="dash-menu-text" href="{{ url('/flight') }}">
                <span class="dash-menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 26 27" fill="none">
                        <path d="M20.9041 15.5232L17.4667 11.777L16.6981 10.9497C16.5849 10.8112 16.5431 10.5623 16.6169 10.3981L17.8699 7.60644C18.2631 6.73063 18.0822 5.39985 17.4778 4.64626C17.2655 4.38656 16.9371 4.23914 16.6019 4.25313C15.6463 4.30647 14.5276 5.06487 14.1345 5.94069L12.8814 8.73235C12.8077 8.89656 12.594 9.03082 12.4153 9.03828L6.21182 8.93926C5.53059 8.91847 4.74181 9.43034 4.46334 10.0507L3.92279 11.255C3.57471 12.0304 3.9866 12.6757 4.84655 12.689L10.3017 12.77C10.7272 12.7746 10.9331 13.0973 10.757 13.4896L10.3025 14.5022L9.56536 16.1444C9.47117 16.3542 9.21743 16.602 9.00594 16.6825L5.93524 17.8581C5.61801 17.9788 5.32519 18.3625 5.28631 18.7178L5.13992 20.1428C5.06253 20.7547 5.55911 21.2846 6.17554 21.2543L9.16585 20.4043C9.62122 20.269 10.1595 20.5106 10.361 20.9408L11.7132 23.74C12.1045 24.2116 12.8263 24.2396 13.2503 23.7833L14.2178 22.7268C14.4533 22.4708 14.5495 21.9878 14.4288 21.6706L13.2664 18.5949C13.1769 18.3793 13.1934 18.025 13.2876 17.8152L14.4792 15.1603C14.6553 14.7681 15.02 14.7125 15.3194 15.0223L19.0056 19.0445C19.5871 19.6782 20.3429 19.5571 20.691 18.7817L21.2316 17.5774C21.51 16.9571 21.3683 16.0275 20.9041 15.5232Z" fill="#474D49"/>
                    </svg>
                </span>
                    <span class="dm-text">ফ্লাইট</span>
                </a>
            </div>
        @endif
        @if(in_array($user_type, ['18x415']) && Auth::user()->working_user_type == 'Guide')
            <div class="dash-menu-item {{ Request::is('guides') || Request::is('guides/*') ? 'active' : '' }}">
                <a class="dash-menu-text" href="{{ url('/guides/index#/guide-application-list') }}">
                <span class="dash-menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 26 27" fill="none">
                        <path d="M20.9041 15.5232L17.4667 11.777L16.6981 10.9497C16.5849 10.8112 16.5431 10.5623 16.6169 10.3981L17.8699 7.60644C18.2631 6.73063 18.0822 5.39985 17.4778 4.64626C17.2655 4.38656 16.9371 4.23914 16.6019 4.25313C15.6463 4.30647 14.5276 5.06487 14.1345 5.94069L12.8814 8.73235C12.8077 8.89656 12.594 9.03082 12.4153 9.03828L6.21182 8.93926C5.53059 8.91847 4.74181 9.43034 4.46334 10.0507L3.92279 11.255C3.57471 12.0304 3.9866 12.6757 4.84655 12.689L10.3017 12.77C10.7272 12.7746 10.9331 13.0973 10.757 13.4896L10.3025 14.5022L9.56536 16.1444C9.47117 16.3542 9.21743 16.602 9.00594 16.6825L5.93524 17.8581C5.61801 17.9788 5.32519 18.3625 5.28631 18.7178L5.13992 20.1428C5.06253 20.7547 5.55911 21.2846 6.17554 21.2543L9.16585 20.4043C9.62122 20.269 10.1595 20.5106 10.361 20.9408L11.7132 23.74C12.1045 24.2116 12.8263 24.2396 13.2503 23.7833L14.2178 22.7268C14.4533 22.4708 14.5495 21.9878 14.4288 21.6706L13.2664 18.5949C13.1769 18.3793 13.1934 18.025 13.2876 17.8152L14.4792 15.1603C14.6553 14.7681 15.02 14.7125 15.3194 15.0223L19.0056 19.0445C19.5871 19.6782 20.3429 19.5571 20.691 18.7817L21.2316 17.5774C21.51 16.9571 21.3683 16.0275 20.9041 15.5232Z" fill="#474D49"/>
                    </svg>
                </span>
                    <span class="dm-text">হজ গাইড</span>
                </a>
            </div>
        @endif
        @if(in_array($user_type, ['6x606','6x607']))
            <div class="dash-menu-item {{ Request::is('pay-order-received') || Request::is('pay-order-received/*') ? 'active' : '' }}">
                <a class="dash-menu-text" href="{{ url('/pay-order-received') }}">
                <span class="dash-menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                        <path d="M15.8002 2.68671C15.3902 2.27671 14.6802 2.55671 14.6802 3.12671V6.61671C14.6802 8.07671 15.9202 9.28671 17.4302 9.28671C18.3802 9.29671 19.7002 9.29671 20.8302 9.29671C21.4002 9.29671 21.7002 8.62671 21.3002 8.22671C19.8602 6.77671 17.2802 4.16671 15.8002 2.68671Z" fill="#474D49"/>
                        <path d="M20.5 10.6667H17.61C15.24 10.6667 13.31 8.73671 13.31 6.36671V3.47672C13.31 2.92672 12.86 2.47672 12.31 2.47672H8.07C4.99 2.47672 2.5 4.47672 2.5 8.04672V16.9067C2.5 20.4767 4.99 22.4767 8.07 22.4767H15.93C19.01 22.4767 21.5 20.4767 21.5 16.9067V11.6667C21.5 11.1167 21.05 10.6667 20.5 10.6667ZM13 16.2267H8.81L9.53 16.9467C9.82 17.2367 9.82 17.7167 9.53 18.0067C9.38 18.1567 9.19 18.2267 9 18.2267C8.81 18.2267 8.62 18.1567 8.47 18.0067L6.47 16.0067C6.4 15.9367 6.36 15.8667 6.32 15.7867C6.31 15.7667 6.3 15.7367 6.29 15.7167C6.27 15.6567 6.26 15.5967 6.25 15.5367C6.25 15.5067 6.25 15.4867 6.25 15.4567C6.25 15.3767 6.27 15.2967 6.3 15.2167C6.3 15.2067 6.3 15.2067 6.31 15.1967C6.34 15.1167 6.4 15.0367 6.46 14.9767C6.47 14.9667 6.47 14.9567 6.48 14.9567L8.48 12.9567C8.77 12.6667 9.25 12.6667 9.54 12.9567C9.83 13.2467 9.83 13.7267 9.54 14.0167L8.82 14.7367H13C13.41 14.7367 13.75 15.0767 13.75 15.4867C13.75 15.8967 13.41 16.2267 13 16.2267Z" fill="#474D49"/>
                    </svg>
                </span>
                    <span class="dm-text">পে অর্ডার রিসিভ</span>
                </a>
            </div>
        @endif
        @if(in_array($user_type, ['6x606','6x607']))
        <div class="dash-menu-item {{ Request::is('reservation') || Request::is('reservation/*') ? 'active' : '' }}">
            <a class="dash-menu-text" href="{{ url('/reservation/') }}">
                <span class="dash-menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="19" viewBox="0 0 24 19" fill="none">
                        <path d="M23.1023 3.80422L22.8662 2.46519C22.6754 1.3863 21.5208 0.687954 20.2864 0.905477L4.22607 3.73758H22.4185C22.6532 3.73758 22.8817 3.76123 23.1023 3.80422Z" fill="#474D49"/>
                        <path d="M22.4183 5.41022H1.57999C0.70825 5.41022 -0.000976562 5.98311 -0.000976562 6.68703V16.811C-0.000976562 17.5149 0.70825 18.0878 1.57999 18.0878H22.4183C23.2898 18.0878 23.999 17.5149 23.999 16.811V6.68703C23.9989 5.98286 23.2898 5.41022 22.4183 5.41022ZM7.43186 16.7876H5.75921V15.348H7.43186V16.7876ZM7.43186 13.9083H5.75921V12.4687H7.43186V13.9083ZM7.43186 11.0291H5.75921V9.58944H7.43186V11.0291ZM7.43186 8.14987H5.75921V6.71017H7.43186V8.14987ZM19.7217 9.88297L18.4345 11.0871L19.5942 14.9607L18.9446 15.6103C18.8733 15.6814 18.7579 15.6814 18.6865 15.6103L16.9579 12.4685L15.3679 14.0366L15.6211 15.2725L15.2165 15.6773C15.1628 15.7311 15.0755 15.7311 15.0223 15.6776L12.7321 13.3874C12.6783 13.3336 12.6783 13.2466 12.7321 13.1931L13.1367 12.788L14.3499 13.0355L15.9191 11.4395L12.7992 9.72223C12.7279 9.65141 12.7279 9.53595 12.7987 9.46462L13.4489 8.81509L17.2969 9.96695L18.5096 8.67066C18.864 8.3163 19.4226 8.30074 19.7572 8.6355C20.0913 8.97001 20.0754 9.52874 19.7217 9.88297Z" fill="#474D49"/>
                    </svg>
                </span>
                <span class="dm-text">রিজার্ভেশন</span>
            </a>
        </div>
        @endif

        @php
        $guide_list_sidebar = \App\Modules\Settings\Models\Configuration::where('caption','Guide_List_Side_Bar')->first();
        $access_users = \App\Modules\Settings\Models\Configuration::where('caption','Guide_List_Side_Bar')->first();
        $access_users_array = json_decode($access_users->value2);
        @endphp
        @if($guide_list_sidebar->value && in_array(\Illuminate\Support\Facades\Auth::user()->user_email,$access_users_array))
         <div class="dash-menu-item {{ Request::is('guide-users/lists') || Request::is('guide-users/view/*') ? 'active' : '' }}">
             <a class="dash-menu-text" href="{{ url('/guide-users/lists') }}">
                 <i class="fas fa-grip-vertical dash-menu-icon"></i> গাইড তালিকা
             </a>
         </div>
        @endif

        @php
        $news_access_users = \App\Modules\Settings\Models\Configuration::where('caption','News_and_press')->first();
        $news_access_users_array = json_decode($news_access_users->value2);
        @endphp

        @if($news_access_users->value == 1 && in_array(\Illuminate\Support\Facades\Auth::user()->user_email,$news_access_users_array))
            <div class="dash-menu-item {{ Request::is('newslist') || Request::is('newslist/*') ? 'active' : '' }}">
                <a class="dash-menu-text" href="{{ url('/newslist') }}">
                    <i class="fas fa-grip-vertical dash-menu-icon"></i>নিউজ ও বিজ্ঞপ্তি
                </a>
            </div>
        @endif

        @if(Auth::user()->working_user_type != 'Pilgrim')
            <div class="dash-menu-item {{ (Request::is('reportv2/*') || Request::is('reportv2') ) ? 'active' : '' }}" >
                <a class="dash-menu-text" href="{{ url("/reportv2") }}"><i class="fas fa-chart-line dash-menu-icon"></i> রিপোর্ট</a>
            </div>
        @endif

        @if ($user_type == '1x101')
            <div class="dash-menu-item">
                <div class="dash-nested-menu {{ (Request::is('settings/*') || Request::is('settings'))  ? 'active' : '' }}">
                    <a class="dash-menu-text toggle-nested-menu" href="javascript:void(0)">
                        <div class="w-100 d-flex justify-content-between">
                            <i class="fa fa-wrench dash-menu-icon" aria-hidden="true"></i> সেটিংস
                            <i class="fa fa-chevron-left chevron-icon"  aria-hidden="true"></i>
                        </div>
                    </a>
                </div>

                <!-- Nested Menu -->
                <div class="nested-menu" style="display: {{(Request::is('settings/*') || Request::is('settings'))  ? 'block' : 'none'}}; padding-top: 3px;">
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("settings/index#/edit-logo") }}">লোগো</a>
                    </div>
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("settings/index#/user-type") }}">ইউজার টাইপ</a>
                    </div>
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("settings/index#/email-sms-queue") }}">ইমেইল এবং এসএমএস</a>
                    </div>
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("settings/index#/pdf-print-requests") }}">পিডিএফ প্রিন্ট রিকোয়েস্ট</a>
                    </div>
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("settings/index#/home-page/home-page-content") }}">হোম পেইজ কন্টেন্ট</a>
                    </div>
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("settings/index#/home-page/home-page-slider") }}">হোম পেইজ স্লাইডার</a>
                    </div>
                    <div class="dash-menu-item">
                        <a class="dash-menu-text" href="{{ url("settings/index#/home-page/user-manual") }}">ইউজার ম্যানুয়াল</a>
                    </div>
                </div>
            </div>
        @endif

    </div>
</aside>

<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $(".toggle-nested-menu").click(function () {
            $('.nested-menu').slideToggle();
            let chevronIcon = $(this).find('.chevron-icon');
            chevronIcon.toggleClass('fa-chevron-left fa-chevron-down'); // Toggle chevron icon
        });
        let urlContainsSettings = window.location.href.indexOf('settings/index') > -1;
        $('.chevron-icon').toggleClass('fa-chevron-left', !urlContainsSettings).toggleClass('fa-chevron-down', urlContainsSettings);

        $('.toggle-nested-menu2').on('click', function(e) {
            e.preventDefault();
            $(this).closest('.dash-menu-item').find('.nested-menu').toggle();

            $(this).find('.chevron-icon2').toggleClass('fa-chevron-left fa-chevron-down');
        });
    });
</script>
