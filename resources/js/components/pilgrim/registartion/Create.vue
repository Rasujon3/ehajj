<script setup>
import {ref, reactive, onMounted, watch} from 'vue';
import axios from "axios";
import moment from 'moment';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import PreviewComponent from './pilgrimInfoPreview.vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const currentStep = ref(1);
const allerros = ref([]);
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

    permanent_post_code: '',
    permanent_district_id: '0',
    permanent_police_station_id: '0',
    permanent_address: '',
    present_post_code: '',
    present_district_id: '0',
    present_police_station_id: '0',
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
    object: '',
    serviceGrade: '0',
    designation: '',

});
const errors = reactive({});
const nidFlag = ref(0);
const passportFlag = ref(0);
const districtList = ref([{id: '0', text: 'Select one'}]);
const occupationList = ref([{id: '0', text: 'Select one'}]);
const bankList = ref([{id: '0', text: 'Select one'}]);
const bankDistrictList = ref([{id: '0', text: 'Select one'}]);
const accountHolderType = ref({});
const bankBranchList = ref([{id: '0', text: 'Select one'}]);
const permanentPoliceStationList = ref([{id: '0', text: 'Select one'}]);
const currentPoliceStationList = ref([{id: '0', text: 'Select one'}]);
const districtBn = ref('');
const policeBn = ref('');
const loadingNext = ref(false);
const imagePreview = ref({
    profile: '',
    dob: '',
});

const occupationListObj = ref({});
const districtListObj = ref({});
const bankListObj = ref({});
const bankDistrictListObj = ref({});
const currentPSObj = ref({});
const permanentPSObj = ref({});
const bankBranchObj = ref({});
const govtServices = ref({});
const govtServiceGrade = ref([]);
const isGovt = ref(false);
const nationalityArr = ref([]);
const nationalityObj = ref({});
const govtServiceGradeObj = ref({});
const age = ref(18);

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

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        pilgrimData.value.pilgrim_img = file;
        createImagePreview(file, 'profile');
    } else {
        pilgrimData.value.pilgrim_img = null;
        imagePreview.value.profile = null;
    }
};

