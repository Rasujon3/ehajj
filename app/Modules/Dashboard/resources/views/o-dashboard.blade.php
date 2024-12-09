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
    .radio_label {
        cursor: pointer;
    }

    .small-box {
        margin-bottom: 0;
        cursor: pointer;
    }

    @media (min-width: 481px) {
        .g_name {
            font-size: 32px;
        }
    }

    @media (max-width: 480px) {
        .g_name {
            font-size: 18px;
        }

        span {
            font-size: 14px;
        }

        label {
            font-size: 14px;
        }
    }

    @media (min-width: 767px) {
        .has_border {
            border-left: 1px solid lightgrey;
        }

        .has_border_right {
            border-right: 1px solid lightgrey;
        }
    }
</style>

@if (isset($services) && in_array($type[0], [1, 4]))

{{--    <div class="dash-content-main">--}}

        <div class="dash-sec-head mt-3" style="display: none;">
            <div class="form-group mb-4">
                <div class="radio-tab-group nav">
                    <div class="radio-tab nav-link active" data-toggle="tab" data-target="#hajjFeatureTabOne">সরকারি ব্যবস্থাপনা</div>
                    <div class="radio-tab nav-link" data-toggle="tab" data-target="#hajjFeatureTabTwo">বেসরকারি ব্যবস্থাপনা</div>
                </div>
            </div>
        </div>
        <div class="dash-feature-section" style="display: none;">
            <div class="tab-content">
                <div class="tab-pane show active fade" id="hajjFeatureTabOne">
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                            <div class="dash-feature-item">
                                <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                <div class="dash-feature-desc">
                                    <p>পিআইডি</p>
                                    <h3>17</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                            <div class="dash-feature-item feature-style-2">
                                <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                <div class="dash-feature-desc">
                                    <p>টিকেট ইস্যু</p>
                                    <h3>500</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                            <div class="dash-feature-item feature-style-3">
                                <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                <div class="dash-feature-desc">
                                    <p>এজেন্সি সংখ্যা</p>
                                    <h3>2,000</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                            <div class="dash-feature-item feature-style-4">
                                <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                <div class="dash-feature-desc">
                                    <p>ভিসা সংক্রান্ত তথ্য</p>
                                    <h3>2,000</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="hajjFeatureTabTwo">
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                            <div class="dash-feature-item">
                                <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                <div class="dash-feature-desc">
                                    <p>পিআইডি</p>
                                    <h3>28</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                            <div class="dash-feature-item feature-style-2">
                                <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                <div class="dash-feature-desc">
                                    <p>টিকেট ইস্যু</p>
                                    <h3>850</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                            <div class="dash-feature-item feature-style-3">
                                <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                <div class="dash-feature-desc">
                                    <p>এজেন্সি সংখ্যা</p>
                                    <h3>1,400</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                            <div class="dash-feature-item feature-style-4">
                                <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                <div class="dash-feature-desc">
                                    <p>ভিসা সংক্রান্ত তথ্য</p>
                                    <h3>3,200</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dash-hajj-info-section py-4" style="display: none;">
            <div class="section-title">
                <h2>হজযাত্রীদের নিবন্ধন তথ্য বিন্যাস</h2>
            </div>
            <div class="tab-content">
                <div class="tab-pane show active fade" id="hajjInfoTabOne">
                    <div class="dash-tab-content">
                        <h3>বছর অনুযায়ী প্রাকনিবন্ধিত হজযাত্রী</h3>
                        <div class="hajj-info-graph">
                            <img src="./assets/custom/images/graph-img-01.webp" alt="Graph">
                        </div>
                        <div class="flex-center mt-4">
                            <div class="hajj-info-type"><span class="clr-box" style="background-color: #0D46C1;"></span> সরকারি ব্যবস্থাপনা</div>
                            <div class="hajj-info-type"><span class="clr-box" style="background-color: #6FD1F6;"></span> বেসরকারি ব্যবস্থাপনা</div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="hajjInfoTabTwo">
                    <div class="dash-tab-content">
                        <h3>বছর অনুযায়ী প্রাকনিবন্ধিত হজযাত্রী</h3>
                        <div class="hajj-info-graph">
                            <img src="./assets/custom/images/graph-img-01.webp" alt="Graph">
                        </div>
                        <div class="flex-center mt-4">
                            <div class="hajj-info-type"><span class="clr-box" style="background-color: #0D46C1;"></span> সরকারি ব্যবস্থাপনা</div>
                            <div class="hajj-info-type"><span class="clr-box" style="background-color: #6FD1F6;"></span> বেসরকারি ব্যবস্থাপনা</div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="hajjInfoTabThree">
                    <div class="dash-tab-content">
                        <h3>বিভাগ অনুযায়ী হজযাত্রী</h3>
                        <div class="hajj-info-graph">
                            <img src="./assets/custom/images/graph-img-01.webp" alt="Graph">
                        </div>
                        <div class="flex-center mt-4">
                            <div class="hajj-info-type"><span class="clr-box" style="background-color: #0D46C1;"></span> সরকারি ব্যবস্থাপনা</div>
                            <div class="hajj-info-type"><span class="clr-box" style="background-color: #6FD1F6;"></span> বেসরকারি ব্যবস্থাপনা</div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="hajjInfoTabFour">
                    <div class="dash-tab-content">
                        <h3>বয়স অনুযায়ী হজযাত্রী</h3>
                        <div class="hajj-info-graph">
                            <img src="./assets/custom/images/graph-img-01.webp" alt="Graph">
                        </div>
                        <div class="flex-center mt-4">
                            <div class="hajj-info-type"><span class="clr-box" style="background-color: #0D46C1;"></span> সরকারি ব্যবস্থাপনা</div>
                            <div class="hajj-info-type"><span class="clr-box" style="background-color: #6FD1F6;"></span> বেসরকারি ব্যবস্থাপনা</div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="hajjInfoTabFive">
                    <div class="dash-tab-content">
                        <h3>পেশা অনুযায়ী হজযাত্রী</h3>
                        <div class="hajj-info-graph">
                            <img src="./assets/custom/images/graph-img-01.webp" alt="Graph">
                        </div>
                        <div class="flex-center mt-4">
                            <div class="hajj-info-type"><span class="clr-box" style="background-color: #0D46C1;"></span> সরকারি ব্যবস্থাপনা</div>
                            <div class="hajj-info-type"><span class="clr-box" style="background-color: #6FD1F6;"></span> বেসরকারি ব্যবস্থাপনা</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dash-tab-menu" role="tablist">
                <div class="dash-tab-menu-group nav nav-tabs" role="presentation">
                    <button class="tab-btn nav-link active" data-toggle="tab" data-target="#hajjInfoTabOne" type="button" role="tab">বছর অনুযায়ী প্রাকনিবন্ধিত হজযাত্রী</button>
                    <button class="tab-btn nav-link" data-toggle="tab" data-target="#hajjInfoTabTwo" type="button" role="tab">হজ সিজন অনুযায়ী হজযাত্রী</button>
                    <button class="tab-btn nav-link" data-toggle="tab" data-target="#hajjInfoTabThree" type="button" role="tab">বিভাগ অনুযায়ী হজযাত্রী </button>
                    <button class="tab-btn nav-link" data-toggle="tab" data-target="#hajjInfoTabFour" type="button" role="tab">বয়স অনুযায়ী হজযাত্রী</button>
                    <button class="tab-btn nav-link" data-toggle="tab" data-target="#hajjInfoTabFive" type="button" role="tab">পেশা অনুযায়ী হজযাত্রী</button>
                </div>
            </div>
        </div>
{{--    </div>--}}

