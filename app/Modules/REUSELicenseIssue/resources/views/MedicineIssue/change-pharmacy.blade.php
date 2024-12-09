<h3>Change your pharmacy</h3>
<div class="form-group">
    <div class="row">
        <label for="" class="col-md-2">Select pharmacy:</label>
        <div class="col-md-4">
            {!! Form::select('pharmacy_id', $pharmacyList, \Illuminate\Support\Facades\Auth::user()->working_company_id, ['class'=>'form-control', 'placeholder'=>'Select Pharmacy', 'id'=>'pharmacy_id']) !!}
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-md-6">
            <button class="btn btn-primary" type="button" id="changePharmacy"><i class="fa fa-sync"></i> Change</button>
        </div>
    </div>
</div>