const onDobImgChange = (event) => {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        pilgrimData.value.dob_img = file;
        createImagePreview(file, 'dob');
    } else {
        pilgrimData.value.dob_img = null;
        imagePreview.value.dob = null;
    }
};
const nidFileUpload = (event) => {
    pilgrimData.value.nationalId_img = event.target.files[0];
};
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
            if (pilgrimData.value.resident !== 'Bangladeshi' && (!pilgrimData.value.nationality2 || pilgrimData.value.nationality2 == '0')) {
                errors.birth_date = 'Nationality is required';
                return false;
            }
            if (!pilgrimData.value.birth_date) {
                errors.birth_date = 'Birth Date is required';
                return false;
            } else if (pilgrimData.value.identity === 'NID' && calculateAge(pilgrimData.value.birth_date) < 18) {
                errors.birth_date = 'You must be at least 18 years old';
                return false;
            }
            if(!pilgrimData.value.identity) {
                errors.identity = 'Identity is required';
                return false;
            }
            if (pilgrimData.value.identity === 'PASSPORT' && !pilgrimData.value.passport_number) {
                errors.passport_number = 'Passport Number is required';
                return false;
            }
            if(pilgrimData.value.identity === 'PASSPORT') {
                if (pilgrimData.value.identity === 'PASSPORT' && !pilgrimData.value.passport_number) {
                    errors.passport_number = 'Passport Number is required';
                    return false;
                } else if (pilgrimData.value.passport_type === 'E-PASSPORT' && !passportPatternEP.test(pilgrimData.value.passport_number)) {
                    errors.passport_number = 'Invalid E-PASSPORT Format';
                    return false;
                } else if (pilgrimData.value.passport_type === 'MRP' && !passportPatternMP.test(pilgrimData.value.passport_number)) {
                    errors.passport_number = 'Invalid MRP PASSPORT Format';
                    return false;
                }
            }
            if (pilgrimData.value.identity === 'NID') {
                const nidValue = pilgrimData.value.nid_number;
                if (!nidValue) {
                    errors.nid_number = 'NID Number is required';
                    return false;
                } else if (nidValue.length !== 10 && nidValue.length !== 13 && nidValue.length !== 17) {
                    errors.nid_number = 'National ID No. must be either 10, 13, or 17 characters long.';
                    return false;
                }else if ( !validateEnglishNumber(nidValue)) {
                    errors.nid_number = 'National ID no must be in English.';
                    return false;
                }
            }

            if (pilgrimData.value.identity === 'DOB') {
                const brnValue = pilgrimData.value.brn;
                const dob = moment(pilgrimData.value.birth_date).year();
                if (!brnValue) {
                    errors.brn_number = 'Birth registration number is required';
                    return false;
                } else if (brnValue.length !== 17) {
                    errors.brn_number = 'Birth registration number must be 17 characters long.';
                    return false;
                } else if (brnValue.slice(0, 4) != dob) {
                    errors.brn_number = 'Birth registration number must be start with birth year';
                    return false;
                }
            }

            break;
        case 2:
            if (!pilgrimData.value.name_bn) {
                errors.name_bn = 'Name in Bangla is required';
                return false;
            } else if(!validateBanglaText(pilgrimData.value.name_bn)) {
                errors.name_bn = 'Name in Bangla is only input bangla character';
                return false;
            }

            if (!pilgrimData.value.name_en) {
                errors.name_en = 'Name in English is required';
                return false;
            }else if ( !validateEnglishText(pilgrimData.value.name_en)) {
                errors.name_en = 'Fill Name in English';
                return false;
            }

            if(pilgrimData.value.identity !== 'PASSPORT') {
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
            } else {
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
            }


            if (!pilgrimData.value.occupation || pilgrimData.value.occupation == '0') {
                errors.occupation = 'Occupation is required';
                return false;
            }

            if ((pilgrimData.value.occupation && pilgrimData.value.occupation != '0') && isGovt.value && !pilgrimData.value.designation) {
                errors.designation = 'Designation is required';
                return false;
            }
            if ((pilgrimData.value.occupation && pilgrimData.value.occupation != '0') && isGovt.value && (!pilgrimData.value.serviceGrade || pilgrimData.value.serviceGrade == '0')) {
                errors.serviceGrade = 'Service Grade is required';
                return false;
            }

            if (!pilgrimData.value.mobile) {
                errors.mobile = 'Mobile Number is required.';
                return false;
            } else if (!mobilePattern.test(pilgrimData.value.mobile)) {
                errors.mobile = 'Invalid mobile number format';
                return false;
            }else if (!validateEnglishText(pilgrimData.value.mobile)) {
                errors.mobile = 'Provide mobile number in English';
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
            break;
        case 3:
            if (!pilgrimData.value.permanent_post_code) {
                errors.permanent_post_code = 'Permanent Post Code is required';
                return false;
            }
            if (!pilgrimData.value.permanent_district_id || pilgrimData.value.permanent_district_id == '0') {
                errors.permanent_district_id = 'Permanent District is required';
                return false;
            }
            if (!pilgrimData.value.permanent_police_station_id || pilgrimData.value.permanent_police_station_id == '0') {
                errors.permanent_police_station_id = 'Permanent Police Station is required';
                return false;
            }
            if (!pilgrimData.value.permanent_address) {
                errors.permanent_address = 'Permanent Address is required';
                return false;
            }
            if (!pilgrimData.value.present_post_code) {
                errors.present_post_code = 'Present Post Code is required';
                return false;
            }
            if (!pilgrimData.value.present_district_id || pilgrimData.value.present_district_id == '0') {
                errors.present_district_id = 'Present District is required';
                return false;
            }
            if (!pilgrimData.value.present_police_station_id || pilgrimData.value.present_police_station_id == '0') {
                errors.present_police_station_id = 'Present Police Station is required';
                return false;
            }
            if (!pilgrimData.value.present_address) {
                errors.present_address = 'Present Address is required';
                return false;
            }
            break;
        case 4:
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
            if (!pilgrimData.value.refund_bank_id || pilgrimData.value.refund_bank_id == '0') {
                errors.refund_bank_id = 'Bank Name is required';
                return false;
            }
            if (!pilgrimData.value.refund_branch_district || pilgrimData.value.refund_branch_district == '0') {
                errors.refund_branch_district = 'Branch District is required';
                return false;
            }
            if (!pilgrimData.value.refund_routing_no || pilgrimData.value.refund_routing_no == '0') {
                errors.refund_routing_no = 'Branch & Routing No is required';
                return false;
            }

            if (!pilgrimData.value.pilgrim_img && pilgrimData.value.identity !== 'PASSPORT') {
                errors.pilgrim_img = 'Photo is required';
                return false;
            }
            if (!pilgrimData.value.dob_img && pilgrimData.value.identity == 'DOB') {
                errors.pilgrim_img = 'Birth Certificate Photo is required';
                return false;
            }

            break;
    }

    return !hasErrors;
};
const submitForm = async () => {
    try {
        const form = new FormData();
        form.append('resident', pilgrimData.value.resident);
        form.append('gender', pilgrimData.value.gender);
        form.append('identity', pilgrimData.value.identity);
        form.append('birth_date', moment(pilgrimData.value.birth_date).format('YYYY-MM-DD'));
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

        form.append('permanent_post_code', pilgrimData.value.permanent_post_code);
        form.append('permanent_district_id', pilgrimData.value.permanent_district_id);
        form.append('permanent_district_name', districtListObj.value[pilgrimData.value.permanent_district_id]);
        form.append('permanent_police_station_id', pilgrimData.value.permanent_police_station_id);
        form.append('permanent_police_station_name', permanentPSObj.value[pilgrimData.value.permanent_police_station_id]);
        form.append('permanent_address', pilgrimData.value.permanent_address);

        form.append('present_post_code', pilgrimData.value.present_post_code);
        form.append('present_district_id', pilgrimData.value.present_district_id);
        form.append('present_district_name', districtListObj.value[pilgrimData.value.present_district_id]);
        form.append('present_police_station_id', pilgrimData.value.present_police_station_id);
        form.append('present_police_station_name', currentPSObj.value[pilgrimData.value.present_police_station_id]);
        form.append('present_address', pilgrimData.value.present_address);

        form.append('refund_account_type', pilgrimData.value.refund_account_type);
        form.append('refund_account_name', pilgrimData.value.refund_account_name);
        form.append('refund_account_number', pilgrimData.value.refund_account_number);
        form.append('refund_bank_id', pilgrimData.value.refund_bank_id);
        form.append('refund_branch_district', pilgrimData.value.refund_branch_district);
        form.append('refund_routing_no', pilgrimData.value.refund_routing_no);

        form.append('nationalId_img', pilgrimData.value.nationalId_img);
        form.append('pilgrim_img', pilgrimData.value.pilgrim_img);
        form.append('dob_img', pilgrimData.value.dob_img);
        form.append('object', pilgrimData.value.object);

        form.append('nationality2', pilgrimData.value.nationality2);
        form.append('designation', pilgrimData.value.designation);
        form.append('serviceGrade', pilgrimData.value.serviceGrade);
        form.append('spouse_name', pilgrimData.value.spouse_name);
        form.append('is_govt_job', isGovt.value);

        loadingNext.value = true;
        const response = await axios.post('/registration/store', form, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        loadingNext.value = false;
        if(response.data.responseCode !== 1) {
            alert(response.data.msg);
            currentStep.value = 1;
            return false;
        }
        if (response.data.status === true) {
            router.push({ path: '/reg-pilgrims-list' });
        }
    } catch (error) {
        allerros.value = error.response.data.errors;
    }
};
const getConfigNidPassport = async () => {
    try{
        const response = await axios.get(`/registration/configuration-verification/nid-passport`);
        nidFlag.value = parseInt(response.data.data.NID_VERIFICATION, 10);
        passportFlag.value = parseInt(response.data.data.PRE_REG_PASSPORT, 10);
    } catch (error) {
            console.error("Error fetching data:", error);
    }
}
const getBankBranch = async () => {
    try {
        if (pilgrimData.value.refund_bank_id && pilgrimData.value.refund_branch_district) {
            const bankId = pilgrimData.value.refund_bank_id;
            const branchDistrictId = pilgrimData.value.refund_branch_district;
            const response = await axios.get(`/registration/get-bank-branch/${bankId}/${branchDistrictId}`);
            bankBranchList.value =  [{id: '0', text: 'Select one'}, ...convertObjectToArray(response.data.data)];
            bankBranchObj.value = response.data.data;
        } else {
            console.warn("Refund bank ID or branch district is not set.");
        }
    } catch (error) {
        console.error("Error fetching bank branch list:", error);
    }
}
const policeStation = async (district_id,flag) => {
    try {
        if (district_id) {
            const response = await axios.get(`/registration/get-police-station/${district_id}`);
            if(flag === 'permanent'){
                permanentPoliceStationList.value = [{id: '0', text: 'Select one'}, ...convertObjectToArray(response.data.data)];
                permanentPSObj.value = response.data.data;
            }else {
                currentPoliceStationList.value = [{id: '0', text: 'Select one'}, ...convertObjectToArray(response.data.data)];
                currentPSObj.value = response.data.data;
            }

        } else {
            console.warn("Refund bank ID or branch district is not set.");
        }
    } catch (error) {
        console.error("Error fetching bank branch list:", error);
    }
}
const getDistrictPoliceStation = async (post_code,flag) => {
    try {
        if (post_code) {
            // Not Complete fetching Some complexity here
            const response = await axios.get(`/registration/get-district-police-station-by-postCode/${post_code}`);
            if(flag === 'permanent'){
               districtBn.value = response.data.data.DistrictBn;
            }else {

            }

        } else {
            console.warn("Post code not found.");
        }
    } catch (error) {
        console.error("Error fetching bank branch list:", error);
    }
}
const permanentPoliceStation = () => {
    const district_ID = pilgrimData.value.permanent_district_id;
    const flag = 'permanent';
    policeStation(district_ID,flag)
};
const currentPoliceStation = () => {
    const current_district_ID = pilgrimData.value.present_district_id;
    const flag = 'current';
    policeStation(current_district_ID,flag)
};
const permanentDistrictPoliceByPostCode = () => {
    const post_code = pilgrimData.value.permanent_post_code;
    const flag = 'permanent';
    getDistrictPoliceStation(post_code,flag)
};
const currentDistrictPoliceByPostCode = () => {
    const post_code = pilgrimData.value.present_post_code;
    const flag = 'current';
    getDistrictPoliceStation(post_code,flag)
};
const convertObjectToArray = (obj) => {
    return Object.entries(obj).map(([id, text]) => ({ id, text }));
};
const getIndexData = async () => {
    try{
        const response = await axios.get(`/registration/get-index-data`);

          if(response.data.status === true){
              occupationList.value = [{id: '0', text: 'Select one'}, ...convertObjectToArray(response.data.data.occupation)];
              districtList.value = [{id: '0', text: 'Select one'}, ...convertObjectToArray(response.data.data.district)];
              bankList.value = [{id: '0', text: 'Select one'}, ...convertObjectToArray(response.data.data.bank_list)];
              bankDistrictList.value =  [{id: '0', text: 'Select one'}, ...convertObjectToArray(response.data.data.district_list)];

              occupationListObj.value = response.data.data.occupation
              districtListObj.value = response.data.data.district
              bankListObj.value = response.data.data.bank_list
              bankDistrictListObj.value =  response.data.data.district_list

              accountHolderType.value = response.data.data.account_owner_type;
              govtServices.value = response.data.data.is_govt;
              govtServiceGrade.value = [{id: '0', text: 'Select one'}, ...convertObjectToArray(response.data.data.govtServiceGrade)];
              govtServiceGradeObj.value = response.data.data.govtServiceGrade;
              nationalityArr.value = [{id: '0', text: 'Select one'}, ...convertObjectToArray(response.data.data.nationalitys)];
              nationalityObj.value = response.data.data.nationalitys;
          }

    } catch (error) {
            console.error("Error fetching data:", error);
    }
}

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
            dob: formattedDate,
            passport_type: pilgrimData.value.passport_type,
            passport_no: pilgrimData.value.passport_number,
        };
        const response = await axios.post(`/registration/passport-verification`,postData);
        if(response.data.status === true){
            pilgrimData.value.name_en = response.data.data.passportData.full_name_english;
            pilgrimData.value.occupation = response.data.data.passportData.occupation;
            pilgrimData.value.marital_status = response.data.data.passportData.marital_status;
            pilgrimData.value.mobile = response.data.data.passportData.mobile;

            pilgrimData.value.permanent_post_code = response.data.data.passportData.per_post_code;
            pilgrimData.value.permanent_district_id = response.data.data.passportData.per_district_id;
            if(pilgrimData.value.permanent_district_id){
                permanentPoliceStation();
            }
            pilgrimData.value.permanent_police_station_id = response.data.data.passportData.per_thana_id;
            pilgrimData.value.permanent_address = response.data.data.passportData.per_village_ward;

            pilgrimData.value.object = response.data.data.requestData;
            handleOccupationChange();
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
    };
    const response = await axios.post(`/registration/check-duplicate-pilgrim`,postData);
    if(response.data.status === true){
        if(pilgrimData.value.identity === 'PASSPORT') {
            pilgrimData.value.name_en = response.data.data.passportData.full_name_english;
            pilgrimData.value.occupation = response.data.data.passportData.occupation;
            pilgrimData.value.marital_status = response.data.data.passportData.marital_status;
            pilgrimData.value.mobile = response.data.data.passportData.mobile;
            pilgrimData.value.spouse_name = response.data.data.passportData.spouse_name;
            pilgrimData.value.father_name_en = response.data.data.passportData.father_name_english;
            pilgrimData.value.mother_name_en = response.data.data.passportData.mother_name_english;

            // PERMANENT ADDRESS
            pilgrimData.value.permanent_post_code = response.data.data.passportData.per_post_code;
            pilgrimData.value.permanent_district_id = response.data.data.passportData.per_district_id;
            if (pilgrimData.value.permanent_district_id) {
                permanentPoliceStation();
            }
            pilgrimData.value.permanent_police_station_id = response.data.data.passportData.per_thana_id;
            pilgrimData.value.permanent_address = response.data.data.passportData.per_village_ward;

            //PRESENT ADDRESS
            pilgrimData.value.present_post_code = response.data.data.passportData.post_code;
            pilgrimData.value.present_district_id = response.data.data.passportData.district_id;
            if (pilgrimData.value.present_district_id) {
                currentPoliceStation();
            }
            pilgrimData.value.present_police_station_id = response.data.data.passportData.thana_id;
            pilgrimData.value.present_address = response.data.data.passportData.village_ward;

            pilgrimData.value.object = response.data.data.passportData;
            imagePreview.value.profile = response.data.data.passportData.passport_image;
            handleOccupationChange();
        }
        currentStep.value += 1;
    } else {
        alert(response.data.message);
        return false;
    }

}
const nextStep = async() => {
    if (validateStep()) {
        if (currentStep.value < 5) {
            // if(currentStep.value === 1 && passportFlag.value === 1 && pilgrimData.value.identity === "PASSPORT"){
            //     loadingNext.value = true;
            //     await passportVerification();
            // }
            if(currentStep.value === 1 && ['NID', 'PASSPORT', 'DOB'].includes(pilgrimData.value.identity) ){
                loadingNext.value = true;
                await checkDuplicatePilgrim();
            } else {
                currentStep.value++;
            }
            loadingNext.value = false;
        }
    } else {
        const errorMessages = Object.values(errors).filter(msg => msg).join('\n');
        alert(errorMessages);
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};
onMounted(() => {
    getConfigNidPassport();
    getIndexData();
});
const createImagePreview = (file, flag = 'profile') => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (e) => {
        if(flag === 'profile') {
            imagePreview.value.profile = e.target.result;
        } else {
            imagePreview.value.dob = e.target.result;
        }
    };
};

