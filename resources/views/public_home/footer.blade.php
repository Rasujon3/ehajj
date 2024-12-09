<footer class="ehajj-portal-footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-top-sec">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <div style="margin-right: 30%">
                                <p style="margin-bottom: 10px">সার্বিক তত্বাবধানে</p>
                                <a href="#" aria-label="" style="margin-top: 10px"><img class="img-block" src="{{ asset('assets/public_page/custom/images/ministry-logo.png') }}" width="" height="" alt="ehaj-portal"></a>
                                <br>
                                <br>
{{--                                <p style="margin-bottom: 10px">কারিগরি সহযোগিতায়</p>--}}
{{--                                <a  href="#" aria-label=""><img class="img-block" src="{{ asset('assets/public_page/custom/images/Business Automation.png') }}" width="" height="" alt=""></a>--}}
{{--                            --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h3 class="footer-title">সেবা</h3>
                            <ul class="footer-menu">
                                <li><a href="{{url('https://prp.pilgrimdb.org')}}" target="_blank">প্রাক-নিবন্ধন ও নিবন্ধন</a></li>
                                <li><a href="{{url('/complain')}}">অভিযোগ</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h3 class="footer-title">অন্যান্য</h3>
                            <ul class="footer-menu">
                                <li><a href="{{url('/contact')}}">যোগাযোগ</a></li>
                                <li><a href="{{url('/privacy-policy')}}" class="clr-navyBlue" target="_blank"> <span style="color: white">প্রাইভেসি পলিসি</span> </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h3 class="footer-title">গুরুত্বপুর্ণ লিংকসমূহ</h3>
                            <ul class="footer-menu">
                                <span id="footer-important-link">অনুসন্ধান হচ্ছে <i class="fa fa-spinner fa-spin" style="font-size:15px !important;"></i></span>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom-sec">
                @if(request()->path() != 'ehaj-apps')
                    <div class="footer-app-logo">
                        <a class="app-icon" href="{{url('/ehaj-apps')}}">
                            <img src="{{ asset('assets/public_page/custom/images/footer-searchBox-logo1.png') }}" height="" alt="">
                        </a>
                    </div>
                    <p>ধর্ম বিষয়ক মন্ত্রণালয়ের অধীনে হজ বিষয়ে হালনাগাদ সকল তথ্য পেতে ই-হজ অ্যাপস ও হজ মোবাইল অ্যাপস দিয়ে হজ বিষয়ে সকল প্রকার তথ্য-উপাত্ত দেখা যাবে।</p>
                @endif
                <p>
                    Call Center : <a href="tel:16136" class="clr-navyBlue" ><span style="color: white">16136</span></a>, <a href="tel:+8809602666707" class="clr-navyBlue" ><span style="color: white">+880 9602666707</span> </a> |
                    Email : <a href="mailto:prp@hajj.gov.bd" class="clr-navyBlue"  target="_blank" ><span style="color: white">prp@hajj.gov.bd</span> </a> |
                    Website : <a href="https://hajj.gov.bd/" class="clr-navyBlue"  target="_blank" ><span style="color: white">hajj.gov.bd</span> </a>
                </p>
            </div>
        </div>
    </div>
</footer>
@if(env('SHOW_WIDGET'))
    <div id="batworld"></div>
    <script>
        window.ba_sw_id = 'd1fa1ef9-204b-4d05-974a-e72974eb7827';
        var s1 = document.createElement('script');
        s1.setAttribute('src', 'https://feedback.oss.net.bd/src/0.1.3/social_widget_link.js');
        document.body.appendChild(s1);
    </script>
@endif

