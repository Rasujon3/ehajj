<div class="section-card-title">
    <h3>Madinah House Allocation</h3>
</div>
<div class="home-alc-container">
    <div class="card-form-group">
        <div class="row">
            <div class="col-lg-6">
                <div class="number-info" id="flight_text">
                    <span>Flight </span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="number-info" id="guide_text">
                    <span>Guide </span>
                </div>
            </div>
        </div>
    </div>

    <div class="dash-section-card card-color-5">
        <div class="section-card-title">
            <h3>Pilgrim Information</h3>
        </div>
        <div class="section-card-container">
            <h6>Total Pilgrim under guide </h6>
            <div class="row">
                <div class="col-lg-4">
                    <div class="number-info">
                        <span>Male</span>
                        {{ isset($total_pilgrim_list_arr['male']) ? $total_pilgrim_list_arr['male'] : 0 }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="number-info">
                        <span>Female</span>
                        {{ isset($total_pilgrim_list_arr['female']) ? $total_pilgrim_list_arr['female'] : 0 }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="number-info">
                        <span>Total </span>
                        {{ isset($total_pilgrim_list_arr['Total']) ? $total_pilgrim_list_arr['Total'] : 0 }}
                    </div>
                </div>
            </div>

            <h6>Allocated Pilgrim</h6>
            <div class="row">
                <div class="col-lg-4">
                    <div class="number-info">
                        <span>Male</span>
                            {{ isset($allocated_pilgrim_list_arr['male']) ? $allocated_pilgrim_list_arr['male'] : 0 }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="number-info">
                        <span>Female</span>
                            {{ isset($allocated_pilgrim_list_arr['female']) ? $allocated_pilgrim_list_arr['female'] : 0 }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="number-info">
                        <span>Total </span>
                            {{ isset($allocated_pilgrim_list_arr['Total']) ? $allocated_pilgrim_list_arr['Total'] : 0 }}
                    </div>
                </div>
            </div>

            <h6>Remaining</h6>
            <div class="row">
                <div class="col-lg-4">
                    <div class="number-info">
                        <span>Male</span>
                        {{ isset($remaining_pilgrim_list_arr['male']) ? $remaining_pilgrim_list_arr['male'] : 0 }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="number-info">
                        <span>Female</span>
                        {{ isset($remaining_pilgrim_list_arr['female']) ? $remaining_pilgrim_list_arr['female'] : 0 }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="number-info">
                        <span>Total </span>
                        {{ isset($remaining_pilgrim_list_arr['Total']) ? $remaining_pilgrim_list_arr['Total'] : 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dash-section-card card-color-6">
        <div class="section-card-title card-title-button">
            <h3>Madinah House Information</h3>
            <select class="form-control" name="report">
                <option value="0">Report</option>
                <option value="1">Others</option>
            </select>
        </div>
        <div class="section-card-container">
            <div class="room-alc-title">
                <h4>Allocation</h4>
                <div class="alc-indicator">
                    <span>ROOM</span>
                    <span class="alc-ind-item ind-male">Male</span>
                    <span class="alc-ind-item ind-female">Female</span>
                    <span class="alc-ind-item ind-both">Both</span>
                </div>
            </div>

            <div class="house-info-table">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="width:10%;">Pilgrim</th>
                        <th style="width:10%;">Total</th>
                        <th>ROOM</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if(!empty($guideList->roomWisePilgrimList))
                        @foreach($guideList->roomWisePilgrimList as $itemIndex=>$item)
                            <tr>
                                <td>{{ $item->Gender }}</td>
                                <td>{{ $item->Total }}</td>
                                <td class="clr-{{ strtolower($item->Gender) }}-house">{{ $item->Rooms }} </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3">No available Data</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="dash-section-card card-color-7">
        <div class="section-card-title">
            <h3>Madinah House New Allocation</h3>
        </div>

        <div class="section-card-container">
            <div class="card-form-group">
                <div class="card-form-block">
                    <label>House Name</label>
                    <select class="form-control customSelectOption" name="House" id="house_id">
                        <option value="">{{ trans('RoomAllocation::messages.select') }}</option>
                        @if(!empty($guideList->madinaHouseList))
                            @foreach($guideList->madinaHouseList as $index => $house)
                                <option value="{{ $house->id }}" house_no="{{ $house->house_no }}">{{ $house->house_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="card-form-block">
                    <label>Floor</label>
                    <select class="form-control customSelectOption" name="Floor" id="floor_id">
                        <option value="">{{ trans('RoomAllocation::messages.select') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card-form-footer">
    <div class="card-btn-block">
        <a href="{{ url('/') }}" class="btn btn-default"><span>Close</span></a>

        <div class="btn-group-right" pageNo="2">
            <button class="btn btn-default prevbtn"><i class="fas fa-long-arrow-alt-left"></i> <span>Previous</span></button>
            <button class="btn btn-primary nextbtn"><span>Next</span> <i class="fas fa-long-arrow-alt-right"></i></button>
        </div>
    </div>
</div>

<script>
    $(".customSelectOption").select2();
</script>
