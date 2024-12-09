<script setup>
import {ref, onMounted} from 'vue';
import { useRoute, useRouter } from 'vue-router';
import moment from 'moment';
import axios from "axios";

const router = useRouter();
const route = useRoute();
const id = ref(null);
const indexData = ref({
    occupationList : '',
    districtList : '',
    bankList : '',
    bankDistrictList : '',
    accountHolderType : '',
    govtServiceList : '',
    nationalitys : '',
});
const imagePreview = ref({
    profile: '',
    dob: ''
});

const bankBranchList = ref({});
const isGovtJob = ref(false);

const pilgrimData = ref({
    resident: 'Bangladeshi',
    nationality2: '0',
    gender: 'male',
    identity: 'NID',
    birth_date: '',
    passport_type: 'E-PASSPORT',
    passport_number: '',
    nid_number: '',
    brn: "",

    name_bn: '',
    name_en: '',
    father_name_bn: '',
    mother_name_bn: '',
    father_name_en: '',
    mother_name_en: '',
    occupation: '0',
    mobile: '',
    marital_status: '',
    spouse_name: '',

    user_code: '',
    created_by: '',
    reg_created_by: '',

    permanent_post_code: '',
    permanent_district_id: '0',
    permanent_police_station: '',
    permanent_address: '',
    present_post_code: '',
    present_district_id: '0',
    present_police_station: '',
    present_address: '',

    refund_account_type: '',
    refund_account_name: '',
    refund_account_number: '',
    refund_bank_id: '0',
    refund_branch_district: '0',
    refund_routing_no: '0',

    nationalId_img: '',
    pilgrim_img: '',
    dob_img: '',
    serviceGrade: '0',
    designation: '',
});

const setPilgrimData = (pilgrim) => {
    pilgrimData.value.resident = pilgrim.resident;
    pilgrimData.value.gender = pilgrim.gender;
    pilgrimData.value.identity = pilgrim.identity;
    pilgrimData.value.birth_date = pilgrim.birth_date;
    pilgrimData.value.passport_type = pilgrim.pp_global_type;
    pilgrimData.value.passport_number = pilgrim.passport_no;
    pilgrimData.value.nid_number = pilgrim.national_id;

    pilgrimData.value.name_bn = pilgrim.full_name_bangla;
    pilgrimData.value.name_en = pilgrim.full_name_english;
    pilgrimData.value.father_name_bn = pilgrim.father_name;
    pilgrimData.value.mother_name_bn = pilgrim.mother_name;
    pilgrimData.value.father_name_en = pilgrim.father_name_english;
    pilgrimData.value.mother_name_en = pilgrim.mother_name_english;
    pilgrimData.value.occupation = pilgrim.occupation;
    pilgrimData.value.mobile = pilgrim.mobile;
    pilgrimData.value.marital_status = pilgrim.marital_status;
    pilgrimData.value.created_by = pilgrim.created_by;
    pilgrimData.value.reg_created_by = pilgrim.reg_created_by;

    pilgrimData.value.permanent_post_code = pilgrim.per_post_code;
    pilgrimData.value.permanent_district_id = pilgrim.per_district_id;
    pilgrimData.value.permanent_police_station = pilgrim.per_police_station;
    pilgrimData.value.permanent_address = pilgrim.per_village_ward;

    pilgrimData.value.present_post_code = pilgrim.post_code;
    pilgrimData.value.present_district_id = pilgrim.district_id;
    pilgrimData.value.present_police_station = pilgrim.police_station;
    pilgrimData.value.present_address = pilgrim.village_ward;
    pilgrimData.value.tracking_no = pilgrim.tracking_no;

    pilgrimData.value.nationality2 = pilgrim.nationality2;
    pilgrimData.value.designation = pilgrim.designation;
    pilgrimData.value.serviceGrade = pilgrim.govt_service_grade;
    pilgrimData.value.spouse_name = pilgrim.spouse_name;
    pilgrimData.value.nationality2 = pilgrim.nationality2;
    pilgrimData.value.brn = pilgrim.birth_certificate;
    pilgrimData.value.group_payment_id = pilgrim.group_payment_id;
}
const fetchPilgrimDataFromApi = async (pilgrimId) => {
    const postData = {
        "pilgrimId": pilgrimId,
    }
    const response = await axios.post(`/pilgrim/pre-reg/get-pilgrim-data`, postData);
    if(response.data.responseCode !== 1) {
        alert('Pilgrim Info not found');
        router.push({ name: 'PreRegPilgrimsList' });
    }
    /*if(response.data.editMood > 0) {
        alert('Can\'t Edit this Pilgrim due to Payment or Permission');
        router.push({ name: 'PreRegPilgrimsList' });
    }*/
    pilgrimData.value.user_code = response.data.user_code;
    setPilgrimData(response.data.pilgrim);
    imagePreview.value.profile = response.data.profile_pic;
    imagePreview.value.dob = response.data.birthCertificate;
    pilgrimData.value.refund_account_type = response.data.refundInfo.owner_type;
    pilgrimData.value.refund_account_name = response.data.refundInfo.account_holder_name;
    pilgrimData.value.refund_account_number = response.data.refundInfo.account_number;
    pilgrimData.value.refund_bank_id = response.data.refundInfo.bank_id;
    pilgrimData.value.refund_branch_district = response.data.refundInfo.dist_code;
    pilgrimData.value.refund_routing_no = response.data.refundInfo.bb_routing_id;
    pilgrimData.value.editMood = response.data.editMood;
    getBankBranch();
}
const convertObjectToArray = (obj) => {
    return Object.entries(obj).map(([id, text]) => ({ id, text }));
};
const getIndexData = async () => {
    try{
        const response = await axios.get(`/pilgrim/pre-reg/get-index-data`);
        if(response.data.status === true){
            indexData.value.occupationList = response.data.data.occupation;
            indexData.value.districtList = response.data.data.district;
            indexData.value.bankList = response.data.data.bank_list;
            indexData.value.bankDistrictList = response.data.data.district_list;
            indexData.value.accountHolderType = response.data.data.account_owner_type;
            indexData.value.govtServiceList = response.data.data.is_govt;
            indexData.value.nationalitys = response.data.data.nationalitys;

        }
    } catch (error) {
        console.error("Error fetching data: ", error);
    }
}

