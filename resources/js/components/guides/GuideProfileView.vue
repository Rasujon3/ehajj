<script setup>
import axios from "axios";
import {onMounted, ref} from "vue";
import {useRoute, useRouter} from "vue-router";
import { useStore } from 'vuex';
import moment from 'moment';

const router = useRouter();
const route = useRoute();
const store = useStore();

let guideProfileInfo = ref({});
let guideVouchers = ref([]);
let ownPilgrimList = ref([]);
const trackingNo = ref('');
let isLoading = ref(false);
let isVoucherDeleteLoading = ref(false);
let isPilgrimAddLoading = ref(false);
let isSubmitLoading = ref(false);
let isReSubmitLoading = ref(false);
let registeredPilgrimList = ref([]);
let checkedPilgrims = ref([]);
let isLastDateExist = ref(false);

const id = route.params.id;

onMounted(async () => {
    await getGuideProfileDetails(id);
    await getOwnPilgrims();
    await checkGuideApplicationLastDate();
});
const getGuideProfileDetails = async (id) => {
    if (id === '') {
        alert('ID not found.');
        return false;
    }
    try {
        let form = new FormData();
        form.append('guide_id', id);
        let response = await axios.post('/guides/get-guide-profile-details', form);
        if (response.data.responseCode === 1) {
            guideProfileInfo.value = response.data?.data?.guide_data[0];
            guideVouchers.value = response.data?.data?.guide_voucher_data;
        }
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg);
            await router.push({ name: 'GuideApplicationList' });
            return false;
        }
    } catch (error) {
        alert('Something went wrong!!! [GPV: 001]');
        await router.push({ name: 'GuideApplicationList'});
    }
}
const editApplication = async (id) => {
    await router.push({ name: 'GuideEdit', params: { id } });
};
const getOwnPilgrims = async () => {
    try {
        let response = await axios.get(`/guides/get-own-pilgrims`);
        if (response.data.responseCode === 1) {
            ownPilgrimList.value = response.data?.data;
        }
        if (response.data?.responseCode === -1) {
            ownPilgrimList.value = [];
            return false;
        }
    } catch (error) {
        ownPilgrimList.value = [];
        alert('Something went wrong!!! [GPV: 002]');
        return false;
    }
}
const onTrackingNoSubmit = async () => {
    isLoading.value = true;
    if (trackingNo.value.trim() === '') {
        alert('Please add tracking no.');
        isLoading.value = false;
        return false;
    }
    try {
        isLoading.value = true;
        let form = new FormData();
        form.append('trackingNo', trackingNo.value);
        let response = await axios.post('/guides/get-registered-pilgrim-by-tracking-no', form);

        if (response.data.responseCode === 1) {
            registeredPilgrimList.value = response.data?.data;
            trackingNo.value = '';
            isLoading.value = false;
        }
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg)
            registeredPilgrimList.value = [];
            trackingNo.value = '';
            isLoading.value = false;
            return false;
        }
    } catch (error) {
        alert('Something went wrong!!! [GPV: 003]');
        registeredPilgrimList.value = [];
        trackingNo.value = '';
        isLoading.value = false;
    }
}
const onPilgrimTabOneClick = () => {
    registeredPilgrimList.value = [];
    // Uncheck all checkboxes
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    checkedPilgrims.value = [];
}
const onPilgrimTabTwoClick = () => {
    registeredPilgrimList.value = [];
    // Uncheck all checkboxes
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    checkedPilgrims.value = [];
}
const handleCheckboxChange = (registeredPilgrim, event) => {
    const pilgrimData = {
        pilgrim_id: registeredPilgrim.id,
        tracking_no: registeredPilgrim.tracking_no,
        reg_voucher_id: registeredPilgrim.reg_voucher_id
    };
    if (event.target.checked) {
        checkedPilgrims.value.push(pilgrimData);
    } else {
        const index = checkedPilgrims.value.findIndex(p => p.pilgrim_id === registeredPilgrim.id);
        if (index !== -1) {
            checkedPilgrims.value.splice(index, 1);
        }
    }
};
const addPilgrimsToGuide = async () => {
    isPilgrimAddLoading.value = true;
    try {
        if (checkedPilgrims.value.length === 0) {
            alert('No data found');
            isPilgrimAddLoading.value = false;
            return false;
        }
        let form = new FormData();
        form.append('guideId', id);
        form.append('pilgrims', JSON.stringify(checkedPilgrims.value));
        let response = await axios.post('/guides/add-pilgrim-to-guide', form);
        if (response.data?.responseCode === -1) {
            isPilgrimAddLoading.value = false;
            alert(response.data?.msg)
            return false;
        }
        if (response.data?.responseCode === 1) {
            // Uncheck all checkboxes
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            checkedPilgrims.value = [];
            registeredPilgrimList.value = [];
            isPilgrimAddLoading.value = false;
            await getGuideProfileDetails(id);
            const modal = document.getElementById('pilgrimAddVoucher');
            // This is not Proper way , it has to change another time
            if (modal) {
                $(modal).modal('hide');
            }
            await getOwnPilgrims();
            alert(response.data?.msg);
        }
    } catch (error) {
        isPilgrimAddLoading.value = false;
        alert('Something went wrong!!! [GPV: 004]');
    }
}
const deleteVoucher = async (voucher_id) => {
    isVoucherDeleteLoading.value = true;
    if (confirm('Are you sure you want to delete this voucher?')) {
        try {
            const response = await axios.get(`/guides/delete-pilgrim`, {
                params: {
                    guidId: id,
                    id: voucher_id,
                }
            });
            if (response.data?.responseCode === 1) {
                alert(response.data?.msg);
                await getGuideProfileDetails(id);
                await getOwnPilgrims();
                isVoucherDeleteLoading.value = false;
            } else {
                alert(response.data?.msg);
                isVoucherDeleteLoading.value = false;
            }
        } catch (error) {
            alert('Something went wrong!!! [GPV: 005]');
            isVoucherDeleteLoading.value = false;
        }
    } else {
        isVoucherDeleteLoading.value = false;
    }
};
const submitGuideRequest = async () => {
    isSubmitLoading.value = true;
    try {
        /*
        if (isLocked.value === 0) {
            alert('Please lock first.');
            $("#submitGuideRequest").prop('disabled', false);
            return false;
        }
        */

        let form = new FormData();
        form.append('guideId', id);
        let response = await axios.post('/guides/submit-guide-request', form);

        if (response.data?.responseCode === 1) {
            await getGuideProfileDetails(id);
            alert(response.data?.msg);
            isSubmitLoading.value = false;
        }
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg);
            isSubmitLoading.value = false;
            return false;
        }
    } catch (error) {
        alert('Something went wrong!!! GPV: 006');
        isSubmitLoading.value = false;
    }
};
const reSubmitGuideRequest = async () => {
    isReSubmitLoading.value = true;
    try {
        let form = new FormData();
        form.append('guideId', id);
        let response = await axios.post('/guides/re-submit-guide-request', form);

        if (response.data?.responseCode === 1) {
            await getGuideProfileDetails(id);
            alert(response.data?.msg);
            isReSubmitLoading.value = false;
        }
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg);
            isReSubmitLoading.value = false;
            return false;
        }
    } catch (error) {
        alert('Something went wrong!!! GPV: 007');
        isReSubmitLoading.value = false;
    }
};
const closeModal = () => {
    const modal = document.getElementById('pilgrimAddVoucher');
    if (modal) {
        $(modal).modal('hide');
    }
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
    <div class="guide-reg-content">
        <div class="border-card-block">
            <div class="bd-card-head">
                <div class="bd-card-title flex-center" style="width: 100%;">
                    <h3>Profile of Guide</h3>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="row mb-4">
                    <div class="col-lg-4">
                        <div class="guide-profile-info">
                            <div class="guide-photo">
                                <img :src="guideProfileInfo.profile_pic ? guideProfileInfo.profile_pic : './assets/custom/images/2024/06/user-photo.png'" alt="Photo">
                            </div>
                            <h3>{{ guideProfileInfo.full_name_english }}</h3>
                            <!--
                            <a href="#ehajjViewNID" class="btn btn-outline-blue" data-toggle="modal"><i class="fa fa-eye" aria-hidden="true"></i> View NID</a>
                            -->
                            <p>Tracking ID : <strong>{{ guideProfileInfo.tracking_no }}</strong></p>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="border-card-block plg-member-list-card">
                            <div class="bd-card-head">
                                <div class="bd-card-title">
                                    <h3>Basic Information</h3>
                                </div>
                            </div>
                            <div class="bd-card-content">
                                <div class="guide-profile-details">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-list-item">
                                                <span class="info-list-title">Name in Bangla:</span> {{ guideProfileInfo.full_name_bangla }}
                                            </div>
                                            <div class="info-list-item">
                                                <span class="info-list-title">Father's Name:</span> {{ guideProfileInfo.father_name }}
                                            </div>
                                            <div class="info-list-item">
                                                <span class="info-list-title">Mother's Name:</span> {{ guideProfileInfo.mother_name }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-list-item">
                                                <span class="info-list-title">Date of Birth:</span> {{ moment(guideProfileInfo.birth_date).format('DD-MMM-YYYY') }}
                                            </div>
                                            <div class="info-list-item">
                                                <span class="info-list-title">Gender:</span> {{ guideProfileInfo.gender }}
                                            </div>
                                            <div class="info-list-item">
                                                <span class="info-list-title">Management:</span> {{ guideProfileInfo.is_govt }}
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <p class="text-bold">Contact Information: </p>
                                            <div class="info-list-item">
                                                <span class="info-list-title">Mobile:</span> {{ guideProfileInfo.mobile }}
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <p class="text-bold">Address: </p>
                                            <div class="info-list-item"><span class="info-list-title">Present Address:</span>
                                                {{ guideProfileInfo.present_address ? guideProfileInfo.present_address + "," : "" }}
                                                {{ guideProfileInfo.police_station ? guideProfileInfo.police_station + "," : "" }}
                                                {{ guideProfileInfo.district ? guideProfileInfo.district + "," : "" }}
                                                {{ guideProfileInfo.post_code ? guideProfileInfo.post_code : "" }}
                                            </div>
                                            <div class="info-list-item"><span class="info-list-title">Permanent Address:</span>
                                                {{ guideProfileInfo.permanent_address ? guideProfileInfo.permanent_address + "," : "" }}
                                                {{ guideProfileInfo.per_police_station ? guideProfileInfo.per_police_station + "," : "" }}
                                                {{ guideProfileInfo.per_district ? guideProfileInfo.per_district + "," : "" }}
                                                {{ guideProfileInfo.per_post_code ? guideProfileInfo.per_post_code : "" }}
                                            </div>
                                            <!--
                                            <div class="info-list-item"><span class="info-list-title">Passport Address:</span> <span class="text-uppercase">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Soluta, saepe, commodi</span></div>
                                            -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bd-card-footer px-0">
                            <div class="flex-space-btw info-btn-group">
                                <button class="btn btn-green" v-if="guideProfileInfo.status_id !== 4 && (guideProfileInfo.status_id == 0 || guideProfileInfo.status_id == 5)" @click="editApplication(guideProfileInfo.id)">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                            <g clip-path="url(#clip0_2139_2329)">
                                                <path d="M10.1055 1.00387L3.50151 7.60787C3.24928 7.85874 3.04931 8.15715 2.91319 8.48583C2.77707 8.8145 2.7075 9.16692 2.70851 9.52266V10.2501C2.70851 10.3938 2.76558 10.5316 2.86716 10.6331C2.96874 10.7347 3.10651 10.7918 3.25017 10.7918H3.97763C4.33338 10.7928 4.68579 10.7232 5.01447 10.5871C5.34314 10.451 5.64155 10.251 5.89242 9.99879L12.4964 3.39479C12.813 3.07746 12.9907 2.64754 12.9907 2.19933C12.9907 1.75111 12.813 1.32119 12.4964 1.00387C12.1745 0.696146 11.7463 0.524414 11.301 0.524414C10.8556 0.524414 10.4274 0.696146 10.1055 1.00387ZM11.7305 2.62887L5.12651 9.23287C4.82107 9.53644 4.40826 9.70733 3.97763 9.70846H3.79184V9.52266C3.79297 9.09204 3.96386 8.67922 4.26742 8.37379L10.8714 1.76979C10.9871 1.65926 11.141 1.59759 11.301 1.59759C11.461 1.59759 11.6148 1.65926 11.7305 1.76979C11.8442 1.88382 11.9081 2.03829 11.9081 2.19933C11.9081 2.36037 11.8442 2.51484 11.7305 2.62887Z" fill="white"></path>
                                                <path d="M12.4583 5.36363C12.3147 5.36363 12.1769 5.42069 12.0753 5.52228C11.9737 5.62386 11.9167 5.76163 11.9167 5.90529V8.625H9.75C9.31903 8.625 8.9057 8.79621 8.60095 9.10095C8.29621 9.4057 8.125 9.81903 8.125 10.25V12.4167H2.70833C2.27736 12.4167 1.86403 12.2455 1.55929 11.9407C1.25454 11.636 1.08333 11.2226 1.08333 10.7917V3.20833C1.08333 2.77736 1.25454 2.36403 1.55929 2.05929C1.86403 1.75454 2.27736 1.58333 2.70833 1.58333H7.60609C7.74974 1.58333 7.88752 1.52627 7.9891 1.42468C8.09068 1.3231 8.14775 1.18533 8.14775 1.04167C8.14775 0.898008 8.09068 0.760233 7.9891 0.658651C7.88752 0.557068 7.74974 0.5 7.60609 0.5H2.70833C1.9903 0.50086 1.30193 0.786478 0.794203 1.2942C0.286478 1.80193 0.00086009 2.4903 0 3.20833L0 10.7917C0.00086009 11.5097 0.286478 12.1981 0.794203 12.7058C1.30193 13.2135 1.9903 13.4991 2.70833 13.5H8.85246C9.20829 13.501 9.56079 13.4315 9.88955 13.2953C10.2183 13.1592 10.5168 12.9593 10.7678 12.707L12.2065 11.2673C12.4587 11.0164 12.6588 10.718 12.795 10.3893C12.9312 10.0607 13.0009 9.70824 13 9.35246V5.90529C13 5.76163 12.9429 5.62386 12.8414 5.52228C12.7398 5.42069 12.602 5.36363 12.4583 5.36363ZM10.0019 11.9411C9.78414 12.1583 9.50878 12.3087 9.20834 12.3744V10.25C9.20834 10.1063 9.2654 9.96857 9.36699 9.86699C9.46857 9.7654 9.60634 9.70834 9.75 9.70834H11.876C11.809 10.0082 11.6588 10.283 11.4427 10.5013L10.0019 11.9411Z" fill="white"></path>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_2139_2329">
                                                    <rect width="13" height="13" fill="white" transform="translate(0 0.5)"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </span>
                                    <span>Edit</span>
                                </button>
                                <div class="footer-btn-group" v-if="guideProfileInfo.status_id === 0 && isLastDateExist">
                                    <button class="btn btn-blue" type="button" :disabled="isSubmitLoading" @click="submitGuideRequest()">
                                    <span class="svg-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                            <path d="M10.6115 0.898248C10.3405 0.620165 9.93954 0.516565 9.56564 0.625618L0.846649 3.16108C0.452154 3.27068 0.17254 3.5853 0.0972174 3.98497C0.0202692 4.39174 0.289046 4.9081 0.64019 5.12402L3.36643 6.79961C3.64605 6.97137 4.00694 6.92829 4.23833 6.69492L7.36015 3.55367C7.5173 3.39009 7.7774 3.39009 7.93455 3.55367C8.0917 3.7118 8.0917 3.96807 7.93455 4.13165L4.80731 7.27345C4.57538 7.50627 4.53203 7.86887 4.70273 8.15023L6.36849 10.9038C6.56357 11.231 6.89954 11.4163 7.26803 11.4163C7.31138 11.4163 7.36015 11.4163 7.4035 11.4109C7.82617 11.3564 8.16214 11.0674 8.28678 10.6584L10.8716 1.9506C10.9854 1.57983 10.8824 1.17633 10.6115 0.898248Z" fill="white"></path>
                                        </svg>
                                    </span>
                                        <span>Submit</span>
                                    </button>
                                </div>
                                <div class="footer-btn-group" v-if="guideProfileInfo.status_id === 5 && isLastDateExist">
                                    <button class="btn btn-blue" type="button" :disabled="isReSubmitLoading" @click="reSubmitGuideRequest()">
                                    <span class="svg-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                            <path d="M10.6115 0.898248C10.3405 0.620165 9.93954 0.516565 9.56564 0.625618L0.846649 3.16108C0.452154 3.27068 0.17254 3.5853 0.0972174 3.98497C0.0202692 4.39174 0.289046 4.9081 0.64019 5.12402L3.36643 6.79961C3.64605 6.97137 4.00694 6.92829 4.23833 6.69492L7.36015 3.55367C7.5173 3.39009 7.7774 3.39009 7.93455 3.55367C8.0917 3.7118 8.0917 3.96807 7.93455 4.13165L4.80731 7.27345C4.57538 7.50627 4.53203 7.86887 4.70273 8.15023L6.36849 10.9038C6.56357 11.231 6.89954 11.4163 7.26803 11.4163C7.31138 11.4163 7.36015 11.4163 7.4035 11.4109C7.82617 11.3564 8.16214 11.0674 8.28678 10.6584L10.8716 1.9506C10.9854 1.57983 10.8824 1.17633 10.6115 0.898248Z" fill="white"></path>
                                        </svg>
                                    </span>
                                        <span>Re-Submit</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-card-block plg-member-list-card mb-0">
                    <div class="bd-card-head">
                        <div class="bd-card-title">
                            <h3>Pilgrim Lists</h3>
                        </div>
                        <div class="title-btn-group" v-if="guideProfileInfo.status_id !== 4">
                            <a class="list-btn-green" href="#pilgrimAddVoucher" data-toggle="modal">
                            <span class="list-btn-icon flex-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5001 10.4C7.93015 10.4 9.9001 8.43006 9.9001 6.00001C9.9001 3.56995 7.93015 1.60001 5.5001 1.60001C3.07005 1.60001 1.1001 3.56995 1.1001 6.00001C1.1001 8.43006 3.07005 10.4 5.5001 10.4ZM6.0501 4.35001C6.0501 4.04625 5.80385 3.80001 5.5001 3.80001C5.19634 3.80001 4.9501 4.04625 4.9501 4.35001V5.45001H3.8501C3.54634 5.45001 3.3001 5.69625 3.3001 6.00001C3.3001 6.30376 3.54634 6.55001 3.8501 6.55001H4.9501V7.65001C4.9501 7.95376 5.19634 8.20001 5.5001 8.20001C5.80385 8.20001 6.0501 7.95376 6.0501 7.65001V6.55001H7.1501C7.45386 6.55001 7.7001 6.30376 7.7001 6.00001C7.7001 5.69625 7.45386 5.45001 7.1501 5.45001H6.0501V4.35001Z" fill="#0F6849"></path>
                                </svg>
                            </span>
                                <span class="list-btn-text">Add Pilgrim</span>
                            </a>
                        </div>
                    </div>
                    <div class="bd-card-content">
                        <div class="table-responsive app-list-table" v-if="guideVouchers.length > 0">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th width="70">SL No</th>
                                    <th>Serial No</th>
                                    <th>Name</th>
                                    <th>Tracking No</th>
                                    <th>Voucher Tracking No</th>
                                    <th>Mobile</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody v-for="(guideVoucher, index) in guideVouchers" :key="guideVoucher.id">
                                    <tr>
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ guideVoucher.pilgrim_is_govt === 'Private' ? 'NG-' : 'G-' }}{{ guideVoucher.pilgrim_serial_no }}</td>
                                        <td>{{ guideVoucher.pilgrim_name }}</td>
                                        <td>{{ guideVoucher.pilgrim_tracking_no }}</td>
                                        <td>{{ guideVoucher.voucher_tracking_no }}</td>
                                        <td>{{ guideVoucher.pilgrim_mobile_no }}</td>
                                        <td>
                                            <div class="btn-flex-center">
                                                <button class="btn-outline-danger" type="button" @click="deleteVoucher(guideVoucher.id)">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="no-data-box" v-else>
                            <span>No data found.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bd-card-footer">
                <div class="flex-space-btw info-btn-group">
                    <router-link :to="{ name: 'GuideApplicationList' }">
                        <button class="btn btn-default"><span>Close</span></button>
                    </router-link>
                    <div class="footer-btn-group"></div>
                </div>
            </div>
        </div>
    </div>

    <!--    NID Image Show Modal     -->
    <!--
    <div class="modal ba-modal fade" id="ehajjViewNID" tabindex="-1" aria-labelledby="ehajjViewNID" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="icon-close-modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 384 512"><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                    </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-nid-photo">
                        <img src="./assets/custom/images/2024/06/nid-photo-front.png" alt="NID-Photo">
                    </div>
                </div>
            </div>
        </div>
    </div>
    -->
