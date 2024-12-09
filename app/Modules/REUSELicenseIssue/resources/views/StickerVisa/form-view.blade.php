<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);

$prefix = '';
if ($type[0] == 5) {
    //$prefix = 'client';
    $prefix = 'client';
}

?>

<div class="dash-content-inner">
    <div class="src-hajj-tracking-number">
        <div class="dash-list-table py-4 toggle_data_talbe" style="display:block;" >
            <div class="card">
                <div class="card-header">
                    স্টিকার ভিসার তথ্য
                </div>
                <div class="card-body">
                    <fieldset>

                        <div class="row">
                            <label for="name" class="col-lg-2 text-left">দলের ধরণ:</label>
                            <div class="col-lg-8">
                                <p id="agency_div">{{$stickerVisaData->team_type}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter Agency no" name="agency" id="agency" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-2 text-left">দলের প্রকার:</label>
                            <div class="col-lg-8">
                                <p id="lisence_no_div">{{$stickerVisaData->sub_team_type}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter license no" name="license_no" id="license_no" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-2 text-left">জিও নাম্বার:</label>
                            <div class="col-lg-8">
                                <p id="name_div">{{$stickerVisaData->go_number}}</p>
                                <input type="hidden" class="form-control" name="full_name_english"  id="name" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-2 text-left">জিওতে সদস্য সংখ্যা:</label>
                            <div class="col-lg-8" >
                                <p id="father_name_div">{{$stickerVisaData->go_member}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter Father name" name="father_name" id="father_name" readonly>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    স্টিকার ভিসা হজযাত্রীদের তথ্য
                </div>
                <div class="card-body">
                    <table class="table dt-responsive table-bordered">
                        <thead>
                        <tr>
                            <th>জিও ক্রমিক নং</th>
                            <th>নাম</th>
                            <th>জন্মতারিখ</th>
                            <th>লিঙ্গ</th>
                            <th>এনআইডি/জন্ম নিবন্ধন নং</th>
                            <th>পাসপোর্ট নং</th>
                            <th>পাসপোর্টের জন্মতারিখ</th>
                            <th>মোবাইল নং</th>
                        </tr>
                        </thead>
                        <tbody id="stickerVisaMembersList">
                          @forelse($stickerPilgrimsData as $item)
                           <tr>
                               <td>{{ $item->go_serial_no}}</td>
                               <td>{{ $item->name}}</td>
                               <td>{{ date('d-M-Y',strtotime($item->dob))  }}</td>
                               <td>{{ $item->gender}}</td>
                               <td>{{ $item->identity_no}}</td>
                               <td>{{ $item->passport_no}}</td>
                               <td>{{ date('d-M-Y',strtotime($item->passport_dob))  }}</td>
                               <td>{{ $item->mobile_no}}</td>
                           </tr>
                          @empty
                              <tr>
                                  <td colspan="6" style="text-align: center" >No Pilgrims Found</td>
                              </tr>
                          @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    $(document).ready(function() {
        $('#gender_change').prop('disabled', true);
    });
</script>

