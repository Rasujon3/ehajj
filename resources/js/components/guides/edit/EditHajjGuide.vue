<script setup>
import axios from "axios";
import {computed, onMounted, ref, watch} from 'vue';
import { useStore } from 'vuex';
import {useRouter, useRoute} from "vue-router";
import EditPilgrimInformation from "./EditPilgrimInformation.vue";
import EditExperience from "./EditExperience.vue";
import EditInformation from "./EditInformation.vue";
import EditPhoto from "./EditPhoto.vue";
import EditPermanentAddress from "./EditPermanentAddress.vue";
import EditPresentAddress from "./EditPresentAddress.vue";
import EditJobInformation from "./EditJobInformation.vue";
import EditEducationalInformation from "./EditEducationalInformation.vue";
import moment from "moment";

const router = useRouter();
const route = useRoute();
const store = useStore();

const id = route.params.id;
let permanentDivisions = ref([]);
let permanentDistricts = ref([]);
let permanentPoliceStation = ref([]);
let presentDivisions = ref([]);
let presentDistricts = ref([]);
let presentPoliceStation = ref([]);
let birthPlace = ref([]);
let guideInfo = ref({});
let guideVouchers = ref({});
const allerros = ref([]);
const isJobHolder = ref(null); // Default selection
let isLastDateExist = ref(false);

let guideData = ref({});

onMounted(async () => {
    $("#edit-guide-info").validate({
        errorPlacement: function () {
            return false;
        }
    });
    await getGuideProfileDetails(id);
    await getDivisionList();
    setTimeout(() => {
        isGuideRegistrationVisible.value = true;
    }, 1000);
    await checkGuideApplicationLastDate();
});

