

    <div class="row">
        <div class="col-md-4 form-group">
            @php
                $process_typeid_2 =  \App\Libraries\Encryption::encodeId($process_type_id);
            @endphp
            <label>Proposed Fight Date and Code</label>
            <input type="text" id="flight_code" class="form-control" readonly/>
            <input type="hidden" id="process_id" name="process_type_id" class="form-control" value="{{$process_typeid_2}}"/>
            <input type="hidden" id= "flight_id" class="form-control" value="{{$pilgrim_data_list->listing_id}}"/>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="exampleFormControlSelect1">
                    Select Hajj Flight</label>
                <select class="form-control" name="flight_id" id="listing_id">

                </select>
                <input type="hidden" id="total_capacity" value=""/>
                <input type="hidden" id="remaining" value=""/>
            </div>
        </div>
        <div class="col-md-4 form-group" >
            <label>Select Trip</label>
            <select class="form-control required valid" id="select_trip" name="trip_id">
                <option selected="selected" value="">Select Below</option>
            </select>

        </div>
    </div>
    <style>
        #FormDiv {
            width: 100%;
        }
    </style>

{{--<table>--}}
{{--    <tr>--}}
{{--        <td><p id="flight_code">{{$pilgrim_data_list->listing_id}}</p></td>--}}
{{--        <td>--}}
{{--            <select class="form-control required valid" id="select_trip" name="trip_id">--}}
{{--                <option value="option1">Option 1</option>--}}
{{--                <option value="option2">Option 2</option>--}}
{{--                <option value="option3">Option 3</option>--}}
{{--            </select>--}}
{{--        </td>--}}
{{--    </tr>--}}
{{--</table>--}}


<script>
    $(document).ready(function() {

        let base_url = window.location.origin + '/process/action/getTripList/' + $('#process_id').val();
        let flight_id = $('#flight_id').val();
        $.ajax({
            url: base_url,
            type: 'POST',
            dataType: 'json',
            data: {'flight_id':flight_id},
            success: function(res) {
                // Clear the select element
                $('#select_trip').empty();

                // Add each option to the select element
                $.each(res.data.data.trips, function(index, value) {
                    $('#select_trip').append('<option value="' + value.id + '">' + value.trip_no + '</option>');
                });
                // $('#flight_code').val(res.data.data.flight.flight_code)

            },
            error: function(xhr, status, error) {
                console.error(xhr);
            }
        });
    });
</script>
<script>
    $(document).ready(function() {

        let base_url = window.location.origin + '/process/action/getFlightList/' + $('#process_id').val();
        let flight_id = $('#flight_id').val();

        $.ajax({
            url: base_url,
            type: 'POST',
            dataType: 'json',
            data: {'flight_id':flight_id},
            success: function(res) {

                // Clear the select element
                $('#listing_id').empty();

                // Add each option to the select element

                $.each(res.data.data, function(index, value) {
                    if(value.id == flight_id){
                        $('#listing_id').append('<option value="' + value.id + '" selected>' + value.Flight + '</option>');
                        $('#flight_code').val(value.Flight);
                    }else{
                        $('#listing_id').append('<option value="' + value.id + '">' + value.Flight + '</option>');
                    }
                });
                // $('#flight_code').val(res.data.data.flight.flight_code)
            },
            error: function(xhr, status, error) {
                console.error(xhr);
            }
        });

    });
</script>




<script>
        // jQuery code that handles the onchange event and triggers an AJAX call
        $(document).ready(function() {
            let base_url = window.location.origin + '/process/action/getTripList/' + $('#process_id').val();
            let flight_id = $('#listing_id').val();

            $('#listing_id').on('change', function() {
                var flight_id = $(this).val();

                $.ajax({
                    url: base_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {'flight_id':flight_id},
                    success: function(res) {
                        // Clear the select element
                        $('#select_trip').empty();

                        // Add each option to the select element

                        $.each(res.data.data.trips, function(index, value) {
                            $('#select_trip').append('<option value="' + value.id + '">' + value.trip_no + '</option>');
                        });
                        $('#flight_code').val(res.data.data.flight.flight_code)
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr);
                    }
                });
            });
        });

    </script>




