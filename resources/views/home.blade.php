@extends('public_home.front')
@section('header-resources')

@endsection
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css"/>
<style>
    #prev_news{
        cursor: pointer;
    }
    .icon-pdf-download {
        background: url({{asset('assets/images/icon-pdf-green.png')}}) no-repeat center center;
        background-size: 20px;
        display: inline-block;
        height: 20px;
        width: 20px;
    }
    .news-seemore-text{
        color: #009343;
        font-size: 16px;
        font-weight: bold;
        display: inline-block;
    }

    #newsTable tr td:nth-child(1) {
        width: 90%;
    }

    #newsTable tr td:nth-child(2) {
        width: 10%;
        text-align: center;
    }


    .news-list-title {
        margin: 0px;
    }

    .news-icon img {
        height: 22px;
        width: 30px;
    }

    #pilgrim_srch_val::placeholder {
        font-size: 12px; /* Adjust the font size as needed */
    }

    .icon-minus, .icon-plus {
        color: #009444;
    }
    #prev_news{
        cursor: pointer;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0px;
    }

    @media screen and (max-width: 600px) {
        #newsTable tr td:nth-child(1) {
            width: 96%;
        }

        #newsTable tr td:nth-child(2) {
            width: 4%;
        }
        #newsTable tr td,#agencyListTable tr td{
            font-size: 14px;
        }
        #agencyListTable tr td:nth-child(1) {
            width: 4%;
        }

        #agencyListTable tr td:nth-child(2) {
            width: 10%;
        }
        .dataTables_info, .dataTables_paginate {
            font-size: 13px;
        }
    }

    .search-container {
        margin-top: 35px;
        margin-bottom: 30px;
        padding: 10px;
        background-color: #f1f1f1;
    }
    .search-container h3{
        margin: 10px 0;
        font-size: 25px;
        line-height: 1.2;
        font-weight: normal;
        white-space: nowrap;
    }
    .search-container .input-group {
        width: 100%;
        position: relative;
    }
    .search-container .input-group input {
        width: 100%;
        padding: 12px 60px 12px 10px;
        border: 1px solid #009444;
        border-radius: 5px;
        overflow: hidden;
    }
    .search-container .input-group input:focus {
        outline: none;
    }
    .search-container .input-group button {
        background-color: #009444;
        color: #fff;
        border: none;
        padding: 7px 10px;
        border-radius: 3px;
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
    }
    .search-container .input-group button:hover {
        background-color: #00684D;
    }

