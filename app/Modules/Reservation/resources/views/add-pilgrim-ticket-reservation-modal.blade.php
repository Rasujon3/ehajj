<style>
#addPilgrimModal>.modal-lg{
    max-width: 1000px;
}
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel">Add pilgrim to Ticket Requisition</h4>
</div>
<div class="modal-body">
    <div class="ehajj-list-table">
        <div class="table-responsive">
            <table id="pilgrim_list1" class="table table-striped table-bordered dt-responsive nowrap"
                    cellspacing="0"
                    width="100%">
                <thead>
                <tr>
                    <th><div class="checkbox"><label><input type="checkbox" value="" class="pilgrim_id_all" id="allPilgrim"></label></div></th>
                    <th>SL no</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Date Of Birth</th>
                    <th>Passport Number</th>
                    <th>Phone Number</th>

                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div><!-- /.table-responsive -->
    </div>
</div>
<div class="modal-footer">
    <span class="footer_text"></span>
    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
    <button type="button" value="Done" class="btn btn-sm btn-primary add_pilgrim_to_ticket_reservation disabled"
            id="{!! $processRefId !!}">Add
    </button>

</div>

{{-- from figma desigh --}}
{{-- <div class="modal-header">
    <h3>Add Individual</h3>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span class="icon-close-modal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <g clip-path="url(#clip0_2216_3720)">
                    <path d="M0.292786 0.292105C0.480314 0.104634 0.734622 -0.000681838 0.999786 -0.000681838C1.26495 -0.000681838 1.51926 0.104634 1.70679 0.292105L7.99979 6.5851L14.2928 0.292105C14.385 0.196594 14.4954 0.120412 14.6174 0.0680032C14.7394 0.0155942 14.8706 -0.011992 15.0034 -0.0131458C15.1362 -0.0142997 15.2678 0.0110021 15.3907 0.0612829C15.5136 0.111564 15.6253 0.185817 15.7192 0.27971C15.8131 0.373602 15.8873 0.485254 15.9376 0.608151C15.9879 0.731047 16.0132 0.862727 16.012 0.995506C16.0109 1.12829 15.9833 1.25951 15.9309 1.38151C15.8785 1.50351 15.8023 1.61386 15.7068 1.7061L9.41379 7.9991L15.7068 14.2921C15.8889 14.4807 15.9897 14.7333 15.9875 14.9955C15.9852 15.2577 15.88 15.5085 15.6946 15.6939C15.5092 15.8793 15.2584 15.9845 14.9962 15.9868C14.734 15.9891 14.4814 15.8883 14.2928 15.7061L7.99979 9.4131L1.70679 15.7061C1.51818 15.8883 1.26558 15.9891 1.00339 15.9868C0.741189 15.9845 0.490376 15.8793 0.304968 15.6939C0.11956 15.5085 0.0143908 15.2577 0.0121124 14.9955C0.00983399 14.7333 0.110628 14.4807 0.292786 14.2921L6.58579 7.9991L0.292786 1.7061C0.105315 1.51858 0 1.26427 0 0.999105C0 0.73394 0.105315 0.479632 0.292786 0.292105Z" fill="black"/>
                </g>
                <defs>
                    <clipPath id="clip0_2216_3720">
                        <rect width="16" height="16" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
        </span>
    </button>
</div>
<div class="modal-body">
    <div class="modal-src">
        <div class="form-group">
            <label>Search by Tracking, Passport, PID Number</label>
            <input type="text" class="form-control" placeholder="Search by Tracking, Passport, PID Number">
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="btn-group-center">
        <button class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
        <button class="btn btn-primary" type="submit">
            <span class="btn-text">Add</span>
        </button>
    </div>
</div> --}}

<input type="hidden" name="_token" value="{{ csrf_token() }}">

@include('partials.datatable-js')
<script type="text/javascript">
    $(document).ready(function () {

        $(document.body).find('#pilgrim_list1').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{url("reservation/get-pilgrim-list-flight-date")}}',
                data: function (d) {
                    d._token = $('input[name="_token"]').val();
                    d.requisition_master_id = '{{ $processRefId }}';
                    d.flight_slot_id = '{{$flight_slot_id}}';
                }
            },
            columns: [
                {data: 'action', name: 'action', orderable: false, searchable: false},
                {
                    data: null,
                    name: 'serial_number',
                    orderable: false,
                    searchable: false
                },
                {data: 'full_name_english', name: 'full_name_english'},
                {data: 'gender', name: 'gender'},
                {data: 'birth_date', name: 'birth_date'},
                {data: 'passport_no', name: 'passport_no'},
                {data: 'mobile', name: 'mobile'}
            ],
            "rowCallback": function(row, data, index) {
                var api = this.api();
                var startIndex = api.page.info().start;
                var counter = startIndex + index + 1;
                $('td:eq(1)', row).html(counter);
            },
            "aaSorting": []
        });

        function addBtnAction() {
            var selected = false;
            $('.pilgrim_id:checked').each(function (i, checkbox) {
                selected = true;
            });
            if (selected) {
                $(".add_pilgrim_to_ticket_reservation").removeClass('disabled').prop('disabled', false);
            } else {
                $(".add_pilgrim_to_ticket_reservation").addClass('disabled').prop('disabled', true);
            }
        }

        $('#allPilgrim').on('change', function(event) {
            if($(this).prop('checked')) {
                $('.pilgrim_id').prop('checked', true);
                addBtnAction();
            } else {
                $('.pilgrim_id').prop('checked', false);
                addBtnAction();
            }
        });

        $("body").on('click', '.pilgrim_id', addBtnAction);

        $(".add_pilgrim_to_ticket_reservation").on('click', function () {
            var no_of_pilgrim = '{{ !empty($noOfPilgrim) ? $noOfPilgrim : 0 }}';

            $(".add_pilgrim_to_ticket_reservation").prop("disabled", true);
            var _token = $('input[name="_token"]').val();
            var ticket_requisition_id = $(this).attr('id');
            var pilgrims_id = [];
            var selected = false;
            /* $('.pilgrim_id:checked').each(function (i, checkbox) {
                pilgrims_id[i] = checkbox.value;
                selected = true;
            }); */
            $('#pilgrim_list1').DataTable().$('input.pilgrim_id:checked').each(function(i, checkbox) {
                pilgrims_id[i] = $(this).val();
                selected = true;
            });

            if(pilgrims_id.length > parseInt(no_of_pilgrim)){
                alert('You can add maximum '+ no_of_pilgrim +' pilgrim in this slot');
                $(".add_pilgrim_to_ticket_reservation").prop("disabled", false);
                return false;
            }

            if (!selected) {
                alert("Please select pilgrim");
                return false;
            }

            $.ajax({
                url: '{{url('/reservation/add-pilgrim-to-ticket-reservation')}}',
                type: 'POST',
                data: {
                    _token: _token,
                    pilgrims_id: pilgrims_id,
                    ticket_requisition_id: ticket_requisition_id,
                    flight_date: '{{ !empty($flightDate) ? \App\Libraries\Encryption::encode($flightDate) : null }}',
                    flight_code: '{{ !empty($flightCode) ? \App\Libraries\Encryption::encode($flightCode) : null }}',
                    pnr_number: '{{ !empty($pnrNumber) ? \App\Libraries\Encryption::encode($pnrNumber) : null }}',
                    no_Of_pilgrim: '{{ !empty($noOfPilgrim) ? \App\Libraries\Encryption::encode($noOfPilgrim) : 0 }}',
                    flight_slot_id: '{{$flight_slot_id}}',
                },
                dataType: 'json',
                success: function (response) {
                    if (response.responseCode === 1) {
                        $('.footer_text').html('<span style="color:green;">Pilgrim added successfully.</span>');
                        $('#addPilgrimModal' + ' .close').click();
                        // $('#voucher_no_search').trigger('click');
                        location.reload();

                    } else {
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                beforeSend: function (xhr) {
                    console.log('before send');
                },
                complete: function () {
                    //completed
                    console.log('completed');
                }
            });
        });
    });
</script>

@section('footer-script')
@endsection
