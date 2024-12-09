<html>

<body>
<?php
$completed_date = strtotime(date($appInfo->completed_date));
?>
<div class="row">
    <div class="col-md-4" style="float: left;width: 60%;">
        <span style="font-size: 18px">নিবন্ধন নং : {{$appInfo->regist_no}}<br>
        ট্রাকিং আইডি : {{$appInfo->tracking_no}}
</span>
    </div>
    <div class="col-md-8 " style="text-align: right">
        <span style="font-size: 16px;"
              class="input_ban">তারিখ: {{\App\Libraries\CommonFunction::convert2Bangla(date('d-m-Y', strtotime($appInfo->completed_date))) }}</span><br>
    </div>
    <br>
    <br>
    <br>
    <div style=" width: 210px;border: 1px solid black; text-align: center; margin: 0px auto">
        <h3> শিল্প নিবন্ধন সনদপত্র</h3>
    </div>
    <br><br>
    <div class="col-md-12 fontSize" style="text-align: justify">

        এই মর্মে প্রত্যয়ন করা যাচ্ছে যে,
        <b>“{{$appInfo->org_nm_bn}}”</b> নামক <b>{{$appInfo->regist_name_bn}}</b> শিল্প প্রতিষ্ঠানটি পরবর্তী পৃষ্ঠায়
        বর্ণিত শর্ত সাপেক্ষে
        <b>“{{$appInfo->ind_category_bn}}”</b> হিসেবে অত্র কার্যালয় কর্তৃক নিবন্ধিত হলো। <br> <br>

        <div style="text-align: center; margin: 0px auto">
            নিবন্ধনের মেয়াদোত্তীর্ণের তারিখঃ <span class="input_ban">{{\App\Libraries\CommonFunction::convert2Bangla(date('d-m-Y', strtotime('+5 years', $completed_date))) }}। </span>
        </div>

    </div>
    <br>
    <br>
    <br>
    <br>

    <div class="col-md-4 fontSize" style="width: 90%; float: left;line-height: 1.2em">
        {{$appInfo->ceo_name}}<br>
        {{$appInfo->designation}}<br>
        {{@$appInfo->org_nm_bn}}<br>
        {{@$appInfo->office_location}}
        {{-- , {{@$appInfo->thana_nm_ban}}, {{@$appInfo->dis_nm_ban}}, {{@$appInfo->div_nm_ban}} --}}
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
    </div>


    <div class="col-md-4 fontSize" style="width: 45%;  float: left;"><br><br><br>
        <img src="data:image/png;base64,{{$qrCode}}" alt="barcode"/>
    </div>
{{--    {{dd($signatory->signature)}}--}}
    <div class="col-md-5 fontSize" style="float:right; text-align: center;">
        <img src="{{ (!empty($signatory->signature) ? url('users/signature/' . $signatory->signature) : url('assets/images/paper-business-contract-pen-signature-icon-vector-15749289.jpg')) }}"/>
        <br>
        <span class="fontSize">{{$signatory->user_first_name}}</span><br>
        <span style="font-size: 14px"> {{$signatory->designation}}<br>
 {{$appInfo->registration_office}}<br>
        ফোনঃ {{@\App\Libraries\CommonFunction::convert2Bangla(@$signatory->user_phone) }}<br>
            ইমেইলঃ {{@$signatory->user_email}}<br></span>
    </div>


    <pagebreak></pagebreak>
    <div class="col-md-12 fontSize">
        উদ্যোক্তা কর্তৃক নিম্মোক্ত তথ্যের ভিত্তিতে প্রতিষ্ঠানটির নিবন্ধন প্রদান করা হলো।
        <br>
        <br>
        প্রতিষ্ঠানের নামঃ {{@$appInfo->org_nm_bn}}
        <br>
        প্রকল্পের নামঃ {{@$appInfo->project_nm}}
        <br>
        কার্যালয়ের ঠিকানাঃ {{@$appInfo->office_location}},
        {{-- {{@$appInfo->thana_nm_ban}} ,{{@$appInfo->dis_nm_ban}},{{@$appInfo->div_nm_ban}} --}}

        মোবাইল নং-{{ \App\Libraries\CommonFunction::convert2Bangla(@$appInfo->office_mobile) }}.
        <br>
        থানাঃ {{$appInfo->thana_nm_ban}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; জেলাঃ {{$appInfo->dis_nm_ban}}
        <br>
        কারখানার ঠিকানাঃ {{$appInfo->factory_location}},
        {{-- {{@$appInfo->f_thana_nm_ban}} ,{{@$appInfo->f_dis_nm_ban}},{{@$appInfo->f_div_nm_ban}}  --}}
        মোবাইল নং-{{ \App\Libraries\CommonFunction::convert2Bangla(@$appInfo->factory_mobile) }}.
        <br>
        থানাঃ {{$appInfo->f_thana_nm_ban}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; জেলাঃ {{$appInfo->f_dis_nm_ban}}
        <br>
        নিবন্ধনের প্রকৃতিঃ {{@$appInfo->regist_name_bn}}
        <br>
        প্রতিষ্ঠানের ধরনঃ {{@$appInfo->company_type_bn}}
        <br>
        শিল্পের খাতঃ {{@$appInfo->ind_sector_bn}}
        <br>
    </div>
    <br>
    <div class="col-md-12 fontSize">
    <table class="table table-bordered">
        <tbody>
        <tr>
            <td></td>
            <td><h4>স্থানীয়</h4></td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->sales_local.'%') }}</td>
            <td><h4>বিদেশী</h4></td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->sales_foreign.'%') }}</td>
        </tr>
        </tbody>
    </table>

    <table class="table table-bordered">
        <thead>
        <tr>
            <td colspan="3"><h4>বিনিয়োগ</h4></td>
        </tr>
        <tr>
            <td colspan="2"><h4>স্থায়ী বিনিয়োগ</h4></td>
            <td></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>১</td>
            <td><h4>ভূমি</h4></td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->local_land_ivst) }}</td>
        </tr>
        <tr>
            <td>২</td>
            <td><h4>ভবন/শেড</h4></td>
            <td>{{\App\Libraries\CommonFunction::convert2Bangla($appInfo->local_building_ivst)}}</td>
        </tr>
        <tr>
            <td>৩</td>
            <td><h4>যন্ত্রপাতি ও সরঞ্জামাদি</h4></td>
            <td>{{\App\Libraries\CommonFunction::convert2Bangla($appInfo->local_machinery_ivst)}}</td>
        </tr>
        <tr>
            <td>৪</td>
            <td><h4>অন্যান্য</h4></td>
            <td>{{\App\Libraries\CommonFunction::convert2Bangla($appInfo->local_others_ivst)}}</td>
        </tr>

        <tr>
            <td colspan="2"><h4>চলতি মূলধন (৩ মাসের)</h4></td>
            <td>{{\App\Libraries\CommonFunction::convert2Bangla($appInfo->local_wc_ivst)}}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-right"><h4>সর্বমোট বিনিয়োগ</h4></td>
            <td>{{\App\Libraries\CommonFunction::convert2Bangla($appInfo->total_fixed_ivst_million)}}</td>
        </tr>
        </tbody>
    </table>

    <table class="table table-bordered">
        <thead>
        <tr>
            <td colspan="3"><h4> বিনিয়োগের উৎস</h4></td>
        </tr>
        <tr>
            <td></td>
            <td><h4>টাকা</h4></td>
            <td><h4>ডলার</h4></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><h4>উদ্যোক্তার সম-মূলধন</h4></td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->ceo_taka_invest) }}</td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->ceo_dollar_invest) }}</td>
        </tr>
        <tr>
            <td><h4>স্থানীয় ঋণ</h4></td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->local_loan_taka) }}</td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->local_loan_dollar) }}</td>
        </tr>
        <tr>
            <td><h4>বিদেশী ঋণ</h4></td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->foreign_loan_taka) }}</td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->foreign_loan_dollar) }}</td>
        </tr>
        <tr>
            <td><h4>সর্বমোট</h4></td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->total_inv_taka) }}</td>
            <td>{{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->total_inv_dollar) }}</td>
        </tr>
        </tbody>
    </table>

    <table class="table table-bordered">
        <tbody>
        <tr>
            <td colspan="3"><h4>দেশভিত্তিক ঋণ</h4></td>
        </tr>
        <tr>
            <td><h4>দেশের নাম</h4></td>
            <td><h4>সংস্থার নাম</h4></td>
            <td><h4>ঋণের পরিমাণ(টাকায়)</h4></td>
        </tr>
        @foreach($loanSrcCountry as $item)
            <tr>
                <td>
                    {{ $item->country_name }}
                </td>
                <td>
                    {{ $item->loan_org_nm }}
                </td>
                <td class="input_ban">
                    {{  \App\Libraries\CommonFunction::convert2Bangla($item->loan_amount) }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>



    <br>
    কর্মসংস্থানঃ স্থানীয় পুরুষ- {{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->local_male) }}, স্থানীয়
    মহিলা- {{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->local_female) }}, বিদেশী
    পুরুষ- {{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->foreign_male) }}, বিদেশী
    মহিলা- {{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->foreign_female) }},
    মোট {{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->manpower_total) }} জন
    <br>
    উদ্যোক্তাঃ {{ $appInfo->ceo_name }}, জাতীয়তা-{{ $appInfo->ceo_nationality }},
    ফোন- {{ \App\Libraries\CommonFunction::convert2Bangla($appInfo->ceo_mobile) }}
    <br>

    <div class="row">
        <div class="col-md-4" style="float: left;width: 45%;"><br><br><br>
            <img src="data:image/png;base64,{{$qrCode}}" alt="Qrcode"/>
        </div>
        <div class="col-md-5 fontSize" style="float:right; text-align: center;">
            <img src="{{ (!empty($signatory->signature) ? url('users/signature/' . $signatory->signature) : url('assets/images/paper-business-contract-pen-signature-icon-vector-15749289.jpg')) }}"/>
            <br>
            <span class="fontSize">{{$signatory->user_first_name}}</span><br>
            <span style="font-size: 14px"> {{$signatory->designation}}<br>
 {{$appInfo->registration_office}}<br>
                ফোনঃ {{@\App\Libraries\CommonFunction::convert2Bangla($signatory->user_phone) }}<br>
                ইমেইলঃ {{$signatory->user_email}}<br>
                </span>
        </div>
    </div>

    </div>


