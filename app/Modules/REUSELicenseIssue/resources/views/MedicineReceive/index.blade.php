@extends('layouts.admin')

@section('content')
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2.min.css") }}">
    @include('partials.messages')
    <div class="card">
        <div class="card-header bg-primary">
            <h5 style="float: left">
                Medicine Received for Session
            </h5>
        </div>
        {!! Form::open(array('method' => 'get', 'id'=> 'formId')) !!}
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <label for="pid" class="col-md-1 required-star">PID:</label>
                    <div class="col-md-3">
                        {!! Form::text('pid', '', ['class'=>'form-control required', 'placeholder'=>'Enter PID number', 'id'=>'pid']) !!}
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary" onclick="searchPilgrim()">Search</button>
                    </div>
                </div>
            </div>
            <div id="receiveDiv">

            </div>
        </div>
        {!! Form::close() !!}
    </div>

@endsection

@section('footer-script')
    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.2.24/jquery.autocomplete.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="{{ asset("assets/plugins/select2/js/select2.full.min.js") }}"></script>

    <script>

        $(document).ready(function() {
            $('#formId').on('submit', function (e) {
                e.preventDefault();
                searchPilgrim();
            });
        })

        function searchPilgrim() {
            let pid = $('#pid').val();
            if (pid == '') {
                alert("Please enter PID");
            } else {
                $.ajax({
                    type: "get",
                    {{--url: "{{ url('medicine-receive/search-pilgrim') }}",--}}
                    data: {pid: pid},
                    url: "<?php echo env('APP_URL').'/medicine-receive/search-pilgrim'?>",
                    success: function (response) {
                        if (response.responseCode == 1) {
                           $('#receiveDiv').html(response.html)
                        } else {

                        }

                    }
                });
            }
        }
        function close_all(){
            $('#receiveDiv').html('')
            $('#pid').val('')
        }
    </script>
@endsection
