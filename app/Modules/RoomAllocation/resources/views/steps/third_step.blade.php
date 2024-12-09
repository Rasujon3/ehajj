<div class="section-card-title">
    <h3>Madinah House Allocation <span>(List of pilgrims under Haj guide <span id="guide_name"></span> [PID: <span id="guide_pid"></span>], [Flight: <span id="flight_text"></span>])</span>
    </h3>
</div>
<div class="home-alc-container">
    @if(!empty($prprDataArr))
        @foreach($prprDataArr as $houseNo => $houseData)
            @if(!empty($houseData))
                @foreach($houseData as $roomNo=>$pilgrimData)
                    <div class="house-alc-table">
                        <div class="house-alc-table-title">
                            <h3>Makkah House No: {{ $houseNo }}</h3>
                            <span class="alc-room-no">Room No: {{ $roomNo }}</span>
                        </div>
                        <div class="house-alc-table-content">
                            <div class="alc-room-list-table">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>PID No</th>
                                        <th>Pilgrim Name</th>
                                        <th>Gender</th>
                                        <th>Madinah House Room</th>
                                        <th style="width: 150px;">
                                            <div style="display:flex">
                                                <a class="btn btn-outline-primary actionForAll setAllPilgrimToRoom" room_no="{{$roomNo}}" href="#ehajjAddPilgrimModal" data-toggle="modal">
                                                    <i class="fas fa-plus-circle"></i> Set All
                                                </a>
                                                <a class="btn btn-outline-danger actionForAll removeAllPilgrimFromRoom" room_no="{{$roomNo}}" type="button" href="#"><i
                                                        class="far fa-trash-alt"></i> Remove All</a>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="tbody_{{$roomNo}}">
                                    @if(!empty($pilgrimData))
                                        @foreach($pilgrimData as $p_index=>$pilgrim)
                                            <tr>
                                                <td>{{$pilgrim->PID}}</td>
                                                <td>
                                                    {{$pilgrim->PilgrimName}}
                                                    {!!  ($pilgrim->Gender == 'female') ? "<br><small>Maharam PID: ".$pilgrim->MaharamPID."</small>" : ''  !!}
                                                </td>
                                                <td>{{$pilgrim->Gender}}</td>
                                                <td>{{($pilgrim->MadinahRoomNo) ? 'H :'.$pilgrim->MadinahHouseNo. '  |  F : '.$pilgrim->MadinahFloorNo.'  |  R :'.$pilgrim->MadinahRoomNo : 'N/A'}}</td>
                                                <td>
                                                    <div class="table-btn-group">
                                                        <input type="hidden" id="pilgrim_id" value="{{$pilgrim->Pilgrim_id}}">
                                                        @if(empty($pilgrim->MadinahRoomNo))
                                                            <a class="btn btn-outline-primary setPilgrimToRoom" pilgrim_id="{{$pilgrim->Pilgrim_id}}" href="#ehajjAddPilgrimModal"
                                                               data-toggle="modal"><i
                                                                    class="fas fa-plus-circle"></i> Set</a>
                                                        @else
                                                            <a class="btn btn-outline-danger removePilgrimFromRoom" type="button" pilgrim_id="{{$pilgrim->Pilgrim_id}}" href="#"><i
                                                                    class="far fa-trash-alt"></i> Remove</a>
                                                        @endif

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5">No available pilgrim</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach
    @endif
</div>

<div class="card-form-footer">
    <div class="card-btn-block">
        <a href="{{ url('/') }}" class="btn btn-default"><span>Close</span></a>

        <div class="btn-group-right" pageNo="3">
            <button class="btn btn-default prevbtn"><i class="fas fa-long-arrow-alt-left"></i> <span>Previous</span>
            </button>
{{--            <button class="btn btn-primary nextbtn"><i class="far fa-save"></i><span>Save</span></button>--}}
        </div>
    </div>
</div>


<div class="modal alc-home-modal fade" id="ehajjAddPilgrimModal" tabindex="-1" aria-labelledby="ehajjAddPilgrimModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="icon-close-modal"><img src="./assets/custom/images/icon-close.svg" alt="Icon"></span>
                </button>
                <h3>Room Information</h3>
                <br>
                <div id="selected_info_portion">
                    <p><strong>House No:</strong> <span id="selected_house_no"></span></p>
                    <p><strong>House Name:</strong> <span id="selected_house_name"></span></p>
                    <p><strong>Floor No:</strong> <span id="selected_floor_no"></span></p>
                </div>
            </div>
            <div class="modal-body">

                <div class="section-card-container">
                    <div class="alc-room-list-table section-gmb-lists">
                        <div class="dash-list-table">
                            <div class="table-responsive">
                                <table class="table dash-table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Room</th>
                                        <th scope="col">Capacity</th>
                                        <th scope="col">Availability</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($apiDataList->roomList))
                                        @foreach($apiDataList->roomList as $roomIndex=>$room)
                                            <tr class="table-row-space"><td colspan="5">&nbsp;</td></tr>
                                            <tr>
                                                <td class="">
                                                    {{$room->room_no}}
                                                <?php if ($room->male > 0 && $room->female > 0){ ?>
                                                    <span class="alc-ind-item clr-both-house">Both</span>
                                                <?php }
                                                    else {
                                                        if ($room->male > 0){ ?>
                                                            <span class="alc-ind-item clr-male-house">M</span>
                                                    <?php }
                                                        if ($room->female > 0){ ?>
                                                            <span class="alc-ind-item clr-female-house">F</span>
                                                    <?php }
                                                    } ?>
                                                </td>
                                                <td>{{$room->capacity}}</td>
                                                <td>{{$room->availability}}</td>
                                                <td>
                                                    <div class="form-check">
                                                        <input style="cursor:pointer" type="radio" name="is_select"
                                                               {{ $room->availability <= 0 ? 'disabled': '' }}
                                                               class="selected_room" value="{{$room->id}}">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="table-row-space"><td colspan="5">&nbsp;</td></tr>
                                        <tr>
                                            <td class="clr-male-house"colspan="5">No room available</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card-btn-block">
                        <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-primary setPilgrimToHouse">Add</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
