@extends('layouts.admin')

@section('header-resources')
    <link rel="stylesheet" href="{{ asset('css/room_allocation/custom-style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/room_allocation/custom-responsive.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/room_allocation/loading.css') }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2.min.css") }}">
@endsection

@section('content')
<div class="dash-content-main">
    <div class="dash-home-allocation-content">
        <div class="hajj-haouse-allocation-steps">
            <div class="hajj-halc-steps">
                <div class="hajj-halc-step-item" id="first_step">
                    <span class="halc-step-num"><span>1</span></span>
                    <span class="halc-step-text">Step</span>
                </div>
                <div class="hajj-halc-step-item" id="second_step">
                    <span class="halc-step-num"><span>2</span></span>
                    <span class="halc-step-text">Step</span>
                </div>
                <div class="hajj-halc-step-item" id="third_step">
                    <span class="halc-step-num"><span>3</span></span>
                    <span class="halc-step-text">Step</span>
                </div>
            </div>
        </div>

        <div class="dash-section-card main-section-card">
            <div class="actionCon">
                <div class="actionType1">
                    <span class="FigureBox">1</span>
                    <span class="FigureBox">2</span>
                    <span class="FigureBox">3</span>
                    <span class="FigureBox">4</span>
                    <span class="FigureBox">5</span>
                </div>
            </div>
            <div id="infoDisplay" style="display:none;"></div>
        </div>
    </div>
</div>





@endsection

