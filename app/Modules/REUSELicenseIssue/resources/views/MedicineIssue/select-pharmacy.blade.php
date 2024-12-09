@extends('layouts.admin')


@section('content')
    <h4>Please select pharmacy to continue</h4>
    <br>
    {!! Form::open(array('url' => 'medicine-issue/save-pharmacy','method' => 'post', 'id'=> 'formId')) !!}
    <div class="form-group">
        <div class="row">
            <label for="pharmacy_id" class="col-md-2">Select pharmacy:</label>
            <div class="col-md-4">
                {!! Form::select('pharmacy_id', $pharmacyList, \Illuminate\Support\Facades\Auth::user()->working_company_id, ['class'=>'form-control', 'placeholder'=>'Select Pharmacy', 'id'=>'pharmacy_id']) !!}
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary" id="changePharmacy"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}


@endsection

@section('footer-script')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.2.24/jquery.autocomplete.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
@endsection
