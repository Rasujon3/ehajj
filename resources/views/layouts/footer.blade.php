{{--<footer class="site-footer">--}}
{{--    <div class="footer-copyright">--}}
{{--        <div class="container">--}}
{{--            <div class="copyright-content">--}}
{{--                <p><strong>Managed by Business Automation Ltd</strong>--}}
{{--                    on behalf of Ministry of Religious Affairs, Bangladesh.</p>--}}
{{--                <div class="float-right developed-by d-none d-sm-inline-block">--}}
{{--                     <span>Version  3.0</span>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</footer>--}}

<!--start  updated footer css & js file linkup -->

<link rel="stylesheet" href="{{ asset('assets/public_page/custom/css/custom-footer-style.css')}}">
<link rel="stylesheet" href="{{ asset('assets/public_page/plugins/fontawesome-free/css/all.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/public_page/custom/css/custom-style.css?v=1') }}">
<link rel="stylesheet" href="{{ asset('assets/public_page/custom/css/custom-responsive.css') }}">

<!--end updated footer css & js file linkup -->

@include('public_home.footer')

@section('footer-script')
    <script>
        $(document).ready(function() {
            getImportantLink();
            function getImportantLink() {
                $.ajax({
                    url: '{{url('get-impotant-link')}}',
                    type: 'GET',
                    success: function (response) {
                        $('#footer-important-link').html(response);
                    }
                });
            }
        });
    </script>
@endsection
