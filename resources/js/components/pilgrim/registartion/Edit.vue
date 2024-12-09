<script setup>
import {ref, onMounted, provide, reactive, watch,computed} from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';
import { useStore } from 'vuex';

import Step2 from "./Step2.vue";
import Step3 from "./Step3.vue";
import Step4 from "./Step4.vue";
import Step5 from "./Step5.vue";
import Indicator from "./Indicator.vue";
import EditPreview from "./EditPreview.vue";
import axios from "axios";
import moment from "moment/moment";
import Step1 from "./Step1.vue";

const store = useStore();
const router = useRouter();
const route = useRoute();
const toast = useToast();
const id = ref(null);
const trackingNo = ref(null);
const currentStep = ref(1);
const indexData = ref({});
const pilgrimData = ref({
    resident: 'Bangladeshi',
    nationality2: '0',
    gender: 'male',
    identity: 'NID',
    birth_date: '',
    passport_type: 'E-PASSPORT',
    passport_number: '',
    nid_number: '',
    brn: '',
    tracking_no: '',
    is_govt: '',

    name_bn: '',
    name_en: '',
    father_name_bn: '',
    mother_name_bn: '',
    father_name_en: '',
    mother_name_en: '',
    occupation: '',
    mobile: '',
    marital_status: '',
    is_registrable: 0,

    pass_name: '',
    pass_dob: '',
    passport_no: '',
    pass_type: '',
    pass_issue_date: '',
    pass_exp_date: '',
    surname: '',
    given_name: '',

    reliable_bd_person: '',
    reliable_bd_person_mobile: '',
    reliable_ksa_person: '',
    reliable_ksa_person_mobile: '',
    nominee: '',
    nominee_mobile: '',

    training_district: '',
    training_venue: '',
    vaccine_district: '',

    pass_per_post_code: '',
    pass_per_district: '',
    pass_per_thana: '',
    pass_per_village: '',

    pass_post_code: '',
    pass_district: '',
    pass_thana: '',
    pass_village: '',

    refund_account_type: '',
    refund_account_name: '',
    refund_account_number: '',
    refund_bank_id: '',
    refund_branch_district: '',
    refund_routing_no: '',

    nationalId_img: '',
    pilgrim_img: '',
    designation: '',
    serviceGrade: '',
    spouse_name: '',
    dob_img: '',

    maharam_tracking_no: '',
    maharam_relation: '',
    maharamID: '',
    maharam_name: '',
});
const allerros = ref([]);
const errors = reactive({});
const imagePreview = ref({
    profile: '',
    dob: '',
});
const permanentPoliceStationList = ref('');
const currentPoliceStationList = ref('');
const bankBranchList = ref([]);
const bankBranchListArr = ref([]);
const loadingNext = ref(false);
const permanentPSArr = ref([]);
const currentPSArr = ref([]);
const venueList = ref('');
const venueListArr = ref([]);
const isGovtJob = ref(false);

provide('pilgrimData', pilgrimData);
provide('indexData', indexData);
provide('allerros', allerros);
provide('permanentPoliceStationList', permanentPoliceStationList);
provide('currentPoliceStationList', currentPoliceStationList);
provide('bankBranchList', bankBranchList);
provide('bankBranchListArr', bankBranchListArr);
provide('permanentPSArr', permanentPSArr);
provide('venueList', venueList);
provide('venueListArr', venueListArr);
provide('currentPSArr', currentPSArr);
provide('isGovtJob', isGovtJob);

