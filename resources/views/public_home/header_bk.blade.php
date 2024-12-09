<header class="site-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            
            <div class="site-nav-box">
                <a class="navbar-brand" href="#"><img src="{{ asset('assets/custom/images/logo-hajj.webp') }}" alt="Logo"></a>
                <button class="navbar-toggler resView collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav btb-navbar mb-2 mb-md-0">
                    <li class="nav-item" style="width: 130px;">
                        <a class="nav-link" target="_blank" href="https://hajj.gov.bd">Hajj Portal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="https://prp.pilgrimdb.org">PRP</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" target="_blank" href="https://prps.pilgrimdb.org">PRPS</a></li>
                    <li class="nav-item"><a class="nav-link" href="#ehajjSearchModal" data-toggle="modal">অনুসন্ধান</a></li>
                </ul>
            </div>


        </div>
    </nav>
</header>

<div class="modal ba-modal fade" id="ehajjSearchModal" tabindex="-1" aria-labelledby="ehajjSearchModal" aria-hidden="true">
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
</div>