const getGuideProfileDetails = async (id) => {
    if (id === '') {
        alert('ID not found.');
        await router.push({ name: 'GuideApplicationList' });
        return false;
    }
    try {
        let form = new FormData();
        form.append('guide_id', id);
        let response = await axios.post('/guides/get-guide-profile-details', form);

        if (response.data.responseCode === 1) {
            guideData.value = response.data?.data?.guide_data[0];
            guideVouchers.value = response.data?.data?.guide_voucher_data;

            // handle for warning msg per & present address id
            guideData.value.district_id = String(guideData.value.district_id);
            guideData.value.thana_id = String(guideData.value.thana_id);
            guideData.value.per_district_id = String(guideData.value.per_district_id);
            guideData.value.per_thana_id = String(guideData.value.per_thana_id);
            guideData.value.birth_place_id = String(guideData.value.birth_place_id);

            if(Object.keys(guideData.value).length > 0) {
                await getAllDistrictList();
            }
            isJobHolder.value = guideData.value.is_employed === 'Yes' ? 'Yes' : 'No';
            guideData.value.hidden_ssc_certificate_link = guideData.value.ssc_certificate_link ? guideData.value.ssc_certificate_link : "";
            guideData.value.hidden_hsc_certificate_link = guideData.value.hsc_certificate_link ? guideData.value.hsc_certificate_link : "";
            guideData.value.hidden_honours_certificate_link = guideData.value.honours_certificate_link ? guideData.value.honours_certificate_link : "";
            guideData.value.hidden_masters_certificate_link = guideData.value.masters_certificate_link ? guideData.value.masters_certificate_link : "";
            guideData.value.hidden_profile_pic = guideData.value.profile_pic ? guideData.value.profile_pic : "";

            guideData.value.ssc_institute_name = guideData.value.ssc_institute_name ? guideData.value.ssc_institute_name : "";
            guideData.value.ssc_passing_year = guideData.value.ssc_passing_year ? guideData.value.ssc_passing_year : "";
            guideData.value.ssc_board_name = guideData.value.ssc_board_name ? guideData.value.ssc_board_name : "";
            guideData.value.ssc_grade = guideData.value.ssc_grade ? guideData.value.ssc_grade : "";
            guideData.value.ssc_certificate_link = guideData.value.ssc_certificate_link ? guideData.value.ssc_certificate_link : "";

            guideData.value.hsc_institute_name = guideData.value.hsc_institute_name ? guideData.value.hsc_institute_name : "";
            guideData.value.hsc_passing_year = guideData.value.hsc_passing_year ? guideData.value.hsc_passing_year : "";
            guideData.value.hsc_board_name = guideData.value.hsc_board_name ? guideData.value.hsc_board_name : "";
            guideData.value.hsc_grade = guideData.value.hsc_grade ? guideData.value.hsc_grade : "";
            guideData.value.hsc_certificate_link = guideData.value.hsc_certificate_link ? guideData.value.hsc_certificate_link : "";

            guideData.value.honours_institute_name = guideData.value.honours_institute_name ? guideData.value.honours_institute_name : "";
            guideData.value.honours_passing_year = guideData.value.honours_passing_year ? guideData.value.honours_passing_year : "";
            guideData.value.honours_board_name = guideData.value.honours_board_name ? guideData.value.honours_board_name : "";
            guideData.value.honours_grade = guideData.value.honours_grade ? guideData.value.honours_grade : "";
            guideData.value.honours_certificate_link = guideData.value.honours_certificate_link ? guideData.value.honours_certificate_link : "";

            guideData.value.masters_institute_name = guideData.value.masters_institute_name ? guideData.value.masters_institute_name : "";
            guideData.value.masters_passing_year = guideData.value.masters_passing_year ? guideData.value.masters_passing_year : "";
            guideData.value.masters_board_name = guideData.value.masters_board_name ? guideData.value.masters_board_name : "";
            guideData.value.masters_grade = guideData.value.masters_grade ? guideData.value.masters_grade : "";
            guideData.value.masters_certificate_link = guideData.value.masters_certificate_link ? guideData.value.masters_certificate_link : "";

            guideData.value.present_division_id = guideData.value.present_division_id ? guideData.value.present_division_id : "";
            guideData.value.present_division_name = guideData.value.present_division_name ? guideData.value.present_division_name : "";
            guideData.value.permanent_division_id = guideData.value.permanent_division_id ? guideData.value.permanent_division_id : "";
            guideData.value.permanent_division_name = guideData.value.permanent_division_name ? guideData.value.permanent_division_name : "";

            guideData.value.birth_place_id = guideData.value.birth_place_id ? guideData.value.birth_place_id : "0";
            guideData.value.birth_place = guideData.value.birth_place ? guideData.value.birth_place : "";
            guideData.value.gender = guideData.value.gender ? guideData.value.gender : "";

        } else {
            alert(response.data?.msg);
            await router.push({ name: 'GuideApplicationList' });
            return false;
        }
    } catch (error) {
        console.dir(error)
        alert('Something went wrong!!! [GPV: 001]');
        await router.push({ name: 'GuideApplicationList'});
    }
}

// Reactive property for checkbox state
const isTermsChecked = ref(false);

// Computed property for button disabled state
const isSubmitDisabled = computed(() => !isTermsChecked.value);

