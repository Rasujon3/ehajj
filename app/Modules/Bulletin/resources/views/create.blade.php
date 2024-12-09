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
    </style>
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
                {!! Form::open(array('url' => route('preview_bulletin'),'method' => 'post', 'class' => 'form-horizontal', 'id' => 'bulletinForm',
            'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                <div class="new-flight-form">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-form-group">
                                <div class="card-form-block {{$errors->has('bulletinType') ? 'has-error' : ''}}">
                                    {!! Form::label('bulletinType','ধরন: ',['class'=>'required-star']) !!}
                                    {!! Form::select('bulletinType', ['PRE_HAJ' => 'Pre-Haj','POST_HAJ' =>'Post-Haj'],
                                        ($lastBulletinHajType == "" || $lastBulletinHajType == "PRE_HAJ") ? 'PRE_HAJ' : 'POST_HAJ',
                                        ['class'=>'form-control required', 'id' => 'bulletinType', 'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('bulletinType','<span class="help-block">:message</span>') !!}
                                </div>
                                <div class="card-form-block {{$errors->has('numberOfPublication') ? 'has-error' : ''}}">
                                    {!! Form::label('numberOfPublication','প্রকাশের সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('numberOfPublication',null,['class'=>'form-control required', 'id' => 'numberOfPublication',  'placeholder'=>'প্রকাশের সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('numberOfPublication','<span class="help-block">:message</span>') !!}
                                </div>
                                <div
                                    class="card-form-block {{$errors->has('publicationDateAndTime') ? 'has-error' : ''}}">
                                    {!! Form::label('publicationDateAndTime','প্রকাশের তারিখ এবং সময়: ',['class'=>'required-star']) !!}
                                    {!! Form::text('publicationDateAndTime',null,['class'=>'form-control required', 'id' => 'publicationDateAndTime',  'placeholder'=>'প্রকাশের তারিখ এবং সময়',
                                    ]) !!}
                                    {!! $errors->first('publicationDateAndTime','<span class="help-block">:message</span>') !!}
                                </div>
                                <div
                                    class="card-form-block {{$errors->has('totalGovHajjPassenger') ? 'has-error' : ''}}">
                                    {!! Form::label('totalGovHajjPassenger','সরকারি হজযাত্রী সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('totalGovHajjPassenger',null,['class'=>'form-control required', 'id' => 'totalGovHajjPassenger', 'placeholder'=>'সরকারি হজযাত্রী সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('totalGovHajjPassenger','<span class="help-block">:message</span>') !!}
                                </div>
                                <div class="card-form-block {{$errors->has('totalFlightCount') ? 'has-error' : ''}}">
                                    {!! Form::label('totalFlightCount','মোট ফ্লাইট সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('totalFlightCount',null,['class'=>'form-control required', 'id' => 'totalFlightCount',  'placeholder'=>'মোট ফ্লাইট সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('totalFlightCount','<span class="help-block">:message</span>') !!}
                                </div>
                                <div class="card-form-block {{$errors->has('soudiaFlightCount') ? 'has-error' : ''}}">
                                    {!! Form::label('soudiaFlightCount','সৌদিয়া ফ্লাইট সংখ্যা : ',['class'=>'required-star']) !!}
                                    {!! Form::text('soudiaFlightCount',null,['class'=>'form-control required', 'id' => 'soudiaFlightCount',  'placeholder'=>'সৌদিয়া ফ্লাইট সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('soudiaFlightCount','<span class="help-block">:message</span>') !!}
                                </div>
                                <div class="card-form-block {{$errors->has('medicalPaperCount') ? 'has-error' : ''}}">
                                    {!! Form::label('medicalPaperCount','চিকিৎসা পত্র সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('medicalPaperCount',null,['class'=>'form-control required', 'id' => 'medicalPaperCount', 'placeholder'=>'চিকিৎসা পত্র সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('medicalPaperCount','<span class="help-block">:message</span>') !!}
                                </div>
                                <div class="card-form-block {{$errors->has('itHelpDesk') ? 'has-error' : ''}}">
                                    {!! Form::label('itHelpDesk','আইটি হেল্প ডেস্ক সেবা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('itHelpDesk',null,['class'=>'form-control required', 'id' => 'itHelpDesk',  'placeholder'=>'আইটি হেল্প ডেস্ক সেবা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('itHelpDesk','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card-form-group">
                                <div class="card-form-block input-has-icon {{$errors->has('bulletinDateEng') ? 'has-error' : ''}}">
                                    {!! Form::label('bulletinDateEng','বুলেটিন তারিখ: ',['class'=>'required-star']) !!}
                                    <div class="">
                                        {!! Form::text('bulletinDateEng',null,['class'=>'form-control bnEng required datetimepicker',
                                            'placeholder'=>'MM/DD/YYYY', 'data-rule-maxlength'=>'20', 'value'=>"12:00 AM"]) !!}
                                        <span class="form-input-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <g clip-path="url(#clip0_2139_2246)">
                                                    <path d="M14.75 2H14V1.25C14 1.05109 13.921 0.860322 13.7803 0.71967C13.6397 0.579018 13.4489 0.5 13.25 0.5C13.0511 0.5 12.8603 0.579018 12.7197 0.71967C12.579 0.860322 12.5 1.05109 12.5 1.25V2H6.5V1.25C6.5 1.05109 6.42098 0.860322 6.28033 0.71967C6.13968 0.579018 5.94891 0.5 5.75 0.5C5.55109 0.5 5.36032 0.579018 5.21967 0.71967C5.07902 0.860322 5 1.05109 5 1.25V2H4.25C3.2558 2.00119 2.30267 2.39666 1.59966 3.09966C0.896661 3.80267 0.501191 4.7558 0.5 5.75L0.5 14.75C0.501191 15.7442 0.896661 16.6973 1.59966 17.4003C2.30267 18.1033 3.2558 18.4988 4.25 18.5H14.75C15.7442 18.4988 16.6973 18.1033 17.4003 17.4003C18.1033 16.6973 18.4988 15.7442 18.5 14.75V5.75C18.4988 4.7558 18.1033 3.80267 17.4003 3.09966C16.6973 2.39666 15.7442 2.00119 14.75 2ZM2 5.75C2 5.15326 2.23705 4.58097 2.65901 4.15901C3.08097 3.73705 3.65326 3.5 4.25 3.5H14.75C15.3467 3.5 15.919 3.73705 16.341 4.15901C16.7629 4.58097 17 5.15326 17 5.75V6.5H2V5.75ZM14.75 17H4.25C3.65326 17 3.08097 16.7629 2.65901 16.341C2.23705 15.919 2 15.3467 2 14.75V8H17V14.75C17 15.3467 16.7629 15.919 16.341 16.341C15.919 16.7629 15.3467 17 14.75 17Z" fill="#474D49"/>
                                                    <path d="M9.5 12.875C10.1213 12.875 10.625 12.3713 10.625 11.75C10.625 11.1287 10.1213 10.625 9.5 10.625C8.87868 10.625 8.375 11.1287 8.375 11.75C8.375 12.3713 8.87868 12.875 9.5 12.875Z" fill="#474D49"/>
                                                    <path d="M5.75 12.875C6.37132 12.875 6.875 12.3713 6.875 11.75C6.875 11.1287 6.37132 10.625 5.75 10.625C5.12868 10.625 4.625 11.1287 4.625 11.75C4.625 12.3713 5.12868 12.875 5.75 12.875Z" fill="#374957"/>
                                                    <path d="M13.25 12.875C13.8713 12.875 14.375 12.3713 14.375 11.75C14.375 11.1287 13.8713 10.625 13.25 10.625C12.6287 10.625 12.125 11.1287 12.125 11.75C12.125 12.3713 12.6287 12.875 13.25 12.875Z" fill="#374957"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_2139_2246">
                                                        <rect width="18" height="18" fill="white" transform="translate(0.5 0.5)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-form-block {{$errors->has('bulletinDate') ? 'has-error' : ''}}">
                                    {!! Form::label('bulletinDate','বুলেটিন তারিখ বাংলায়: ',['class'=>'required-star']) !!}
                                    {!! Form::text('bulletinDate',null,['class'=>'form-control required', 'id' => 'bulletinDate',  'placeholder'=>'বুলেটিন তারিখ বাংলায়',
                                    ]) !!}
                                    {!! $errors->first('bulletinDate','<span class="help-block">:message</span>') !!}
                                </div>
                                <div class="card-form-block {{$errors->has('totalHajjPassenger') ? 'has-error' : ''}}">
                                    {!! Form::label('totalHajjPassenger','সর্বমোট হজযাত্রী সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('totalHajjPassenger',null,['class'=>'form-control required', 'id' => 'totalHajjPassenger', 'placeholder'=>'সর্বমোট হজযাত্রী সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('totalHajjPassenger','<span class="help-block">:message</span>') !!}
                                </div>
                                <div
                                    class="card-form-block {{$errors->has('nonGovHajjPassengerCount') ? 'has-error' : ''}}">
                                    {!! Form::label('nonGovHajjPassengerCount','বেসরকারি হজযাত্রী সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('nonGovHajjPassengerCount',null,['class'=>'form-control required', 'id' => 'nonGovHajjPassengerCount',  'placeholder'=>'বেসরকারি হজযাত্রী সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('nonGovHajjPassengerCount','<span class="help-block">:message</span>') !!}
                                </div>
                                <div
                                    class="card-form-block {{$errors->has('bimanPassengerCount') ? 'has-error' : ''}}">
                                    {!! Form::label('bimanPassengerCount','বিমান হজযাত্রী সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('bimanPassengerCount',null,['class'=>'form-control required', 'id' => 'bimanPassengerCount',  'placeholder'=>'বিমান হজযাত্রী সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('bimanPassengerCount','<span class="help-block">:message</span>') !!}
                                </div>
                                <div
                                    class="card-form-block {{$errors->has('soudiaPassengerCount') ? 'has-error' : ''}}">
                                    {!! Form::label('soudiaPassengerCount','সৌদিয়া হজযাত্রী সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('soudiaPassengerCount',null,['class'=>'form-control required', 'id' => 'soudiaPassengerCount',  'placeholder'=>'সৌদিয়া হজযাত্রী সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('soudiaPassengerCount','<span class="help-block">:message</span>') !!}
                                </div>
                                <div
                                    class="card-form-block {{$errors->has('flynasPassengerCount') ? 'has-error' : ''}}">
                                    {!! Form::label('flynasPassengerCount','ফ্লাইনাস হজযাত্রী সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('flynasPassengerCount',null,['class'=>'form-control required', 'id' => 'flynasPassengerCount',  'placeholder'=>'ফ্লাইনাস হজযাত্রী সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('flynasPassengerCount','<span class="help-block">:message</span>') !!}
                                </div>
                                <div
                                    class="card-form-block {{$errors->has('bimanFlightNumberCount') ? 'has-error' : ''}}">
                                    {!! Form::label('bimanFlightNumberCount','বিমান ফ্লাইট সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('bimanFlightNumberCount',null,['class'=>'form-control required', 'id' => 'bimanFlightNumberCount', 'placeholder'=>'বিমান ফ্লাইট সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('bimanFlightNumberCount','<span class="help-block">:message</span>') !!}
                                </div>
                                <div
                                    class="card-form-block {{$errors->has('flynasFlightNumberCount') ? 'has-error' : ''}}">
                                    {!! Form::label('flynasFlightNumberCount','ফ্লাইনাস ফ্লাইট সংখ্যা: ',['class'=>'required-star']) !!}
                                    {!! Form::text('flynasFlightNumberCount',null,['class'=>'form-control required', 'id' => 'flynasFlightNumberCount', 'placeholder'=>'ফ্লাইনাস ফ্লাইট সংখ্যা',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('flynasFlightNumberCount','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="card-form-group">
                            <label>বডি টেক্সট মেসেজ (যদি কিছু থাকে)</label>
                            <textarea class="form-control" id="additional_txt" name="additional_txt" cols="40"
                                      rows="10"></textarea>
                        </div>

                        <div class="card-form-group">
                            <label for="activities">একনজরে হজ এর কার্যক্রম <span class="required-star"></span></label>
                            <textarea class="form-control required" id="activities" name="activities" cols="40"
                                      rows="10">{{$fixedText}}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bd-card-footer">
                <div class="flex-space-btw info-btn-group">
                    <a href="{{ route('bulletin_list') }}">
                        {!! Form::button('Close', array('type' => 'button', 'class' => 'btn btn-default')) !!}
                    </a>
                    <button type="submit" class="btn btn-green"><span>Preview</span></button>
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
    <script src="https://cdn.ckeditor.com/4.20.2/standard/ckeditor.js"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            CKEDITOR.replace('additional_txt', {});
            CKEDITOR.replace('activities', {});

            function validateActivitiesCKEditorData() {
                let editorContent = CKEDITOR.instances.activities.getData().trim();
                if (editorContent === '') {
                    toastr.error("একনজরে হজ এর কার্যক্রম তথ্য পূরণ করুন")
                    return false;
                }
                return true;
            }

            $("#bulletinForm").validate({
                errorPlacement: function () {
                    return false;
                }
            });

            $('#bulletinForm').submit(function(e){
                if (!validateActivitiesCKEditorData()) {
                    e.preventDefault();
                }
            });

            let today = new Date();
            let yyyy = today.getFullYear();
            let mm = today.getMonth() + 1;
            let dd = today.getDate();

            $('.datetimepicker').datetimepicker({
                viewMode: 'months',
                sideBySide: true,
                // minDate: (mm) + '/' + dd + '/' + yyyy,
                maxDate: (mm) + '/' + dd + '/' + (yyyy + 5),
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-clock-o",
                    clear: "fa fa-trash-o"
                },
                format: 'YYYY-MM-DD'
            });


            $('.datetimepicker').on('click', function () {
                $(this).data('DateTimePicker').date(moment());
            });

        });
    </script>
@endsection
