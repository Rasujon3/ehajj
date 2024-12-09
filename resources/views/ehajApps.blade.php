@extends('public_home.front')
@section('header-resources')

@endsection
<style>
    .footer-appstore {
        width: 100% !important;
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: center;
    }
</style>
@section('body')
    <section class="prp-main-sec">
        <div class="container">
            <div class="white-box p-5 mb-3" style="padding-bottom: 10px !important;">
                <div class="row hjp-row-gap">
                    <div class="col-lg-12">
                        <div class="mb-4">
                            <h3>হজ ব্যবস্থাপনায় মোবাইল অ্যাপ,</h3>
                            <p>বিজনেস অটোমেশন ২০২২ সনে হজ ব্যবস্থাপনার সঙ্গে সংশ্লিষ্টদের জন্য মোবাইল অ্যাপ “ই-হজ বিডি (e-Hajj BD)” পরীক্ষামূলকভাবে চালু করেছে। বর্তমানে হজযাত্রীদের জন্য মোবাইল অ্যাপ “হজ গাইড” থাকলেও হজ ব্যবস্থাপনার সঙ্গে সংশ্লিষ্টদের কাজের জন্য কোনো মোবাইল এপ্লিকেশন ছিল না। পরীক্ষামূলক এই মোবাইল অ্যাপের মাধ্যমে সৌদি আরব পর্বে বেসরকারি এজেন্সির মোনাজ্জেম/ ইউজার, সরকারি গাইড অথবা হেল্পডেস্ক ইউজারগন হারানো হজযাত্রী ও লাগেজ ব্যবস্থাপনা, ট্রাভেল প্যাকেজ ব্যবস্থাপনার তথ্য আদান প্রদান করতে পারবেন। এতে, হজ কার্যক্রমে গতিশীলতা বৃদ্ধি পাবে এবং হজযাত্রীদের সেবা উন্নত হবে বলে আশা করা হচ্ছে।</p>
                            <p>বর্তমানে এই মোবাইল অ্যাপে বাংলাদেশ পর্বে হজযাত্রীদের প্রাক-নিবন্ধন, নিবন্ধন ও রিফান্ড সহ বিভিন্ন কার্যক্রম সংযোজন করা হয়েছে</p>

                        </div>
                    </div>
                    <div class="footer-appstore">
                        <a href="https://apps.apple.com/pk/app/ehaj/id1625546283" target="_blank"><img class="img-block" src="{{ asset('assets/public_page/custom/images/logo-appstore.svg') }}" alt="applestore"></a>
                        <a href="https://play.google.com/store/apps/details?id=com.bat.hmis" target="_blank"><img class="img-block" src="{{ asset('assets/public_page/custom/images/logo-googleplay.svg') }}" alt="gooleplay"></a>
                    </div>
                </div>
            </div>

        </div>
    </section>

@endsection