//const isJobHolder = ref(guideInfo.value.is_employed !== null ? 'Yes' : 'No'); // Default selection
const handleJobHolderUpdate = (newValue) => {
    isJobHolder.value = newValue;
};
let isSameAsPermanent = ref(false);
const getDivisionList = async () => {
    try {
        let response = await axios.get(`/guides/get-division-list`);
        if (response.data?.responseCode === 1) {
            permanentDivisions.value = [...response.data.data];
            presentDivisions.value = [...response.data.data];
        } else {
            permanentDivisions.value = [];
            presentDivisions.value = [];
        }
    } catch (error) {
        permanentDivisions.value = [];
        presentDivisions.value = [];
    }
}
const getAllDistrictList = async () => {
    try {
        let response = await axios.get(`/guides/get-all-district-list`);
        if (response.data?.responseCode === 1) {
            permanentDistricts.value = [];
            presentDistricts.value = [];
            //permanentPoliceStation.value = [];
            birthPlace.value = [];
            const fetchedDistricts = [...response.data?.data]?.map(district => ({
                id: district.area_id,
                text: district.area_nm_ban
            }));
            permanentDistricts.value = [{ id: '0', text: 'Select One' }, ...fetchedDistricts];
            presentDistricts.value = [{ id: '0', text: 'Select One' }, ...fetchedDistricts];
            birthPlace.value = [{ id: '0', text: 'Select One' }, ...fetchedDistricts];
        } else {
            permanentDistricts.value = [];
            presentDistricts.value = [];
            birthPlace.value = [];
        }
    } catch (error) {
        permanentDistricts.value = [];
        presentDistricts.value = [];
        birthPlace.value = [];
    }
}
const getPresentPoliceStationList = async (districtId = 1) => {
    try {
        let response = await axios.get(`/guides/get-police-station-list`, {
            params: {
                lang: 'en',
                districtId: districtId
            }
        });
        if (response.data?.responseCode === 1) {
            const fetchedPoliceStations = [...response.data?.data]?.map(policeStation => ({
                id: policeStation.area_id,
                text: policeStation.area_nm_ban
            }));
            presentPoliceStation.value = [];
            presentPoliceStation.value = [{ id: '0', text: 'Select One' }, ...fetchedPoliceStations];
        } else {
            presentPoliceStation.value = [];
        }
    } catch (error) {
        presentPoliceStation.value = [];
    }
}
const getPermanentPoliceStationList = async (districtId  = 1) => {
    try {
        let response = await axios.get(`/guides/get-police-station-list`, {
            params: {
                lang: 'en',
                districtId: districtId
            }
        });
        if (response.data?.responseCode === 1) {
            const fetchedPoliceStations = [...response.data?.data]?.map(policeStation => ({
                id: policeStation.area_id,
                text: policeStation.area_nm_ban
            }));
            permanentPoliceStation.value = [];
            permanentPoliceStation.value = [{ id: '0', text: 'Select One' }, ...fetchedPoliceStations];
        } else {
            permanentPoliceStation.value = [];
        }
    } catch (error) {
        permanentPoliceStation.value = [];
    }
}

// Define reactive states for visibility
const isGuideRegistrationVisible = ref(true);
const isGuidePilgrimInformationVisible = ref(false);

const handleNextClick = async () => {
    await router.push({ name: 'GuideVoucherEdit', params: { id } });
};
const handlePreviousClick = () => {
    isGuideRegistrationVisible.value = true;
    isGuidePilgrimInformationVisible.value = false;
};

