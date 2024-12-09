<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);
$check_association_from = checkCompanyAssociationForm();
$bscicUsers = getBscicUser();
$is_eligibility = 0;
if ($user_type == '5x505') {
    $is_eligibility = \App\Libraries\CommonFunction::checkEligibility();
}
?>

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
        cursor: pointer;
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


    /**
     * Service Registration Guide
     */
    .srv-tab-desc p,
    .srv-tab-desc ul li,
    .srv-tab-desc h3,
    .dash-service-guide-sec .section-title h2,
    .srv-tab-desc h3{
        font-family: "Poppins", sans-serif;
    }

    .dash-service-guide-sec{
        padding-bottom: 20px;
        position: relative;
    }
    .dash-service-guide-sec .section-title{
        display: block;
        padding-bottom: 10px;
    }
    .dash-service-guide-sec .section-title h2{
        font-size: 25px;
        font-weight: bold;
        margin: 0;
    }
    .ehajj-service-tabm-menu{
        padding-bottom: 10px;
    }
    .ehajj-service-tabm-menu .row{
        margin-left: -8px;
        margin-right: -8px;
    }
    .ehajj-service-tabm-menu .row .col-lg-3,
    .ehajj-service-tabm-menu .row .col-md-6{
        padding-left: 8px;
        padding-right: 8px;
    }

    .dash-service-guide-sec .tab-tigger {
        border-radius: 12px;
        position: relative;
        height: 126px;
        cursor: pointer;
        padding: 1rem;
        border-bottom: 4px solid transparent;
    }

    .ehajj-srv-tab-content{
        position: relative;
        width: 100%;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.10);
        border: 1px solid transparent;
        overflow: hidden;
    }
    .ehajj-srv-tab-content.srv-content-highlight{
        border-color: #0F6849;
    }
    .ehajj-srv-tab-content .srv-tab-desc{
        padding: 30px 20px 20px;
        text-align: left;
        border-radius: 0 0 8px 8px;
    }
    .ehajj-srv-tab-content.srv-content-highlight .srv-tab-desc{
        background-color: #f8f9fa;
        color: #071638;
    }
    .ehajj-srv-tab-content.srv-content-highlight .srv-tab-desc a{
        color: #071638;
    }
    .srv-tab-desc h3{
        font-size: 18px;
        font-weight: bold;
        margin: 0 0 10px;
    }
    .srv-tab-desc ul{
        padding: 0 0 0 20px;
        margin: 0;
    }
    .srv-tab-desc p{
        font-size: 16px;
        font-weight: normal;
        line-height: 1.5;
        margin: 0 0 20px;
    }
    .srv-tab-desc ul li{
        list-style-type: decimal;
        font-size: 16px;
        font-weight: 400;
        line-height: 1.5;
        text-align: left;
        margin-bottom: 10px;
    }

    .dash-service-guide-sec .tab-tigger.one {
        background: linear-gradient(121.8deg, #8062f5 14.23%, #9989f7 99.69%);
    }
    .dash-service-guide-sec .tab-tigger.two {
        background: linear-gradient(119.64deg, #37babb 22.42%, #5de1e1 118.96%);
    }
    .dash-service-guide-sec .tab-tigger.three {
        background: linear-gradient(121.8deg, #2973ea 14.23%, #4290ff 99.69%);
    }
    .dash-service-guide-sec .tab-tigger.four {
        background: linear-gradient(121.8deg, #f56059 14.23%, #fc7869 99.69%);
    }

    .dash-service-guide-sec .tab-tigger.one.active {
        background: linear-gradient(121.8deg, #8265f520 14.23%, #ffffff 99.69%);
        border-color: #8062f5;
    }

    .dash-service-guide-sec .tab-tigger.two.active {
        background: linear-gradient(121.8deg, #37babb20 14.23%, #ffffff 99.69%);
        border-color: #37babb;
    }
    .dash-service-guide-sec .tab-tigger.three.active {
        background: linear-gradient(121.8deg, #2973ea20 14.23%, #ffffff 99.69%);
        border-bottom: 4px solid;
        border-color: #2973ea;
    }
    .dash-service-guide-sec .tab-tigger.four.active {
        background: linear-gradient(121.8deg, #f5605920 14.23%, #ffffff 99.69%);
        border-color: #f56059;
    }

    .dash-service-guide-sec .tab-tigger.one.active .content-area svg rect {
        fill: #8062f5;
    }
    .dash-service-guide-sec .tab-tigger.one.active .content-area svg stop {
        stop-color: white;
    }

    .dash-service-guide-sec .tab-tigger .srv-tab-svg-circle {
        height: 126px;
        position: absolute;
        top: 0;
        right: 0;
    }
    .dash-service-guide-sec .tab-tigger.active .srv-tab-svg-circle {
        opacity: 0;
    }

    .dash-service-guide-sec .tab-tigger .content-area {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }

    .dash-service-guide-sec .tab-tigger .content-area > div {
        color: white;
    }
    .dash-service-guide-sec .tab-tigger.active .content-area > div {
        color: #071638;
    }

    .dash-service-guide-sec .tab-tigger .content-area h2 {
        font-weight: bold;
        font-size: 42px;
        margin: 0;
    }

    .srv-tab-steps{
        position: relative;
        width: 100%;
        max-width: 645px;
        margin: 0 auto;
        padding: 20px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    .srv-tab-steps img{
        max-width: 100%;
    }

    @media (max-width: 480px){
        .dash-service-guide-sec .tab-tigger{
            height: 110px;
        }
        .dash-service-guide-sec .tab-tigger .content-area{
            column-gap: 25px;
        }
        .srv-tab-desc h3{
            font-size: 16px;
        }
        .srv-tab-desc p,
        .srv-tab-desc ul li{
            font-size: 14px;
        }

    }

</style>
@include('partials.datatable-css')
<link rel="stylesheet" href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}" />

@if (in_array($user_type, ['6x606', '6x607']))
     @include("Flight::dashboard", ['from' => 'flightDashboard'])
@endif

@if (empty($delegated_desk))
    <div class="modal fade" id="ProjectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="frmAddProject"></div>
        </div>
    </div>
@endif
<div class="row">
    <div class="col-lg-12">
        @if (in_array($user_type, ['18x415']))
            <div class="dash-content-main">
                <!-- Start service guide content -->
                <div class="dash-service-guide-sec">
                    <div class="card-header" style="background: #f8f9fa !important; color: #071638; margin-bottom: 10px">
                        <h3 style="padding-top: 15px !important;">ই-হজ সেবা সমূহ</h3>
                    </div>
                    <div class="ehajj-service-tabm-menu">
                        <div class="row" role="tablist">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="tab-tigger one active" data-toggle="tab" href="#ehajjDashBoardTabOne" role="tab">
                                    <div class="content-area">
                                        <div class="service-tab-menu-text">
                                            <h2 class="mb-0">{{$countData['pre-reg'] ?? 0}}</h2>
                                            <p>প্রাক নিবন্ধন</p>
                                        </div>
                                        <div class="service-tab-menu-icon">
                                            <svg width="51" height="50" viewBox="0 0 51 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="0.444824" width="50" height="50" rx="25" fill="#E0D9FF" />
                                                <path d="M33.7791 17.9813L29.2593 13.7053C28.7785 13.2505 28.1493 13 27.4875 13H18.8823C17.4607 13 16.3042 14.1565 16.3042 15.5781V34.4219C16.3042 35.8435 17.4607 37 18.8823 37H32.0073C33.4289 37 34.5854 35.8435 34.5854 34.4219V19.8542C34.5854 19.1487 34.2915 18.4661 33.7791 17.9813ZM32.4133 18.625H28.9136C28.7843 18.625 28.6792 18.5199 28.6792 18.3906V15.0923L32.4133 18.625ZM32.0073 35.5938H18.8823C18.2362 35.5938 17.7104 35.068 17.7104 34.4219V15.5781C17.7104 14.932 18.2362 14.4062 18.8823 14.4062H27.2729V18.3906C27.2729 19.2953 28.0089 20.0312 28.9136 20.0312H33.1792V34.4219C33.1792 35.068 32.6535 35.5938 32.0073 35.5938Z" fill="url(#paint0_linear_3831_13957)" />
                                                <path d="M30.4604 22.375H20.1479C19.7596 22.375 19.4448 22.6898 19.4448 23.0781C19.4448 23.4664 19.7596 23.7812 20.1479 23.7812H30.4604C30.8488 23.7812 31.1636 23.4664 31.1636 23.0781C31.1636 22.6898 30.8488 22.375 30.4604 22.375Z" fill="url(#paint1_linear_3831_13957)" />
                                                <path d="M30.4604 26.125H20.1479C19.7596 26.125 19.4448 26.4398 19.4448 26.8281C19.4448 27.2164 19.7596 27.5312 20.1479 27.5312H30.4604C30.8488 27.5312 31.1636 27.2164 31.1636 26.8281C31.1636 26.4398 30.8488 26.125 30.4604 26.125Z" fill="url(#paint2_linear_3831_13957)" />
                                                <path d="M23.5567 29.875H20.1479C19.7596 29.875 19.4448 30.1898 19.4448 30.5781C19.4448 30.9664 19.7596 31.2812 20.1479 31.2812H23.5567C23.945 31.2812 24.2598 30.9664 24.2598 30.5781C24.2598 30.1898 23.945 29.875 23.5567 29.875Z" fill="url(#paint3_linear_3831_13957)" />
                                                <defs>
                                                    <linearGradient id="paint0_linear_3831_13957" x1="16.2128" y1="22.12" x2="36.906" y2="31.8927" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#8062F5" />
                                                        <stop offset="1" stop-color="#9989F7" />
                                                    </linearGradient>
                                                    <linearGradient id="paint1_linear_3831_13957" x1="19.3862" y1="22.9094" x2="19.972" y2="25.936" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#8062F5" />
                                                        <stop offset="1" stop-color="#9989F7" />
                                                    </linearGradient>
                                                    <linearGradient id="paint2_linear_3831_13957" x1="19.3862" y1="26.6594" x2="19.972" y2="29.686" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#8062F5" />
                                                        <stop offset="1" stop-color="#9989F7" />
                                                    </linearGradient>
                                                    <linearGradient id="paint3_linear_3831_13957" x1="19.4207" y1="30.4094" x2="20.6313" y2="32.9792" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#8062F5" />
                                                        <stop offset="1" stop-color="#9989F7" />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                    <svg class="srv-tab-svg-circle" viewBox="0 0 140 126" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle opacity="0.08" cx="52.2692" cy="27.2692" r="44.7692" stroke="white" stroke-width="15" />
                                        <circle opacity="0.08" cx="87.1154" cy="73.7311" r="44.7692" stroke="white" stroke-width="15" />
                                    </svg>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="tab-tigger two" data-toggle="tab" href="#ehajjDashBoardTabTwo" role="tab">
                                    <div class="content-area">
                                        <div class="service-tab-menu-text">
                                            <h2 class="mb-0">{{$countData['reg'] ?? 0}}</h2>
                                            <p>নিবন্ধন</p>
                                        </div>
                                        <div class="service-tab-menu-icon">
                                            <svg width="51" height="50" viewBox="0 0 51 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="0.444824" width="50" height="50" rx="25" fill="#B5F9FA" />
                                                <g clip-path="url(#clip0_3831_14045)">
                                                    <path d="M36.6263 19.3338C36.2257 19.0763 35.7488 18.9902 35.2835 19.0914C34.8182 19.1926 34.42 19.4689 34.1626 19.8695L33.7781 20.4676L31.5367 23.9544V13.4688C31.5367 13.2099 31.3269 13 31.068 13H13.9141C13.6552 13 13.4453 13.2099 13.4453 13.4688V30.4169C13.4453 30.6757 13.6552 30.8856 13.9141 30.8856C14.173 30.8856 14.3828 30.6757 14.3828 30.4169V13.9375H30.5992V25.4128L27.766 29.8202C27.7342 29.8694 27.7113 29.927 27.7001 29.9845L27.3406 31.8383C27.1558 31.7709 26.9438 31.7525 26.7198 31.7943C26.386 31.8567 26.0747 32.0127 25.801 32.2199C25.7909 32.0625 25.776 31.904 25.7575 31.7335C25.7056 31.2565 25.4957 30.8693 25.1667 30.6433C24.4807 30.1723 23.6501 30.7262 23.3771 30.9081C22.4934 31.4974 21.7345 32.3335 21.1825 33.3259C21.0567 33.5522 21.1381 33.8376 21.3644 33.9634C21.4365 34.0036 21.5147 34.0226 21.5918 34.0226C21.7564 34.0226 21.9161 33.9357 22.0019 33.7816C22.4817 32.9189 23.1371 32.195 23.8973 31.6882C24.4315 31.3319 24.5979 31.39 24.6361 31.4162C24.7764 31.5125 24.8153 31.7411 24.8255 31.8349C24.886 32.3914 24.9026 32.7838 24.833 33.509C24.8112 33.7353 24.955 33.9446 25.1741 34.0054C25.3932 34.0664 25.6243 33.9611 25.7224 33.7561C25.9369 33.3078 26.4076 32.8064 26.8921 32.7159C26.9888 32.6979 27.0429 32.7156 27.0528 32.7674C27.0666 32.8506 27.0538 33.1682 27.0255 33.463C26.9474 33.8918 27.4548 34.1898 27.7866 33.9149L30.5993 31.9081V36.0625H14.3828V34.5419C14.3828 34.283 14.173 34.0731 13.9141 34.0731C13.6552 34.0731 13.4453 34.283 13.4453 34.5419V36.5312C13.4453 36.7901 13.6552 37 13.9141 37H31.068C31.3269 37 31.5367 36.7901 31.5367 36.5312V30.5485L36.7776 22.3957L37.162 21.7975C37.6936 20.9706 37.4533 19.8654 36.6263 19.3338ZM34.9512 20.3764C35.2002 19.989 35.7314 19.8731 36.1194 20.1224C36.5115 20.3745 36.6254 20.8985 36.3734 21.2906L36.2424 21.4945L34.8202 20.5802L34.9512 20.3764ZM28.1692 32.4901L28.4608 30.9865L28.4888 30.8422L29.0123 31.1786L29.5357 31.5151L29.0668 31.8497L28.1692 32.4901ZM30.6743 30.1564C30.6741 30.1567 30.6738 30.157 30.6737 30.1573L30.2303 30.847L28.8082 29.9329L31.4623 25.8042C31.4627 25.8035 31.463 25.8028 31.4635 25.8021L34.3132 21.3688L35.7355 22.2831L30.6743 30.1564Z" fill="url(#paint0_linear_3831_14045)" />
                                                    <path d="M22.2457 15.2002H17.0298C16.7709 15.2002 16.561 15.4101 16.561 15.6689V21.5901C16.561 21.8489 16.7709 22.0588 17.0298 22.0588H22.2457C22.5046 22.0588 22.7145 21.8489 22.7145 21.5901V15.6689C22.7145 15.4101 22.5046 15.2002 22.2457 15.2002ZM21.777 16.1377V19.4927C21.5181 19.2881 21.2334 19.1455 20.9579 19.0463C21.1231 18.7965 21.2195 18.4976 21.2195 18.1764C21.2195 17.3043 20.5099 16.5947 19.6377 16.5947C18.7656 16.5947 18.056 17.3043 18.056 18.1764C18.056 18.4972 18.1523 18.7959 18.3171 19.0455C18.0417 19.1446 17.7572 19.287 17.4985 19.4915V16.1377H21.777ZM19.638 18.8205C19.6368 18.8205 19.6351 18.8204 19.6338 18.8204C19.2803 18.8183 18.9935 18.5303 18.9935 18.1764C18.9935 17.8212 19.2825 17.5321 19.6377 17.5321C19.993 17.5321 20.282 17.8212 20.282 18.1764C20.282 18.5304 19.9949 18.8185 19.6413 18.8204C19.6403 18.8205 19.6389 18.8205 19.638 18.8205ZM17.5425 21.1213C17.608 20.7996 17.7475 20.5378 17.9639 20.3279C18.5733 19.7367 19.5998 19.7573 19.6217 19.7579C19.6233 19.7579 19.625 19.7578 19.6267 19.7579C19.6304 19.7579 19.634 19.7581 19.6378 19.7581C19.6419 19.7581 19.646 19.7579 19.6501 19.7578C19.6516 19.7578 19.6531 19.7579 19.6546 19.7578C19.6649 19.758 20.6983 19.7373 21.3087 20.3263C21.5265 20.5364 21.667 20.7989 21.7329 21.1213H17.5425Z" fill="url(#paint1_linear_3831_14045)" />
                                                    <path d="M24.9238 17.0693H28.3633C28.6222 17.0693 28.832 16.8594 28.832 16.6006C28.832 16.3417 28.6222 16.1318 28.3633 16.1318H24.9238C24.6649 16.1318 24.4551 16.3417 24.4551 16.6006C24.4551 16.8594 24.6649 17.0693 24.9238 17.0693Z" fill="url(#paint2_linear_3831_14045)" />
                                                    <path d="M24.9238 19.1299H28.3633C28.6222 19.1299 28.832 18.92 28.832 18.6611C28.832 18.4023 28.6222 18.1924 28.3633 18.1924H24.9238C24.6649 18.1924 24.4551 18.4023 24.4551 18.6611C24.4551 18.92 24.6649 19.1299 24.9238 19.1299Z" fill="url(#paint3_linear_3831_14045)" />
                                                    <path d="M24.9238 21.1904H28.3633C28.6222 21.1904 28.832 20.9805 28.832 20.7217C28.832 20.4628 28.6222 20.2529 28.3633 20.2529H24.9238C24.6649 20.2529 24.4551 20.4628 24.4551 20.7217C24.4551 20.9805 24.6649 21.1904 24.9238 21.1904Z" fill="url(#paint4_linear_3831_14045)" />
                                                    <path d="M16.6494 23.7949V26.1015C16.6494 26.3603 16.8593 26.5702 17.1182 26.5702H28.3632C28.6221 26.5702 28.8319 26.3603 28.8319 26.1015V23.7949C28.8319 23.5361 28.6221 23.3262 28.3632 23.3262H17.1182C16.8593 23.3262 16.6494 23.536 16.6494 23.7949ZM17.5869 24.2637H27.8944V25.6327H17.5869V24.2637Z" fill="url(#paint5_linear_3831_14045)" />
                                                    <path d="M24.218 28.4131C24.218 28.1542 24.0081 27.9443 23.7493 27.9443H17.2837C17.0248 27.9443 16.8149 28.1542 16.8149 28.4131C16.8149 28.6719 17.0248 28.8818 17.2837 28.8818H23.7493C24.0081 28.8818 24.218 28.6719 24.218 28.4131Z" fill="url(#paint6_linear_3831_14045)" />
                                                    <path d="M17.2837 29.9092C17.0248 29.9092 16.8149 30.1191 16.8149 30.3779C16.8149 30.6368 17.0248 30.8467 17.2837 30.8467H20.328C20.5869 30.8467 20.7967 30.6368 20.7967 30.3779C20.7967 30.1191 20.5869 29.9092 20.328 29.9092H17.2837Z" fill="url(#paint7_linear_3831_14045)" />
                                                    <path d="M13.5246 32.7586C13.6663 32.9713 13.9619 33.0289 14.1743 32.888C14.3873 32.7467 14.4447 32.45 14.3037 32.2378C14.1623 32.0252 13.866 31.9672 13.654 32.1084C13.44 32.2509 13.385 32.5452 13.5246 32.7586Z" fill="url(#paint8_linear_3831_14045)" />
                                                </g>
                                                <defs>
                                                    <linearGradient id="paint0_linear_3831_14045" x1="15.0607" y1="25" x2="42.5212" y2="40.6232" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#37BABB" />
                                                        <stop offset="1" stop-color="#5DE1E1" />
                                                    </linearGradient>
                                                    <linearGradient id="paint1_linear_3831_14045" x1="16.9752" y1="18.6295" x2="24.3687" y2="22.4035" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#37BABB" />
                                                        <stop offset="1" stop-color="#5DE1E1" />
                                                    </linearGradient>
                                                    <linearGradient id="paint2_linear_3831_14045" x1="24.7497" y1="16.6006" x2="25.5726" y2="18.7865" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#37BABB" />
                                                        <stop offset="1" stop-color="#5DE1E1" />
                                                    </linearGradient>
                                                    <linearGradient id="paint3_linear_3831_14045" x1="24.7497" y1="18.6611" x2="25.5726" y2="20.847" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#37BABB" />
                                                        <stop offset="1" stop-color="#5DE1E1" />
                                                    </linearGradient>
                                                    <linearGradient id="paint4_linear_3831_14045" x1="24.7497" y1="20.7217" x2="25.5726" y2="22.9076" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#37BABB" />
                                                        <stop offset="1" stop-color="#5DE1E1" />
                                                    </linearGradient>
                                                    <linearGradient id="paint5_linear_3831_14045" x1="17.4694" y1="24.9482" x2="20.785" y2="32.0323" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#37BABB" />
                                                        <stop offset="1" stop-color="#5DE1E1" />
                                                    </linearGradient>
                                                    <linearGradient id="paint6_linear_3831_14045" x1="17.3132" y1="28.4131" x2="17.8425" y2="30.791" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#37BABB" />
                                                        <stop offset="1" stop-color="#5DE1E1" />
                                                    </linearGradient>
                                                    <linearGradient id="paint7_linear_3831_14045" x1="17.0829" y1="30.3779" x2="17.9647" y2="32.5087" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#37BABB" />
                                                        <stop offset="1" stop-color="#5DE1E1" />
                                                    </linearGradient>
                                                    <linearGradient id="paint8_linear_3831_14045" x1="13.5106" y1="32.4981" x2="14.5786" y2="33.1054" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#37BABB" />
                                                        <stop offset="1" stop-color="#5DE1E1" />
                                                    </linearGradient>
                                                    <clipPath id="clip0_3831_14045">
                                                        <rect width="24" height="24" fill="white" transform="translate(13.4448 13)" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                    <svg class="srv-tab-svg-circle" viewBox="0 0 140 126" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle opacity="0.08" cx="52.2692" cy="27.2692" r="44.7692" stroke="white" stroke-width="15" />
                                        <circle opacity="0.08" cx="87.1154" cy="73.7311" r="44.7692" stroke="white" stroke-width="15" />
                                    </svg>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="tab-tigger three" data-toggle="tab" href="#ehajjDashBoardTabThree" role="tab">
                                    <div class="content-area">
                                        <div class="service-tab-menu-text">
                                            <h2 class="mb-0">{{$countData['guide'] ?? 0}}</h2>
                                            <p>হজ গাইড</p>
                                        </div>
                                        <div class="service-tab-menu-icon">
                                            <svg width="51" height="50" viewBox="0 0 51 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="0.444824" width="50" height="50" rx="25" fill="#C8DEFF" />
                                                <g clip-path="url(#clip0_3831_14108)">
                                                    <path d="M35.5411 28.2349C34.7457 27.536 33.7811 26.9321 32.6738 26.4401C32.2005 26.2299 31.6466 26.4431 31.4364 26.9162C31.2262 27.3893 31.4393 27.9432 31.9125 28.1536C32.8461 28.5685 33.6507 29.0697 34.3035 29.6434C35.1082 30.3505 35.5698 31.3745 35.5698 32.4531V34.1875C35.5698 34.7044 35.1492 35.125 34.6323 35.125H16.2573C15.7404 35.125 15.3198 34.7044 15.3198 34.1875V32.4531C15.3198 31.3745 15.7814 30.3505 16.5862 29.6434C17.5334 28.811 20.2932 26.875 25.4448 26.875C29.2701 26.875 32.3823 23.7628 32.3823 19.9375C32.3823 16.1122 29.2701 13 25.4448 13C21.6196 13 18.5073 16.1122 18.5073 19.9375C18.5073 22.1738 19.5712 24.166 21.2191 25.4354C18.2037 26.0983 16.328 27.3741 15.3486 28.2349C14.1388 29.2979 13.4448 30.8352 13.4448 32.4531V34.1875C13.4448 35.7384 14.7064 37 16.2573 37H34.6323C36.1832 37 37.4448 35.7384 37.4448 34.1875V32.4531C37.4448 30.8352 36.7509 29.2979 35.5411 28.2349ZM20.3823 19.9375C20.3823 17.1461 22.6534 14.875 25.4448 14.875C28.2363 14.875 30.5073 17.1461 30.5073 19.9375C30.5073 22.7289 28.2363 25 25.4448 25C22.6534 25 20.3823 22.7289 20.3823 19.9375Z" fill="url(#paint0_linear_3831_14108)" />
                                                </g>
                                                <defs>
                                                    <linearGradient id="paint0_linear_3831_14108" x1="13.3248" y1="22.12" x2="37.3248" y2="37" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#2973EA" />
                                                        <stop offset="1" stop-color="#4290FF" />
                                                    </linearGradient>
                                                    <clipPath id="clip0_3831_14108">
                                                        <rect width="24" height="24" fill="white" transform="translate(13.4448 13)" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                    <svg class="srv-tab-svg-circle" viewBox="0 0 140 126" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle opacity="0.08" cx="52.2692" cy="27.2692" r="44.7692" stroke="white" stroke-width="15" />
                                        <circle opacity="0.08" cx="87.1154" cy="73.7311" r="44.7692" stroke="white" stroke-width="15" />
                                    </svg>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="tab-tigger four" data-toggle="tab" href="#ehajjDashBoardTabFour" role="tab">
                                    <div class="content-area">
                                        <div class="service-tab-menu-text">
                                            <h2 class="mb-0">{{$countData['pre_reg_refund'] ?? 0}}</h2>
                                            <p>রিফান্ড</p>
                                        </div>
                                        <div class="service-tab-menu-icon">
                                            <svg width="51" height="50" viewBox="0 0 51 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="0.444824" width="50" height="50" rx="25" fill="#FBD3D0" />
                                                <g clip-path="url(#clip0_3831_14129)">
                                                    <path d="M33.601 27.2753V16.5156C33.601 14.5771 32.0709 13 30.19 13H15.6704C14.4432 13 13.4448 13.9989 13.4448 15.2266V18.9286C13.4448 19.1875 13.6547 19.3974 13.9136 19.3974H16.9586V23.312C16.9586 23.5709 17.1685 23.7808 17.4273 23.7808C17.6862 23.7808 17.8961 23.5709 17.8961 23.312V15.2266C17.8961 14.7466 17.7434 14.3015 17.4842 13.9375H30.1901C31.554 13.9375 32.6636 15.094 32.6636 16.5156V27.1583C32.6169 27.157 32.57 27.1563 32.523 27.1563C29.8091 27.1563 27.6012 29.3642 27.6012 32.0781C27.6012 32.2642 27.6116 32.448 27.6318 32.6288L27.4961 32.5417C27.3418 32.4428 27.144 32.4428 26.9897 32.5417L25.2798 33.6392L23.5698 32.5417C23.4156 32.4428 23.2177 32.4428 23.0634 32.5417L21.3535 33.6392L19.6436 32.5417C19.4893 32.4428 19.2914 32.4428 19.1372 32.5417L17.8961 33.3384V27.5307C17.8961 27.2719 17.6862 27.062 17.4273 27.062C17.1685 27.062 16.9586 27.2719 16.9586 27.5307V34.1962C16.9586 34.3676 17.0521 34.5253 17.2025 34.6075C17.3529 34.6897 17.5362 34.6832 17.6805 34.5907L19.3904 33.4932L21.1004 34.5907C21.2546 34.6896 21.4525 34.6896 21.6068 34.5907L23.3167 33.4932L25.0266 34.5907C25.1038 34.6402 25.1917 34.6649 25.2798 34.6649C25.3678 34.6649 25.4558 34.6402 25.5329 34.5907L27.2429 33.4932L27.9769 33.9643C28.7184 35.745 30.4769 37 32.523 37C35.2369 37 37.4448 34.792 37.4448 32.0781C37.4448 29.7344 35.7982 27.7681 33.601 27.2753ZM16.9586 18.4599H14.3823V15.2266C14.3823 14.5157 14.9601 13.9375 15.6704 13.9375C16.3806 13.9375 16.9585 14.5157 16.9585 15.2266L16.9586 18.4599ZM32.523 36.0625C30.326 36.0625 28.5386 34.2751 28.5386 32.0781C28.5386 29.8812 30.326 28.0938 32.523 28.0938C34.7199 28.0938 36.5073 29.8812 36.5073 32.0781C36.5073 34.2751 34.7199 36.0625 32.523 36.0625Z" fill="url(#paint0_linear_3831_14129)" />
                                                    <path d="M35.1643 31.1834C35.0537 31.1047 34.9208 31.0807 34.7977 31.1061C34.4197 30.2199 33.5417 29.6055 32.523 29.6055C32.0896 29.6055 31.6621 29.72 31.2867 29.9367C31.0625 30.0662 30.9857 30.3528 31.1151 30.577C31.2446 30.8012 31.5312 30.8782 31.7555 30.7486C31.9885 30.6141 32.254 30.5429 32.523 30.5429C33.0776 30.5429 33.565 30.8363 33.8349 31.2795C33.7561 31.3119 33.6853 31.3662 33.6322 31.4407C33.482 31.6516 33.5312 31.9442 33.7421 32.0944L34.255 32.4598C34.3375 32.5186 34.4324 32.5468 34.5265 32.5468C34.673 32.5468 34.8172 32.4784 34.9087 32.35L35.2741 31.8371C35.4243 31.6263 35.3751 31.3336 35.1643 31.1834Z" fill="url(#paint1_linear_3831_14129)" />
                                                    <path d="M33.2907 33.4077C33.0576 33.5422 32.7921 33.6133 32.5231 33.6133C31.9703 33.6133 31.4824 33.3196 31.2119 32.8765C31.2904 32.844 31.361 32.7898 31.4139 32.7155C31.5641 32.5047 31.5149 32.212 31.3041 32.0618L30.7912 31.6964C30.5802 31.5461 30.2876 31.5954 30.1374 31.8062L29.772 32.3191C29.6219 32.53 29.671 32.8227 29.8819 32.9729C29.9644 33.0316 30.0593 33.0599 30.1534 33.0599C30.1856 33.0599 30.2177 33.0565 30.2492 33.05C30.6283 33.9356 31.5079 34.5508 32.5231 34.5508C32.9565 34.5508 33.384 34.4363 33.7595 34.2195C33.9837 34.0901 34.0605 33.8034 33.931 33.5792C33.8016 33.3551 33.515 33.2783 33.2907 33.4077Z" fill="url(#paint2_linear_3831_14129)" />
                                                    <path d="M20.9448 22.9688C20.9448 22.5552 20.6084 22.2188 20.1948 22.2188C19.7813 22.2188 19.4448 22.5552 19.4448 22.9688H19.9448C19.9448 22.8309 20.057 22.7188 20.1948 22.7188C20.3327 22.7188 20.4448 22.8309 20.4448 22.9688V23.7188H19.9448V24.2188H20.4448V25.7188C20.4448 26.2702 20.8934 26.7188 21.4448 26.7188C21.9962 26.7188 22.4448 26.2702 22.4448 25.7188C22.4448 25.4431 22.2205 25.2188 21.9448 25.2188H21.4448V25.7188H21.9448C21.9448 25.9945 21.7205 26.2188 21.4448 26.2188C21.1691 26.2188 20.9448 25.9945 20.9448 25.7188V24.2188H22.4448V23.7188H20.9448V22.9688Z" fill="url(#paint3_linear_3831_14129)" />
                                                    <path d="M29.9106 25.6562H24.4634C24.2045 25.6562 23.9946 25.8662 23.9946 26.125C23.9946 26.3838 24.2045 26.5937 24.4634 26.5937H29.9106C30.1694 26.5937 30.3793 26.3838 30.3793 26.125C30.3793 25.8662 30.1694 25.6562 29.9106 25.6562Z" fill="url(#paint4_linear_3831_14129)" />
                                                    <path d="M29.9106 20.9688H24.4634C24.2045 20.9688 23.9946 21.1787 23.9946 21.4375C23.9946 21.6963 24.2045 21.9062 24.4634 21.9062H29.9106C30.1694 21.9062 30.3793 21.6963 30.3793 21.4375C30.3793 21.1787 30.1694 20.9688 29.9106 20.9688Z" fill="url(#paint5_linear_3831_14129)" />
                                                    <path d="M29.9106 23.3125H24.4634C24.2045 23.3125 23.9946 23.5224 23.9946 23.7812C23.9946 24.0401 24.2045 24.25 24.4634 24.25H29.9106C30.1694 24.25 30.3793 24.0401 30.3793 23.7812C30.3793 23.5224 30.1694 23.3125 29.9106 23.3125Z" fill="url(#paint6_linear_3831_14129)" />
                                                    <path d="M31.14 19.0938V16.0469C31.14 15.788 30.9301 15.5781 30.6713 15.5781H19.8882C19.6293 15.5781 19.4194 15.788 19.4194 16.0469V19.0938C19.4194 19.3526 19.6293 19.5625 19.8882 19.5625H30.6713C30.9301 19.5625 31.14 19.3526 31.14 19.0938ZM30.2025 18.625H20.3569V16.5156H30.2025V18.625Z" fill="url(#paint7_linear_3831_14129)" />
                                                    <path d="M17.4279 25.8896C17.6867 25.8896 17.8966 25.6797 17.8966 25.4209C17.8966 25.1621 17.6867 24.9521 17.4279 24.9521H17.4275C17.1687 24.9521 16.959 25.1621 16.959 25.4209C16.959 25.6797 17.169 25.8896 17.4279 25.8896Z" fill="url(#paint8_linear_3831_14129)" />
                                                </g>
                                                <defs>
                                                    <linearGradient id="paint0_linear_3831_14129" x1="13.3248" y1="22.12" x2="37.3248" y2="37" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#F56059" />
                                                        <stop offset="1" stop-color="#FC7869" />
                                                    </linearGradient>
                                                    <linearGradient id="paint1_linear_3831_14129" x1="31.0307" y1="30.7232" x2="34.2994" y2="33.692" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#F56059" />
                                                        <stop offset="1" stop-color="#FC7869" />
                                                    </linearGradient>
                                                    <linearGradient id="paint2_linear_3831_14129" x1="29.6635" y1="32.7271" x2="32.9323" y2="35.6959" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#F56059" />
                                                        <stop offset="1" stop-color="#FC7869" />
                                                    </linearGradient>
                                                    <linearGradient id="paint3_linear_3831_14129" x1="19.4298" y1="23.9288" x2="22.977" y2="25.3949" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#F56059" />
                                                        <stop offset="1" stop-color="#FC7869" />
                                                    </linearGradient>
                                                    <linearGradient id="paint4_linear_3831_14129" x1="23.9627" y1="26.0125" x2="24.4321" y2="27.9947" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#F56059" />
                                                        <stop offset="1" stop-color="#FC7869" />
                                                    </linearGradient>
                                                    <linearGradient id="paint5_linear_3831_14129" x1="23.9627" y1="21.325" x2="24.4321" y2="23.3072" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#F56059" />
                                                        <stop offset="1" stop-color="#FC7869" />
                                                    </linearGradient>
                                                    <linearGradient id="paint6_linear_3831_14129" x1="23.9627" y1="23.6687" x2="24.4321" y2="25.6509" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#F56059" />
                                                        <stop offset="1" stop-color="#FC7869" />
                                                    </linearGradient>
                                                    <linearGradient id="paint7_linear_3831_14129" x1="19.3608" y1="17.0922" x2="23.1114" y2="23.9325" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#F56059" />
                                                        <stop offset="1" stop-color="#FC7869" />
                                                    </linearGradient>
                                                    <linearGradient id="paint8_linear_3831_14129" x1="16.9543" y1="25.3084" x2="17.8919" y2="25.8898" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#F56059" />
                                                        <stop offset="1" stop-color="#FC7869" />
                                                    </linearGradient>
                                                    <clipPath id="clip0_3831_14129">
                                                        <rect width="24" height="24" fill="white" transform="translate(13.4448 13)" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                    <svg class="srv-tab-svg-circle" viewBox="0 0 140 126" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle opacity="0.08" cx="52.2692" cy="27.2692" r="44.7692" stroke="white" stroke-width="15" />
                                        <circle opacity="0.08" cx="87.1154" cy="73.7311" r="44.7692" stroke="white" stroke-width="15" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content ehajj-tab-content-area">
                        <div class="tab-pane active" id="ehajjDashBoardTabOne" role="tabpanel">
                            @if(isset($selfUserHelpText['pre_reg']))
                                <div class="ehajj-srv-tab-content srv-content-highlight">
                                    @if(isset($selfUserHelpText['pre_reg']['service_step_image']))
                                        <div class="srv-tab-steps">
                                            <img src="{{$selfUserHelpText['pre_reg']['service_step_image']}}" alt="Steps-Image" />
                                        </div>
                                    @endif

                                    <div class="srv-tab-desc">
                                        <h3>{!! $selfUserHelpText['pre_reg']['heder_text']!!}</h3>
                                        <ul>{!! $selfUserHelpText['pre_reg']['help_text'] !!}</ul>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane" id="ehajjDashBoardTabTwo" role="tabpanel">
                            @if(isset($selfUserHelpText['reg']))
                                <div class="ehajj-srv-tab-content srv-content-highlight">
                                    @if(isset($selfUserHelpText['reg']['service_step_image']))
                                        <div class="srv-tab-steps">
                                            <img src="{{$selfUserHelpText['reg']['service_step_image']}}" alt="Steps-Image" />
                                        </div>
                                    @endif
                                    <div class="srv-tab-desc">
                                        <h3>{!! $selfUserHelpText['reg']['heder_text']!!}</h3>
                                        <ul>{!! $selfUserHelpText['reg']['help_text'] !!}</ul>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane" id="ehajjDashBoardTabThree" role="tabpanel">
                            @if(isset( $selfUserHelpText['guide']))
                                <div class="ehajj-srv-tab-content srv-content-highlight">
                                    @if(isset($selfUserHelpText['guide']['service_step_image']))
                                        <div class="srv-tab-steps">
                                            <img src="{{$selfUserHelpText['guide']['service_step_image']}}" alt="Steps-Image" />
                                        </div>
                                    @endif

                                    <div class="srv-tab-desc">
                                        <h3>{!! $selfUserHelpText['guide']['heder_text']!!}</h3>
                                        <ul>{!! $selfUserHelpText['guide']['help_text'] !!}</ul>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane" id="ehajjDashBoardTabFour" role="tabpanel">
                            @if(isset($selfUserHelpText['refund']))
                                <div class="ehajj-srv-tab-content srv-content-highlight">
                                    @if(isset($selfUserHelpText['refund']['service_step_image']))
                                        <div class="srv-tab-steps">
                                            <img src="{{$selfUserHelpText['refund']['service_step_image']}}" alt="Steps-Image" />
                                        </div>
                                    @endif

                                    <div class="srv-tab-desc">
                                        <h3>{!! $selfUserHelpText['refund']['heder_text']!!}</h3>
                                        <ul>{!! $selfUserHelpText['refund']['help_text'] !!}</ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- End service guide content -->
            </div>
        @endif
        @if ( $user_type != '18x415')
            <div class="card card-magenta border border-magenta" style="">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-10">
                            <h5><i class="fa fa-list"></i> <b>{!! trans('ProcessPath::messages.dashboard_application_list') !!}
                                    <span class="list_name"></span>
                                    <!-- @if (isset($process_info->name) && !empty($process_info->name))
                                        for
                                        ({{ $process_info->name }}) -->
                                </b>
                                <!-- @endif -->
                            </h5>

                        </div>
    {{--                    @if ( count($process_type_data_arr) > 0 && $user_type != '17x171' )--}}
    {{--                        <button type="button" class="btn btn-default" data-toggle="modal" style="text-align: right" data-target="#processTypeModal">--}}
    {{--                            <i class="fa fa-plus"></i><b>  New Application </b>--}}
    {{--                        </button>--}}
    {{--                    @endif--}}

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

                @if ( !in_array($user_type,['5x505','18x415']))
                    <div class="">
                        <div class="row m-2">
                            @if(count($process_type_data_arr) > 0)
                                @foreach($process_type_data_arr as $key=>$val)
                                        <div class="col-lg-3 col-md-4 col-sm-12 serviceBoxes " id="{{$val['id']}}">
                                            <div class="small-box" style="background-color: {{ $val['panel'] }}">
                                                <div class="inner text-white">
                                                    <h3>{{ !empty($val['total']) ? $val['total'] :'0' }}</h3>
                                                    <p>{{ !empty($val['name']) ? $val['name'] :'N/A'}}</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-file"></i>
                                                </div>
                                                <div class="small-box-footer">
                                                    Application list
                                                    <i class="fa fa-arrow-circle-right"></i>
                                                </div>
                                            </div>
                                        </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="card-body" id="statuswiseAppsDiv" style="display: none">

                        </div>
                    </div>

                    {{--            <div class="nav-tabs-custom" style="margin-top: 15px;padding: 0px 5px;">--}}
                    {{--                <nav class="navbar navbar-expand-mdjustify-content-center">--}}

                    {{--                    <ul class="nav nav-tabs">--}}
                    {{--                        @if ($user_type != '1x101')--}}
                    {{--                            @if(\Illuminate\Support\Facades\Auth::user()->desk_id != 0)--}}
                    {{--                                <li id="tab1" class="nav-item ">--}}
                    {{--                                    <a data-toggle="tab" href="#list_desk" class="mydesk nav-link active"--}}
                    {{--                                       aria-expanded="true">--}}
                    {{--                                        <b>{!! trans('ProcessPath::messages.my_desk') !!}</b>--}}
                    {{--                                    </a>--}}
                    {{--                                </li>--}}
                    {{--                                <li id="tab1" class="nav-item ">--}}
                    {{--                                    <a data-toggle="tab" href="#my_list_desk" class="myapplication nav-link "--}}
                    {{--                                       aria-expanded="true">--}}
                    {{--                                        <b>My Application</b>--}}
                    {{--                                    </a>--}}
                    {{--                                </li>--}}
                    {{--                            @else--}}
                    {{--                                <li id="tab1" class="nav-item ">--}}
                    {{--                                    <a data-toggle="tab" href="#my_list_desk" class="myapplication nav-link active"--}}
                    {{--                                       aria-expanded="true">--}}
                    {{--                                        <b>My Application</b>--}}
                    {{--                                    </a>--}}
                    {{--                                </li>--}}
                    {{--                            @endif--}}
                    {{--                        @else--}}
                    {{--                            <li id="tab1" class="nav-item active">--}}
                    {{--                                <a data-toggle="tab" href="#list_desk" class="mydesk nav-link active"--}}
                    {{--                                   aria-expanded="true">--}}
                    {{--                                    <b>{!! trans('ProcessPath::messages.list') !!}</b>--}}
                    {{--                                </a>--}}
                    {{--                            </li>--}}
                    {{--                        @endif--}}

                    {{--                        <li id="tab4" class="nav-item">--}}
                    {{--                            <a data-toggle="tab" href="#favoriteList" class="favorite_list nav-link"--}}
                    {{--                               aria-expanded="true">--}}
                    {{--                                <b>{!! trans('ProcessPath::messages.favourite') !!}</b>--}}
                    {{--                            </a>--}}
                    {{--                        </li>--}}

                    {{--                        <li id="tab3" class="nav-item">--}}
                    {{--                            <a class="nav-link" data-toggle="tab" href="#list_search" id="search_by_keyword"--}}
                    {{--                               aria-expanded="false">--}}
                    {{--                                <b>{!! trans('ProcessPath::messages.search') !!}</b>--}}
                    {{--                            </a>--}}
                    {{--                        </li>--}}
                    {{--                    </ul>--}}

                    {{--                    @if ( $user_type != '5x505' )--}}
                    {{--                        <ul class="navbar-nav ml-auto">--}}
                    {{--                            <div class="row">--}}
                    {{--                                <li class="process_type_tab nav-item" id="processDropdown">--}}
                    {{--                                    {!! Form::select('ProcessType', ['0' => 'সকল তথ্য'] + $ProcessType, $process_type_id, [--}}
                    {{--                                        'class' => 'form-control ProcessType',--}}
                    {{--                                    ]) !!}--}}
                    {{--                                </li>--}}
                    {{--                            </div>--}}
                    {{--                        </ul>--}}
                    {{--                    @endif--}}
                    {{--                </nav>--}}
                    {{--                <div id="reyad" class="tab-content">--}}
                    {{--                    <div id="list_desk" class="tab-pane active" style="margin-top: 20px">--}}
                    {{--                        <table id="table_desk" class="table table-striped table-bordered display"--}}
                    {{--                               style="width: 100%">--}}
                    {{--                            <thead>--}}
                    {{--                            <tr>--}}
                    {{--                                <th style="width: 15%;">{!! trans('ProcessPath::messages.tracking_no') !!}</th>--}}
                    {{--                                <th>{!! trans('ProcessPath::messages.current_desk') !!}</th>--}}
                    {{--                                <th>{!! trans('ProcessPath::messages.process_type') !!}</th>--}}
                    {{--                                <th style="width: 35%">{!! trans('ProcessPath::messages.reference_data') !!}</th>--}}
                    {{--                                <th>{!! trans('ProcessPath::messages.status_') !!}</th>--}}
                    {{--                                <th>{!! trans('ProcessPath::messages.modified') !!}</th>--}}
                    {{--                                <th>{!! trans('ProcessPath::messages.action') !!}</th>--}}
                    {{--                            </tr>--}}
                    {{--                            </thead>--}}
                    {{--                            <tbody>--}}
                    {{--                            </tbody>--}}
                    {{--                        </table>--}}
                    {{--                    </div>--}}

                    {{--                    <div id="my_list_desk" class="tab-pane" style="margin-top: 20px">--}}
                    {{--                        <table id="my_application_desk" class="table table-striped table-bordered display"--}}
                    {{--                               style="width: 100%">--}}
                    {{--                            <thead>--}}
                    {{--                            <tr>--}}
                    {{--                                <th style="width: 15%;">{!! trans('ProcessPath::messages.tracking_no') !!}</th>--}}
                    {{--                                <th>{!! trans('ProcessPath::messages.current_desk') !!}</th>--}}
                    {{--                                <th>{!! trans('ProcessPath::messages.process_type') !!}</th>--}}
                    {{--                                <th style="width: 35%">{!! trans('ProcessPath::messages.reference_data') !!}</th>--}}
                    {{--                                <th>{!! trans('ProcessPath::messages.status_') !!}</th>--}}
                    {{--                                <th>{!! trans('ProcessPath::messages.modified') !!}</th>--}}
                    {{--                                <th>{!! trans('ProcessPath::messages.action') !!}</th>--}}
                    {{--                            </tr>--}}
                    {{--                            </thead>--}}
                    {{--                            <tbody>--}}
                    {{--                            </tbody>--}}
                    {{--                        </table>--}}
                    {{--                    </div>--}}

                    {{--                    <div id="list_search" class="tab-pane" style="margin-top: 20px">--}}
                    {{--                        @include('ProcessPath::search')--}}
                    {{--                    </div>--}}
                    {{--                    <div id="list_delg_desk" class="tab-pane" style="margin-top: 20px">--}}
                    {{--                        <div class="table-responsive">--}}
                    {{--                            <table id="table_delg_desk" class="table table-striped table-bordered display"--}}
                    {{--                                   style="width: 100%">--}}
                    {{--                                <thead>--}}
                    {{--                                <tr>--}}
                    {{--                                    <th style="width: 15%;">{!! trans('ProcessPath::messages.tracking_no') !!}</th>--}}
                    {{--                                    <th>{!! trans('ProcessPath::messages.current_desk') !!}</th>--}}
                    {{--                                    <th>{!! trans('ProcessPath::messages.process_type') !!}</th>--}}
                    {{--                                    <th style="width: 35%">{!! trans('ProcessPath::messages.reference_data') !!}</th>--}}
                    {{--                                    <th>{!! trans('ProcessPath::messages.status_') !!}</th>--}}
                    {{--                                    <th>{!! trans('ProcessPath::messages.modified') !!}</th>--}}
                    {{--                                    <th>{!! trans('ProcessPath::messages.action') !!}</th>--}}
                    {{--                                </tr>--}}
                    {{--                                </thead>--}}
                    {{--                                <tbody>--}}
                    {{--                                </tbody>--}}
                    {{--                            </table>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}

                    {{--                    <div id="favoriteList" class="tab-pane" style="margin-top: 20px">--}}
                    {{--                        <div class="table-responsive">--}}
                    {{--                            <table id="favorite_list" class="table table-striped table-bordered display"--}}
                    {{--                                   style="width: 100%">--}}
                    {{--                                <thead>--}}
                    {{--                                <tr>--}}
                    {{--                                    <th style="width: 15%;">{!! trans('ProcessPath::messages.tracking_no') !!}</th>--}}
                    {{--                                    <th>{!! trans('ProcessPath::messages.current_desk') !!}</th>--}}
                    {{--                                    <th>{!! trans('ProcessPath::messages.process_type') !!}</th>--}}
                    {{--                                    <th style="width: 35%">{!! trans('ProcessPath::messages.reference_data') !!}</th>--}}
                    {{--                                    <th>{!! trans('ProcessPath::messages.status_') !!}</th>--}}
                    {{--                                    <th>{!! trans('ProcessPath::messages.modified') !!}</th>--}}
                    {{--                                    <th>{!! trans('ProcessPath::messages.action') !!}</th>--}}
                    {{--                                </tr>--}}
                    {{--                                </thead>--}}
                    {{--                                <tbody>--}}
                    {{--                                </tbody>--}}
                    {{--                            </table>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                </div>--}}
                    {{--            </div>--}}
                </div>
           @endif
        @endif
    </div>
</div>

@section('footer-script')
    @include('partials.datatable-js')
    <script src="{{ asset('assets/scripts/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
    <script>
        $('.dash-service-guide-sec div[data-toggle="tab"]').click(function(e) {
            e.preventDefault();
            $('.dash-service-guide-sec div[data-toggle="tab"]').removeClass("active").attr("aria-selected", "false");
            $(this).addClass("active").attr("aria-selected", "true");
            const target = $(this).attr("href") || $(this).data("target");
            $(".tab-pane").removeClass("active show");
            $(target).addClass("active show");
        });
    </script>

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
<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
