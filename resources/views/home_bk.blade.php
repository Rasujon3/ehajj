@extends('public_home.front')
@section('header-resources')

@endsection
@section('body')

    <section class="home-intro-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="home-slider">
                        <div id="hajjBD_homeSlider" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#hajjBD_homeSlider" data-slide-to="0" class="active"></li>
                                <li data-target="#hajjBD_homeSlider" data-slide-to="1"></li>
                                <li data-target="#hajjBD_homeSlider" data-slide-to="2"></li>
                            </ol>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="{{ asset('assets/custom/images/slider-img-01.webp') }}" class="d-block w-100" alt="Slider Images">
                                </div>
                                <div class="carousel-item">
                                    <img src="{{ asset('assets/custom/images/slider-img-01.webp') }}" class="d-block w-100" alt="Slider Images">
                                </div>
                                <div class="carousel-item">
                                    <img src="{{ asset('assets/custom/images/slider-img-01.webp') }}" class="d-block w-100" alt="Slider Images">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-target="#hajjBD_homeSlider" data-slide="prev"></button>
                            <button class="carousel-control-next" type="button" data-target="#hajjBD_homeSlider" data-slide="next"></button>
                        </div>
                    </div>
                </div>
                @include('public_home.login_panel')
            </div>
        </div>
    </section>


    <section class="hajj-registration-info">
        <div class="container">
            <div class="hajj-reg-info-container py-3">
            <div class="row">
                <div class="col-lg-6">
                    <div class="hajj-reg-info-content">
                        <h3>অপেক্ষমান প্রাক-নিবন্ধিত</h3>
                        <div class="hajj-info-block">
                            <div class="hajj-reg-info-item">
                                <div class="hajj-info-box bg-clr-1">
                                    <p>সরকারি ব্যবস্থাপনা</p>
                                    <h2 id="preRegDataGov"></h2>
                                    <span class="info-person">জন</span>
                                </div>
                            </div>
                            <div class="hajj-reg-info-item">
                                <div class="hajj-info-box bg-clr-2">
                                    <p>বেসরকারি ব্যবস্থাপনা</p>
                                    <h2 id="preRegDataPrivate"></h2>
                                    <span class="info-person">জন</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hajj-reg-info-content">
                        <h3>নিবন্ধিত</h3>
                        <div class="hajj-info-block">
                            <div class="hajj-reg-info-item">
                                <div class="hajj-info-box bg-clr-1">
                                    <p>সরকারি ব্যবস্থাপনা</p>
                                    <h2 id="RegDataGov"></h2>
                                    <span class="info-person">জন</span>
                                </div>
                            </div>
                            <div class="hajj-reg-info-item">
                                <div class="hajj-info-box bg-clr-2">
                                    <p>বেসরকারি ব্যবস্থাপনা</p>
                                    <h2 id="RegDataPrivate"></h2>
                                    <span class="info-person">জন</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </section>



    {{-- <section class="hajj-registration-info">
        <div class="container">
            <div class="sec-bg-gray py-3">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hajj-reg-info-content">
                            <h3>অপেক্ষমান প্রাক-নিবন্ধিত</h3>
                            <div class="hajj-info-block">
                                <div class="hajj-reg-info-item">
                                    <p>সরকারি ব্যবস্থাপনা</p>
                                    <h2 id="preRegDataGov"></h2>

                                </div>
                                <div class="hajj-reg-info-item">
                                    <p>বেসরকারি ব্যবস্থাপনা</p>
                                    <h2 id="preRegDataPrivate"></h2>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="hajj-reg-info-content">
                            <h3>নিবন্ধিত</h3>
                            <div class="hajj-info-block">
                                <div class="hajj-reg-info-item">
                                    <p>সরকারি ব্যবস্থাপনা</p>
                                    <h2 id="RegDataGov"></h2>

                                </div>
                                <div class="hajj-reg-info-item">
                                    <p>বেসরকারি ব্যবস্থাপনা</p>
                                    <h2 id="RegDataPrivate"></h2>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <section class="hajj-timer-secton py-5">
        <div class="container">

            <div class="hajj-date-info">
                <h3 style="text-align: center" id="hajj_possible_date"></h3>
            </div>
            <div class="hajj-timer-content">
                <div id="hajjDateCounter" class="hajj-count-timer">
                    <div class="hajj-timer-item">
                        <h2 class="days">0</h2>
                        <span class="days_text">Days</span>
                    </div>
                    <div class="hajj-timer-item">
                        <h2 class="hours">0</h2>
                        <span class="hours_text">Hours</span>
                    </div>
                    <div class="hajj-timer-item">
                        <h2 class="minutes">0</h2>
                        <span class="minutes_text">Munite</span>
                    </div>
                    <div class="hajj-timer-item">
                        <h2 class="seconds">0</h2>
                        <span class="seconds_text">Second</span>
                    </div>
                </div>
            </div>



                <div>&nbsp;</div>
                <div>&nbsp;</div>


            <div class="home-src-lists">


                <div class="row">

                    <div class="col-lg-6">
                        <div class="hajj-date-info" style="text-align: center">
                            <h3>সর্বশেষ নোটিশ এবং বিজ্ঞপ্তি সমূহ</h3>
                        </div>

                        <div class="dash-list-table">
                                <div class="table-responsive">

                                    <div style="width: 96%;margin: 0 auto;">
                                        <table id="dtBasicExample" class="table table-striped table-bordered dataTable" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <th class="th-sm">নোটিশ</th>
                                            </tr>
                                            </thead>
                                            <tbody id="notice_body">
                                            </tbody>
{{--                                            <tfoot>--}}
{{--                                                <tr>--}}
{{--                                                    <th class="th-sm">ক্রমিক নং</th>--}}

{{--                                                </tr>--}}
{{--                                            </tfoot>--}}
                                        </table>
                                    </div>


                                </div>

                        </div>

                    </div>
                    <div class="col-lg-6">

                        <div class="hajj-date-info" style="text-align: center">
                            <h3>রিসোর্স</h3>
                        </div>


                        <div class="dash-list-table">
                            <div class="table-responsive">
                                <table class="table dash-table">
                                    <tbody>
                                        <tr class="table-row-space"><td>&nbsp;</td></tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr class="table-row-space"><td>&nbsp;</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </section>
<style>
    .table-responsive{
        overflow-x: hidden !important;
    }
    .dataTables_info{display: none !important;}
    .dataTables_filter input{padding-left: 0px !important;margin-left: 0px !important;}
    .dataTables_filter label{font-size:12px !important;}
    .bs-select label{font-size:12px !important;}
</style>

@section('footer-script')


    <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>


    <script>
        var hajj_day_count_down = '06/27/2023 00:00:00';

        $('#haji_search').click(function (e) {
            e.preventDefault();
            var searchval= $('#haji_search_val').val();
            if(searchval.length==4 && ( $.isNumeric( searchval )==true ) ){
                // window.location.href = 'https://prp.pilgrimdb.org/agencies/'+ searchval ;
                window.open('https://prp.pilgrimdb.org/agencies/'+ searchval, '_blank')
            }else{
                // window.location.href = 'https://prp.pilgrimdb.org/web/pilgrim-search?q='+ searchval ;
                window.open('https://prp.pilgrimdb.org/web/pilgrim-search?q='+ searchval, '_blank')
            }

        });

        $(document).ready(function () {

            $('#hajjDateCounter').countdown({
                date: hajj_day_count_down,
            });

            $(document).ready(function () {
                $('#dtBasicExample').DataTable({
                    "pagingType": "simple_numbers",

                });
                $('.dataTables_length').addClass('bs-select');
            });

            function addCommas(nStr)
            {
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

                        // let dataLen = (typeof response.data.data.hajj_performing_date.hajj_date !=undefined) ? convert2Bangla(response.data.data.hajj_performing_date.hajj_date) : '';
                        //$('#hajj-date').html(dataLen);
                        //hajj_day_count_down = dataLen;

                        let preRegDataGov = ( typeof response.data.data.preragdata.Government !=undefined ) ? convert2Bangla(response.data.data.preragdata.Government) : 0;
                        let preRegDataPrivate = ( typeof response.data.data.preragdata.Private !=undefined ) ? convert2Bangla(response.data.data.preragdata.Private) : 0;
                        let RegDataGov = ( typeof response.data.data.regData.Government !=undefined ) ? convert2Bangla(response.data.data.regData.Government) : 0;
                        let RegDataPrivate = ( typeof response.data.data.regData.Private !=undefined ) ? convert2Bangla(response.data.data.regData.Private) : 0;
                        let hajj_possible_date = ( typeof response.data.data.hajj_possible_date !=undefined ) ? response.data.data.hajj_possible_date : '';

                        $('#preRegDataGov').html(convert2Bangla(addCommas(preRegDataGov)));
                        $('#preRegDataPrivate').html(convert2Bangla(addCommas(preRegDataPrivate)));
                        $('#RegDataGov').html(convert2Bangla(addCommas(RegDataGov)));
                        $('#RegDataPrivate').html(convert2Bangla(addCommas(RegDataPrivate)));
                        $('#hajj_possible_date').html(hajj_possible_date);
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
        $.ajax({
            url: "/get-notice-list",
            success: function(data) {
                // Iterate over the response data and append rows to the table
                var tbody = $("#notice_body");
                tbody.html(data.html);
            }
        });
    </script>



@endsection
@endsection