function isFormValid() {
    const form = document.getElementById('edit-guide-info');
    return form.checkValidity();
}
function jobHolderValidation() {
    if (isJobHolder.value === 'Yes') {
        if (!guideData.value.occupation) {
            $('#occupation').addClass('has-error');
            alert('Occupation can not be empty');
            return false;
        } else {
            $('#designation').removeClass('has-error');
        }
        if (!guideData.value.designation) {
            $('#designation').addClass('has-error');
            alert('designation can not be empty');
            return false;
        } else {
            $('#designation').removeClass('has-error');
        }

        if (!guideData.value.office_name) {
            $('#office_name').addClass('has-error');
            alert('office_name can not be empty');
            return false;
        } else {
            $('#office_name').removeClass('has-error');
        }

        if (!guideData.value.office_address) {
            $('#office_address').addClass('has-error');
            alert('office_address can not be empty');
            return false;
        } else {
            $('#office_address').removeClass('has-error');
        }
    }
    if ((isJobHolder.value === 'Yes' && guideData.value.designation &&
        guideData.value.office_name && guideData.value.office_address)) {
        return true;
    }
    if (isJobHolder.value === 'No') {
        return true;
    }
}
function calculateAge(birthDate) {
    const today = new Date();
    const birthDateObj = new Date(birthDate);
    let age = today.getFullYear() - birthDateObj.getFullYear();
    const monthDiff = today.getMonth() - birthDateObj.getMonth();
    if (monthDiff < 0 || monthDiff === 0 && today.getDate() < birthDateObj.getDate()) {
        age--;
    }
    return age;
}
const dateOfBirthValidation = () => {
    if (!guideData.value.birth_date) {
        alert('Birth date is required.');
        return false;
    } else if (calculateAge(guideData.value.birth_date) < 18) {
        alert('You must be at least 18 years old');
        return false;
    } else {
        return true;
    }
};
function educationalValidation() {
    if (guideData.value.ssc_institute_name || guideData.value.ssc_grade || guideData.value.ssc_board_name
        || guideData.value.ssc_passing_year || guideData.value.ssc_certificate_link) {

        if (!guideData.value.ssc_institute_name) {
            alert('ssc_institute_name can not be empty.')
            return false;
        }
        if (!guideData.value.ssc_passing_year) {
            alert('ssc_passing_year can not be empty.')
            return false;
        }
        if (!guideData.value.ssc_board_name) {
            alert('ssc_board_name can not be empty.')
            return false;
        }
        if (!guideData.value.ssc_grade) {
            alert('ssc_grade can not be empty.')
            return false;
        }
        if (!guideData.value.ssc_certificate_link) {
            alert('ssc_certificate_link can not be empty.')
            return false;
        }
        return true;
    }

    if (guideData.value.hsc_institute_name || guideData.value.hsc_grade || guideData.value.hsc_board_name
        || guideData.value.hsc_passing_year || guideData.value.hsc_certificate_link ) {

        if (!guideData.value.hsc_institute_name) {
            alert('hsc_institute_name can not be empty.')
            return false;
        }
        if (!guideData.value.hsc_passing_year) {
            alert('hsc_passing_year can not be empty.')
            return false;
        }
        if (!guideData.value.hsc_board_name) {
            alert('hsc_board_name can not be empty.')
            return false;
        }
        if (!guideData.value.hsc_grade) {
            alert('hsc_grade can not be empty.')
            return false;
        }
        if (!guideData.value.hsc_certificate_link) {
            alert('hsc_certificate_link can not be empty.')
            return false;
        }
        return true;
    }

    if (guideData.value.honours_institute_name || guideData.value.honours_grade || guideData.value.honours_board_name
        || guideData.value.honours_passing_year || guideData.value.honours_certificate_link ) {
        if (!guideData.value.honours_institute_name) {
            alert('honours_institute_name can not be empty.')
            return false;
        }
        if (!guideData.value.honours_passing_year) {
            alert('honours_passing_year can not be empty.')
            return false;
        }
        if (!guideData.value.honours_board_name) {
            alert('honours_board_name can not be empty.')
            return false;
        }
        if (!guideData.value.honours_grade) {
            alert('honours_grade can not be empty.')
            return false;
        }
        if (!guideData.value.honours_certificate_link) {
            alert('honours_certificate_link can not be empty.')
            return false;
        }
        return true;
    }

    if (guideData.value.masters_institute_name || guideData.value.masters_grade || guideData.value.masters_board_name
        || guideData.value.masters_passing_year || guideData.value.masters_certificate_link) {
        if (!guideData.value.masters_institute_name) {
            alert('masters_institute_name can not be empty.')
            return false;
        }
        if (!guideData.value.masters_passing_year) {
            alert('masters_passing_year can not be empty.')
            return false;
        }
        if (!guideData.value.masters_board_name) {
            alert('masters_board_name can not be empty.')
            return false;
        }
        if (!guideData.value.masters_grade) {
            alert('masters_grade can not be empty.')
            return false;
        }
        if (!guideData.value.masters_certificate_link) {
            alert('masters_certificate_link can not be empty.')
            return false;
        }
        return true;
    }
    return true;
}
function permanentDistrictValidation() {
    if (guideData.value.per_district_id == '0') {
        $('#permanentDistrict').addClass('has-error');
        alert('Permanent district can not be empty');
        return false;
    } else {
        $('#permanentDistrict').removeClass('has-error');
        return true;
    }
}
function presentDistrictValidation() {
    if (guideData.value.district_id == '0') {
        $('#presentDistrict').addClass('has-error');
        alert('Permanent district can not be empty');
        return false;
    } else {
        $('#presentDistrict').removeClass('has-error');
        return true;
    }
}
function permanentThanaValidation() {
    if (guideData.value.per_thana_id == '0') {
        $('#permanentThana').addClass('has-error');
        alert('Permanent thana can not be empty');
        return false;
    } else {
        $('#permanentThana').removeClass('has-error');
        return true;
    }
}
function presentThanaValidation() {
    if (guideData.value.thana_id == '0') {
        $('#presentThana').addClass('has-error');
        alert('Present thana can not be empty');
        return false;
    } else {
        $('#presentThana').removeClass('has-error');
        return true;
    }
}
function nidValidation() {
    if(guideData.value.national_id.toString().length !== 10 &&
        guideData.value.national_id.toString().length !== 13 &&
        guideData.value.national_id.toString().length !== 17) {
        alert('National ID No. must be either 10, 13, or 17 characters long.');
        return false;
    } else {
        return true;
    }
}
const validateBanglaText = (input) => {
    const banglaPattern = /[\u0980-\u09FF\s\:\-\.\u0964]+/g;
    const filteredInput = input.match(banglaPattern) ? input.match(banglaPattern).join('') : '';
    return input === filteredInput;
}
const mobilePattern = /^(?:\+88|88)?(01[3-9]\d{8})$/;
function birthPlaceValidation() {
    if (!guideData.value.birth_place_id) {
        $('#birthPlace').addClass('has-error');
        alert('Birth place can not be empty');
        return false;
    } else {
        $('#birthPlace').removeClass('has-error');
        return true;
    }
}
const toggleFormButtons = (state) => {
    $("#submit-btn").prop('disabled', state);
    $("#next-btn").prop('disabled', state);
};
const saveForm = async () => {
    const isValid = await isFormValid();
    if (!isValid) {
        const requiredInputs = document.querySelectorAll('.required:invalid');
        const validInputs = document.querySelectorAll('.required:valid');

        if(requiredInputs.length > 0) {
            requiredInputs[0].focus();
        }
        requiredInputs.forEach(input => {
            input.classList.add('has-error');
        });
        validInputs.forEach(input => {
            input.classList.remove('has-error');
        });
        alert('Please fill up all required field.')
        return false;
    }
    const dateOfBirthValidationResponse = dateOfBirthValidation();
    const nidValidationResponse = nidValidation();
    const birthPlaceValidationResponse = birthPlaceValidation();
    const permanentDistrictValidationResponse = permanentDistrictValidation();
    const presentDistrictValidationResponse = presentDistrictValidation();
    const permanentThanaValidationResponse = permanentThanaValidation();
    const presentThanaValidationResponse = presentThanaValidation();
    const jobHolderValidationResponse = jobHolderValidation();
    const educationalValidationResponse = educationalValidation();

    if (!mobilePattern.test(guideData.value.mobile)) {
        alert('Invalid mobile number format');
        return false;
    }

    if (isValid && jobHolderValidationResponse && educationalValidationResponse
        && dateOfBirthValidationResponse && permanentDistrictValidationResponse
        && presentDistrictValidationResponse && permanentThanaValidationResponse
        && presentThanaValidationResponse && nidValidationResponse && birthPlaceValidationResponse
    ) {
        toggleFormButtons(true);
        try {
            const form = new FormData();
            form.append('full_name_bangla', guideData.value.full_name_bangla);
            form.append('birth_date', moment(guideData.value.birth_date).format('YYYY-MM-DD'));
            form.append('mother_name', guideData.value.mother_name);
            form.append('father_name', guideData.value.father_name);
            form.append('full_name_english', guideData.value.full_name_english);
            form.append('national_id', guideData.value.national_id);
            form.append('mobile', guideData.value.mobile);
            form.append('per_district', guideData.value.per_district);
            form.append('per_district_id', guideData.value.per_district_id);
            form.append('per_thana_id', guideData.value.per_thana_id);
            form.append('per_post_code', guideData.value.per_post_code);
            form.append('per_police_station', guideData.value.per_police_station);
            form.append('post_code', guideData.value.post_code);
            form.append('thana_id', guideData.value.thana_id);
            form.append('district', guideData.value.district);
            form.append('district_id', guideData.value.district_id);
            form.append('present_division_id', guideData.value.present_division_id);
            form.append('present_division_name', guideData.value.present_division_name);
            form.append('permanent_division_id', guideData.value.permanent_division_id);
            form.append('permanent_division_name', guideData.value.permanent_division_name);
            if (isSameAsPermanent.value) {
                form.append('police_station', guideData.value.per_police_station);
            } else {
                form.append('police_station', guideData.value.police_station);
            }
            form.append('isJobHolder', isJobHolder.value);
            if (isJobHolder.value === 'Yes') {
                form.append('occupation', guideData.value.occupation);
                form.append('designation', guideData.value.designation);
                form.append('office_name', guideData.value.office_name);
                form.append('office_address', guideData.value.office_address);
            } else {
                form.append('occupation', '');
                form.append('designation', '');
                form.append('office_name', '');
                form.append('office_address', '');
            }
            form.append('ssc_institute_name', guideData.value.ssc_institute_name);
            form.append('ssc_passing_year', guideData.value.ssc_passing_year);
            form.append('ssc_board_name', guideData.value.ssc_board_name);
            form.append('ssc_grade', guideData.value.ssc_grade);
            form.append('ssc_certificate_link', guideData.value.ssc_certificate_link);
            form.append('hsc_institute_name', guideData.value.hsc_institute_name);
            form.append('hsc_passing_year', guideData.value.hsc_passing_year);
            form.append('hsc_board_name', guideData.value.hsc_board_name);
            form.append('hsc_grade', guideData.value.hsc_grade);
            form.append('hsc_certificate_link', guideData.value.hsc_certificate_link);
            form.append('honours_institute_name', guideData.value.honours_institute_name);
            form.append('honours_passing_year', guideData.value.honours_passing_year);
            form.append('honours_board_name', guideData.value.honours_board_name);
            form.append('honours_grade', guideData.value.honours_grade);
            form.append('honours_certificate_link', guideData.value.honours_certificate_link);
            form.append('masters_institute_name', guideData.value.masters_institute_name);
            form.append('masters_passing_year', guideData.value.masters_passing_year);
            form.append('masters_board_name', guideData.value.masters_board_name);
            form.append('masters_grade', guideData.value.masters_grade);
            form.append('masters_certificate_link', guideData.value.masters_certificate_link);
            form.append('experience', guideData.value.experience);
            form.append('additional_experience', guideData.value.additional_experience);
            form.append('profile_pic', guideData.value.profile_pic);
            form.append('present_address', guideData.value.present_address);
            form.append('permanent_address', guideData.value.permanent_address);

            form.append('hidden_profile_pic', guideData.value.hidden_profile_pic);
            form.append('hidden_ssc_certificate_link', guideData.value.hidden_ssc_certificate_link);
            form.append('hidden_hsc_certificate_link', guideData.value.hidden_hsc_certificate_link);
            form.append('hidden_honours_certificate_link', guideData.value.hidden_honours_certificate_link);
            form.append('hidden_masters_certificate_link', guideData.value.hidden_masters_certificate_link);

            form.append('birth_place_id', guideData.value.birth_place_id);
            form.append('birth_place', guideData.value.birth_place);
            form.append('gender', guideData.value.gender);

            const id = guideData.value.id;
            await axios.post(`/guides/update-haj-guide/${id}`, form, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            }).then(async function (resp) {
                if (resp?.data?.status === 200) {
                    alert('Data updated successfully!');
                    await store.dispatch('resetGuideInfo');
                    await router.push({ name: 'GuideApplicationList', params: { id } });
                }
                if (resp?.data?.status === 422) {
                    allerros.value = resp?.data?.msg;
                    alert(resp?.data?.msg);
                }
                if (resp?.data?.status === false) {
                    alert(resp.data?.msg);
                }
                toggleFormButtons(false);
            }).catch((error) => {
                console.dir(error)
                allerros.value = error.response?.data?.errors;
                toggleFormButtons(false);
            });
        } catch (error) {
            console.dir(error)
            allerros.value = error.response?.data?.errors;
            toggleFormButtons(false);
        }
    }
};