@section('footer-script')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.2.24/jquery.autocomplete.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="{{asset("assets/plugins/select2/js/select2.full.min.js")}}"></script>

    @include('partials.datatable-js')
    <script src="{{asset('assets/plugins/jquery.table2excel.js')}}"></script>

    <script>
        let flight_val = 0;
        let flight_text = '';
        let guide_val = 0;
        let guide_text = '';
        let guide_pid = '';
        let house_id = 0;
        let house_no = 0;
        let house_name = '';
        let floor_id = 0;
        let floor_no = 0;
        let pilgrim_list = [];

        $(document).ready(function(){
            fetchFirstStep();

            $(document).on('change','#flight_id',function(){
                let flight_val = $(this).val();
                let obj = $(this);
                obj.after('<span class="loading_data">Loading...</span>');
                $.ajax({
                    url: "{{ url('room-allocation/getGuideListByFlightId') }}",
                    type: "POST",
                    data: {
                        flight_val:flight_val
                    },
                    success: function(response){
                        if(response.responseCode == 1){
                            html = "<option value=''>{{ trans("RoomAllocation::messages.select") }}</option>";
                            $.each(response.data, function(index, value) {
                                if (guide_val  && guide_val == value.GuideID) {
                                    html += '<option value="' + value.GuideID + '" pid="'+ value.PID +'" selected>' + value.pilgrim_name + '</option>';
                                } else {
                                    html += '<option value="' + value.GuideID+ '" pid="'+ value.PID +'" >' + value.pilgrim_name + '</option>';
                                }
                            });
                            $('#guide_id').html(html);
                        }else{
                            alert(response.msg);
                        }
                        obj.next().hide();

                    },
                    error: function (jqXHR, exception) {
                        alert("something was wrong")
                        console.log(jqXHR);
                        obj.next().hide();
                    }
                })
            });

            $(document).on('change','#house_id',function(){
                let house_val = $(this).val();
                let obj = $(this);
                obj.after('<span class="loading_data">Loading...</span>');
                $.ajax({
                    url: "{{ url('room-allocation/fetchFloorList') }}",
                    type: "POST",
                    data: {
                        house_val:house_val,
                    },
                    success: function(response){
                        if(response.responseCode == 1){
                            html = "<option value=''>{{ trans("RoomAllocation::messages.select") }}</option>";
                            $.each(response.data, function(index, value) {
                                if (floor_id  && floor_id == index) {
                                    html += '<option value="' + index+ '" selected>' + value + '</option>';
                                } else {
                                    html += '<option value="' + index+ '" >' + value + '</option>';
                                }
                            });
                            $('#floor_id').html(html);
                        }else{
                            alert(response.msg);
                        }
                        obj.next().hide();
                    },
                    error: function (jqXHR, exception) {
                        alert("something was wrong")
                        console.log(jqXHR);
                        obj.next().hide();
                    }
                })
            });

            $(document).on('click','.prevbtn',function(){
                let pageNo = $(this).parent().attr('pageNo');
                if(pageNo == 2){
                    fetchFirstStep();
                    $('#second_step').removeClass('step-current');
                }
                else if(pageNo == 3){
                    fetchSecondStep();
                    $('#third_step').removeClass('step-current');
                }
            });

            $(document).on('click','.nextbtn',function(){
                let pageNo = $(this).parent().attr('pageNo');
                if(pageNo == 1){
                    fetchSecondStep();
                }
                else if(pageNo == 2){
                    fetchThirdStep();
                }
            });

            $(document).on('click','.setPilgrimToRoom',function(){
                pilgrim_list = [];
                let pilgrim_id_from_attr = $(this).attr('pilgrim_id');
                pilgrim_list.push(pilgrim_id_from_attr);
            });

            $(document).on('click','.setAllPilgrimToRoom',function(){
                pilgrim_list = [];
                let room_no = $(this).attr('room_no');
                $(`#tbody_${room_no} td div #pilgrim_id`).each(function() {
                    pilgrim_list.push($(this).val());
                });
            });

            $(document).on('click','.removeAllPilgrimFromRoom', async function(){
                let btnObj = $(this);
                let btnContent = btnObj.html();
                btnObj.html('<i class="fa fa-spinner fa-pulse"></i> &nbsp;'+ btnContent);
                btnObj.prop('disabled', true);

                let room_no = $(this).attr('room_no');
                pilgrim_list = await prprPilgrimListForRemove(room_no);

                $.ajax({
                    url: '{{ url('room-allocation/removeFromRoom') }}',
                    type: 'POST',
                    data: {
                        pilgrim_list:pilgrim_list,
                    },
                    success:function(response){
                        if(response.responseCode == 1){
                            $(document).find('#ehajjAddPilgrimModal').modal('hide');
                            alert(response.msg);
                        }else{
                            $(document).find('#ehajjAddPilgrimModal').modal('hide');
                            alert(response.msg);
                        }
                        fetchThirdStep();
                        btnObj.html(btnContent);
                        btnObj.prop('disabled', false);
                    },
                    error: function(jqXHR){
                        alert("something was wrong")
                        console.log(jqXHR);
                        btnObj.html(btnContent);
                        btnObj.prop('disabled', false);
                    }
                });

            });

            $(document).on('click','.removePilgrimFromRoom',function(){
                let pilgrim_id_from_attr = $(this).attr('pilgrim_id');
                let btnObj = $(this);
                let btnContent = btnObj.html();
                btnObj.html('<i class="fa fa-spinner fa-pulse"></i> &nbsp;'+ btnContent);
                btnObj.prop('disabled', true);
                $.ajax({
                    url: '{{ url('room-allocation/removeFromRoom') }}',
                    type: 'POST',
                    data: {
                        pilgrim_list:[pilgrim_id_from_attr],
                    },
                    success:function(response){
                        if(response.responseCode == 1){
                            $(document).find('#ehajjAddPilgrimModal').modal('hide');
                            alert(response.msg);
                        }else{
                            $(document).find('#ehajjAddPilgrimModal').modal('hide');
                            alert(response.msg);
                        }
                        fetchThirdStep();
                        btnObj.html(btnContent);
                        btnObj.prop('disabled', false);
                    },
                    error: function(jqXHR){
                        alert("something was wrong")
                        console.log(jqXHR);
                        btnObj.html(btnContent);
                        btnObj.prop('disabled', false);
                    }
                });
            });

            /*set pilgrim to house*/
            $(document).on('click','.setPilgrimToHouse',function(){
                if(pilgrim_list.length == 0){
                    alert('Pilgrim List not found. Please refresh the page.');
                    return;
                }

                let btnObj = $(this);
                let btnContent = btnObj.html();
                btnObj.html('<i class="fa fa-spinner fa-pulse"></i> &nbsp;'+ btnContent);
                btnObj.prop('disabled', true);

                let room_id = $('.selected_room:checked').val();
                let selectRoomTrObj =  $('.selected_room:checked').closest('tr');
                let availability=selectRoomTrObj.find("td:eq(2)").text();

                if(availability <=0 || pilgrim_list.length > availability){
                    alert('Pilgrim cannot added to this room during unavailability.');
                    btnObj.html(btnContent);
                    btnObj.prop('disabled', false);
                    pilgrim_list = [];
                    $('.selected_room:checked').val('')
                    $(document).find('#ehajjAddPilgrimModal').modal('hide');
                    return;
                }

                $.ajax({
                    url: '{{ url('room-allocation/setToRoom') }}',
                    type: 'POST',
                    data: {
                        room_id:room_id,
                        pilgrim_list:pilgrim_list,
                        house_id:house_id,
                        floor_id:floor_id,
                    },
                    success:function(response){
                        if(response.responseCode == 1){
                           $(document).find('#ehajjAddPilgrimModal').modal('hide');
                           alert(response.msg);
                        }else{
                            $(document).find('#ehajjAddPilgrimModal').modal('hide');
                            alert(response.msg);
                        }
                        fetchThirdStep();
                        btnObj.html(btnContent);
                        btnObj.prop('disabled', false);
                    },
                    error: function(jqXHR){
                        alert("something was wrong")
                        console.log(jqXHR);
                        btnObj.html(btnContent);
                        btnObj.prop('disabled', false);
                    }
                });
            });

        });

        function fetchFirstStep(){
            $.ajax({
                url: '{{ url('room-allocation/first-step') }}',
                type: 'GET',
                success:function(response){
                    $('.actionCon').hide();
                    $('#first_step').addClass('step-current');
                    $('.main-section-card #infoDisplay').show().html(response.html);
                    if(flight_val){
                        $(document).find('#flight_id option[value="'+flight_val+'"]').prop('selected',true);
                        $(document).find('#flight_id').trigger('change');
                    }

                },
                error: function(jqXHR){
                    alert("something was wrong")
                    console.log(jqXHR);
                }
            });
        }

        function fetchSecondStep(){
            let flight_val_inp = ($(document).find('#flight_id').val()) ? $(document).find('#flight_id').val() : flight_val;
            let flight_text_inp = ($(document).find('#flight_id option:selected').text()) ? $(document).find('#flight_id option:selected').text() : flight_text;
            let guide_val_inp = ($(document).find('#guide_id').val()) ? $(document).find('#guide_id').val() : guide_val;
            let guide_text_inp = ($(document).find('#guide_id option:selected').text()) ? $(document).find('#guide_id option:selected').text() : guide_text;
            let guide_pid_inp = ($(document).find('#guide_id option:selected').attr('pid')) ? $(document).find('#guide_id option:selected').attr('pid') : guide_pid;

            if(!flight_val_inp || !guide_val_inp){
                alert('Please select flight and guide.');
                return;
            }

            $('.actionCon').show();
            $('.main-section-card #infoDisplay').hide();

            $.ajax({
                url: "{{ url('room-allocation/second-step') }}",
                type: "POST",
                data: {
                    flight_val:flight_val_inp,
                    guide_val:guide_val_inp
                },
                success: function(response){
                    if(response.responseCode == 1){
                        $('.actionCon').hide();
                        $('#second_step').addClass('step-current');
                        $('.main-section-card #infoDisplay').show().html(response.html);

                        $(document).find('#flight_text').append(flight_text_inp);
                        $(document).find('#guide_text').append(guide_text_inp);
                        $(document).find('#guide_pid').append(guide_pid_inp);
                        flight_val = flight_val_inp;
                        guide_val = guide_val_inp;
                        flight_text = flight_text_inp;
                        guide_text = guide_text_inp;
                        guide_pid = guide_pid_inp;

                        if(house_id){
                            console.log(house_id);
                            $(document).find('#house_id option[value="'+house_id+'"]').prop('selected',true);
                            $(document).find('#house_id').trigger('change');
                        }
                    }else{
                        $('.actionCon').hide();
                        $('#first_step').addClass('step-current');
                        $('.main-section-card #infoDisplay').show();
                        alert(response.msg);
                    }
                },
                error: function (jqXHR, exception) {
                    alert("something was wrong")
                    console.log(jqXHR);
                    obj.next().hide();
                }
            })

        }

        function fetchThirdStep(){
            let house_id_inp = ($(document).find('#house_id').val()) ? $(document).find('#house_id').val() : house_id;
            let house_no_inp = ($(document).find('#house_id option:selected').attr('house_no')) ? $(document).find('#house_id option:selected').attr('house_no') : house_no;
            let house_name_inp = ($(document).find('#house_id option:selected').text()) ? $(document).find('#house_id option:selected').text() : house_name;
            let floor_id_inp = ($(document).find('#floor_id').val()) ? $(document).find('#floor_id').val() : floor_id;
            let floor_no_inp = ($(document).find('#floor_id option:selected').text()) ? $(document).find('#floor_id option:selected').text() : floor_no;

            if(!house_id_inp || !floor_id_inp){
                alert('Please select house and floor.');
                return;
            }

            $('.actionCon').show();
            $('.main-section-card #infoDisplay').hide();

            $.ajax({
                url: "{{ url('room-allocation/third-step') }}",
                type: "POST",
                data: {
                    house_id:house_id_inp,
                    floor_id:floor_id_inp,
                    flight_val:flight_val,
                    guide_val:guide_val
                },
                success: function(response){
                    if(response.responseCode == 1){
                        $('.actionCon').hide();
                        $('#third_step').addClass('step-current');
                        $('.main-section-card #infoDisplay').show().html(response.html);
                        house_id = house_id_inp;
                        floor_id = floor_id_inp;

                        house_no = house_no_inp;
                        house_name = house_name_inp;
                        floor_no = floor_no_inp;

                        $('#flight_text').text(flight_text);
                        $('#guide_name').text(guide_text);
                        $('#guide_pid').text(guide_pid);

                        $('#selected_house_no').text(house_no_inp);
                        $('#selected_house_name').text(house_name_inp);
                        $('#selected_floor_no').text(floor_no_inp);
                    }else{
                        $('.actionCon').hide();
                        $('#second_step').addClass('step-current');
                        $('.main-section-card #infoDisplay').show();
                        alert(response.msg);
                    }
                },
                error: function (jqXHR, exception) {
                    alert("something was wrong")
                    console.log(jqXHR);
                    obj.next().hide();
                }
            })

        }

        function prprPilgrimListForRemove(room_no) {
            return new Promise((resolve, reject) => {
                let pilgrim_list = [];
                $(`#tbody_${room_no} td div #pilgrim_id`).each(function() {
                    pilgrim_list.push($(this).val());
                });
                resolve(pilgrim_list);
            });
        }
    </script>
@endsection
