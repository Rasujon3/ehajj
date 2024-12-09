<script setup>
import {ref, onMounted, provide, reactive, watch} from 'vue';
import { useRoute, useRouter } from 'vue-router';

import Step2 from "./Step2.vue";
import Step3 from "./Step3.vue";
import Step4 from "./Step4.vue";
import Indicator from "./Indicator.vue";
import EditPreview from "./EditPreview.vue";
import axios from "axios";
import moment from "moment/moment";
import Step1 from "./Step1.vue";

const router = useRouter();
const route = useRoute();
const id = ref(null);
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

    name_bn: '',
    name_en: '',
    father_name_bn: '',
    mother_name_bn: '',
    father_name_en: '',
    mother_name_en: '',
    occupation: '',
    mobile: '',
    marital_status: '',

    permanent_post_code: '',
    permanent_district_id: '',
    permanent_police_station_id: '',
    permanent_address: '',

    present_post_code: '',
    present_district_id: '',
    present_police_station_id: '',
    present_address: '',

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
const isGovtJob = ref(false);

provide('pilgrimData', pilgrimData);
provide('indexData', indexData);
provide('allerros', allerros);
provide('permanentPoliceStationList', permanentPoliceStationList);
provide('currentPoliceStationList', currentPoliceStationList);
provide('bankBranchList', bankBranchList);
provide('bankBranchListArr', bankBranchListArr);
provide('permanentPSArr', permanentPSArr);
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

    pilgrimData.value.name_bn = pilgrim.full_name_bangla;
    pilgrimData.value.name_en = pilgrim.full_name_english;
    pilgrimData.value.father_name_bn = pilgrim.father_name;
    pilgrimData.value.mother_name_bn = pilgrim.mother_name;
    pilgrimData.value.father_name_en = pilgrim.father_name_english;
    pilgrimData.value.mother_name_en = pilgrim.mother_name_english;
    pilgrimData.value.occupation = pilgrim.occupation;
    pilgrimData.value.mobile = pilgrim.mobile;
    pilgrimData.value.marital_status = pilgrim.marital_status;

    pilgrimData.value.permanent_post_code = pilgrim.per_post_code;
    pilgrimData.value.permanent_district_id = pilgrim.per_district_id;
    pilgrimData.value.permanent_police_station_id = pilgrim.per_thana_id;
    pilgrimData.value.permanent_address = pilgrim.per_village_ward;

    pilgrimData.value.present_post_code = pilgrim.post_code;
    pilgrimData.value.present_district_id = pilgrim.district_id;
    pilgrimData.value.present_police_station_id = pilgrim.thana_id;
    pilgrimData.value.present_address = pilgrim.village_ward;
    pilgrimData.value.tracking_no = pilgrim.tracking_no;

    pilgrimData.value.designation = pilgrim.designation;
    pilgrimData.value.serviceGrade = pilgrim.govt_service_grade;
    pilgrimData.value.spouse_name = pilgrim.spouse_name;
    pilgrimData.value.nationality2 = pilgrim.nationality2 ? pilgrim.nationality2 : '0';

}
const fetchPilgrimDataFromApi = async (pilgrimId) => {
    const postData = {
        "pilgrimId": pilgrimId,
    }
    loadingNext.value = true;
    const response = await axios.post(`/pilgrim/pre-reg/get-edit-pilgrim-data`, postData);
    loadingNext.value = false;
    if(response.data.responseCode !== 1) {
        alert('Pilgrim Info not found');
        router.push({ name: 'PreRegPilgrimsList' });
    }
    if(response.data.editMood > 0) {
        alert('Can\'t Edit this Pilgrim due to Payment or Permission');
        router.push({ name: 'PreRegPilgrimsList' });
    }
    setPilgrimData(response.data.pilgrim);
    imagePreview.value.profile = response.data.profile_pic;
    indexData.value.pilgrimImgae = response.data.profile_pic;
    imagePreview.value.dob = response.data.birthCertificate;
    pilgrimData.value.refund_account_type = response.data.refundInfo.owner_type;
    pilgrimData.value.refund_account_name = response.data.refundInfo.account_holder_name;
    pilgrimData.value.refund_account_number = response.data.refundInfo.account_number;
    pilgrimData.value.refund_bank_id = response.data.refundInfo.bank_id;
    pilgrimData.value.refund_branch_district = response.data.refundInfo.dist_code;
    pilgrimData.value.refund_routing_no = response.data.refundInfo.bb_routing_id;
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
    id.value = pilgrimId;
    await fetchPilgrimDataFromApi(pilgrimId);
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
}
const prevStep = () => {
    if(currentStep.value > 1)
    currentStep.value -= 1;
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
                } else if (nidValue.length !== 10 && nidValue.length !== 17) {
                    errors.nid_number = 'জাতীয় পরিচয়পত্র নম্বর ১০ সংখ্যা অথবা ১৭ সংখ্যার হতে হবে। আপনার জাতীয় পরিচয়পত্রে ১৩ সংখ্যা থাকলে তার আগে আপনার জন্ম সন যোগ করুন।';
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
            if (!pilgrimData.value.name_en) {
                errors.name_en = 'Name in English is required';
                return false;
            }

            if(pilgrimData.value.identity !== 'PASSPORT') {
                if (!pilgrimData.value.name_bn) {
                    errors.name_bn = 'Name in Bangla is required';
                    return false;
                } else if(!validateBanglaText(pilgrimData.value.name_bn)) {
                    errors.name_bn = 'Name in Bangla is only input bangla character';
                    return false;
                }
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
            break;
        case 3:
            if (!pilgrimData.value.permanent_post_code) {
                errors.permanent_post_code = 'Permanent Post Code is required';
                return false;
            }
            if (!pilgrimData.value.permanent_district_id) {
                errors.permanent_district_id = 'Permanent District is required';
                return false;
            }
            if (!pilgrimData.value.permanent_police_station_id) {
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
            if (!pilgrimData.value.present_district_id) {
                errors.present_district_id = 'Present District is required';
                return false;
            }
            if (!pilgrimData.value.present_police_station_id) {
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
    pilgrimData.value.pilgrim_img = file;
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
        form.append('permanent_district_name', indexData.value.districtList[pilgrimData.value.permanent_district_id]);
        form.append('permanent_police_station_id', pilgrimData.value.permanent_police_station_id);
        form.append('permanent_police_station_name', permanentPoliceStationList.value[pilgrimData.value.permanent_police_station_id]);
        form.append('permanent_address', pilgrimData.value.permanent_address);

        form.append('present_post_code', pilgrimData.value.present_post_code);
        form.append('present_district_id', pilgrimData.value.present_district_id);
        form.append('present_district_name', indexData.value.districtList[pilgrimData.value.present_district_id]);
        form.append('present_police_station_id', pilgrimData.value.present_police_station_id);
        form.append('present_police_station_name', currentPoliceStationList.value[pilgrimData.value.present_police_station_id]);
        form.append('present_address', pilgrimData.value.present_address);

        form.append('refund_account_type', pilgrimData.value.refund_account_type);
        form.append('refund_account_name', pilgrimData.value.refund_account_name);
        form.append('refund_account_number', pilgrimData.value.refund_account_number);
        form.append('refund_bank_id', pilgrimData.value.refund_bank_id);
        form.append('refund_branch_district', pilgrimData.value.refund_branch_district);
        form.append('refund_routing_no', pilgrimData.value.refund_routing_no);

        form.append('nationalId_img', pilgrimData.value.nationalId_img);
        form.append('pilgrim_img', pilgrimData.value.pilgrim_img);
        form.append('tracking_no', pilgrimData.value.tracking_no);
        form.append('pilgrim_id', id.value);

        form.append('designation', pilgrimData.value.designation);
        form.append('serviceGrade', pilgrimData.value.serviceGrade);
        form.append('spouse_name', pilgrimData.value.spouse_name);
        form.append('is_govt_job', isGovtJob.value);
        form.append('dob_img', pilgrimData.value.dob_img);

        loadingNext.value = true;
        const response = await axios.post('/pilgrim/pre-reg/pilgrim-update', form, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        loadingNext.value = false;
        if(response.data.responseCode !== 1) {
            //alert(response.data.msg);
            currentStep.value = 1;
            toastr.error(response.data.msg, '', {
                positionClass: 'toast-top-right',
            });
            return false;
        }
        if (response.data.status === true) {
            toastr.success('Pilgrim data updated successfully.', '', {
                positionClass: 'toast-top-right',
            });
            router.push({ path: '/pilgrims-list' });
        }
    } catch (error) {
        allerros.value = error.response.data.errors;
    }
}

const getPoliceStation = async (district_id,flag) => {
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
    const district_ID = pilgrimData.value.permanent_district_id;
    const flag = 'permanent';
    getPoliceStation(district_ID,flag)
};
const currentPoliceStation = () => {
    const current_district_ID = pilgrimData.value.present_district_id;
    const flag = 'current';
    getPoliceStation(current_district_ID,flag)
};

watch(() => pilgrimData.value.permanent_district_id, (newVal, oldVal) => {
    permanentPoliceStation(newVal);
});
watch(() => pilgrimData.value.present_district_id, (newVal, oldVal) => {
    currentPoliceStation(newVal);
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
    if(newVal) {
        imagePreview.value.profile = newVal;
    } else {
        imagePreview.value.profile = indexData.value.pilgrimImgae;
    }
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
            <h5 class="card-title pt-2 pb-2">Edit Pilgrim</h5>
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

                <!-- Step 4 -->
                <template v-if="currentStep === 3">
                    <Step3 />
                </template>

                <!-- Step 4 -->
                <template v-if="currentStep === 4">
                    <Step4 :imagePreview="imagePreview" @onFileChange="onFileChange" @onDOBFileChange="onDOBFileChange" />
                </template>
                <!-- Step 5 -->
                <template v-if="currentStep === 5">
                    <EditPreview :imagePreview="imagePreview" />
                </template>

                <!-- Buttons 4 -->
                <button type="button" class="btn btn-default float-left" @click="prevStep" :disabled="currentStep < 2">
                    পূর্ববর্তী
                </button>
                <button v-if="currentStep < 5 && !loadingNext" type="button" class="btn btn-primary float-right" @click="nextStep">
                    পরবর্তী
                </button>
                <button v-if="currentStep < 5 && loadingNext" type="button" class="btn btn-primary float-right" disabled>
                    অপেক্ষা করুন...
                </button>
                <button v-if="currentStep === 5 && !loadingNext" type="button" class="btn btn-primary float-right" @click="submitData">
                    আপডেট
                </button>
                <button v-if="currentStep === 5 && loadingNext" type="button" class="btn btn-primary float-right" disabled>
                    অপেক্ষা করুন...
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