<pagebreak></pagebreak>

<div class="text-center">
    <h4>শিল্প উদ্যোক্তা/উদ্যোক্তাদের জন্য কতিপয় জ্ঞাতব্য বিষয়</h4>
</div>
    <div style="font-size:13.70px; text-align: justify">
        ১. শিল্প উদ্যোক্তা/উদ্যোক্তাগণ তাঁদের প্রকল্প বাস্তবায়ন ও চালু করার জন্য অবকাঠামোগত ও অন্যান্য সুবিধাদি
        স্বীয় উদ্যোগে সংগ্রহ করিবেন। প্রয়োজনে সংশ্লিষ্ট এসব সেবা/সুবিধাদি পাওয়ার জন্য উদ্যোক্তার অনুরোধে বিসিক
        সহায়তা প্রদান করবে। এক্ষেত্রে উদ্যোক্তা/ উদ্যোক্তাগণকে বিসিকের জেলা কার্যালয়/আঞ্চলিক কার্যালয়/প্রধান
        কার্যালয়ে যোগাযোগ করতে হবে।
        <br>
        ২. বেসরকারী খাতে বিনিয়োগ উৎসাহিত করার লক্ষে বিসিকের পারমর্শদাতা কর্মকর্তাগণ বিনিয়োগ সংক্রান্ত পরামর্শ, তথ্য,
        সরকারী নীতিমালা, পদ্ধতি ও নিয়ন্ত্রণ বিষয়ক ব্যাখ্যাদানসহ সর্বপ্রকার সেবা প্রদান করিবে।
        <br>
        ৩. শিল্প প্রতিষ্ঠানের মালিকানা অথবা কারখানার অবস্থান সম্পর্কিত যে কোন পরিবর্তন/ সংশোধন আবশ্যিকভাবে বিসিককে
        অবহিত করতে হবে।

        <br>
        ৪. সরকার ঘোষিত শিল্প নীতিমালা, জাতীয় রাজস্ব বোর্ড এবং অন্যান্য সংশ্লিষ্ট সরকারি সংস্থাসমূহ কর্তৃক সময়ে সময়ে
        জারীকৃত প্রজ্ঞাপন অনুযায়ী শিল্প প্রতিষ্ঠান/ প্রকল্পটি বিভিন্ন প্রকার কর রেয়াত এবং আর্থিক সুবিধাদি পাওয়ার
        যোগ্য হতে পারে। সরকার ঘোষিত এসব নীতিমালা/ প্রজ্ঞাপন/পরিপত্র সমূহের অনুলিপি বিসিক এর জেলা কার্যালয়/ আঞ্চলিক
        কার্যালয়/ প্রধান কার্যালয় হতে সংগ্রহ করা যাবে।
        <br>
        ৫. প্রস্তাবিত শিল্প প্রকল্পের বাস্তবায়ন অগ্রগতি যথাসময়ে বিসিককে অবহিত করতে হবে। যেসকল প্রস্তাবিত প্রকল্পের
        বাস্তবায়ন নিবন্ধন সনদপত্রে প্রদত্ত সময়সীমার মধ্যে সম্পূর্ণ না হবে, সে সব প্রকল্পের নিবন্ধন সময়সীমা অতিক্রমের
        ০৩ (তিন) মাসের মধ্যে নবায়ন করে নিতে হবে; অন্যথায় প্রদত্ত নিবন্ধন বাতিল বলে গণ্য করা হবে।
        <br>
        ৬. যেসকল শিল্প-পণ্য বাজারজাত করার পূর্বে বাংলাদেশ স্ট্যান্ডার্ডস এন্ড টেস্টিং ইনস্টিটিউশন (বি.এস.টি.আই)
        কর্তৃক নির্ধারিত মানসম্পন্ন হওয়া বাধ্যতামূলক, সেসকল পণ্যের বি.এস.টি.আই এর সনদ নিতে হবে। এতদবিষয়ে সকল
        প্রজ্ঞাপনের অনুলিপি বিসিক এর জেলা কার্যালয়/ আঞ্চলিক কার্যালয়/ প্রধান কার্যালয় হতে পাওয়া যাবে।
        <br>
        ৭. উৎপাদন প্রক্রিয়া ও কর্মকান্ডের উপর ভিত্তি করে যেসকল শিল্প প্রতিষ্ঠান স্থাপনের পূর্বে পরিবেশ অধিদপ্তরের
        ছাড়পত্রসহ অন্যান্য সনদের প্রয়োজন, সেসকল শিল্প প্রতিষ্ঠানকে সংশ্লিষ্ট সেবার সনদ নিতে হবে।
        <br>
        ৮. এছাড়াও, শিল্প প্রতিষ্ঠান/ প্রকল্প স্থাপনে নিম্নলিখিত শর্তসমূহ অবশ্যই পালনীয়:
        <br>

        <div style="padding-left: 45px; ">

            ৮.১ কারখানার সম্মুখে কমপক্ষে ১.৪x০.৮ মিটার সাইজের সাইনবোর্ড “বিসিক কর্তৃক কুটির/ মাইক্রো/ ক্ষুদ্র/
            মাঝারি/ বৃহৎ শিল্প হিসেবে নিবন্ধনকৃত” লিখে লাগাতে হবে।
            <br>
            ৮.২ এ শিল্প নিবন্ধন সনদপত্র কোনোভাবেই হস্তান্তরযোগ্য নয় এবং ইহা ০৫ (পাঁচ) বছর পর পর নবায়নযোগ্য। শিল্প
            নিবন্ধনের মেয়াদ অতিক্রান্ত হওয়ার ০৩ (তিন) মাসের মধ্যে নবায়ন করে নিতে হবে।
            <br>
            ৮.৩ দাখিলকৃত কাগজপত্রের ভিত্তিতে এ নিবন্ধনপত্র ইস্যু করা হলো;
            <br>
            ৮.৪ কারখানায় কোন প্রকার অবৈধ/নিষিদ্ধ পণ্য উৎপাদন করা যাবেনা। অবৈধ/নিষিদ্ধ পণ্য উৎপাদন করা হলে তার
            দায়-দায়িত্ব উদ্যোক্তাকেই বহন করতে হবে।
            <br>
            ৮.৫ এই রেজিস্ট্রেশন উৎপাদিত পণ্যের মান নিয়ন্ত্রণে নিশ্চয়তা বহন করে না;
            <br>
            ৮.৬ উৎপাদিত পণ্যের মান নিয়ন্ত্রণে সংশ্লিষ্ট কর্তৃপক্ষের অনুমোদন গ্রহণ করতে হবে;
            <br>
            ৮.৭ পণ্য উৎপাদনে আইনি জটিলতায় বিসিক কর্তৃপক্ষ দায়ী থাকবে না;
            <br>
            ৮.৮ পরিবেশ ছাড়পত্র গ্রহণ সাপেক্ষে পরিবেশ দূষণমুক্ত রাখতে হবে;
            <br>
            ৮.৯ দুর্ঘটনা এড়ানোর লক্ষে ফায়ার সার্ভিস বিভাগের অনুমতি নিতে হবে এবং পর্যাপ্ত সংখ্যক অগ্নি নির্বাপক
            যন্ত্র কারখানায় স্থাপন করতে হবে।
            <br>


        </div>
        ৯. উপরে বর্ণিত কোন শর্ত ভঙ্গ করলে বিসিক কর্তৃপক্ষ এই শিল্প নিবন্ধন বাতিল করার ক্ষমতা সংরক্ষণ করে।
    </div>


    <div class="row">
    <div class="col-md-4" style="float: left;width: 40%;">
        <br><br><br>
        <img src="data:image/png;base64,{{$qrCode}}" alt="Qrcode"/>
    </div>
    <div class="col-md-6 " style="float:right; text-align: center; ">
                    <img src="{{ (!empty($signatory->signature) ? url('users/signature/' . $signatory->signature) : url('assets/images/paper-business-contract-pen-signature-icon-vector-15749289.jpg')) }}"/>
        <br>
        <span class="fontSize">{{$signatory->user_first_name}}</span><br>
        <span style="font-size: 14px"> {{$signatory->designation}}<br>
 {{$appInfo->registration_office}}<br>
                ফোনঃ {{@\App\Libraries\CommonFunction::convert2Bangla($signatory->user_phone) }}<br>
                ইমেইলঃ {{$signatory->user_email}}<br>
                </span>
    </div>

</div>

</div>
</body>
</html>