onMounted(async () => {
    const pilgrimId = route.params.id;
    id.value = pilgrimId;
    await fetchPilgrimDataFromApi(pilgrimId);
    await getIndexData();
    await handleOccupationChange();
});

const handleOccupationChange = async (value = 0) => {
    if(indexData.value.govtServiceList[pilgrimData.value.occupation]) {
        isGovtJob.value = true;
    } else {
        isGovtJob.value = false;
    }
}
const getBankBranch = async () => {
    try {
        if (pilgrimData.value.refund_bank_id && pilgrimData.value.refund_branch_district) {
            const bankId = pilgrimData.value.refund_bank_id;
            const branchDistrictId = pilgrimData.value.refund_branch_district;
            const response = await axios.get(`/pilgrim/pre-reg/get-bank-branch/${bankId}/${branchDistrictId}`);
            bankBranchList.value = response.data.data;
        } else {
            console.warn("Refund bank ID or branch district is not set.");
        }
    } catch (error) {
        console.error("Error fetching bank branch list:", error);
    }
}

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
                            </div>
                            <div class="col-md-6">
                                <div  v-if="pilgrimData.identity !== 'PASSPORT'" class="row mb-3">
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
                                        <div class="col-md-7">{{ pilgrimData.serviceGrade ? pilgrimData.serviceGrade : ''  }}</div>
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
                                <div class="col-md-7">{{ pilgrimData.permanent_district_id ? indexData.districtList[pilgrimData.permanent_district_id] : '' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Police Station</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.permanent_police_station }}</div>
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
                                <div class="col-md-7">{{ pilgrimData.present_district_id ? indexData.districtList[pilgrimData.present_district_id] : '' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Police Station</strong></div>
                                <div class="col-md-1"><strong>:</strong></div>
                                <div class="col-md-7">{{ pilgrimData.present_police_station }}</div>
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
                <div class="buttons-container float-right" v-if="pilgrimData.editMood === 0 && pilgrimData.group_payment_id === 0 && (pilgrimData.user_code == pilgrimData.created_by || pilgrimData.user_code == pilgrimData.reg_created_by)">
                    <router-link :to="{ name: 'EditPreRegPilgrim', params: { id: id } }"
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
</style>