</style>
@section('body')
    <section class="home-intro-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="home-slider">
                        <div id="hajjBD_homeSlider" class="carousel slide" data-ride="carousel">
                            {{-- <ol class="carousel-indicators">
                                <li data-target="#hajjBD_homeSlider" data-slide-to="0" class="active"></li>
                                <li data-target="#hajjBD_homeSlider" data-slide-to="1"></li>
                                <li data-target="#hajjBD_homeSlider" data-slide-to="2"></li>
                            </ol> --}}
                            @if (count($home_slider_image) > 0)
                                <ol class="carousel-indicators">
                                    @foreach ($home_slider_image as $key => $value)
                                        <li data-target="#hajjBD_homeSlider" data-slide-to="{{$key}}" class="{{$key==0 ? 'active' : ''}}"></li>
                                    @endforeach
                                </ol>
                            @endif
                            <div class="carousel-inner">
                                @if (count($home_slider_image) > 0)
                                    @foreach ($home_slider_image as $key => $value)
                                    <div class="carousel-item {{$key==0 ? 'active' : ''}}">
                                        <div class="home-slider-item" style="background-image: url({{ asset($value['slider_image'])}});">
                                            <div class="slider-notice">
                                                <div class="slider-notice-item" style="display: none">
                                                    <h3 id="hajjDateCounter1"><strong><span class="days" style=" font-weight: bold; font-size: 40px"></span></strong><span>দিন বাকি</span></h3>
                                                    <p class="hajj_possible_date" id="hajj_possible_date_s1"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="search-container">
                        <div class="row">
                            <div class="col-md-4 d-flex align-items-center justify-content-center text-center">
                                <h3>তথ্য অনুসন্ধান</h3>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="" placeholder="হজযাত্রীর ট্র্যাকিং নম্বর, পাসপোর্ট নম্বর অথবা হজ এজেন্সি লাইসেন্স নম্বর দিন" id="haji_search_val">
                                    <button type="button" id="haji_search">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                {{-- @include('public_home.login_panel') --}}
            </div>
        </div>
    </section>

    <section class="hajj-registration-info">
        <div class="container">
            <div class="hajj-reg-info-container py-3">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hajj-reg-info-content">
                            <span class="reg-info-cornar"></span>
                            <div class="hajj-reg-title">
                                <h3>নিবন্ধিত হজযাত্রী</h3>
                                <span class="reg-subtitle" id="reg-subTitle"></span>
                            </div>
                            <div class="hajj-info-block">
                                <div class="hajj-reg-info-item">
                                    <div class="hajj-info-box">
                                        <h2><span id="RegDataGov"><i class="fa fa-spinner fa-spin" style="font-size:26px !important;"></i></span> জন</h2>
                                        <p>সরকারি মাধ্যম</p>
                                    </div>
                                </div>
                                <div class="hajj-reg-info-item">
                                    <div class="hajj-info-box">
                                        <h2><span id="RegDataPrivate"><i class="fa fa-spinner fa-spin" style="font-size:26px !important;"></i></span> জন</h2>
                                        <p>বেসরকারি মাধ্যম</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hajj-reg-info-content">
                            <span class="reg-info-cornar"></span>
                            <div class="hajj-reg-title">
                                <h3>প্রাক-নিবন্ধিত হজে গমনেচ্ছু</h3>
                            </div>
                            <div class="hajj-info-block">
                                <div class="hajj-reg-info-item">
                                    <div class="hajj-info-box">
                                        <h2><span id="preRegDataGov"><i class="fa fa-spinner fa-spin" style="font-size:26px !important;"></i></span> জন</h2>
                                        <p>সরকারি মাধ্যম</p>
                                    </div>
                                </div>
                                <div class="hajj-reg-info-item">
                                    <div class="hajj-info-box">
                                        <h2><span id="preRegDataPrivate"><i class="fa fa-spinner fa-spin" style="font-size:26px !important;"></i></span> জন</h2>
                                        <p>বেসরকারি মাধ্যম</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="hajj-notice-sec">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @if($is_schedule_flight_show)
                    <div class="home-notice-table" style="margin-bottom: {{$is_schedule_flight_show && $is_notice_show ? "40px":"0px" }}" >
                        <div class="sec-title mb-2">
                            <h2 id="flight_list_title" style="text-align: center"></h2>
                            <p style="text-align: center">হজ পোর্টালে সংশ্লিষ্ট এয়ারলাইন্স কর্তৃক হতে সরাসরি এন্ট্রি / হালনাগাদকৃত ফ্লাইট সিডিউল</p>
                        </div>
                        <div class="dash-list-table mb-5" id="schedule_flight_list">

                        </div>
                    </div>
                    @endif
                    <br>
                    @if($is_notice_show)
                    <div class="hajj-recent-notice">
                        <div class="sec-title">
                            <h2>সাম্প্রতিক নোটিশ ও বিজ্ঞপ্তি</h2>
                        </div>

                        <div class="notice-container" id="dynamic_news_list">

                        </div>
                    </div>
                    @endif
                </div>
{{--                <div class="col-xl-4 col-lg-5">--}}
{{--                    <div class="hajj-others-notice">--}}

{{--                        <div class="hajj-package-list">--}}

{{--                        </div>--}}