const changePerDistrict = (value) => {
    permanentPoliceStation();
}
const changePresentDistrict = (value) => {
    currentPoliceStation();
}

const changeBank = value => {
    getBankBranch();
}
const changeBankDistrict = value => {
    getBankBranch();
}

const handleOccupationChange = (value = '') => {
    if(govtServices.value[pilgrimData.value.occupation]) {
        isGovt.value = true;
    } else {
        isGovt.value = false;
    }
}

watch(() => pilgrimData.value.birth_date, (oldValue, newValue) => {
    age.value = calculateAge(pilgrimData.value.birth_date);
    if(age.value < 18 && pilgrimData.value.identity === 'NID') {
        pilgrimData.value.identity = '';
    }else if(age.value >= 18 && pilgrimData.value.identity === '') {
        pilgrimData.value.identity = 'NID';
    }
})


</script>

<template>
    <div class="card card-magenta border border-magenta">
        <div class="card-header">
            <h5 class="card-title pt-2 pb-2">Add Pilgrim</h5>
        </div>
        <div class="card-body">
            <!-- Step Indicator -->
            <div class="row">
                <div class="col-md-4"></div>
                <div class="stepper col-md-4">
                    <div :class="['step', currentStep >= 1 ? 'active' : '']">
                        <div class="circle">1</div>
                    </div>

                    <div :class="['line', currentStep > 1 ? 'completed' : '']"></div>
                    <div :class="['step', currentStep >= 2 ? 'active' : '']">
                        <div class="circle">2</div>
                    </div>

                    <div :class="['line', currentStep > 2 ? 'completed' : '']"></div>
                    <div :class="['step', currentStep >= 3 ? 'active' : '']">
                        <div class="circle">3</div>
                    </div>

                    <div :class="['line', currentStep > 3 ? 'completed' : '']"></div>
                    <div :class="['step', currentStep >= 4 ? 'active' : '']">
                        <div class="circle">4</div>
                    </div>

                    <div :class="['line', currentStep > 4 ? 'completed' : '']"></div>
                    <div :class="['step', currentStep >= 5 ? 'active' : '']">
                        <div class="circle">5</div>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <br>
            <!-- Form Steps -->
            <form>
                <!-- Step 1 -->
                <div v-if="currentStep === 1">
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Resident</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div class="col-md-7">
                            <label class="passport-radio">
                                <input name="resident" type="radio" :checked="pilgrimData.resident === 'Bangladeshi'" v-model="pilgrimData.resident" value="Bangladeshi" /> Bangladeshi
                            </label>
                            <label class="passport-radio">
                                <input name="resident" type="radio" :checked="pilgrimData.resident === 'NRB'" v-model="pilgrimData.resident" value="NRB" /> NRB
                            </label>
                        </div>
                    </div>
                    <template v-if="pilgrimData.resident !== 'Bangladeshi'">
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Nationality</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.nationality2 ? 'has-error' : '']">
                                <Select2 v-model="pilgrimData.nationality2"
                                         :options="nationalityArr"
                                         class="input-sm select2Field"
                                />
                                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.nationality2[0] }}</span>
                            </div>
                        </div>
                    </template>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Gender</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div class="col-md-7">
                            <label class="passport-radio">
                                <input name="gender" type="radio" checked="" v-model="pilgrimData.gender" value="male" /> Male
                            </label>
                            <label class="passport-radio">
                                <input name="gender" type="radio" v-model="pilgrimData.gender" value="female" /> Female
                            </label>
                        </div>
                    </div>

                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Birth Date</label>
                        <div class="col-md-1" style="font-weight: bold">:</div>
                        <div :class="['col-md-7', allerros.birth_date ? 'has-error' : '']">
                            <Datepicker
                                v-model="pilgrimData.birth_date"
                                :enableTimePicker="false"
                                type="date"
                                :maxDate="new Date()"
                                format="dd-MMM-yyyy"
                                placeholder="dd-mm-yyyy"
                                :text-input="true"
                                autoApply
                            />
                            <span v-if="allerros.birth_date" class="text-danger">{{ allerros.birth_date }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Identity Type</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div class="col-md-7">
                            <template v-if="age >= 18">
                                <label class="passport-radio">
                                    <input :disabled="age < 18" name="identity" type="radio" :checked="age >= 18" v-model="pilgrimData.identity" value="NID"  /> NID
                                </label>
                            </template>

                            <template v-if="passportFlag === 1">
                                <label class="passport-radio">
                                    <input name="identity" type="radio" v-model="pilgrimData.identity" value="PASSPORT" /> PASSPORT
                                </label>
                            </template>
                            <template v-if="age < 18 || pilgrimData.resident === 'NRB'">
                                <label class="passport-radio">
                                    <input name="identity" type="radio" v-model="pilgrimData.identity" value="DOB" /> Birth Certificate
                                </label>
                            </template>
                        </div>
                    </div>

                    <div v-if="pilgrimData.identity === 'PASSPORT' && passportFlag === 1">
                        <div  class="row form-group">
                            <label class="control-label col-md-3 required-star">Passport Type</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div class="col-md-7">
                                <label class="passport-radio">
                                    <input name="pass-type" type="radio" checked v-model="pilgrimData.passport_type" value="E-PASSPORT" /> E-PASSPORT
                                </label>
                                <label class="passport-radio">
                                    <input name="pass-type" type="radio" v-model="pilgrimData.passport_type" value="MRP" /> MRP
                                </label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Passport Number</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.passport_number ? 'has-error' : '']">
                                <input type="text" v-model="pilgrimData.passport_number" placeholder="Passport number" class="form-control" />
                                <span v-if="allerros.passport_number" class="text-danger">{{ allerros.passport_number[0] }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="pilgrimData.identity === 'NID'">
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">NID Number</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.nid_number ? 'has-error' : '']">
                                <input type="text" v-model="pilgrimData.nid_number" placeholder="NID number" class="form-control" />
                                <span v-if="allerros.nid_number" class="text-danger">{{ allerros.nid_number[0] }}</span>
                            </div>
                        </div>
                    </div>

                    <div v-else>
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Birth Registration Number</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.brn_number ? 'has-error' : '']">
                                <input type="text" v-model="pilgrimData.brn" placeholder="Birth registration number" class="form-control" />
                                <span v-if="allerros.brn_number" class="text-danger">{{ allerros.brn_number[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Step 2 -->
                <div v-if="currentStep === 2">
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Name in Bangla</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.name_bn ? 'has-error' : '']">
                            <input type="text" v-model="pilgrimData.name_bn" placeholder="Name in Bangla" class="form-control" />
                            <span v-if="allerros.name_bn" class="text-danger">{{ allerros.name_bn[0] }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Name in English</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.name_en ? 'has-error' : '']">
                            <input type="text" v-model="pilgrimData.name_en" placeholder="Name in English" class="form-control" />
                            <span v-if="allerros.name_en" class="text-danger">{{ allerros.name_en[0] }}</span>
                        </div>
                    </div>
                    <template v-if="pilgrimData.identity === 'PASSPORT'">
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Father Name</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.father_name_en ? 'has-error' : '']">
                                <input type="text" v-model="pilgrimData.father_name_en" placeholder="Father Name" class="form-control" />
                                <span v-if="allerros.father_name_en" class="text-danger">{{ allerros.father_name_en[0] }}</span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Mother Name </label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.mother_name_en ? 'has-error' : '']">
                                <input type="text" v-model="pilgrimData.mother_name_en" placeholder="Mother Name " class="form-control" />
                                <span v-if="allerros.mother_name_en" class="text-danger">{{ allerros.mother_name_en[0] }}</span>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Father Name in Bangla</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.father_name_bn ? 'has-error' : '']">
                                <input type="text" v-model="pilgrimData.father_name_bn" placeholder="Father Name in Bangla" class="form-control" />
                                <span v-if="allerros.father_name_bn" class="text-danger">{{ allerros.father_name_bn[0] }}</span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Mother Name in Bangla </label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.mother_name_bn ? 'has-error' : '']">
                                <input type="text" v-model="pilgrimData.mother_name_bn" placeholder="Mother Name in Bangla " class="form-control" />
                                <span v-if="allerros.mother_name_bn" class="text-danger">{{ allerros.mother_name_bn[0] }}</span>
                            </div>
                        </div>
                    </template>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Occupation</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.occupation ? 'has-error' : '']">
                            <Select2 v-model="pilgrimData.occupation"
                                     :options="occupationList"
                                     class="input-sm select2Field"
                                     @select="handleOccupationChange($event)"
                            />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.occupation[0] }}</span>
                        </div>
                    </div>

                    <template v-if="isGovt">
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Designation</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.designation ? 'has-error' : '']">
                                <input type="text" v-model="pilgrimData.designation" placeholder="Designation" class="form-control" />
                                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.designation[0] }}</span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Govt Service Grade</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.serviceGrade ? 'has-error' : '']">
                                <Select2 v-model="pilgrimData.serviceGrade"
                                         :options="govtServiceGrade"
                                         class="input-sm select2Field"
                                />
                                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.serviceGrade[0] }}</span>
                            </div>
                        </div>
                    </template>

                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Mobile Number</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.pilgrimData ? 'has-error' : '']">
                            <input type="text" v-model="pilgrimData.mobile" placeholder="Mobile number" class="form-control" />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.mobile[0] }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Marital Status</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.pilgrimData ? 'has-error' : '']">
                            <select v-model="pilgrimData.marital_status" class="form-control input-sm">
                                <option value="">Select one</option>
                                <option value="Married">Married</option>
                                <option value="Unmarried">Unmarried</option>
                                <option value="Others">Others</option>
                            </select>
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.marital_status[0] }}</span>
                        </div>
                    </div>
                    <template v-if="pilgrimData.marital_status === 'Married'">
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Spouse Name</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.pilgrimData ? 'has-error' : '']">
                                <input type="text" v-model="pilgrimData.spouse_name" placeholder="Spouse Name" class="form-control" />
                                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.spouse_name[0] }}</span>
                            </div>
                        </div>
                    </template>
                </div>
                <!-- Step 3 -->
                <div v-if="currentStep === 3">
                    <div class="row form-group">
                        <h6 class="control-label col-md-8"><b> Permanent Address (In Bangladesh)</b></h6> <br>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Post Code</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-6', allerros.permanent_post_code ? 'has-error' : '']">
                            <input type="text" v-model="pilgrimData.permanent_post_code" placeholder="Post Code" class="form-control" />
                            <span v-if="allerros.permanent_post_code" class="text-danger">{{ allerros.permanent_post_code }}</span>
                        </div>
                        <div class="col-md-1"> <button type="button" @click="permanemtDistrictPoliceByPostCode" class="btn btn-primary">Search</button></div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">District</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.permanent_district_id ? 'has-error' : '']">
                            <Select2 v-model="pilgrimData.permanent_district_id"
                                     :options="districtList"
                                     class="input-sm"
                                     @select="changePerDistrict($event)"
                            />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.permanent_district_id[0] }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Police Station</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.permanent_police_station_id ? 'has-error' : '']">
                            <Select2 v-model="pilgrimData.permanent_police_station_id"
                                     :options="permanentPoliceStationList"
                                     class="input-sm"
                            />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.permanent_police_station_id }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Address</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.permanent_address ? 'has-error' : '']">
                            <input type="text" v-model="pilgrimData.permanent_address" placeholder="Address" class="form-control" />
                            <span v-if="allerros.permanent_address" class="text-danger">{{ allerros.permanent_address }}</span>
                        </div>
                    </div>

                    <div class="row form-group">
                        <h6 class="control-label col-md-8"> <b>Current Address (In Bangladesh)</b></h6> <br>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Post Code</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-6', allerros.present_post_code ? 'has-error' : '']">
                            <input type="text" v-model="pilgrimData.present_post_code" placeholder="Post Code" class="form-control" />
                            <span v-if="allerros.present_post_code" class="text-danger">{{ allerros.present_post_code }}</span>
                        </div>
                        <div class="col-md-1"> <button type="button" @click="currentDistrictPoliceByPostCode"  class="btn btn-primary">Search</button></div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">District</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.present_district_id ? 'has-error' : '']">

                            <Select2 v-model="pilgrimData.present_district_id"
                                     :options="districtList"
                                     class="input-sm"
                                     @select="changePresentDistrict($event)"
                            />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.present_district_id }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Police Station</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.present_police_station_id ? 'has-error' : '']">
                            <Select2 v-model="pilgrimData.present_police_station_id"
                                     :options="currentPoliceStationList"
                                     class="input-sm"
                                     @select="changePresentDistrict($event)"
                            />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.present_police_station_id[0] }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Address</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.present_address ? 'has-error' : '']">
                            <input type="text" v-model="pilgrimData.present_address" placeholder="Address" class="form-control" />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.present_address[0] }}</span>
                        </div>
                    </div>
                </div>
                <!-- Step 4 -->
                <div v-if="currentStep === 4">
                    <div class="row form-group">
                        <h6 class="control-label col-md-8"><b> Refundable Account Information</b></h6> <br>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Account Holder Type</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.refund_account_type ? 'has-error' : '']">
                            <select v-model="pilgrimData.refund_account_type" class="form-control input-sm">
                                <option value="">Select one</option>
                                <option v-for="(account_owner,index) in accountHolderType" :key="index" :value="index">
                                    {{ account_owner }}
                                </option>
                            </select>
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_account_type[0] }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Account Number</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.refund_account_number ? 'has-error' : '']">
                            <input type="text" v-model="pilgrimData.refund_account_number" placeholder="Account Number" class="form-control" />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_account_number[0] }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Account Holder Name</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.refund_account_name ? 'has-error' : '']">
                            <input type="text" v-model="pilgrimData.refund_account_name" placeholder="Account Holder Name" class="form-control" />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_account_name[0] }}</span>
                        </div>
                    </div>

                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Bank Name</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.refund_bank_id ? 'has-error' : '']">
                            <Select2 v-model="pilgrimData.refund_bank_id"
                                     :options="bankList"
                                     class="input-sm"
                                     @select="changeBank($event)"
                            />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_bank_id[0] }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Branch District</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.refund_branch_district ? 'has-error' : '']">
                            <Select2 v-model="pilgrimData.refund_branch_district"
                                     :options="bankDistrictList"
                                     class="input-sm"
                                     @select="changeBankDistrict($event)"
                            />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_branch_district[0] }}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-md-3 required-star">Branch & Routing No</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.refund_routing_no ? 'has-error' : '']">
                            <Select2 v-model="pilgrimData.refund_routing_no"
                                     :options="bankBranchList"
                                     class="input-sm"
                            />
                            <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_routing_no[0] }}</span>
                        </div>
                    </div>

                    <div class="row form-group" v-if="pilgrimData.identity !== 'PASSPORT'">
                        <label class="control-label col-md-3 required-star">Photo</label>
                        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                        <div :class="['col-md-7', allerros.pilgrim_img ? 'has-error' : '']">
                            <input type="file" id="pilgrim_img" ref="pilgrim_img" class="form-control" @change="onFileChange" />
                            <span v-if="allerros.pilgrim_img" class="text-danger">{{ allerros.pilgrim_img[0] }}</span>
                            <span class="help-block">[File Format: *.jpg/ .jpeg/.png/ .gif | Max size 1 MB]</span>

                        </div>
                    </div>

                    <template v-if="pilgrimData.identity === 'DOB'">
                        <div class="row form-group">
                            <label class="control-label col-md-3 required-star">Birth Certificate Image</label>
                            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                            <div :class="['col-md-7', allerros.pilgrim_img ? 'has-error' : '']">
                                <input type="file" id="pilgrim_img" ref="dob_img" class="form-control" @change="onDobImgChange" />
                                <span v-if="allerros.pilgrim_img" class="text-danger">{{ allerros.pilgrim_img[0] }}</span>
                                <span class="help-block">[File Format: *.jpg/ .jpeg/.png/ .gif | Max size 1 MB]</span>
                            </div>
                        </div>
                    </template>
                    <div class="row mb-4" v-if="pilgrimData.identity !== 'PASSPORT'">
                        <div class="col-md-3"></div>
                        <div class="col-md-1"></div>
                        <div class="col-md-3 mt-3" v-if="imagePreview.profile">
                            <p>Pilgrim Image</p>
                            <img :src="imagePreview.profile" alt="" class="image-preview">
                        </div>
                        <div class="col-md-4 mt-3" v-if="imagePreview.dob && pilgrimData.identity === 'DOB'">
                            <p>Birth Certificate</p>
                            <img :src="imagePreview.dob" alt="" class="image-preview" style="max-width: 250px; border: 1px solid black; padding: 2px; border-radius: 5px;">
                        </div>
                    </div>
                </div>
                <div v-if="currentStep === 5">
                    <PreviewComponent
                        v-if="currentStep === 5"
                        :pilgrimData="pilgrimData"
                        :currentStep="currentStep"
                        :occupationList="occupationListObj"
                        :districtList="districtListObj"
                        :bankList="bankListObj"
                        :bankDistrictList="bankDistrictListObj"
                        :permanentPoliceStationList="permanentPSObj"
                        :currentPoliceStationList="currentPSObj"
                        :bankBranchList="bankBranchObj"
                        :imagePreview="imagePreview"
                        :isGovt="isGovt"
                        :govtServiceGradeObj="govtServiceGradeObj"
                        :nationalityObj="nationalityObj"
                        />
                </div>
                <template v-if="currentStep > 0">
                    <button type="button" class="btn btn-default float-left" @click="prevStep">
                        Previous
                    </button>
                </template>
                <template v-else>
                    <button type="button" class="btn btn-default float-left" disabled>
                        Previous
                    </button>
                </template>

                <template v-if="currentStep < 5">
                    <button v-if="!loadingNext" type="button" class="btn btn-primary float-right" @click="nextStep">
                        Next
                    </button>
                    <button v-else type="button" class="btn btn-primary float-right" disabled>
                        Loading...
                    </button>
                </template>

                <div v-if="currentStep === 5" class="buttons-container float-right">
                    <button v-if="!loadingNext" @click.prevent="submitForm" class="btn btn-primary ">Submit</button>
                    <button v-else class="btn btn-primary" disabled>Loading...</button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.passport-radio {
    margin-right: 20px;
}
.stepper {
    display: flex;
    align-items: center;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 5px;
}

.line {
    height: 2px;
    flex-grow: 1;
    background-color: #e0e0e0;
}

.active .circle {
    background-color: #007bff;
    color: white;
}

.completed .line {
    background-color: #007bff !important;
}
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