@endif


@if (Auth::user()->user_type == '5x505')
    @if ( (Auth::user()->working_company_id == 0 && count($check_association_from) == 0) || $is_eligibility == 0 )

{{--        @include('CompanyAssociation::company-association-form')--}}

    @else
        <div class="dash-content-main" style="display: none;">
            <div class="dash-sec-head mt-3">
                <div class="form-group mb-4">
                    <div class="radio-tab-group nav">
                        <div class="radio-tab nav-link active" data-toggle="tab" data-target="#hajjFeatureTabOne">সরকারি ব্যবস্থাপনা</div>
                        <div class="radio-tab nav-link" data-toggle="tab" data-target="#hajjFeatureTabTwo">বেসরকারি ব্যবস্থাপনা</div>
                    </div>
                </div>
            </div>
            <div class="dash-feature-section" style="display: none;">
                <div class="tab-content">
                    <div class="tab-pane show active fade" id="hajjFeatureTabOne">
                        <div class="row">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                                <div class="dash-feature-item">
                                    <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                    <div class="dash-feature-desc">
                                        <p>পিআইডি</p>
                                        <h3>17</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                                <div class="dash-feature-item feature-style-2">
                                    <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                    <div class="dash-feature-desc">
                                        <p>টিকেট ইস্যু</p>
                                        <h3>500</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                                <div class="dash-feature-item feature-style-3">
                                    <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                    <div class="dash-feature-desc">
                                        <p>এজেন্সি সংখ্যা</p>
                                        <h3>2,000</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                                <div class="dash-feature-item feature-style-4">
                                    <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                    <div class="dash-feature-desc">
                                        <p>ভিসা সংক্রান্ত তথ্য</p>
                                        <h3>2,000</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="hajjFeatureTabTwo">
                        <div class="row">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                                <div class="dash-feature-item">
                                    <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                    <div class="dash-feature-desc">
                                        <p>পিআইডি</p>
                                        <h3>28</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                                <div class="dash-feature-item feature-style-2">
                                    <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                    <div class="dash-feature-desc">
                                        <p>টিকেট ইস্যু</p>
                                        <h3>850</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                                <div class="dash-feature-item feature-style-3">
                                    <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                    <div class="dash-feature-desc">
                                        <p>এজেন্সি সংখ্যা</p>
                                        <h3>1,400</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                                <div class="dash-feature-item feature-style-4">
                                    <div class="dash-feature-icon"><img src="./assets/custom/images/dash-feature-icon-white.svg" alt="Iocn"></div>
                                    <div class="dash-feature-desc">
                                        <p>ভিসা সংক্রান্ত তথ্য</p>
                                        <h3>3,200</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dash-hajj-info-section py-4" style="display: none;">
                <div class="section-title">
                    <h2>হজযাত্রীদের নিবন্ধন তথ্য বিন্যাস</h2>
                </div>
                <div class="tab-content">
                    <div class="tab-pane show active fade" id="hajjInfoTabOne">
                        <div class="dash-tab-content">
                            <h3>বছর অনুযায়ী প্রাকনিবন্ধিত হজযাত্রী</h3>
                            <div class="hajj-info-graph">
                                <img src="./assets/custom/images/graph-img-01.webp" alt="Graph">
                            </div>
                            <div class="flex-center mt-4">
                                <div class="hajj-info-type"><span class="clr-box" style="background-color: #0D46C1;"></span> সরকারি ব্যবস্থাপনা</div>
                                <div class="hajj-info-type"><span class="clr-box" style="background-color: #6FD1F6;"></span> বেসরকারি ব্যবস্থাপনা</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="hajjInfoTabTwo">
                        <div class="dash-tab-content">
                            <h3>বছর অনুযায়ী প্রাকনিবন্ধিত হজযাত্রী</h3>
                            <div class="hajj-info-graph">
                                <img src="./assets/custom/images/graph-img-01.webp" alt="Graph">
                            </div>
                            <div class="flex-center mt-4">
                                <div class="hajj-info-type"><span class="clr-box" style="background-color: #0D46C1;"></span> সরকারি ব্যবস্থাপনা</div>
                                <div class="hajj-info-type"><span class="clr-box" style="background-color: #6FD1F6;"></span> বেসরকারি ব্যবস্থাপনা</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="hajjInfoTabThree">
                        <div class="dash-tab-content">
                            <h3>বিভাগ অনুযায়ী হজযাত্রী</h3>
                            <div class="hajj-info-graph">
                                <img src="./assets/custom/images/graph-img-01.webp" alt="Graph">
                            </div>
                            <div class="flex-center mt-4">
                                <div class="hajj-info-type"><span class="clr-box" style="background-color: #0D46C1;"></span> সরকারি ব্যবস্থাপনা</div>
                                <div class="hajj-info-type"><span class="clr-box" style="background-color: #6FD1F6;"></span> বেসরকারি ব্যবস্থাপনা</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="hajjInfoTabFour">
                        <div class="dash-tab-content">
                            <h3>বয়স অনুযায়ী হজযাত্রী</h3>
                            <div class="hajj-info-graph">
                                <img src="./assets/custom/images/graph-img-01.webp" alt="Graph">
                            </div>
                            <div class="flex-center mt-4">
                                <div class="hajj-info-type"><span class="clr-box" style="background-color: #0D46C1;"></span> সরকারি ব্যবস্থাপনা</div>
                                <div class="hajj-info-type"><span class="clr-box" style="background-color: #6FD1F6;"></span> বেসরকারি ব্যবস্থাপনা</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="hajjInfoTabFive">
                        <div class="dash-tab-content">
                            <h3>পেশা অনুযায়ী হজযাত্রী</h3>
                            <div class="hajj-info-graph">
                                <img src="./assets/custom/images/graph-img-01.webp" alt="Graph">
                            </div>
                            <div class="flex-center mt-4">
                                <div class="hajj-info-type"><span class="clr-box" style="background-color: #0D46C1;"></span> সরকারি ব্যবস্থাপনা</div>
                                <div class="hajj-info-type"><span class="clr-box" style="background-color: #6FD1F6;"></span> বেসরকারি ব্যবস্থাপনা</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dash-tab-menu" role="tablist">
                    <div class="dash-tab-menu-group nav nav-tabs" role="presentation">
                        <button class="tab-btn nav-link active" data-toggle="tab" data-target="#hajjInfoTabOne" type="button" role="tab">বছর অনুযায়ী প্রাকনিবন্ধিত হজযাত্রী</button>
                        <button class="tab-btn nav-link" data-toggle="tab" data-target="#hajjInfoTabTwo" type="button" role="tab">হজ সিজন অনুযায়ী হজযাত্রী</button>
                        <button class="tab-btn nav-link" data-toggle="tab" data-target="#hajjInfoTabThree" type="button" role="tab">বিভাগ অনুযায়ী হজযাত্রী </button>
                        <button class="tab-btn nav-link" data-toggle="tab" data-target="#hajjInfoTabFour" type="button" role="tab">বয়স অনুযায়ী হজযাত্রী</button>
                        <button class="tab-btn nav-link" data-toggle="tab" data-target="#hajjInfoTabFive" type="button" role="tab">পেশা অনুযায়ী হজযাত্রী</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif


