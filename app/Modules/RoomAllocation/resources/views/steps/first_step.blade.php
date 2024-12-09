<div class="section-card-title">
    <h3>Madinah House Allocation</h3>
</div>
<div class="home-alc-container">
    <div class="card-form-group">
        <div class="card-form-block">
            <label>Flight</label>
            <select class="form-control customSelectOption" name="flight" id="flight_id">
                <option value="">{{ trans('RoomAllocation::messages.select') }}</option>
                @if(!empty($flightList))
                    @foreach($flightList as $index => $flight)
                        <option value="{{ $index }}">{{ $flight }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="card-form-block">
            <label>Guide</label>
            <select class="form-control customSelectOption" name="Guide" id="guide_id">
                <option value="">{!! trans('RoomAllocation::messages.select') !!}</option>
            </select>
        </div>
    </div>
</div>

<div class="card-form-footer">
    <div class="card-btn-block" pageNo="1">
        <a href="{{ url('/') }}" class="btn btn-default"><i class="fas fa-long-arrow-alt-left"></i> <span>Close</span></a>
        <button class="btn btn-primary nextbtn"><span>Next</span> <i class="fas fa-long-arrow-alt-right"></i></button>
    </div>
</div>
<script>
    $(".customSelectOption").select2();
</script>
