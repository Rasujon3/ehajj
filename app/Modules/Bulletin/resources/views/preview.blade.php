@extends('layouts.admin')
@section('header-resources')
    <!-- DateTimePicker CSS -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    {{-- CK editor --}}
    {{-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> --}}
@endsection

@section('content')
    <style>
        /* Custom styles for datetimepicker buttons */
        .datetimepicker .btn-picker {
            background-color: #007bff;
            color: #fff;
            border: 1px solid #007bff;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 14px;
        }

        .datetimepicker .btn-picker:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .card-heading{
            font-weight: 500;
            border-radius: 8px 8px 0 0;
            /* color: #FFCC00; */
            color: #076724;
            font-family: 'ShonarBanglaRegular';
            font-size: 20px;
            line-height: 22px;
            padding: 10px 18px 0;
            text-shadow: 0 1px #fff;
            margin: 0;
            margin-left:10px;
            background: linear-gradient(135deg, rgba(219,219,219,1) 0%,rgba(238,238,238,1) 50%,rgba(224,224,224,1) 51%,rgba(243,242,242,1) 100%);
        }

        .card-body{
            background: #f8f8f8;
            font-family: 'ShonarBanglaRegular';
            padding: 20px 10px;
            text-align: justify;
            width: 100%;
            border-radius: 3px;
            margin-bottom: 8px;
            width: 97%;
            margin-left:10px;
        }

        ul {
            padding: 21px;
            margin: 0;
            list-style: none;
        }

        li {
             display: list-item; list-style: square;
        }

    </style>
    <?php
    function filterBanglaBuletin($str){
        if(str_contains($str, "[:bn]")){
            $fields = explode("[:bn]", $str);
            $str = $fields[1];
            if(str_contains($fields[1], "[:]")){
                $str = str_replace("[:]", "",$fields[1]);
            }
        }

        if(str_contains($str, "<!--:-->")){
            $fields = explode("<!--:-->", $str);
            $str = $fields[0];
            if(str_contains($fields[0], "<!--:bn-->")){
                $str = str_replace("<!--:bn-->", "",$fields[0]);
            }
        }
        return $str;
    }

    function alignStyle($str){
        if(str_contains($str, "text-align: center")){
            $str = str_replace("text-align: center", "text-align: center; font-size:25px;",$str);
        }

        if(str_contains($str, "\r\n<ul>")){
            $str = str_replace("\r\n<ul>", '<ul style="padding-left:20px; list-style-position: inside; list-style: initial;">',$str);
        }
        if(str_contains($str, "\r\n</ul>")){
            $str = str_replace("\r\n</ul>", "</ul>",$str);
        }

        if(str_contains($str, "\r\n \t<li>")){
            $str = str_replace("\r\n \t<li>", '<li style="display: list-item; list-style: square;">',$str);
        }

        if(str_contains($str, "\r\n \t")){
            $str = str_replace("\r\n \t", "<br/>    ",$str);
        }

        if(str_contains($str, "\r\n")){
            $str = str_replace("\r\n", "<br/>",$str);
        }

        return $str;
    }
    ?>
    {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
    {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}

    <div class="dash-content-main">
        <div class="border-card-block">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <span class="title-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M15.7997 2.20999C15.3897 1.79999 14.6797 2.07999 14.6797 2.64999V6.13999C14.6797 7.59999 15.9197 8.80999 17.4297 8.80999C18.3797 8.81999 19.6997 8.81999 20.8297 8.81999C21.3997 8.81999 21.6997 8.14999 21.2997 7.74999C19.8597 6.29999 17.2797 3.68999 15.7997 2.20999Z"
                                fill="white"/>
                            <path
                                d="M20.5 10.19H17.61C15.24 10.19 13.31 8.26 13.31 5.89V3C13.31 2.45 12.86 2 12.31 2H8.07C4.99 2 2.5 4 2.5 7.57V16.43C2.5 20 4.99 22 8.07 22H15.93C19.01 22 21.5 20 21.5 16.43V11.19C21.5 10.64 21.05 10.19 20.5 10.19ZM13 15.75H8.81L9.53 16.47C9.82 16.76 9.82 17.24 9.53 17.53C9.38 17.68 9.19 17.75 9 17.75C8.81 17.75 8.62 17.68 8.47 17.53L6.47 15.53C6.4 15.46 6.36 15.39 6.32 15.31C6.31 15.29 6.3 15.26 6.29 15.24C6.27 15.18 6.26 15.12 6.25 15.06C6.25 15.03 6.25 15.01 6.25 14.98C6.25 14.9 6.27 14.82 6.3 14.74C6.3 14.73 6.3 14.73 6.31 14.72C6.34 14.64 6.4 14.56 6.46 14.5C6.47 14.49 6.47 14.48 6.48 14.48L8.48 12.48C8.77 12.19 9.25 12.19 9.54 12.48C9.83 12.77 9.83 13.25 9.54 13.54L8.82 14.26H13C13.41 14.26 13.75 14.6 13.75 15.01C13.75 15.42 13.41 15.75 13 15.75Z"
                                fill="white"/>
                        </svg>
                    </span>
                    <h3>Create Bulletin</h3>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            <div class="bd-card-content">
            {!! Form::open(array('url' => route('store_bulletin'),'method' => 'post', 'class' => 'form-horizontal', 'id' => 'bulletinForm',
            'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                {!! Form::hidden('flag', '', ['id' => 'flag']) !!}
            <div class="row">
                <input type="hidden" name="serialized_bulletin_info" value="{{ Encryption::encode($serializedBulletinData)}}">
                <input type="hidden" name="serialized_bulletin_template" value="{{ Encryption::encode($serializedBulletinContent)}}">
                <div class="col-md-12" style="padding-left: 16.5%">
                        <table id="buletinTable" class="display">
                            <thead>
                                <tr>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background-color:#f8f8f8">
                                    <td>
                                        <div class="card-heading" name="bulletin_subject">{!! filterBanglaBuletin($bulletinSubject) !!}</div>
                                        <div class="card-body" name="bulletin_content">
                                            {!! filterBanglaBuletin(unserialize($serializedBulletinContent)) !!}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                </div>
            </div>
            </div>

            <div class="bd-card-footer">
                <div class="flex-space-btw info-btn-group">
                    <a href="{{ route('bulletin_list') }}">
                        {!! Form::button('Close', array('type' => 'button', 'class' => 'btn btn-default')) !!}
                    </a>
                    <div style="display: flex; flex-direction: row">
                        <button type="submit" id="saveAndDraft" class="btn btn-success" style="margin-right: 10px !important;"><span>Save & Draft</span></button>
                        <button type="submit" class="btn btn-green" id="saveAndPublish"><span>Save & Publish</span></button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!} <!-- /.form end -->
        </div>
    </div>
@endsection

@section('footer-script')

    <script src="{{ asset('assets/scripts/moment.js') }}"></script>
    <script src="{{ asset('assets/scripts/jquery.validate.min.js') }}"></script>
    <!-- DateTimePicker JS -->
    <script src="{{ asset('assets/scripts/bootstrap-datetimepicker.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#saveAndPublish').click(function() {
                $('#flag').val('save');
                $('#bulletinForm').submit();
            });

            $('#saveAndDraft').click(function() {
                $('#flag').val('draft');
                $('#bulletinForm').submit();
            });
        });
    </script>
@endsection