{{--                        <div class="hajj-important-links">--}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
    </section>
@endsection
@section('footer-script')
{{--    <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>--}}
    <script>
        // $('#haji_search').click(function (e) {
        //     e.preventDefault();
        //     var searchval= $('#haji_search_val').val();
        //     if(!searchval){
        //         alert('Please provide valid tracking no, passport no or hajj agency license number.');
        //         return false;
        //     }
        //     if(searchval.length==4 && ( $.isNumeric( searchval )==true ) ){
        //         // window.location.href = 'https://prp.pilgrimdb.org/agencies/'+ searchval ;
        //         window.open('https://prp.pilgrimdb.org/agencies/'+ searchval, '_blank')
        //     }else{
        //         // window.location.href = 'https://prp.pilgrimdb.org/web/pilgrim-search?q='+ searchval ;
        //         window.open('https://prp.pilgrimdb.org/web/pilgrim-search?q='+ searchval, '_blank')
        //     }
        // });

        $('#haji_search').click(function (e) {
            e.preventDefault();
            performSearch();
        });

        $('#haji_search_val').keypress(function (e) {
            if (e.which === 13) { // Check if Enter key is pressed
                e.preventDefault();
                performSearch();
            }
        });

        function performSearch() {
            var searchval = $('#haji_search_val').val();
            const hostname = `{{ $_SERVER['HTTP_HOST'] }}`;

            if (!searchval) {
                alert('Please provide valid tracking no, passport no, or hajj agency license number.');
                return false;
            }
            if (searchval.length == 4 && ($.isNumeric(searchval) == true)) {
                if(hostname === 'uat-ehaj.oss.net.bd' || hostname === 'uat-ehaj.oss.net.bd/' || hostname === 'localhost:8000' || hostname === 'localhost:8000/') {
                    window.open('https://prpuat.oss.net.bd/agencies/' + searchval, '_blank');
                }
                window.open('https://prp.pilgrimdb.org/agencies/' + searchval, '_blank');
            } else {
                if(hostname === 'uat-ehaj.oss.net.bd' || hostname === 'uat-ehaj.oss.net.bd/' || hostname === 'localhost:8000' || hostname === 'localhost:8000/') {
                    window.open('https://prpuat.oss.net.bd/web/pilgrim-search?q=' + searchval, '_blank');
                }
                //window.open('https://prp.pilgrimdb.org/web/pilgrim-search?q=' + searchval, '_blank');
                window.open('https://pilgrim.hajj.gov.bd/web/pilgrim-search?q=' + searchval, '_blank');
            }
        }

        $(document).ready(function () {
            loadResourcesLinks();

            function loadResourcesLinks (){
                $.ajax({
                    url: 'getResourcesLinksData',
                    type: 'ajax',
                    method: 'get',
                    success: function (response) {
                        if(response.responseCode == 0){
                            $('.hajj-package-list').html(response.resources_link)
                            // $('.hajj-important-links').html(response.important_link)
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    },
                });
            }

            function addCommas(nStr){
                nStr= convert2English(nStr);
                nStr += '';
                x = nStr.split('.');
                x1 = x[0];
                x2 = x.length > 1 ? '.' + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
                }
                return x1 + x2;
            }

            $.ajax({
                url: 'getCountData',
                type: 'ajax',
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    if(response.data.data != undefined){
                        let preRegDataGov = ( typeof response.data.data.preragdata.Government !== 'undefined' ) ? convert2Bangla(response.data.data.preragdata.Government) : 0;
                        let preRegDataPrivate = ( typeof response.data.data.preragdata.Private !== 'undefined' ) ? convert2Bangla(response.data.data.preragdata.Private) : 0;
                        let RegDataGov = ( typeof response.data.data.regData.Government !== 'undefined') ? convert2Bangla(response.data.data.regData.Government) : 0;
                        let RegDataPrivate = ( typeof response.data.data.regData.Private !== 'undefined' ) ? convert2Bangla(response.data.data.regData.Private) : 0;
                        let hajj_possible_date = ( typeof response.data.data.hajj_possible_date !== 'undefined' ) ? response.data.data.hajj_possible_date : '';
                        let hajj_days_remain_bangla = ( typeof response.data.data.days_remains !== 'undefined' ) ? response.data.data.days_remains : '';

                        $('#preRegDataGov').html(convert2Bangla(addCommas(preRegDataGov)));
                        $('#preRegDataPrivate').html(convert2Bangla(addCommas(preRegDataPrivate)));
                        $('#RegDataGov').html(convert2Bangla(addCommas(RegDataGov)));
                        $('#RegDataPrivate').html(convert2Bangla(addCommas(RegDataPrivate)));
                        // $('#hajj_possible_date').html(hajj_possible_date);
                        // $('#hajj_possible_date_s1').html(hajj_possible_date);
                        // $('#hajj_possible_date_s2').html(hajj_possible_date);
                        // $('#hajj_possible_date_s3').html(hajj_possible_date);
                        $('.hajj_possible_date').html(hajj_possible_date);
                        $('#reg-subTitle').html(response.data.data.hajj_session.caption);

                        var hajj_day_count_down = response.data.data.hajj_performing_date;
                        $('.days').html(hajj_days_remain_bangla)
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
            });
        });

        function convert2Bangla(engVal) {
            engVal = engVal.toString();
            engVal = engVal.replaceAll("0", "০");
            engVal = engVal.replaceAll("1", "১");
            engVal = engVal.replaceAll("2", "২");
            engVal = engVal.replaceAll("3", "৩");
            engVal = engVal.replaceAll("4", "৪");
            engVal = engVal.replaceAll("5", "৫");
            engVal = engVal.replaceAll("6", "৬");
            engVal = engVal.replaceAll("7", "৭");
            engVal = engVal.replaceAll("8", "৮");
            engVal = engVal.replaceAll("9", "৯");
            return engVal;
        }

        function convert2English(banVal) {
            banVal = banVal.toString();
            banVal = banVal.replaceAll("০", "0");
            banVal = banVal.replaceAll("১", "1");
            banVal = banVal.replaceAll("২", "2");
            banVal = banVal.replaceAll("৩", "3");
            banVal = banVal.replaceAll("৪", "4");
            banVal = banVal.replaceAll("৫", "5");
            banVal = banVal.replaceAll("৬", "6");
            banVal = banVal.replaceAll("৭", "7");
            banVal = banVal.replaceAll("৮", "8");
            banVal = banVal.replaceAll("৯", "9");
            banVal = banVal.replaceAll("|", ".");
            return banVal;
        }
    </script>

    <script>
        @if($is_schedule_flight_show)
            getScheduleFlightList("/get-schedule-flight")
        @endif
        @if($is_notice_show)
            getPublicPageCount();
        @endif

        $(document).on('click','#showMoreBtn',function(){
            if($('.notice-item').hasClass('d-none')){
                $('.notice-item').removeClass('d-none');
            }
            $(this).hide();
            $('#allNews').removeClass('d-none');
        });

        function getScheduleFlightList(site_url){
            $.ajax({
                url: site_url,
                success: function(data) {
                    $("#schedule_flight_list").html(data.html);
                    $("#flight_list_title").text(data.title);
                    if(parseInt(data.flight_list_count) > 10){
                        $("#schedule_flight_list").css({
                            'height': '635px',
                            'overflow-y': 'scroll'
                        });
                    }
                }
            });
        }

        $(document).on('click', '#prev_news', function () {
            $('#dynamic_news_list').html('{{ \App\Libraries\CommonFunction::convert2Bangla(2023) }} সালের নিউজ লোড করা হচ্ছে। অপেক্ষা করুন ...');
            $.ajax({
                url: 'all-news',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.responseCode == 1) {
                        $('#dynamic_news_list').html(response.html);
                        $(document).find('#newsTable').DataTable({
                            "ordering": false,
                            "dom": 'frtip',
                            "language": {
                                "info": "{{ \App\Libraries\CommonFunction::convert2Bangla(2023) }} সালের _TOTAL_টি বিজ্ঞপ্তির মধ্যে _START_ থেকে _END_টি দেখানো হচ্ছে",
                                "paginate": {
                                    "previous": "পূর্ববর্তী",
                                    "next": "পরবর্তী",
                                }
                            }
                        });
                    }
                }
            });
        });

        $(document).on('click','.yearBtn',function(){
            let selectedYear = $(this).attr('year');
            if(selectedYear === 'recent') {
                getPublicPageCount();
                return;
            }
            if(selectedYear){
                $('#dynamic_news_list').html(convert2Bangla(selectedYear)+' সালের নিউজ লোড করা হচ্ছে। অপেক্ষা করুন ...');
                $.ajax({
                    url: '/get-selected-news/'+selectedYear,
                    type: 'GET',
                    success: function(response) {
                        if(response.responseCode == 1){
                            $('#dynamic_news_list').html(response.html);
                            $(document).find('#newsTable').DataTable({
                                "ordering": false,
                                "dom": 'frtip',
                                "language": {
                                    "info": convert2Bangla(selectedYear) +" সালের _TOTAL_টি বিজ্ঞপ্তির মধ্যে _START_ থেকে _END_টি দেখানো হচ্ছে",
                                    "paginate": {
                                        "previous": "পূর্ববর্তী",
                                        "next": "পরবর্তী",
                                    }
                                }
                            });
                        }
                        else{
                            // $('#dynamic_news_list').html(response.msg);
                            $('#dynamic_news_list').html(response.html)
                        }
                    }
                });
            }
        });


        function getPublicPageCount() {
            $('#dynamic_news_list').html('নিউজ লোড করা হচ্ছে। অপেক্ষা করুন ...');
            $.ajax({
                url: 'get-public-page-api',
                type: 'GET',
                success: function (response) {
                    if (response.responseCode == 1) {
                        let countData = response.data.countData;

                        $('#dynamic_news_list').html(response.data.newsList);
                        $(document).find('#newsTable').DataTable({
                            "ordering": false,
                            "dom": 'frtip',
                            "language": {
                                "info": "সাম্প্রতিক _TOTAL_টি বিজ্ঞপ্তির মধ্যে _START_ থেকে _END_টি দেখানো হচ্ছে",
                                "paginate": {
                                    "previous": "পূর্ববর্তী",
                                    "next": "পরবর্তী",
                                }
                            }
                        });

                    }
                }
            });
        }
    </script>
@endsection
