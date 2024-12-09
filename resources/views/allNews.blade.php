@extends('public_home.front')
@section('header-resources')
    <link href="{{ asset("assets/plugins/jquery-ui/jquery-ui.css") }}" rel="Stylesheet" type="text/css"/>
@endsection

@section('body')
    <div class="" style="margin-top:90px;">
        <div class="container">
            @if($is_notice_show)
                <div class="hajj-recent-notice">
                    <div class="sec-title">
                        <h2>সাম্প্রতিক সকল নোটিশ ও বিজ্ঞপ্তি</h2>
                    </div>

                    <div class="form-group mb-4 p-0 col-md-4">
                        <label for="inputDate">তারিখ :</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="inputDate">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <small id="loading_msg" style="display:none;">Loading data... <i
                                class="fa fa-spinner fa-spin"></i></small>
                    </div>

                    <div class="notice-container" id="dynamic_news_list"></div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('footer-script')
    <script src="{{ asset("assets/plugins/jquery-ui/jquery-ui.js") }}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $('#inputDate').datepicker({
                format: 'dd-M-yyyy',
            });

            $(document).on('change', '#inputDate', function () {
                $('#loading_msg').show();
                $.ajax({
                    url: 'fetch-all-news',
                    type: 'POST',
                    data: {
                        date: $(this).val(),
                        _token: '{{csrf_token()}}'
                    },
                    success: function (response) {
                        $('#loading_msg').hide();
                        if (response.responseCode == 1) {
                            $('#dynamic_news_list').html('').html(response.html);
                        }
                        else{
                            $('#dynamic_news_list').html('').html(response.msg);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    },
                });
            });
        });
    </script>
@endsection
