
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="bn" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="bn" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="bn" class="no-js">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="HV4D6RvK2fcKhVShcH8JlT7TZZex1QhSWgVvVKQj">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title>E-HajjBD | New Dashboard</title>

    <!-- Fav icon -->
    <link rel="shortcut icon" type="image/ico" href="favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/custom/css/custom-style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/custom/css/custom-responsive.css') }}">
</head>

<body class="hold-transition hajj-dashboard sidebar-mini layout-fixed">
    <div class="wrapper site-main">
        <input type="hidden" name="_token" value="HV4D6RvK2fcKhVShcH8JlT7TZZex1QhSWgVvVKQj">

        <header class="site-header">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <div class="site-nav-box">
                        <div class="dash-head-left">
                            <button class="navbar-toggler resView collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                                <span class="navbar-toggler-icon"></span>
                            </button>

                            <div class="site-nav-box">
                                <a class="navbar-brand" href="index.html"><img src="./assets/custom/images/logo-hajj.webp" alt="Logo"></a>
                            </div>
                        </div>

                        <div class="deskView header-search">
                            <div class="search-group">
                                <input name="search_anything" class="form-control" type="search" placeholder="Search Anything…." aria-label="Search">
                                <span class="search-icon"><img src="./assets/custom/images/icon-search.svg" alt="Icon"></span>
                            </div>
                        </div>

                        <div class="nav-btn-group nav-user-profile">
                            <a href="#"><span class="icon-notification"></span></a>

                            <div class="dropdown">
                                <div class="dropdown-toggle hajj-top-user" type="button" id="hajjUserInfo" data-toggle="dropdown" aria-expanded="false">
                                    <div class="dash-user-info flex-center">
                                        <div class="dash-user-picture"><img src="./assets/custom/images/icon-user-img.svg" alt="Image"></div>
                                        <span class="dash-user-name deskView">সফিকুর রহমান সরকার</span>
                                    </div>
                                </div>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="hajjUserInfo">
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <img src="./assets/custom/images/icon-user-img.svg" alt="Image">
                                        </div>
                                        <div class="user-info-title mb-4">
                                            <h3>সফিকুর রহমান সরকার</h3>
                                            <span class="login-time">Last Login: 31 Dec 2023 | 02:34:54</span>

                                            <button class="btn btn-secondary mt-3"><i class="fa fa-unlock-alt mr-1" aria-hidden="true"></i> Access Log</button>
                                        </div>
                                        <div class="user-btn-group">
                                            <button type="button" class="btn btn-success">Profile</button>
                                            <button type="button" class="btn btn-danger">Log out</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <div class="hajj-dashboard-content">
            <div class="container-fluid">
                <div class="hajj-dashboard-container">
                    <aside id="navbarCollapse" class="dash-sidebar collapse">
                        <div class="dash-navbar">
                            <div class="dash-menu-item {{ Request::is('flight-dashboard') ? 'active' : '' }}">
                                <a class="dash-menu-text" href="{{ url('flight-dashboard') }}">
                                    <span class="dash-menu-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                            <path d="M5.92 11.9464C7.33 11.9464 8.46 13.0874 8.46 14.5074V17.9164C8.46 19.3264 7.33 20.4764 5.92 20.4764H2.54C1.14 20.4764 0 19.3264 0 17.9164V14.5074C0 13.0874 1.14 11.9464 2.54 11.9464H5.92ZM17.4601 11.9464C18.8601 11.9464 20.0001 13.0874 20.0001 14.5074V17.9164C20.0001 19.3264 18.8601 20.4764 17.4601 20.4764H14.0801C12.6701 20.4764 11.5401 19.3264 11.5401 17.9164V14.5074C11.5401 13.0874 12.6701 11.9464 14.0801 11.9464H17.4601ZM5.92 0.476715C7.33 0.476715 8.46 1.62672 8.46 3.03772V6.44671C8.46 7.86671 7.33 9.00671 5.92 9.00671H2.54C1.14 9.00671 0 7.86671 0 6.44671V3.03772C0 1.62672 1.14 0.476715 2.54 0.476715H5.92ZM17.4601 0.476715C18.8601 0.476715 20.0001 1.62672 20.0001 3.03772V6.44671C20.0001 7.86671 18.8601 9.00671 17.4601 9.00671H14.0801C12.6701 9.00671 11.5401 7.86671 11.5401 6.44671V3.03772C11.5401 1.62672 12.6701 0.476715 14.0801 0.476715H17.4601Z" fill="#474D49"/>
                                        </svg>
                                    </span>
                                    <span class="dm-text">Dashboard</span>
                                </a>
                            </div>
                            <div class="dash-menu-item {{ Request::is('flight') ? 'active' : '' }}">
                                <a class="dash-menu-text" href="{{ url('/flight') }}">
                                    <span class="dash-menu-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 26 27" fill="none">
                                            <path d="M20.9041 15.5232L17.4667 11.777L16.6981 10.9497C16.5849 10.8112 16.5431 10.5623 16.6169 10.3981L17.8699 7.60644C18.2631 6.73063 18.0822 5.39985 17.4778 4.64626C17.2655 4.38656 16.9371 4.23914 16.6019 4.25313C15.6463 4.30647 14.5276 5.06487 14.1345 5.94069L12.8814 8.73235C12.8077 8.89656 12.594 9.03082 12.4153 9.03828L6.21182 8.93926C5.53059 8.91847 4.74181 9.43034 4.46334 10.0507L3.92279 11.255C3.57471 12.0304 3.9866 12.6757 4.84655 12.689L10.3017 12.77C10.7272 12.7746 10.9331 13.0973 10.757 13.4896L10.3025 14.5022L9.56536 16.1444C9.47117 16.3542 9.21743 16.602 9.00594 16.6825L5.93524 17.8581C5.61801 17.9788 5.32519 18.3625 5.28631 18.7178L5.13992 20.1428C5.06253 20.7547 5.55911 21.2846 6.17554 21.2543L9.16585 20.4043C9.62122 20.269 10.1595 20.5106 10.361 20.9408L11.7132 23.74C12.1045 24.2116 12.8263 24.2396 13.2503 23.7833L14.2178 22.7268C14.4533 22.4708 14.5495 21.9878 14.4288 21.6706L13.2664 18.5949C13.1769 18.3793 13.1934 18.025 13.2876 17.8152L14.4792 15.1603C14.6553 14.7681 15.02 14.7125 15.3194 15.0223L19.0056 19.0445C19.5871 19.6782 20.3429 19.5571 20.691 18.7817L21.2316 17.5774C21.51 16.9571 21.3683 16.0275 20.9041 15.5232Z" fill="#474D49"/>
                                        </svg>
                                    </span>
                                    <span class="dm-text">Flight</span>
                                </a>
                            </div>
                            <div class="dash-menu-item">
                                <a class="dash-menu-text" href="#">
                                    <span class="dash-menu-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                            <path d="M15.8002 2.68671C15.3902 2.27671 14.6802 2.55671 14.6802 3.12671V6.61671C14.6802 8.07671 15.9202 9.28671 17.4302 9.28671C18.3802 9.29671 19.7002 9.29671 20.8302 9.29671C21.4002 9.29671 21.7002 8.62671 21.3002 8.22671C19.8602 6.77671 17.2802 4.16671 15.8002 2.68671Z" fill="#474D49"/>
                                            <path d="M20.5 10.6667H17.61C15.24 10.6667 13.31 8.73671 13.31 6.36671V3.47672C13.31 2.92672 12.86 2.47672 12.31 2.47672H8.07C4.99 2.47672 2.5 4.47672 2.5 8.04672V16.9067C2.5 20.4767 4.99 22.4767 8.07 22.4767H15.93C19.01 22.4767 21.5 20.4767 21.5 16.9067V11.6667C21.5 11.1167 21.05 10.6667 20.5 10.6667ZM13 16.2267H8.81L9.53 16.9467C9.82 17.2367 9.82 17.7167 9.53 18.0067C9.38 18.1567 9.19 18.2267 9 18.2267C8.81 18.2267 8.62 18.1567 8.47 18.0067L6.47 16.0067C6.4 15.9367 6.36 15.8667 6.32 15.7867C6.31 15.7667 6.3 15.7367 6.29 15.7167C6.27 15.6567 6.26 15.5967 6.25 15.5367C6.25 15.5067 6.25 15.4867 6.25 15.4567C6.25 15.3767 6.27 15.2967 6.3 15.2167C6.3 15.2067 6.3 15.2067 6.31 15.1967C6.34 15.1167 6.4 15.0367 6.46 14.9767C6.47 14.9667 6.47 14.9567 6.48 14.9567L8.48 12.9567C8.77 12.6667 9.25 12.6667 9.54 12.9567C9.83 13.2467 9.83 13.7267 9.54 14.0167L8.82 14.7367H13C13.41 14.7367 13.75 15.0767 13.75 15.4867C13.75 15.8967 13.41 16.2267 13 16.2267Z" fill="#474D49"/>
                                        </svg>
                                    </span>
                                    <span class="dm-text">Pay Order Received </span>
                                </a>
                            </div>
                            <div class="dash-menu-item">
                                <a class="dash-menu-text" href="#">
                                    <span class="dash-menu-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="19" viewBox="0 0 24 19" fill="none">
                                            <path d="M23.1023 3.80422L22.8662 2.46519C22.6754 1.3863 21.5208 0.687954 20.2864 0.905477L4.22607 3.73758H22.4185C22.6532 3.73758 22.8817 3.76123 23.1023 3.80422Z" fill="#474D49"/>
                                            <path d="M22.4183 5.41022H1.57999C0.70825 5.41022 -0.000976562 5.98311 -0.000976562 6.68703V16.811C-0.000976562 17.5149 0.70825 18.0878 1.57999 18.0878H22.4183C23.2898 18.0878 23.999 17.5149 23.999 16.811V6.68703C23.9989 5.98286 23.2898 5.41022 22.4183 5.41022ZM7.43186 16.7876H5.75921V15.348H7.43186V16.7876ZM7.43186 13.9083H5.75921V12.4687H7.43186V13.9083ZM7.43186 11.0291H5.75921V9.58944H7.43186V11.0291ZM7.43186 8.14987H5.75921V6.71017H7.43186V8.14987ZM19.7217 9.88297L18.4345 11.0871L19.5942 14.9607L18.9446 15.6103C18.8733 15.6814 18.7579 15.6814 18.6865 15.6103L16.9579 12.4685L15.3679 14.0366L15.6211 15.2725L15.2165 15.6773C15.1628 15.7311 15.0755 15.7311 15.0223 15.6776L12.7321 13.3874C12.6783 13.3336 12.6783 13.2466 12.7321 13.1931L13.1367 12.788L14.3499 13.0355L15.9191 11.4395L12.7992 9.72223C12.7279 9.65141 12.7279 9.53595 12.7987 9.46462L13.4489 8.81509L17.2969 9.96695L18.5096 8.67066C18.864 8.3163 19.4226 8.30074 19.7572 8.6355C20.0913 8.97001 20.0754 9.52874 19.7217 9.88297Z" fill="#474D49"/>
                                        </svg>
                                    </span>
                                    <span class="dm-text">Reservation</span>
                                </a>
                            </div>
                            <div class="dash-menu-item">
                                <a class="dash-menu-text" href="#">
                                    <span class="dash-menu-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                            <path d="M16.19 2.47672H7.81C4.17 2.47672 2 4.64671 2 8.28672V16.6567C2 20.3067 4.17 22.4767 7.81 22.4767H16.18C19.82 22.4767 21.99 20.3067 21.99 16.6667V8.28672C22 4.64671 19.83 2.47672 16.19 2.47672ZM7.63 18.6267C7.63 19.0367 7.29 19.3767 6.88 19.3767C6.47 19.3767 6.13 19.0367 6.13 18.6267V16.5567C6.13 16.1467 6.47 15.8067 6.88 15.8067C7.29 15.8067 7.63 16.1467 7.63 16.5567V18.6267ZM12.75 18.6267C12.75 19.0367 12.41 19.3767 12 19.3767C11.59 19.3767 11.25 19.0367 11.25 18.6267V14.4767C11.25 14.0667 11.59 13.7267 12 13.7267C12.41 13.7267 12.75 14.0667 12.75 14.4767V18.6267ZM17.87 18.6267C17.87 19.0367 17.53 19.3767 17.12 19.3767C16.71 19.3767 16.37 19.0367 16.37 18.6267V12.4067C16.37 11.9967 16.71 11.6567 17.12 11.6567C17.53 11.6567 17.87 11.9967 17.87 12.4067V18.6267ZM17.87 9.24672C17.87 9.65672 17.53 9.99672 17.12 9.99672C16.71 9.99672 16.37 9.65672 16.37 9.24672V8.27672C13.82 10.8967 10.63 12.7467 7.06 13.6367C7 13.6567 6.94 13.6567 6.88 13.6567C6.54 13.6567 6.24 13.4267 6.15 13.0867C6.05 12.6867 6.29 12.2767 6.7 12.1767C10.07 11.3367 13.07 9.56672 15.45 7.06672H14.2C13.79 7.06672 13.45 6.72672 13.45 6.31672C13.45 5.90672 13.79 5.56672 14.2 5.56672H17.13C17.17 5.56672 17.2 5.58672 17.24 5.58672C17.29 5.59672 17.34 5.59671 17.39 5.61671C17.44 5.63671 17.48 5.66671 17.53 5.69671C17.56 5.71671 17.59 5.72672 17.62 5.74672C17.63 5.75672 17.63 5.76672 17.64 5.76672C17.68 5.80672 17.71 5.84671 17.74 5.88671C17.77 5.92671 17.8 5.95672 17.81 5.99672C17.83 6.03672 17.83 6.07672 17.84 6.12672C17.85 6.17672 17.87 6.22672 17.87 6.28672C17.87 6.29672 17.88 6.30672 17.88 6.31672V9.24672H17.87Z" fill="#474D49"/>
                                        </svg>
                                    </span>
                                    <span class="dm-text">Report</span>
                                </a>
                            </div>

                            <div class="dash-menu-item dd-submenu-item">
                                <a class="dash-menu-text collapsed" href="#" data-toggle="collapse" data-target="#dashToggleMenuNewApp">
                                    <i class="fab fa-dashcube dash-menu-icon"></i> Dropdown Menu
                                    <span class="dash-arrow-icon"><i class="fas fa-chevron-down"></i></span>
                                </a>

                                <div id="dashToggleMenuNewApp" class="collapse navbar-collapse">
                                    <ul class="dash-submenu">
                                        <li><a href="#">- Submen Menu 1</a></li>
                                        <li><a href="#">- Submen Menu 2</a></li>
                                        <li><a href="#">- Submen Menu 3</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="dash-sidebar-logout">
                            <button class="btn btn-normal"><span class="btn-icon icon-logout"></span> Log out</button>
                        </div>
                    </aside>
                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="site-footer">
            <div class="footer-copyright">
                <div class="container">
                    <div class="copyright-content">
                        <p>Managed by <a class="clr-navyBlue" href="#">Business Automation Ltd</a> on behalf of Ministry Of Religious Affairs <br>Help Desk : <a href="tel:+8809602666707">+880 9602666707</a>,  Email <a href="mailto:info@hajj.gov.bd">info@hajj.gov.bd</a></p>

                        <div class="developed-by">
                            <span>কারিগরি সহযোগিতায়</span>
                            <a href="index.html"><img src="{{ asset('assets/custom/images/ba-main-logo.webp') }}" alt="Logo"></a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/custom/js/datatables.min.js') }}"></script>

    @yield('script')
</body>

</html>