@if($type[0] == 21 )
    <div class="card">

        <div class="card-body">
            <h4><i class="fa fa-list"></i> হজযাত্রী {{\Illuminate\Support\Facades\Auth::user()->user_first_name}} - প্রোফাইল </h4>
            <div class="container">
{{--                <form>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-md-6">--}}
{{--                            <label for="name">Name:</label>--}}
{{--                            <input type="text" class="form-control" value="{{\Illuminate\Support\Facades\Auth::user()->user_first_name}}" readonly id="name">--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6">--}}
{{--                            <label for="email">Mobile:</label>--}}
{{--                            <input type="text" class="form-control" readonly id="mobile" value="">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    @php--}}
{{--                    $tracking_no_array = explode("@",\Illuminate\Support\Facades\Auth::user()->user_email);--}}
{{--                    @endphp--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-md-6">--}}
{{--                            <label for="tracking">Tracking No:</label>--}}
{{--                            <input type="text" class="form-control" readonly id="tracking" value="{{$tracking_no_array[0]}}">--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6">--}}
{{--                            <label for="passport">Passport No:</label>--}}
{{--                            <input type="text" class="form-control" readonly id="passport">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-3 col-sm-offset-1">
                    {{--                    <div class="card card-default" id="browseimagepp">--}}
                    <div class="row">
                        <label class="float-right" for="user_pic">
                            <figure>
                                <img
                                    src="{{ CommonComponent()->dynamicImageUrl(\Illuminate\Support\Facades\Auth::user()->user_pic, 'userProfile', 'users/profile-pic/') }}"
                                    class="img-responsive img-thumbnail"
                                    id="user_pic_preview"/>
                            </figure>
                        </label>

                        {{--                            <div class="col-sm-6 col-md-8">--}}
                        {{--                                <h4 id="profile_image">--}}
                        {{--                                    {!! Form::label('user_pic',trans('Users::messages.profile_image'), ['class'=>'text-left required-star']) !!}--}}
                        {{--                                </h4>--}}
                        {{--                                <span class="text-success col-lg-8 text-left"--}}
                        {{--                                      style="font-size: 9px; font-weight: bold; display: block;">[File Format: *.jpg/ .jpeg/ .png | Width 300PX, Height 300PX]</span>--}}

                        {{--                                <span id="user_err" class="text-danger col-lg-8 text-left"--}}
                        {{--                                      style="font-size: 10px;"> {!! $errors->first('applicant_photo','<span class="help-block">:message</span>') !!}</span>--}}
                        {{--                                <div class="clearfix"><br/></div>--}}

                        {{--                            </div>--}}


                    </div>
                    {{--                    </div>--}}
                </div>
{{--                <div class="col-md-1 col-sm-1"></div>--}}

                    <div class="col-md-4">
                        <fieldset>
                            <div
                                class="form-group row {{ $errors->has('user_first_name') ? 'has-error' : ''}}">
                                <label
                                    class="col-lg-4 text-left ">Name</label>
                                <div class="col-lg-8">
                                    {{ \Illuminate\Support\Facades\Auth::user()->user_first_name }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 text-left">Email</label>
                                <div class="col-lg-8">
                                    {{ \Illuminate\Support\Facades\Auth::user()->user_email }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 text-left">Mobile</label>
                                <div class="col-lg-8">
                                    {{ \Illuminate\Support\Facades\Auth::user()->user_mobile }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label
                                    class="col-lg-4 text-left">Guide Mobile</label>
                                <div class="col-lg-8" id="guide_mobile">

                                </div>
                            </div>

                        </fieldset>
                    </div>

                <div class="col-md-4">
                    <fieldset>


                        <div class="form-group row">
                            <label
                                class="col-lg-4 text-left">Tracking No</label>
                            <div class="col-lg-8" >
                                @php
                                    $tracking_no_array = explode("@",\Illuminate\Support\Facades\Auth::user()->user_email);
                                @endphp
                                <input type="hidden" id="tracking" value="{{$tracking_no_array[0]}}"/>

                                {{ $tracking_no_array[0] }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label
                                class="col-lg-4 text-left">Passport No</label>
                            <div class="col-lg-8" id="passport">

                            </div>
                        </div>
                        <div class="form-group row">
                            <label
                                class="col-lg-4 text-left">Guide Name</label>
                            <div class="col-lg-8" id="guide_name">

                            </div>
                        </div>

                    </fieldset>
                </div>






            </div>
        </div>
    </div>
    <br>
    <div class="card-header">
        <div class="row">
            <div class="col-lg-10">
                <h5><i class="fa fa-list"></i> <b> দলভুক্ত সদস্যদের তালিকা :
                        <span class="list_name"></span>
                    </b></h5><b>

                </b></div><b>

        </b></div>
        </div>
    <div class="card card-magenta border border-magenta">

        <div class="table-responsive">
            <table class="table table-bordered" id="myTable">
                <thead class="table-info">
                <tr>
                    <th scope="col">পিআইডি</th>
                    <th scope="col">নাম</th>
                    <th scope="col">ট্র্যাকিং নম্বর</th>
                    {{-- <th scope="col">পিআইডি</th> --}}
                    <th scope="col">মোবাইল</th>
                    <th scope="col">গাইড নাম</th>
                    <th scope="col">গাইড মোবাইল</th>
                </tr>
                </thead>
                <tbody class="load_pilgrim">

                </tbody>
            </table>
        </div>
    </div>
@endif

@section('chart_script')
    @if (!empty($map_script_array))
        {{-- @include('partials.amchart-js') --}}
        <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>

        @foreach ($map_script_array as $script)
            <script type="text/javascript">
                $(function() {
                    <?php echo $script; ?>
                });
            </script>
        @endforeach
    @endif
@endsection

<script>
    function approveAndRejectCompanyAssoc(e, key) {
        var r = confirm("Are you sure?");
        if (r !== true) {
            return false;
        }
        e.disabled = true;
        const button_text = e.innerText;
        const loading_sign = '...<i class="fa fa-spinner fa-spin"></i>';

        var companyAssocId = e.value;
        var type = $("input:radio[name='userType']:checked").val()

        $.ajax({
            url: "{{ url('client/company-association/approve-reject') }}",
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                companyAssocId: companyAssocId,
                type: type,
                key: key,
            },
            beforeSend: function() {
                e.innerHTML = button_text + loading_sign;
            },
            success: function(response) {
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.warning('Yor approval was not successful!');
                console.log(errorThrown);
            },
            complete: function() {
                toastr.success('Yor approval was successful!');
            }
        });
    }
</script>

<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>


<script type="text/javascript">
    $('#draftButton').click(function() {
        $('.draftButtonForm').submit();
    });

    $('#progressButton').click(function() {
        $('.progressButtonForm').submit();
    });

    $('#approvedButton').click(function() {
        $('.approvedButtonForm').submit();
    });
    $('#othersButton').click(function() {
        $('.othersButtonForm').submit();
    });

    $('#shortfallButton').click(function() {
        $('.shortfallButtonForm').submit();
    });

    $('#rejectedButton').click(function() {
        $('.rejectedButtonForm').submit();
    });



</script>
@if($type[0]==21)
<script>
    // let base_url = window.location.origin + '/process/action/getPilgrimDataByUser/';

    $.ajax({
        cache:false,
        {{--url: "<?php echo '/process/action/getPilgrimDataByUser'?>",--}}
        url: "<?php echo env('APP_URL').'/process/action/getPilgrimDataByUser'?>",
        type: "POST",
        dataType: 'json',
        data: { "_token": "{{ csrf_token() }}"},
        success: function(response) {
            console.log(response);
            // var selected_pilgrim_array = response.data.data.available_pid;
            // console.log(response.data.data);
            if (response.data.data == undefined || response.data.data.length == 0 ) {
                Swal.fire({
                    // text: 'These applications are already listed:' + listed_arr.toString(),
                    text: 'Data Not Found',
                    icon: 'warning',
                    // showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                })
                $('.load_pilgrim').html("<tr class='mt-3'><td class='pt-4 text-red' colspan='6'>Data not found!!</td></tr>");
            }
            else {

                let dataLen = response.data.data.length;
                $('#total_pilgrim').html(dataLen);

                $('.load_pilgrim').html('');

                for(var i =0 ; i<dataLen; i++){
                    console.log(response.data.data[i]['flight_code']);
                    if(response.data.data[i]['pilgrim_listing_id']!=0){

                        $("#draft").css("display", "none");
                        $("#save").css("display", "none");

                    }


                        if($('#tracking').val() == response.data.data[i]['tracking_no']){
                            $('#passport').html(response.data.data[i]['passport_no'])
                            $('#guide_name').html(response.data.data[i]['guide_name'])
                            $('#guide_mobile').html(response.data.data[i]['guide_mobile'])
                            $('#user_pic_preview').attr("src", response.data.data[i]['p_image']);

                            // $('#mobile').ht(response.data.data[i]['mobile'])
                        }
                        if($('#tracking').val() != response.data.data[i]['tracking_no']){
                            $('.load_pilgrim').append(

                                '<tr id="row' + i + '">'+
                                '<td>'+ '<input type="hidden" class="form-control" id="pid'+i+'" name="pid[]" value="'+response.data.data[i]['pid']+'" readonly/>'+response.data.data[i]['pid']+'</td>'+

                                '<td>'+
                                '<input type="hidden" name="pilgrim_id[]"  value="'+response.data.data[i]['id']+'" />'+
                                '<input type="hidden" class="form-control" id="full_name_english'+i+'" name="full_name_english[]" value="'+response.data.data[i]['full_name_english']+'" readonly/>'+response.data.data[i]['full_name_english']+'</td>'+
                                '<td>'+
                                '<input type="hidden" class="form-control" id="tracking_no'+i+'" name="tracking_no[]" value="'+response.data.data[i]
                                    ['tracking_no']+'" readonly/>'+response.data.data[i]
                                    ['tracking_no']+'</td>'+
                                '<td>'+
                                '<input type="hidden" class="form-control" id="mobile'+i+'" name="mobile[]" value="'+response.data.data[i]['mobile']+'" readonly/> '+response.data.data[i]['mobile']+' </td>'+
                                '<td>'+
                                '<input type="hidden" class="form-control" id="mobile'+i+'" name="mobile[]" value="'+response.data.data[i]['mobile']+'" readonly/> '+(response.data.data[i]['guide_name']!=null ? response.data.data[i]['guide_name'] : '' )+'</td>'+
                                '<td>'+
                                '<input type="hidden" class="form-control" id="mobile'+i+'" name="mobile[]" value="'+response.data.data[i]['mobile']+'" readonly/> '+(response.data.data[i]['guide_mobile'] != null ? response.data.data[i]['guide_mobile'] : '')+'</td>'+
                                '</tr>'
                            );
                        }


                }

            }
        },
        error: function(data) {
            // console.log((data.responseJSON.errors));
        }
    })
</script>
@endif