<!--    Pilgrim Lists Modal     -->
    <div class="modal pilgrimOptModal ba-modal fade" id="pilgrimAddVoucher" tabindex="-1" aria-labelledby="pilgrimAddVoucher" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="icon-close-modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                <g clip-path="url(#clip0_3579_8655)">
                                    <path d="M0.292786 0.792593C0.480314 0.605122 0.734622 0.499806 0.999786 0.499806C1.26495 0.499806 1.51926 0.605122 1.70679 0.792593L7.99979 7.08559L14.2928 0.792593C14.385 0.697083 14.4954 0.6209 14.6174 0.568491C14.7394 0.516082 14.8706 0.488496 15.0034 0.487342C15.1362 0.486189 15.2678 0.51149 15.3907 0.561771C15.5136 0.612052 15.6253 0.686305 15.7192 0.780198C15.8131 0.874091 15.8873 0.985743 15.9376 1.10864C15.9879 1.23154 16.0132 1.36321 16.012 1.49599C16.0109 1.62877 15.9833 1.75999 15.9309 1.882C15.8785 2.004 15.8023 2.11435 15.7068 2.20659L9.41379 8.49959L15.7068 14.7926C15.8889 14.9812 15.9897 15.2338 15.9875 15.496C15.9852 15.7582 15.88 16.009 15.6946 16.1944C15.5092 16.3798 15.2584 16.485 14.9962 16.4873C14.734 16.4895 14.4814 16.3888 14.2928 16.2066L7.99979 9.91359L1.70679 16.2066C1.51818 16.3888 1.26558 16.4895 1.00339 16.4873C0.741189 16.485 0.490376 16.3798 0.304968 16.1944C0.11956 16.009 0.0143908 15.7582 0.0121124 15.496C0.00983399 15.2338 0.110628 14.9812 0.292786 14.7926L6.58579 8.49959L0.292786 2.20659C0.105315 2.01907 0 1.76476 0 1.49959C0 1.23443 0.105315 0.980121 0.292786 0.792593Z" fill="black"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_3579_8655">
                                        <rect width="16" height="16" fill="white" transform="translate(0 0.5)"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                    </button>
                    <div class="modal-title">
                        <h3>আপনার সাথে যে সকল হজযাত্রী যেতে চান তাদের ট্রাকিং নম্বর দিয়ে হজযাত্রী সংযুক্ত করুন ।</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="pilgrim-tab-menu" role="tablist">
                        <div class="pilgrim-tab-menu-group nav nav-tabs" role="presentation">
                            <button class="tab-btn nav-link active" data-toggle="tab" data-target="#pilgrimTabOne" type="button" role="tab" @click="onPilgrimTabOneClick()">আপনার নিবন্ধিত হজযাত্রী</button>
                            <button class="tab-btn nav-link" data-toggle="tab" data-target="#pilgrimTabTwo" type="button" role="tab" @click="onPilgrimTabTwoClick()">হজযাত্রী সার্চ করে সংযুক্ত করুন</button>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane show active fade" id="pilgrimTabOne">
                            <div class="pilgrim-tab-content">
                                <div class="ehajj-list-table" v-if="ownPilgrimList.length > 0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered mt-0">
                                            <thead>
                                            <tr>
                                                <th style="width: 50px;">SL No.</th>
                                                <th class="text-left">Name</th>
                                                <th>Tracking No</th>
                                                <th>Serial No</th>
                                                <th style="width: 100px;">Add To Voucher</th>
                                            </tr>
                                            </thead>
                                            <tbody v-for="(ownPilgrim, index) in ownPilgrimList" :key="ownPilgrim.id">
                                                <tr>
                                                    <td>{{ index + 1 }}</td>
                                                    <td class="text-left">{{ ownPilgrim.full_name_english }}</td>
                                                    <td>{{ ownPilgrim.tracking_no }}</td>
                                                    <td>{{ ownPilgrim.serial_no }}</td>
                                                    <td>
                                                        <div class="text-center hajj-cb-wrap">
                                                            <div class="bs_checkbox">
                                                                <input
                                                                    type="checkbox"
                                                                    :id="'hajj_pilgrim_voucher_tab1_1' + index"
                                                                    @change="handleCheckboxChange(ownPilgrim, $event)"
                                                                >
                                                                <label :for="'hajj_pilgrim_voucher_tab1_1' + index"></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--
                                                                        <div class="list-table-footer">
                                                                            <div class="flex-space-btw">
                                                                                <div class="list-showing-text">
                                                                                    <span>Showing 1 to 10 of 25 entries</span>
                                                                                </div>
                                                                                <div class="ehajj-list-pagination">
                                                                                    <div class="ehajj-pagination flex-end">
                                                                                        <nav class="pagination-nav" aria-label="Table Navigation">
                                                                                            <ul class="pagination">
                                                                                                <li class="page-item">
                                                                                                    <a class="page-link" href="#" aria-label="Previous">
                                                                                                    <span aria-hidden="true">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                                                                            <path d="M15.1602 7.41L10.5802 12L15.1602 16.59L13.7502 18L7.75016 12L13.7502 6L15.1602 7.41Z" fill="#091E42"></path>
                                                                                                        </svg>
                                                                                                    </span>
                                                                                                    </a>
                                                                                                </li>
                                                                                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="#">...</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="#">9</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="#">10</a></li>
                                                                                                <li class="page-item">
                                                                                                    <a class="page-link" href="#" aria-label="Next">
                                                                                                    <span aria-hidden="true">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                                                                            <path d="M8.83984 7.41L13.4198 12L8.83984 16.59L10.2498 18L16.2498 12L10.2498 6L8.83984 7.41Z" fill="#091E42"></path>
                                                                                                        </svg>
                                                                                                    </span>
                                                                                                    </a>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </nav>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        -->
                                </div>
                                <div class="no-data-box" v-else>
                                    <span>No data found.</span>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pilgrimTabTwo">
                            <div class="pilgrim-tab-content">
                                <div class="form-searchbar">
                                    <label>হজযাত্রীদের ট্র্যাকিং নম্বর</label>
                                    <div class="inline-src-group">
                                        <input class="form-control" type="text" placeholder="Pilgrim Tracking No" v-model="trackingNo">
                                        <button class="btn btn-green" type="button" :disabled="isLoading" @click="onTrackingNoSubmit()" >
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                <path d="M13.4998 12.7346L10.1078 9.34266C10.9889 8.26507 11.4221 6.89004 11.3178 5.50201C11.2136 4.11397 10.5798 2.81911 9.54754 1.88527C8.51532 0.951435 7.16366 0.450059 5.77215 0.484851C4.38063 0.519643 3.05572 1.08794 2.07147 2.0722C1.08721 3.05646 0.518911 4.38137 0.484119 5.77288C0.449326 7.1644 0.950703 8.51605 1.88454 9.54827C2.81838 10.5805 4.11324 11.2143 5.50127 11.3186C6.88931 11.4229 8.26433 10.9897 9.34193 10.1086L12.7338 13.5005L13.4998 12.7346ZM5.91643 10.2505C5.05937 10.2505 4.22157 9.99635 3.50896 9.5202C2.79634 9.04404 2.24093 8.36727 1.91295 7.57545C1.58497 6.78364 1.49915 5.91235 1.66636 5.07177C1.83356 4.23118 2.24627 3.45906 2.8523 2.85303C3.45832 2.247 4.23045 1.83429 5.07104 1.66709C5.91162 1.49989 6.78291 1.5857 7.57472 1.91368C8.36654 2.24166 9.04331 2.79707 9.51946 3.50969C9.99562 4.2223 10.2498 5.06011 10.2498 5.91716C10.2485 7.06604 9.79151 8.16749 8.97913 8.97986C8.16675 9.79224 7.0653 10.2492 5.91643 10.2505Z" fill="white"></path>
                                            </svg>
                                        </span>
                                            <span>Search</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="ehajj-list-table" v-if="registeredPilgrimList.length > 0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th style="width: 50px;">SL No.</th>
                                                <th class="text-left">Name</th>
                                                <th>Traking No</th>
                                                <th>Serial No</th>
                                                <th style="width: 100px;">Add To Voucher</th>
                                            </tr>
                                            </thead>
                                            <tbody v-for="(registeredPilgrim, index) in registeredPilgrimList" :key="registeredPilgrim.id">
                                                <tr>
                                                    <td>{{ index + 1 }}</td>
                                                    <td class="text-left">{{ registeredPilgrim.full_name_english }}</td>
                                                    <td>{{ registeredPilgrim.tracking_no }}</td>
                                                    <td>{{ registeredPilgrim.serial_no }}</td>
                                                    <td>
                                                        <div class="text-center hajj-cb-wrap">
                                                            <div class="bs_checkbox">
                                                                <input
                                                                    type="checkbox"
                                                                    :id="'hajj_pilgrim_voucher_tab2_' + index"
                                                                    @change="handleCheckboxChange(registeredPilgrim, $event)"
                                                                >
                                                                <label :for="'hajj_pilgrim_voucher_tab2_' + index"></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="no-data-box" v-else>
                                    <span>No data found.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="footer-btn-group">
                        <button class="btn btn-default" data-dismiss="modal"><span>Close</span></button>
                        <button
                            class="btn btn-blue"
                            type="button"
                            :disabled="checkedPilgrims.length === 0 || isPilgrimAddLoading"
                            @click="addPilgrimsToGuide()">
                            <span>
                                Add
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
