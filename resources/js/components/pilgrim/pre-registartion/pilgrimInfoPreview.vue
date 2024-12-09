<script setup>
import {defineProps, defineEmits} from 'vue';
import moment from 'moment';

const {
    pilgrimData,
    currentStep,
    occupationList,
    districtList,
    bankList,
    bankDistrictList,
    permanentPoliceStationList,
    currentPoliceStationList,
    bankBranchList,
    imagePreview,
    isGovt,
    govtServiceGradeObj,
    nationalityObj
    } = defineProps([
    'pilgrimData',
    'currentStep',
    'occupationList',
    'districtList',
    'bankList',
    'bankDistrictList',
    'permanentPoliceStationList',
    'currentPoliceStationList',
    'bankBranchList',
    'imagePreview',
    'isGovt',
    'govtServiceGradeObj',
    'nationalityObj',
    ]);

</script>

<template>
    <div class="card-body previewCard">
        <div class="preview-container">
            <div class="card card-magenta border border-magenta">
                <div class="card-header" style="background-color: #D9EDF7 !important;">
                    <h5 class="card-title pt-2 pb-2" style="color: black !important;">Pilgrim Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Resident</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.resident }}</div>
                            </div>
                            <div class="row mb-3" v-if="pilgrimData.resident !== 'Bangladeshi'">
                                <div class="col-md-4"><strong>Nationality</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ nationalityObj[pilgrimData.nationality2] }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Gender</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.gender }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Birth Date</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ moment(pilgrimData.birth_date).format('DD-MMM-yyyy') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Identity</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.identity }}</div>
                            </div>
                            <div class="row mb-3" v-if="pilgrimData.identity === 'NID'">
                                <div class="col-md-4"><strong>NID Number</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.nid_number }}</div>
                            </div>
                            <div v-else-if="pilgrimData.identity === 'PASSPORT'">
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Passport Type</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.passport_type }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Passport Number</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.passport_number }}</div>
                                </div>
                            </div>
                            <div class="row mb-3" v-if="pilgrimData.identity === 'DOB'">
                                <div class="col-md-4"><strong>Birth Registration Number</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.brn }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3" v-if="pilgrimData.identity !== 'PASSPORT'">
                                <div class="col-md-4"><strong>Name (BN)</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.name_bn }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Name (EN)</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.name_en }}</div>
                            </div>
                            <template v-if="pilgrimData.identity === 'PASSPORT'">
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Father's Name (EN)</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.father_name_en }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Mother's Name (EN)</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.mother_name_en }}</div>
                                </div>
                            </template>
                            <template v-else >
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Father's Name (BN)</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.father_name_bn }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Mother's Name (BN)</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.mother_name_bn }}</div>
                                </div>
                            </template>

                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Occupation</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ occupationList[pilgrimData.occupation] }}</div>
                            </div>
                            <div v-if="isGovt">
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Designation</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.designation ? pilgrimData.designation : ''  }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Govt Service Grade</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.serviceGrade ? govtServiceGradeObj[pilgrimData.serviceGrade] : ''  }}</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Mobile</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.mobile }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Marital Status</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.marital_status }}</div>
                            </div>
                            <div v-if="pilgrimData.marital_status === 'Married'">
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Spouse Name</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.spouse_name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-magenta border border-magenta">
                <div class="card-header" style="background-color: #D9EDF7 !important;">
                    <h5 class="card-title pt-2 pb-2" style="color: black !important;">Pilgrim Address</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Post Code</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.permanent_post_code }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>District</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ districtList[pilgrimData.permanent_district_id] }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Police Station</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ permanentPoliceStationList[pilgrimData.permanent_police_station_id] }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Address</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.permanent_address }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Post Code</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.present_post_code }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>District</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ districtList[pilgrimData.present_district_id] }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Police Station</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ currentPoliceStationList[pilgrimData.present_police_station_id] }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Address</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.present_address }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-magenta border border-magenta">
                <div class="card-header" style="background-color: #D9EDF7 !important;">
                    <h5 class="card-title pt-2 pb-2" style="color: black !important;">Refundable Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Account Holder Type</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.refund_account_type }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Account Number</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.refund_account_number }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Account Holder Name</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.refund_account_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Bank Name</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ bankList[pilgrimData.refund_bank_id] }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Branch District</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ bankDistrictList[pilgrimData.refund_branch_district] }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Branch & Routing No</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ bankBranchList[pilgrimData.refund_routing_no] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-magenta border border-magenta">
                <div class="card-header" style="background-color: #D9EDF7 !important;">
                    <h5 class="card-title pt-2 pb-2" style="color: black !important;">Pilgrim Documents</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p>
                                <strong>Pilgrim Photo: </strong>
                                <div class="mt-3" v-if="imagePreview.profile">
                                    <img :src="imagePreview.profile" alt="Image preview" style="max-width: 100%; height: auto;">
                                </div>
                            </p>
                        </div>
                        <div class="col-md-4" v-if="pilgrimData.identity === 'DOB'">
                            <p>
                                <strong>Birth Certificate: </strong>
                                <div class="mt-3" v-if="imagePreview.dob">
                                    <img :src="imagePreview.dob" alt="Image preview" style="max-width: 100%; height: auto;">
                                </div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <span class="text-danger"><b>উপরোক্ত সকল তথ্য মিলিয়ে দেখার পর সকল তথ্য ঠিক থাকলে "সাবমিট" বাটনে ক্লিক করুন। নতুন হজযাত্রী আবেদন করতে হলে  নতুন হজযাত্রী যুক্ত করুন এ ক্লিক করুন। পেমেন্ট করার জন্য, ভাউচার তৈরী করে পেমেন্ট সম্পন্ন করুন।</b></span>
        </div>
    </div>
</template>

<style scoped>
.preview-container {
    padding: 20px;
}

.buttons-container {
    margin-top: 20px;
    text-align: right;
}

@media only screen and (max-width: 600px) {
    .previewCard {
        padding: 0px;
    }
    .preview-container {
        padding: 0px;
    }
}
</style>



