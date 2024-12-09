<div class="row">
    <div class="col-md-2 text-center">
        <img src="{{ $responseData->data->pilgrim->picture }}" alt="" width="60%">
        <div><strong>{{ $responseData->data->pilgrim->full_name_english }}</strong>
            <p><strong>PID: </strong>{{ $responseData->data->pilgrim->pid }}</p></div>
    </div>
    <div class="col-md-10">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Tracking Number:</label>
                    </div>
                    <div class="col-md-3">
                        {{$responseData->data->pilgrim->tracking_no}}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Pilgrim Type:</label>
                    </div>
                    <div class="col-md-9">
                        {{$responseData->data->pilgrim->is_govt}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Passport Number:</label>
                    </div>
                    <div class="col-md-3">
                        {{$responseData->data->pilgrim->passport_no}}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Gender:</label>
                    </div>
                    <div class="col-md-9">
                        {{$responseData->data->pilgrim->gender}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
@if(count($medData) > 0)
    <div class="row table-responsive">
        <table id="issueTable" class="table table-bordered">
            <thead style="background-color: #F7F7F7">
            <tr>
                <th class="text-center">Date</th>
                <th class="text-center">Medicine</th>
                <th class="text-center">Quantity</th>
            </tr>
            </thead>
            <tbody>
            @foreach($medData as $data)
                <tr>
                    <td>{{ date('d M Y', strtotime($data->date)) }}</td>
                    <td>{{ $data->med_type.' '.$data->trade_name.' '.$data->sku }}</td>
                    <td>{{ $data->quantity }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-default" onclick="close_all()">Close</button>
    </div>
@else
    <h5>No data found for this pilgrim</h5>
@endif