const validateBanglaText = (input) => {
    const banglaPattern = /[\u0980-\u09FF\s\u0964\.,<>\/?;:'"|\{}+=_\-\(\)@*^~`&%$#!]+/g;
    const filteredInput = input.match(banglaPattern) ? input.match(banglaPattern).join('') : '';
    return input === filteredInput;
};

const validateEnglishText = (input) => {
    const englishPattern = /[a-zA-Z0-9\s\.,<>\/?;:'"{}|\+=_\-\)\(*&^%$#@!`~]+/g;
    const filteredInput = input.match(englishPattern) ? input.match(englishPattern).join('') : '';
    return input === filteredInput;
};

const validateEnglishNumber = (input) => {
    const englishNumberPattern = /^[0-9]+$/;
    return englishNumberPattern.test(input);
};

const setPilgrimData = (pilgrim) => {
    pilgrimData.value.resident = pilgrim.resident;
    pilgrimData.value.gender = pilgrim.gender;
    pilgrimData.value.identity = pilgrim.identity;
    pilgrimData.value.birth_date = pilgrim.birth_date;
    pilgrimData.value.passport_type = pilgrim.pp_global_type;
    pilgrimData.value.passport_number = pilgrim.passport_no;
    pilgrimData.value.nid_number = pilgrim.national_id;
    pilgrimData.value.brn = pilgrim.birth_certificate;

    pilgrimData.value.tracking_no = pilgrim.tracking_no;
    pilgrimData.value.is_govt = pilgrim.is_govt;
    pilgrimData.value.name_bn = pilgrim.full_name_bangla;
    pilgrimData.value.name_en = pilgrim.full_name_english;
    pilgrimData.value.father_name_bn = pilgrim.father_name;
    pilgrimData.value.mother_name_bn = pilgrim.mother_name;
    pilgrimData.value.father_name_en = pilgrim.father_name_english;
    pilgrimData.value.mother_name_en = pilgrim.mother_name_english;
    pilgrimData.value.occupation = pilgrim.occupation;
    pilgrimData.value.mobile = pilgrim.mobile;
    pilgrimData.value.marital_status = pilgrim.marital_status;
    pilgrimData.value.maharam_relation = pilgrim.maharam_relation  === null ? '' : pilgrim.maharam_relation;
    // pilgrimData.value.maharam_tracking_no = pilgrim.maharam_tracking_no;
    // pilgrimData.value.maharamID = pilgrim.maharamID;

    // passport information

    pilgrimData.value.pass_name = pilgrim.pass_name;
    pilgrimData.value.pass_dob = pilgrim.pass_dob;
    pilgrimData.value.passport_no = pilgrim.passport_no;
    pilgrimData.value.pass_type = pilgrim.pass_type;
    pilgrimData.value.pass_issue_date = pilgrim.pass_issue_date;
    pilgrimData.value.pass_exp_date = pilgrim.pass_exp_date;
    pilgrimData.value.surname = pilgrim.surname;
    pilgrimData.value.given_name = pilgrim.given_name;

    pilgrimData.value.pass_per_post_code = pilgrim.pass_per_post_code;
    pilgrimData.value.pass_per_district = pilgrim.pass_per_district;
    pilgrimData.value.pass_per_thana = pilgrim.pass_per_thana;
    pilgrimData.value.pass_per_village = pilgrim.pass_per_village;

    pilgrimData.value.pass_post_code = pilgrim.pass_post_code;
    pilgrimData.value.pass_district = pilgrim.pass_district;
    pilgrimData.value.pass_thana = pilgrim.pass_thana;
    pilgrimData.value.pass_village = pilgrim.pass_village;

    pilgrimData.value.tracking_no = pilgrim.tracking_no;

    pilgrimData.value.designation = pilgrim.designation;
    pilgrimData.value.serviceGrade = pilgrim.govt_service_grade;
    pilgrimData.value.spouse_name = pilgrim.spouse_name;
    pilgrimData.value.nationality2 = pilgrim.nationality2 ? pilgrim.nationality2 : '0';

    pilgrimData.value.reliable_bd_person = pilgrim.reliable_bd_person ?? '';
    pilgrimData.value.reliable_bd_person_mobile = pilgrim.reliable_bd_person_mobile ?? '';
    pilgrimData.value.reliable_ksa_person = pilgrim.reliable_ksa_person ?? '';
    pilgrimData.value.reliable_ksa_person_mobile = pilgrim.reliable_ksa_person_mobile ?? '';
    pilgrimData.value.nominee = pilgrim.nominee ?? '';
    pilgrimData.value.nominee_mobile = pilgrim.nominee_mobile ?? '';
    pilgrimData.value.training_district = pilgrim.training_district;
    pilgrimData.value.training_venue = pilgrim.training_venue;
    pilgrimData.value.vaccine_district = pilgrim.vaccine_district;

}
const fetchPilgrimDataFromApi = async (pilgrimId,trackingNo) => {
    const postData = {
        "pilgrimId": pilgrimId,
        "tracking_no": trackingNo,
    }
    loadingNext.value = true;
    const response = await axios.post(`/registration/get-edit-reg-pilgrim`, postData);
    loadingNext.value = false;
    if(response.data.responseCode !== 1) {
        alert('Pilgrim Info not found');
        router.push({ name: 'RegPilgrimsList' });
    }
    pilgrimData.value.is_registrable = response.data.pilgrim.is_registrable;
    if(pilgrimData.value.is_registrable == 1){
        currentStep.value += 1;
    }
    setPilgrimData(response.data.pilgrim);
    imagePreview.value.profile = response.data.profile_pic;
    imagePreview.value.dob = response.data.birthCertificate;
    pilgrimData.value.refund_account_type = response.data.refundInfo.owner_type;
    pilgrimData.value.refund_account_name = response.data.refundInfo.account_holder_name;
    pilgrimData.value.refund_account_number = response.data.refundInfo.account_number;
    pilgrimData.value.refund_bank_id = response.data.refundInfo.bank_id;
    pilgrimData.value.refund_branch_district = response.data.refundInfo.dist_code;
    pilgrimData.value.refund_routing_no = response.data.refundInfo.bb_routing_id;

    pilgrimData.value.maharam_name = response.data?.maharam_name;
    pilgrimData.value.maharam_tracking_no = response.data?.maharam_tracking_no;
    pilgrimData.value.maharamID = response.data?.maharamID;
}
const convertObjectToArray = (obj) => {
    return Object.entries(obj).map(([id, text]) => ({ id, text }));
};
const getIndexData = async () => {
    try{
        loadingNext.value = true;
        const response = await axios.get(`/pilgrim/pre-reg/get-index-data`);
        loadingNext.value = false;
        if(response.data.status === true){
            indexData.value.occupationList = response.data.data.occupation;
            indexData.value.occupationListArr = convertObjectToArray(response.data.data.occupation);
            indexData.value.districtList = response.data.data.district;
            indexData.value.districtListArr = convertObjectToArray(response.data.data.district);
            indexData.value.trainingDistrictArr =  indexData.value.districtListArr;
            indexData.value.bankList = response.data.data.bank_list;
            indexData.value.bankListArr = convertObjectToArray(response.data.data.bank_list);
            indexData.value.bankDistrictList = response.data.data.district_list;
            indexData.value.bankDistrictListArr = convertObjectToArray(response.data.data.district_list);
            indexData.value.accountHolderType = response.data.data.account_owner_type;

            indexData.value.govtServiceGradeObj = convertObjectToArray(response.data.data.govtServiceGrade);
            indexData.value.govtServiceList = response.data.data.is_govt;
            indexData.value.nationalitys = response.data.data.nationalitys;
            indexData.value.nationalityArr = [{id: '0', text: 'Select one'}, ...convertObjectToArray(response.data.data.nationalitys)];
            indexData.value.nationalityObj = response.data.data.nationalitys;
        }
    } catch (error) {
        console.error("Error fetching data: ", error);
    }
}

onMounted(async () => {
    const pilgrimId = route.params.id;
    const pilgrimTrackingNo = route.params.trackingNo;
    id.value = pilgrimId;
    trackingNo.value = pilgrimTrackingNo;
    await fetchPilgrimDataFromApi(pilgrimId,pilgrimTrackingNo);
    await getIndexData();
    await handleOccupationChange();
});

const formatDate = (dateString) => {
    const dateObj = new Date(dateString);
    const year = dateObj.getFullYear();
    const month = (dateObj.getMonth() + 1).toString().padStart(2, '0');
    const day = dateObj.getDate().toString().padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const passportVerification = async () => {
    try{
        const formattedDate = formatDate(pilgrimData.value.birth_date);
        const postData = {
            pilgrim_id: id.value,
            tracking_no: trackingNo.value,
            dob: formattedDate,
            passport_type: pilgrimData.value.passport_type,
            passport_no: pilgrimData.value.passport_number,
            nid_number: pilgrimData.value.nid_number,
            name_en: pilgrimData.value.name_en,
        };
        const response = await axios.post(`/registration/passport-verification`, postData);
        if(response.data.responseCode === 5 && response.data.status === false){
            const pilgrimId = route.params.id;
            const pilgrimTrackingNo = route.params.trackingNo;
            const type = response.data.type;
            const passNo = response.data.passportNo;
            const dob = response.data.dob;
            const proceedWithRouting = async () => {
                if (pilgrimId && pilgrimTrackingNo && type && passNo && dob) {
                    await router.push({
                        name: 'RegPassportVerifyReq',
                        query: {
                            pilgrimTrackingNo: pilgrimTrackingNo,
                            pilgrimId: pilgrimId,
                            passportType: response.data.type,
                            passportNo: response.data.passportNo,
                            dob: response.data.dob,
                        }
                    });
                } else {
                    toast.open({
                        message: 'Passport data is missing!',
                        type: 'error',
                        position: 'top-right',
                        duration: 5000,
                    });
                }
            };
            proceedWithRouting();

        } else if (response.data.responseCode === -5 && response.data.status === false){
            toast.open({
                message: response.data.message,
                type: 'error',
                position: 'top-right',
                duration: 5000,
            });
        } else if(response.data.status === true){
            //pilgrimData.value.name_en = response.data.data.passportData.full_name_english;
            pilgrimData.value.father_name_en = response.data.data.passportData.father_name_english;
            pilgrimData.value.mother_name_en = response.data.data.passportData.mother_name_english;
            pilgrimData.value.occupation = response.data.data.passportData.occupation;
            pilgrimData.value.marital_status = response.data.data.passportData.marital_status;
            pilgrimData.value.spouse_name = response.data.data.passportData.spouse_name;
            pilgrimData.value.identity = "PASSPORT";

            // passport information
            pilgrimData.value.pass_name = response.data.data.passportData.pass_name;
            pilgrimData.value.pass_dob = response.data.data.passportData.pass_dob;
            pilgrimData.value.passport_no = response.data.data.passportData.passport_no;
            pilgrimData.value.pass_type = response.data.data.passportData.pass_type;
            pilgrimData.value.pass_issue_date = response.data.data.passportData.pass_issue_date;
            pilgrimData.value.pass_exp_date = response.data.data.passportData.pass_exp_date;
            pilgrimData.value.surname = response.data.data.passportData.surname;
            pilgrimData.value.given_name = response.data.data.passportData.given_name;

            pilgrimData.value.pass_per_post_code = response.data.data.passportData.pass_per_post_code;
            pilgrimData.value.pass_per_district = response.data.data.passportData.pass_per_district;
            if(pilgrimData.value.pass_per_district){
                //permanentPoliceStation();
            }
            pilgrimData.value.pass_per_thana = response.data.data.passportData.pass_per_thana;
            pilgrimData.value.pass_per_village = response.data.data.passportData.pass_per_village;

            pilgrimData.value.pass_post_code = response.data.data.passportData.pass_post_code;
            pilgrimData.value.pass_district = response.data.data.passportData.pass_district;
            pilgrimData.value.pass_thana = response.data.data.passportData.pass_thana;
            pilgrimData.value.pass_village = response.data.data.passportData.pass_village;

            pilgrimData.value.object = response.data.data.requestData;
            imagePreview.value.profile = response.data.data.resizePPImg;
            handleOccupationChange();
            currentStep.value += 1;
        }else{
            toast.open({
                message: response.data.message,
                type: 'error',
                position: 'top-right',
                duration: 3000,
            });
        }

    } catch (error) {
        console.error("Passport Verification Failed");
    }
}

const checkDuplicatePilgrim = async () => {
    const formattedDate = formatDate(pilgrimData.value.birth_date);
    const postData = {
        identity: pilgrimData.value.identity,
        dob: formattedDate,
        passport_type: pilgrimData.value.passport_type,
        passport_no: pilgrimData.value.passport_number,
        nid_number: pilgrimData.value.nid_number,
        brn: pilgrimData.value.brn,
        id: id.value,
    };
    const response = await axios.post(`/pilgrim/pre-reg/check-duplicate-pilgrim-edit`,postData);
    if(response.data.status === true){
        currentStep.value += 1;
    } else {
        alert(response.data.message);
        return false;
    }

}

const nextStep = async () => {
    if (validateStep()) {
        if (currentStep.value < 6) {
            if(currentStep.value === 1 && ['E-PASSPORT', 'MRP'].includes(pilgrimData.value.passport_type) && pilgrimData.value.is_registrable != 1){
                loadingNext.value = true;
                await passportVerification();
            } else {
                currentStep.value++;
            }
            loadingNext.value = false;
        }
    } else {
        const errorMessages = Object.values(errors).filter(msg => msg).join('\n');
        alert(errorMessages);
    }
}
const prevStep = () => {
    if((currentStep.value > 1 && pilgrimData.value.is_registrable == 0) || (currentStep.value > 2 && pilgrimData.value.is_registrable == 1)){
        currentStep.value -= 1;
    }
}
const calculateAge = (birthDate) => {
    if(!birthDate) {
        return 0;
    }
    const today = new Date();
    const birthDateObj = new Date(birthDate);
    let age = today.getFullYear() - birthDateObj.getFullYear();
    const monthDiff = today.getMonth() - birthDateObj.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
        age--;
    }
    return age;
};
const validateStep = () => {
    // Clear previous errors
    Object.keys(errors).forEach(key => {
        errors[key] = '';
    });

    let hasErrors = false;
    const mobilePattern = /^(?:\+88|88)?(01[3-9]\d{8})$/;
    const passportPatternEP = /^[A-Z]\d{8}$/;
    const passportPatternMP = /^[A-Z]{2}\d{7}$/;

    switch (currentStep.value) {
        case 1:
            if (!['MRP', 'E-PASSPORT'].includes(pilgrimData.value.passport_type)) {
                errors.passport_number = 'Passport type is required';
                return false;
            }

            if (!pilgrimData.value.passport_number) {
                errors.passport_number = 'Passport Number is required';
                return false;
            } else if (pilgrimData.value.passport_type === 'E-PASSPORT' && !passportPatternEP.test(pilgrimData.value.passport_number)) {
                errors.passport_number = 'Invalid E-PASSPORT Format';
                return false;
            } else if (pilgrimData.value.passport_type === 'MRP' && !passportPatternMP.test(pilgrimData.value.passport_number)) {
                errors.passport_number = 'Invalid MRP PASSPORT Format';
                return false;
            }

            break;

        case 2:
            /*if (!pilgrimData.value.name_bn) {
                errors.name_bn = 'Name in Bangla is required';
                return false;
            } else if(!validateBanglaText(pilgrimData.value.name_bn)) {
                errors.name_bn = 'Name in Bangla is only input bangla character';
                return false;
            }*/
            if (!pilgrimData.value.name_en) {
                errors.name_en = 'Name in English is required';
                return false;
            }

            /* for requirement change
            if (!pilgrimData.value.father_name_bn) {
                errors.father_name_bn = 'Father\'s Name in Bangla is required';
                return false;
            } else if(!validateBanglaText(pilgrimData.value.father_name_bn)) {
                errors.father_name_bn = 'Father\'s Name in Bangla is only input bangla character';
                return false;
            }

            if (!pilgrimData.value.mother_name_bn) {
                errors.mother_name_bn = 'Mother\'s Name in Bangla is required';
                return false;
            } else if(!validateBanglaText(pilgrimData.value.mother_name_bn)) {
                errors.mother_name_bn = 'Mother\s Name in Bangla is only input bangla character';
                return false;
            }

             */

            if (!pilgrimData.value.father_name_en) {
                errors.father_name_en = 'Father Name is required';
                return false;
            } else if(!validateEnglishText(pilgrimData.value.father_name_en)) {
                errors.father_name_en = 'Father Name only input english character';
                return false;
            }

            if (!pilgrimData.value.mother_name_en) {
                errors.mother_name_en = 'Mother Name is required';
                return false;
            } else if(!validateEnglishText(pilgrimData.value.mother_name_en)) {
                errors.mother_name_en = 'Mother Name only input english character';
                return false;
            }


            if (!pilgrimData.value.occupation) {
                errors.occupation = 'Occupation is required';
                return false;
            }
            if (pilgrimData.value.occupation && isGovtJob.value && !pilgrimData.value.designation) {
                errors.designation = 'Designation is required';
                return false;
            }
            if (pilgrimData.value.occupation && isGovtJob.value && !pilgrimData.value.serviceGrade) {
                errors.serviceGrade = 'Service Grade is required';
                return false;
            }
            if (!pilgrimData.value.mobile) {
                errors.mobile = 'Mobile Number is required';
                return false;
            } else if (!mobilePattern.test(pilgrimData.value.mobile)) {
                errors.mobile = 'Invalid mobile number format';
                return false;
            }
            if (!pilgrimData.value.marital_status) {
                errors.marital_status = 'Marital Status is required';
                return false;
            }
            if (pilgrimData.value.marital_status === 'Married' && !pilgrimData.value.spouse_name) {
                errors.spouse_name = 'Spouse Name is required';
                return false;
            }
            if (calculateAge(pilgrimData.value.birth_date) < 18 && (!pilgrimData.value.maharam_tracking_no || !pilgrimData.value.maharamID)) {
                errors.maharam_tracking_no = 'Maharam tracking number is required';
                return false;
            }
            if (calculateAge(pilgrimData.value.birth_date) < 18 && !pilgrimData.value.maharam_relation) {
                errors.maharam_relation = 'Maharam relation number is required';
                return false;
            }
            if ((pilgrimData.value.maharam_tracking_no || pilgrimData.value.maharamID) && !pilgrimData.value.maharam_relation) {
                errors.maharam_relation = 'Maharam relation number is required';
                return false;
            }
            if ((!pilgrimData.value.maharam_tracking_no || !pilgrimData.value.maharamID) && pilgrimData.value.maharam_relation) {
                errors.maharam_tracking_no = 'Maharam tracking number is required';
                return false;
            }
            break;
        case 3:
            if (!pilgrimData.value.pass_per_post_code) {
                errors.permanent_post_code = 'Pass Permanent Post Code is required';
                return false;
            }
            if (!pilgrimData.value.pass_per_district) {
                errors.permanent_district_id = 'Pass Permanent District is required';
                return false;
            }
            if (!pilgrimData.value.pass_per_thana) {
                errors.permanent_police_station_id = 'Pass Permanent Police Station is required';
                return false;
            }
            if (!pilgrimData.value.pass_per_village) {
                errors.permanent_address = 'Pass Permanent Address is required';
                return false;
            }
            if (!pilgrimData.value.pass_post_code) {
                errors.present_post_code = 'Pass Present Post Code is required';
                return false;
            }
            if (!pilgrimData.value.pass_district) {
                errors.present_district_id = 'Pass Present District is required';
                return false;
            }
            if (!pilgrimData.value.pass_thana) {
                errors.present_police_station_id = 'Pass Present Police Station is required';
                return false;
            }
            if (!pilgrimData.value.pass_village) {
                errors.present_address = 'Pass Present Address is required';
                return false;
            }
            break;
        case 4:
            if (!pilgrimData.value.training_district) {
                errors.training_district = 'Training District is required';
                return false;
            }
            if (!pilgrimData.value.training_venue) {
                errors.training_venue = 'Training Venue is required';
                return false;
            }
            if (!pilgrimData.value.vaccine_district) {
                errors.vaccine_district = 'Vaccine District is required';
                return false;
            }
            break;
        case 5:
            if (!pilgrimData.value.refund_account_type) {
                errors.refund_account_type = 'Account Holder Type is required';
                return false;
            }
            if (!pilgrimData.value.refund_account_number) {
                errors.refund_account_number = 'Account Number is required';
                return false;
            } else if (!/^\d+$/.test(pilgrimData.value.refund_account_number)) {
                errors.refund_account_number = 'Account Number must contain only numbers';
                return false;
            } else if (pilgrimData.value.refund_account_number.length < 13 || pilgrimData.value.refund_account_number.length > 17) {
                errors.refund_account_number = 'Account Number must be between 13 and 17 characters long';
                return false;
            }
            if (!pilgrimData.value.refund_account_name) {
                errors.refund_account_name = 'Account Holder Name is required';
                return false;
            }
            if (!pilgrimData.value.refund_bank_id) {
                errors.refund_bank_id = 'Bank Name is required';
                return false;
            }
            if (!pilgrimData.value.refund_branch_district) {
                errors.refund_branch_district = 'Branch District is required';
                return false;
            }
            if (!pilgrimData.value.refund_routing_no) {
                errors.refund_routing_no = 'Branch & Routing No is required';
                return false;
            }
            break;
    }

    return !hasErrors;
};

const createImagePreview = (file, flag) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (e) => {
        if(flag === 'profile') {
            imagePreview.value.profile = e.target.result;
        } else if(flag === 'dob') {
            imagePreview.value.dob = e.target.result;
        }
    };
};
const onFileChange = (file) => {
    if (file && file.type.startsWith('image/')) {
        pilgrimData.value.pilgrim_img = file;
        createImagePreview(file, 'profile');
    } else {
        pilgrimData.value.pilgrim_img = null;
        imagePreview.value.profile = null;
    }
};
const onDOBFileChange = (file) => {
    if (file && file.type.startsWith('image/')) {
        pilgrimData.value.dob_img = file;
        createImagePreview(file, 'dob');
    } else {
        pilgrimData.value.dob_img = null;
        imagePreview.value.dob = null;
    }
};

const submitData = async () => {
    try {
        const form = new FormData();

        form.append('resident', pilgrimData.value.resident);
        form.append('gender', pilgrimData.value.gender);
        form.append('identity', pilgrimData.value.identity);
        form.append('birth_date', moment(pilgrimData.value.birth_date).format('YYYY-MM-DD'));
        form.append('pass_dob', moment(pilgrimData.value.pass_dob).format('YYYY-MM-DD'));
        form.append('passport_type', pilgrimData.value.passport_type);
        form.append('passport_number', pilgrimData.value.passport_number);
        form.append('nid_number', pilgrimData.value.nid_number);
        form.append('brn', pilgrimData.value.brn);

        form.append('name_bn', pilgrimData.value.name_bn);
        form.append('name_en', pilgrimData.value.name_en);
        form.append('father_name_bn', pilgrimData.value.father_name_bn);
        form.append('mother_name_bn', pilgrimData.value.mother_name_bn);
        form.append('father_name_en', pilgrimData.value.father_name_en);
        form.append('mother_name_en', pilgrimData.value.mother_name_en);
        form.append('occupation', pilgrimData.value.occupation);
        form.append('mobile', pilgrimData.value.mobile);
        form.append('marital_status', pilgrimData.value.marital_status);
        form.append('maharamID', pilgrimData.value.maharamID);
        form.append('maharam_relation', pilgrimData.value.maharam_relation);
        form.append('maharam_tracking_no', pilgrimData.value.maharam_tracking_no);

        form.append('pass_per_post_code', pilgrimData.value.pass_per_post_code);
        form.append('pass_per_district', pilgrimData.value.pass_per_district);
        form.append('pass_per_thana', pilgrimData.value.pass_per_thana);
        form.append('pass_per_village', pilgrimData.value.pass_per_village);

        form.append('pass_post_code', pilgrimData.value.pass_post_code);
        form.append('pass_district', pilgrimData.value.pass_district);
        form.append('pass_thana', pilgrimData.value.pass_thana);
        form.append('pass_village', pilgrimData.value.pass_village);

        form.append('refund_account_type', pilgrimData.value.refund_account_type);
        form.append('refund_account_name', pilgrimData.value.refund_account_name);
        form.append('refund_account_number', pilgrimData.value.refund_account_number);
        form.append('refund_bank_id', pilgrimData.value.refund_bank_id);
        form.append('refund_branch_district', pilgrimData.value.refund_branch_district);
        form.append('refund_routing_no', pilgrimData.value.refund_routing_no);

        form.append('nationalId_img', pilgrimData.value.nationalId_img);
        form.append('pilgrim_img', pilgrimData.value.pilgrim_img);
        form.append('passport_img', imagePreview.value.profile);
        form.append('tracking_no', pilgrimData.value.tracking_no);
        form.append('pilgrim_id', id.value);

        form.append('designation', pilgrimData.value.designation);
        form.append('serviceGrade', pilgrimData.value.serviceGrade);
        form.append('spouse_name', pilgrimData.value.spouse_name);
        form.append('is_govt_job', isGovtJob.value);
        form.append('dob_img', pilgrimData.value.dob_img);

        form.append('pass_name', pilgrimData.value.pass_name);
        form.append('pass_type', pilgrimData.value.pass_type);
        form.append('passport_no', pilgrimData.value.passport_no);
        form.append('pass_issue_date', moment(pilgrimData.value.pass_issue_date).format('YYYY-MM-DD'));
        form.append('pass_exp_date', moment(pilgrimData.value.pass_exp_date).format('YYYY-MM-DD'));
        form.append('surname', pilgrimData.value.surname);
        form.append('given_name', pilgrimData.value.given_name);

        form.append('reliable_bd_person', pilgrimData.value.reliable_bd_person);
        form.append('reliable_bd_person_mobile', pilgrimData.value.reliable_bd_person_mobile);
        form.append('reliable_ksa_person', pilgrimData.value.reliable_ksa_person);
        form.append('reliable_ksa_person_mobile', pilgrimData.value.reliable_ksa_person_mobile);
        form.append('nominee', pilgrimData.value.nominee);
        form.append('nominee_mobile', pilgrimData.value.nominee_mobile);

        form.append('training_district', pilgrimData.value.training_district);
        form.append('training_venue', pilgrimData.value.training_venue);
        form.append('vaccine_district', pilgrimData.value.vaccine_district);

        loadingNext.value = true;
        const response = await axios.post('/registration/pilgrim-update', form, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        loadingNext.value = false;

        if(response.data.responseCode !== 1) {
            toast.open({
                message: response.data.msg,
                type: 'error',
                position: 'top-right',
                duration: 3000,
            });
            currentStep.value = 1;
            return false;
        }

        if (response.data.status === true) {
            toast.open({
                message: response.data.msg,
                type: 'success',
                position: 'top-right',
                duration: 3000,
            });
            router.push({ path: '/reg-pilgrims-list' });
        }
    } catch (error) {
        allerros.value = error.response.data.errors;
    }
}

const getPoliceStation = async (district_id, flag) => {
    try {
        if (district_id) {
            loadingNext.value = true;
            const response = await axios.get(`/pilgrim/pre-reg/get-police-station/${district_id}`);
            loadingNext.value = false;
            if(flag === 'permanent'){
                permanentPoliceStationList.value = response.data.data;
                permanentPSArr.value = [{id: '', text: 'Select one'}, ...convertObjectToArray(response.data.data)];
            }else {
                currentPoliceStationList.value = response.data.data;
                currentPSArr.value = [{id: '', text: 'Select one'}, ...convertObjectToArray(response.data.data)];
            }

        } else {
            console.warn("Refund bank ID or branch district is not set.");
        }
    } catch (error) {
        console.error("Error fetching bank branch list:", error);
    }
}

const permanentPoliceStation = () => {
    const district_ID = pilgrimData.value.pass_per_district;
    const flag = 'permanent';
    getPoliceStation(district_ID, flag)
};

const currentPoliceStation = () => {
    const current_district_ID = pilgrimData.value.pass_district;
    const flag = 'current';
    getPoliceStation(current_district_ID, flag)
};

const getVenueList = async (district_id) => {
    try {
        if (district_id) {
            loadingNext.value = true;
            const response = await axios.get(`/registration/get-venue-list/${district_id}`);
            loadingNext.value = false;
            //pilgrimData.value.training_venue = '';
            venueList.value = response.data.data;
            venueListArr.value = [{id: '', text: 'Select one'}, ...convertObjectToArray(response.data.data)];
        }
    } catch (error) {
        console.error("Error fetching venue list:", error);
    }
}

const venue = (newVal) => {
    const district_ID = pilgrimData.value.training_district;
    getVenueList(district_ID);
};

watch(() => pilgrimData.value.pass_per_district, (newVal, oldVal) => {
    permanentPoliceStation(newVal);
});

watch(() => pilgrimData.value.pass_district, (newVal, oldVal) => {
    currentPoliceStation(newVal);
});

watch(() => pilgrimData.value.training_district, (newVal, oldVal) => {
    venue(newVal);
});

const getBankBranch = async () => {
    try {
        if (pilgrimData.value.refund_bank_id && pilgrimData.value.refund_branch_district) {
            const bankId = pilgrimData.value.refund_bank_id;
            const branchDistrictId = pilgrimData.value.refund_branch_district;
            const response = await axios.get(`/pilgrim/pre-reg/get-bank-branch/${bankId}/${branchDistrictId}`);
            bankBranchList.value = response.data.data;
            bankBranchListArr.value = convertObjectToArray(response.data.data);
        } else {
            console.warn("Refund bank ID or branch district is not set.");
        }
    } catch (error) {
        console.error("Error fetching bank branch list:", error);
    }
}

watch(() => pilgrimData.value.refund_bank_id, (newVal, oldVal) => {
    if(newVal != 0 || newVal !== null) {
        getBankBranch();
    }
});

watch(() => pilgrimData.value.refund_branch_district, (newVal) => {
    if(newVal != 0 || newVal !== null) {
        getBankBranch();
    }
});

watch(() => pilgrimData.value.pilgrim_img, (newVal, oldVal) => {
    imagePreview.value.profile = newVal;
});

watch(() => pilgrimData.value.dob_img, (newVal, oldVal) => {
    imagePreview.value.dob = newVal;
});

const handleOccupationChange = async (value = 0) => {
    isGovtJob.value = !!(indexData.value.govtServiceList && indexData.value.govtServiceList[pilgrimData.value.occupation]);
}

</script>

<template>
    <div class="card card-magenta border border-magenta">
        <div class="card-header">
            <h5 class="card-title pt-2 pb-2">Pilgrim Registration</h5>
        </div>
        <div class="card-body">
            <!-- Step Indicator -->
            <Indicator :currentStep="currentStep" />
            <br>
            <!-- Form Steps -->
            <form>
                <!-- Step 1 -->
                <template v-if="currentStep === 1">
                    <Step1 />
                </template>

                <!-- Step 2 -->
                <template v-if="currentStep === 2">
                    <Step2 @handleOccupationChange="handleOccupationChange" />
                </template>

                <!-- Step 3 -->
                <template v-if="currentStep === 3">
                    <Step3 />
                </template>

                <!-- Step 4 -->
                <template v-if="currentStep === 4">
                    <Step4 />
                </template>

                <!-- Step 4 -->
                <template v-if="currentStep === 5">
                    <Step5 :imagePreview="imagePreview" @onFileChange="onFileChange" />
                </template>
                <!-- Step 5 -->
                <template v-if="currentStep === 6">
                    <EditPreview :imagePreview="imagePreview" />
                </template>

                <!-- Buttons 4 -->
                <button type="button" class="btn btn-default float-left" @click="prevStep" :disabled="currentStep < 2">
                    Previous
                </button>
                <button v-if="currentStep < 6 && !loadingNext" type="button" class="btn btn-primary float-right" @click="nextStep">
                    Next
                </button>
                <button v-if="currentStep < 6 && loadingNext" type="button" class="btn btn-primary float-right" disabled>
                    Loading...
                </button>
                <button v-if="currentStep === 6 && !loadingNext" type="button" class="btn btn-primary float-right" @click="submitData">
                    Update
                </button>
                <button v-if="currentStep === 6 && loadingNext" type="button" class="btn btn-primary float-right" disabled>
                    Loading...
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
<style>
    .select2-container .select2-selection--single {
        height: 39px;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        top: 7px;
    }
    .select2-container .select2-selection--single .select2-selection__rendered{
        padding-left: 0px;
        font-size: 14px;
    }
</style>