// Watch for changes to isGuidePilgrimInformationVisible
watch(isGuidePilgrimInformationVisible, (newValue) => {
    if (newValue) {
        scrollToTop();
    }
});

// Function to scroll to the top of the page
const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
const checkGuideApplicationLastDate = async () => {
    isLastDateExist.value = false;
    try {
        const response = await axios.get(`/guides/check-guide-application-last-date`);
        if (response.data?.responseCode === 1) {
            isLastDateExist.value = true;
        } else {
            isLastDateExist.value = false;
        }
    } catch (error) {
        isLastDateExist.value = false;
    }
}

</script>

<template>
    <form @submit.prevent="saveForm" id="edit-guide-info">
        <!--        <div v-if="isGuideRegistrationVisible" class="hajj-main-content" id="guideRegistration">-->
        <div v-if="isGuideRegistrationVisible" class="hajj-main-content" id="guideRegistration">
            <div class="container">
                <!-- Step for guide reg start -->
                <div class="guide-pilgrim-steps">
                    <div class="gp-step-item step-active">
                        <span class="step-circle">
                            <span class="step-number">1</span>
                        </span>
                        <span class="step-title">Guide Registration</span>
                    </div>
                    <div class="gp-step-item">
                        <span class="step-circle">
                            <span class="step-number">2</span>
                        </span>
                        <span class="step-title">Pilgrim Information</span>
                    </div>
                </div>
                <!-- Step for guide reg end -->
                <div class="guide-reg-content">
                    <div class="border-card-block">
                        <div class="bd-card-content">
                            <EditInformation
                                :guideData="guideData"
                                :birthPlace="birthPlace"
                                :allerros="allerros"
                            />

                            <EditPhoto
                                :guideData="guideData"
                                :allerros="allerros"
                            />

                            <div class="row">
                                <EditPermanentAddress
                                    :guideData="guideData"
                                    :allerros="allerros"
                                    :permanentDivisions="permanentDivisions"
                                    :permanentDistricts="permanentDistricts"
                                    :permanentPoliceStation="permanentPoliceStation"
                                    @getPermanentPoliceStationList="getPermanentPoliceStationList"
                                />

                                <EditPresentAddress
                                    :guideData="guideData"
                                    :allerros="allerros"
                                    :presentDivisions="presentDivisions"
                                    :presentDistricts="presentDistricts"
                                    :presentPoliceStation="presentPoliceStation"
                                    @getPresentPoliceStationList="getPresentPoliceStationList"
                                    :isSameAsPermanent="isSameAsPermanent"
                                    @toggleSameAsPermanent="isSameAsPermanent = $event"
                                />
                            </div><!-- /row -->

                            <EditJobInformation
                                :guideData="guideData"
                                :allerros="allerros"
                                :isJobHolder="isJobHolder"
                                @update:isJobHolder="handleJobHolderUpdate"
                            />

                            <EditEducationalInformation
                                :guideData="guideData"
                                :allerros="allerros"
                            />

                            <EditExperience
                                :guideData="guideData"
                                :allerros="allerros"
                            />

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="alert alert-warning terms-alert" role="alert">
                                        <div class="term-notice-block">
                                            <input class="checkbox mr-2" type="checkbox" id="user_reg_terms" v-model="isTermsChecked">
                                            <label class="text-sm mb-0" for="user_reg_terms">আমি এই মর্মে অঙ্গীকার করছি যে, আবেদন ফরমে তথ্য সঠিক এবং সংযুক্ত সকল ডকুমেন্ট স্পষ্টভাবে এবং সকল পাতা প্রদান করা হয়েছে মর্মে আমি নিশ্চিত হয়েছি।</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bd-card-footer">
                            <div class="flex-space-btw info-btn-group">
                                <router-link :to="{ name: 'GuideApplicationList' }">
                                    <button class="btn btn-default"><span>Close</span></button>
                                </router-link>
                                <div class="footer-btn-group">
                                    <button v-if="isLastDateExist && (guideData.status_id == 0 || guideData.status_id == 5)" id="submit-btn" class="btn btn-squire btn-outline-blue" type="submit" :disabled="isSubmitDisabled">
                                        <span>Update</span>
                                    </button>
                                    <button id="next-btn" class="btn btn-blue" type="button" @click="handleNextClick" :disabled="isSubmitDisabled">
                                        <span>Next</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--        <div v-if="isGuidePilgrimInformationVisible" id="guidePilgrimInformation">-->
        <div v-if="isGuidePilgrimInformationVisible" :class="isGuidePilgrimInformationVisible ? 'd-block' : 'd-none'" id="guidePilgrimInformation">
            <EditPilgrimInformation
                @previous-click="handlePreviousClick"
                :guideData="guideData"
                :allerros="allerros"
            />
        </div>

    </form>
</template>

<style scoped>
.has-error {
    border: 1px solid red;
}
.has-tr-error {
    border: 2px solid red;
}
</style>
