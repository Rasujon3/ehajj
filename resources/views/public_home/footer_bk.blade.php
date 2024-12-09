<footer class="site-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-content">
                <div class="footer-appstore">
                    <a target="_blank" href="https://apps.apple.com/sa/app/haj-guide/id1387764857"><img class="img-block" src="{{ asset('assets/custom/images/img-logo-appstore.webp') }}" alt="applestore"></a>
                    <a target="_blank" href="https://play.google.com/store/apps/details?id=com.bat.pilgrimguide&pli=1"><img class="img-block" src="{{ asset('assets/custom/images/img-logo-googleplay.webp') }}" alt="gooleplay"></a>
                </div>
                <div >
                    ধর্ম বিষয়ক মন্ত্রানালয়ের অধীনে হজ ব্যবস্থাপনা বিষয়ে হালনাগাদ সকল তথ্য পেতে ই-হজ অ্যাপস। হজ মোবাইল অ্যাপস দিয়ে হজ বিষয়ে সকল প্রকার তথ্য-উপাত্ত দেখা যাবে। 
                </div>
                <div class="footer-menu">
                    <ul class="footer-menu-list">
                        {{-- <li><a href="#">নিউজলেটার</a></li> --}}
                        {{-- <li><a href="#">শর্তাবলী</a></li> --}}
                        <li><a target="_blank" href="https://forms.gle/Z44qEynZoHYNApam8">মানোন্নয়নের পরামর্শ</a></li>
                        {{-- <li><a href="#">গুরুত্বপূর্ণ লিংক</a></li> --}}
                    </ul>
                    <ul class="footer-social-menu">
                        <li><a target="_blank" href="https://www.facebook.com/hajguide"><i class="fab fa-facebook-f"></i></a></li>
                        {{-- <li><a href="#"><i class="fab fa-youtube"></i></a></li> --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-copyright">
        <div class="container">


            <div class="copyright-content">
                <p>Managed by Business Automation Ltd on behalf of Ministry Of Religious Affairs <br>Call Center : <a href="tel:+8809602666707">+880 9602666707,16136</a>,  Email <a href="mailto:info@hajj.gov.bd">info@hajj.gov.bd</a></p>

                <div class="developed-by">
                    <span>কারিগরি সহযোগিতায়</span>
                    <a target="_blank" href="https://ba-systems.com"><img src="{{ asset('assets/custom/images/ba-main-logo.webp') }}" alt="Logo"></a>
                </div>

                <div class="dby-others-logo">
                    {{-- <img src="{{ asset('assets/custom/images/ehaj--app-logo.svg') }}" alt="Logo"> --}}
                </div>

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
