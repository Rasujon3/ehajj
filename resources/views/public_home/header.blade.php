<?php
    $url = env('KEYCLOAK_AUTH_URL');
    $credentials = [
        'client_id' =>  env('KEYCLOAK_CLIENT_ID'),
        'redirect_uri' =>  env('KEYCLOAK_REDIRECT_URI'),
        'response_type' => 'code',
        'scope' => 'openid',
    ];
    $httpQueryString = http_build_query($credentials);
    $authorize_uri = $url.'?'.$httpQueryString;
?>
<header class="site-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <div class="site-nav-box">
                <a class="navbar-brand" href="{{url('/')}}" aria-label="EHajj Site Logo"><img src="{{ asset('assets/custom/images/logo-hajj.webp') }}" width="213" height="60" alt="Logo"></a>

                <div class="menu-res-button resView">
                    {{-- <a class="nav-src-btn" href="#ehajjSearchModal" data-toggle="modal" aria-label="Menu Search">
                        <i class="fa fa-search"></i>
                    </a> --}}
                    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav btb-navbar mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link" href="https://hajj.gov.bd" aria-label="Hajj Portal">হজ পোর্টাল</a>
                    </li>
                    {{-- <li class="nav-item"><a class="nav-link" href="https://prp.pilgrimdb.org" aria-label="PRC">পিআরপি</a></li> --}}
                    <li class="nav-item"><a class="nav-link" style="background: #00684D !important; color: white" href="{{ $authorize_uri }}" aria-label="PRC">লগইন</a></li>
                    <li class="nav-item deskView">
                        {{-- <a class="nav-link nav-src-btn" href="#ehajjSearchModal" data-toggle="modal" aria-label="Menu Search">
                            <i class="fa fa-search"></i>
                        </a> --}}
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>


<!--<div class="modal ba-modal fade" id="ehajjSearchModal" tabindex="-1" aria-labelledby="ehajjSearchModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="icon-close-modal"><img src="{{ asset('assets')}}/custom/images/icon-close.svg" alt="Icon"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-src">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="হজযাত্রীর ট্র্যাকিং নম্বর , পাসপোর্ট নম্বর অথবা হজ এজেন্সি লাইসেন্স নম্বর দিন" id="haji_search_val">
                        <div class="input-group-append">
                            <button class="btn btn-success" type="button" id="haji_search">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="src-btn-group">
                    <a class="btn" href="#" aria-label="হজযাত্রী অনুসন্ধান">হজযাত্রী অনুসন্ধান</a>
                    <a class="btn" href="#" aria-label="এজেন্সি অনুসন্ধান">এজেন্সি অনুসন্ধান</a>
                </div>
            </div>
        </div>
    </div>
</div>-->

{{-- <div class="modal ba-modal fade" id="ehajjSearchModal" tabindex="-1" aria-labelledby="ehajjSearchModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                হজ যাত্রী অথবা হজ এজেন্সি অনুসন্ধান
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="icon-close-modal"><img src="./assets/custom/images/icon-close.svg" alt="Icon"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="">
                    হজযাত্রী অনুসন্ধান করার জন্য ট্র্যাকিং নম্বর অথবা পাসপোর্ট নম্বর দিয়ে অনুসন্ধান করুন। হজ এজেন্সি অনুসন্ধান করতে চার সংখ্যার এজেন্সি লাইসেন্স নম্বর দিন।
                    <div style="height:10px;"></div>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="হজযাত্রীর ট্র্যাকিং নম্বর , পাসপোর্ট নম্বর অথবা হজ এজেন্সি লাইসেন্স নম্বর দিন।" id="haji_search_val">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="button" id="haji_search">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div style="height:10px;"></div>
                    <div style="height:10px;"></div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
