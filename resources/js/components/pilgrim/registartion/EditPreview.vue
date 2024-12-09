<script setup>
import {inject, onMounted, ref} from 'vue';
import moment from "moment";
import axios from "axios";

// const indexData = inject('indexData');
const pilgrimData = inject('pilgrimData');
const currentPoliceStationList = inject('currentPoliceStationList');
const permanentPoliceStationList = inject('permanentPoliceStationList');
const bankBranchList = inject('bankBranchList');
const isGovtJob = inject('isGovtJob');
const {imagePreview} = defineProps(['imagePreview']);

const indexData = ref({
    occupationList : '',
    districtList : '',
    bankList : '',
    bankDistrictList : '',
    accountHolderType : '',
    govtServiceList : '',
    nationalitys : '',
    thanaList : '',
});
const getIndexData = async () => {
    try {
        const response = await axios.get(`/pilgrim/reg/get-reg-index-data`);
        if(response.data.status === true) {
            indexData.value.occupationList = response.data?.data?.occupation;
            indexData.value.districtList = response.data?.data?.district;
            indexData.value.bankList = response.data?.data?.bank_list;
            indexData.value.bankDistrictList = response.data?.data?.district_list;
            indexData.value.accountHolderType = response.data?.data?.account_owner_type;
            indexData.value.govtServiceList = response.data?.data?.is_govt;
            indexData.value.nationalitys = response.data?.data?.nationalitys;
            indexData.value.thanaList = response.data?.data?.thana;
        }
    } catch (error) {
        console.error("Error fetching data: ", error);
    }
}
onMounted(async () => {
    await getIndexData();
});
</script>

<template>
    <div class="card-body">
        <div class="preview-container">
            <div class="card card-magenta border border-magenta">
                <div class="card-header" style="background-color: #D9EDF7 !important;">
                    <h5 class="card-title pt-2 pb-2" style="color: black !important;">Pilgrim Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Tracking No</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.tracking_no }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Resident</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.resident }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Gender</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.gender }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Birth Date</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ moment(pilgrimData.birth_date).format('DD-MMM-YYYY') }}</div>
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
                            <div class="row mb-3" v-else>
                                <div class="col-md-4"><strong>Birth Registration Number</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.brn }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Management</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.is_govt }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
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
                                <div class="col-md-7">{{ pilgrimData.occupation ? indexData.occupationList[pilgrimData.occupation] : '' }}</div>
                            </div>
                            <template v-if="isGovtJob">
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Designation</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.designation ? pilgrimData.designation : ''  }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Govt Service Grade</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.serviceGrade }}</div>
                                </div>
                            </template>
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
                            <template v-if="pilgrimData.marital_status === 'Married'">
                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Spouse Name</strong></div>
                                    <div class="col-md-1"><strong>:</strong></div>
                                    <div class="col-md-7">{{ pilgrimData.spouse_name }}</div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-magenta border border-magenta">
                <div class="card-header" style="background-color: #D9EDF7 !important;">
                    <h5 class="card-title pt-2 pb-2" style="color: black !important;">Passport Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-bold text-center mb-3">Passport General Information</div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Passport Type</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_type }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Passport No</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.passport_no }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Date of Issue</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_issue_date }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Date of Expiry</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_exp_date }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-bold text-center mb-3">Passport Permanent Address (In Bangladesh)</div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Post Code</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_per_post_code }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>District</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_per_district ? indexData.districtList[pilgrimData.pass_per_district] : '' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Police Station</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_per_thana ? indexData.thanaList[pilgrimData.pass_per_thana] : '' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Address</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_per_village }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="text-bold text-center mb-3">Passport Present Address (In Bangladesh)</div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Post Code</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_post_code }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>District</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_district ? indexData.districtList[pilgrimData.pass_district] : '' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Police Station</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_thana ? indexData.thanaList[pilgrimData.pass_thana] : '' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Address</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.pass_village }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-magenta border border-magenta">
                <div class="card-header" style="background-color: #D9EDF7 !important;">
                    <h5 class="card-title pt-2 pb-2" style="color: black !important;">Others Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-bold text-center mb-3">হজযাত্রী যেখানে ট্রেনিং করতে চান তার  তথ্য</div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>District</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.training_district ? indexData.districtList[pilgrimData.training_district] : '' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Training Venue</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.training_venue ? indexData.districtList[pilgrimData.training_district] : '' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-bold text-center mb-3">হজযাত্রী যে জেলায় ভ্যাকসিন নিতে চায় তার তথ্য</div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>District</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.vaccine_district ? indexData.districtList[pilgrimData.vaccine_district] : '' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="text-bold text-center mb-3">সৌদি আরবে অবস্থানকালে জরুরী প্রয়োজনে বাংলাদেশে যে ব্যক্তির সঙ্গে যোগাযোগ করবেন তার তথ্য</div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Name</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.reliable_bd_person }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Mobile</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.reliable_bd_person_mobile }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-bold text-center mb-3">জরুরী প্রয়োজনে সৌদি আরবে যে ব্যক্তির (যদি থাকে) সঙ্গে যোগাযোগ করবেন তার তথ্য</div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Name</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.reliable_ksa_person }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Mobile</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.reliable_ksa_person_mobile }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="text-bold text-center mb-3">সৌদি আরবে মৃত্যুবরণ করলে টাকা ও মালামাল যিনি গ্রহণ করবেন তার তথ্য</div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Name</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.nominee }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Mobile</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.nominee_mobile }}</div>
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
                                <div class="col-md-7">{{ pilgrimData.refund_bank_id ? indexData.bankList[pilgrimData.refund_bank_id] : ''}}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Branch District</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.refund_branch_district ? indexData.bankDistrictList[pilgrimData.refund_branch_district] : '' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Branch & Routing No</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.refund_routing_no ? bankBranchList[pilgrimData.refund_routing_no] : '' }}</div>
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
                                <strong>Photo: </strong>
                                <div class="mt-3" v-if="imagePreview.profile">
                                    <img :src="imagePreview.profile" alt="Image preview" style="max-width: 100%; height: auto;">
                                </div>
                            </p>
                        </div>
                        <template v-if="pilgrimData.identity === 'DOB'">
                            <div class="col-md-4">
                                <p>
                                    <strong>Birth Certificate: </strong>
                                    <div class="mt-3" v-if="imagePreview.dob">
                                        <img :src="imagePreview.dob" alt="Image preview" style="max-width: 100%; height: auto;">
                                    </div>
                                </p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="buttons-container float-right" v-if="pilgrimData.editMood === 0">
                    <router-link :to="{ name: 'EditRegPilgrim', params: { id: id } }"
                                 class="btn btn-primary">
                        <i class="fa fa-edit"></i> Edit
                    </router-link>
                </div>
            </div>
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
.image-preview {
    width: 150px;
    border: 2px solid black;
    margin-top: 5px;
}
</style>
