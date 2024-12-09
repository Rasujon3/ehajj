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
const backToGuideEdit = async() => {
    await router.push({ name: 'GuideEdit', params: { id } });
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
    <div class="hajj-main-content">
        <div class="container">
            <div class="guide-pilgrim-steps">
                <div class="gp-step-item">
                        <span class="step-circle">
                            <span class="step-number">1</span>
                        </span>
                    <span class="step-title">Guide Registration</span>
                </div>
                <div class="gp-step-item step-active">
                        <span class="step-circle">
                            <span class="step-number">2</span>
                        </span>
                    <span class="step-title">Pilgrim Information</span>
                </div>
            </div>

            <div class="guide-reg-content">
                <div class="border-card-block">
                    <div class="bd-card-head">
                        <div class="bd-card-title">
                                <span class="title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M14.3498 2H9.64977C8.60977 2 7.75977 2.84 7.75977 3.88V4.82C7.75977 5.86 8.59977 6.7 9.63977 6.7H14.3498C15.3898 6.7 16.2298 5.86 16.2298 4.82V3.88C16.2398 2.84 15.3898 2 14.3498 2Z" fill="white"/>
                                        <path d="M17.24 4.82047C17.24 6.41047 15.94 7.71047 14.35 7.71047H9.65004C8.06004 7.71047 6.76004 6.41047 6.76004 4.82047C6.76004 4.26047 6.16004 3.91047 5.66004 4.17047C4.25004 4.92047 3.29004 6.41047 3.29004 8.12047V17.5305C3.29004 19.9905 5.30004 22.0005 7.76004 22.0005H16.24C18.7 22.0005 20.71 19.9905 20.71 17.5305V8.12047C20.71 6.41047 19.75 4.92047 18.34 4.17047C17.84 3.91047 17.24 4.26047 17.24 4.82047ZM15.34 12.7305L11.34 16.7305C11.19 16.8805 11 16.9505 10.81 16.9505C10.62 16.9505 10.43 16.8805 10.28 16.7305L8.78004 15.2305C8.49004 14.9405 8.49004 14.4605 8.78004 14.1705C9.07004 13.8805 9.55004 13.8805 9.84004 14.1705L10.81 15.1405L14.28 11.6705C14.57 11.3805 15.05 11.3805 15.34 11.6705C15.63 11.9605 15.63 12.4405 15.34 12.7305Z" fill="white"/>
                                    </svg>
                                </span>
                            <h3>Pilgrim Information</h3>
                        </div>
                        <!-- <button v-if="isLocked === 0" class="btn btn-squire btn-outline-golden" id="lock-btn" type="button" @click="locked(1)">
                            <span class="svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                    <g clip-path="url(#clip0_3547_13228)">
                                        <path d="M10.2923 4.83333V4.29167C10.2923 3.28605 9.89284 2.32163 9.18177 1.61055C8.47069 0.899478 7.50626 0.5 6.50065 0.5C5.49504 0.5 4.53061 0.899478 3.81954 1.61055C3.10846 2.32163 2.70898 3.28605 2.70898 4.29167V4.83333H1.08398V11.875C1.08398 12.306 1.25519 12.7193 1.55994 13.0241C1.86468 13.3288 2.27801 13.5 2.70898 13.5H10.2923C10.7233 13.5 11.1366 13.3288 11.4414 13.0241C11.7461 12.7193 11.9173 12.306 11.9173 11.875V4.83333H10.2923ZM7.04232 10.25H5.95899V8.08333H7.04232V10.25ZM9.20899 4.83333H3.79232V4.29167C3.79232 3.57337 4.07766 2.8845 4.58557 2.37659C5.09348 1.86867 5.78236 1.58333 6.50065 1.58333C7.21895 1.58333 7.90782 1.86867 8.41573 2.37659C8.92364 2.8845 9.20899 3.57337 9.20899 4.29167V4.83333Z" fill="#B38705"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_3547_13228">
                                            <rect width="13" height="13" fill="white" transform="translate(0 0.5)"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <img src="/assets/custom/images/2024/06/lock.png" alt="lock" style="height: 25px !important;"/>
                            <span>Click to lock this job</span>
                        </button>-->
                        <!-- <button v-else class="btn btn-squire btn-outline-golden" type="button" id="unlock-btn" @click="locked(0)">
                            <span class="svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                    <g clip-path="url(#clip0_3547_13228)">
                                        <path d="M10.2923 4.83333V4.29167C10.2923 3.28605 9.89284 2.32163 9.18177 1.61055C8.47069 0.899478 7.50626 0.5 6.50065 0.5C5.49504 0.5 4.53061 0.899478 3.81954 1.61055C3.10846 2.32163 2.70898 3.28605 2.70898 4.29167V4.83333H1.08398V11.875C1.08398 12.306 1.25519 12.7193 1.55994 13.0241C1.86468 13.3288 2.27801 13.5 2.70898 13.5H10.2923C10.7233 13.5 11.1366 13.3288 11.4414 13.0241C11.7461 12.7193 11.9173 12.306 11.9173 11.875V4.83333H10.2923ZM7.04232 10.25H5.95899V8.08333H7.04232V10.25ZM9.20899 4.83333H3.79232V4.29167C3.79232 3.57337 4.07766 2.8845 4.58557 2.37659C5.09348 1.86867 5.78236 1.58333 6.50065 1.58333C7.21895 1.58333 7.90782 1.86867 8.41573 2.37659C8.92364 2.8845 9.20899 3.57337 9.20899 4.29167V4.83333Z" fill="#B38705"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_3547_13228">
                                            <rect width="13" height="13" fill="white" transform="translate(0 0.5)"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <img src="/assets/custom/images/2024/06/unlock.png" alt="lock" style="height: 25px !important;"/>
                            <span>Click to Unlock this job</span>
                        </button>-->
                    </div>

                    <div class="bd-card-content">
                        <div class="border-card-block plg-member-list-card">
                            <div class="bd-card-head">
                                <div class="bd-card-title">
                                    <h3>List of Members</h3>
                                </div>
                                <div class="title-btn-group">
                                    <a class="list-btn-green" href="#pilgrimAddVoucher" data-toggle="modal">
                                            <span class="list-btn-icon flex-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5001 10.4C7.93015 10.4 9.9001 8.43006 9.9001 6.00001C9.9001 3.56995 7.93015 1.60001 5.5001 1.60001C3.07005 1.60001 1.1001 3.56995 1.1001 6.00001C1.1001 8.43006 3.07005 10.4 5.5001 10.4ZM6.0501 4.35001C6.0501 4.04625 5.80385 3.80001 5.5001 3.80001C5.19634 3.80001 4.9501 4.04625 4.9501 4.35001V5.45001H3.8501C3.54634 5.45001 3.3001 5.69625 3.3001 6.00001C3.3001 6.30376 3.54634 6.55001 3.8501 6.55001H4.9501V7.65001C4.9501 7.95376 5.19634 8.20001 5.5001 8.20001C5.80385 8.20001 6.0501 7.95376 6.0501 7.65001V6.55001H7.1501C7.45386 6.55001 7.7001 6.30376 7.7001 6.00001C7.7001 5.69625 7.45386 5.45001 7.1501 5.45001H6.0501V4.35001Z" fill="#0F6849"></path>
                                                </svg>
                                            </span>
                                        <span class="list-btn-text">Add New Voucher</span>
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
                            <button @click="backToGuideEdit" class="btn btn-default"><span>Previous</span></button>

                            <div class="footer-btn-group">
                                <!--
                                <button class="btn btn-success" id="generateVoucher" type="button" v-if="isLocked === 1 && voucherInfo.length > 0 && pdfLink === ''" @click="pdfGenerate()">
                                    <span class="svg-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                            <g clip-path="url(#clip0_3579_10357)">
                                                <path d="M11.5999 3.20833H9.20899V0.817417L11.5999 3.20833ZM11.9173 4.29167V13.5H1.08398V2.125C1.08398 1.69402 1.25519 1.2807 1.55994 0.975952C1.86468 0.671205 2.27801 0.5 2.70898 0.5L8.12565 0.5V4.29167H11.9173ZM3.79232 9.16667H7.58399V8.08333H3.79232V9.16667ZM9.20899 10.25H3.79232V11.3333H9.20899V10.25ZM9.20899 5.91667H3.79232V7H9.20899V5.91667Z" fill="#F7F7F7"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3579_10357">
                                                    <rect width="13" height="13" fill="white" transform="translate(0 0.5)"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </span>
                                    <span>Generate Voucher</span>
                                </button>
                                <a v-if="isLocked === 1 && voucherInfo.length > 0 && pdfLink !== ''" target="_blank" :href="pdfLink">
                                   <button class="btn btn-squire btn-outline-blue" type="button" id="pdfShowBtn">
                                        <span class="svg-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                                <path d="M5.0104 7.19058C4.75656 6.93674 4.345 6.93674 4.09116 7.19058C3.83732 7.44442 3.83732 7.85598 4.09116 8.10982L6.04116 10.0598C6.295 10.3137 6.70656 10.3137 6.9604 10.0598L8.9104 8.10982C9.16424 7.85597 9.16424 7.44442 8.9104 7.19058C8.65656 6.93674 8.245 6.93674 7.99116 7.19058L7.15078 8.03096L7.15078 4.4002H10.4008C11.1188 4.4002 11.7008 4.98223 11.7008 5.7002V10.2502C11.7008 10.9682 11.1188 11.5502 10.4008 11.5502H2.60078C1.88281 11.5502 1.30078 10.9682 1.30078 10.2502V5.7002C1.30078 4.98223 1.88281 4.4002 2.60078 4.4002H5.85078V8.03096L5.0104 7.19058Z" fill="#0065FF"/>
                                                <path d="M5.85078 3.1002C5.85078 2.74121 6.1418 2.4502 6.50078 2.4502C6.85977 2.4502 7.15078 2.74121 7.15078 3.1002L7.15078 4.4002H5.85078L5.85078 3.1002Z" fill="#0065FF"/>
                                            </svg>
                                        </span>
                                        <span>Download Voucher</span>
                                   </button>
                                </a>
                                -->
                                <!--
                                <button v-if="isLocked === 0" class="btn btn-squire btn-outline-golden"  type="button" @click="locked(1)">
                                    <span class="svg-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                            <g clip-path="url(#clip0_3547_13228)">
                                                <path d="M10.2923 4.83333V4.29167C10.2923 3.28605 9.89284 2.32163 9.18177 1.61055C8.47069 0.899478 7.50626 0.5 6.50065 0.5C5.49504 0.5 4.53061 0.899478 3.81954 1.61055C3.10846 2.32163 2.70898 3.28605 2.70898 4.29167V4.83333H1.08398V11.875C1.08398 12.306 1.25519 12.7193 1.55994 13.0241C1.86468 13.3288 2.27801 13.5 2.70898 13.5H10.2923C10.7233 13.5 11.1366 13.3288 11.4414 13.0241C11.7461 12.7193 11.9173 12.306 11.9173 11.875V4.83333H10.2923ZM7.04232 10.25H5.95899V8.08333H7.04232V10.25ZM9.20899 4.83333H3.79232V4.29167C3.79232 3.57337 4.07766 2.8845 4.58557 2.37659C5.09348 1.86867 5.78236 1.58333 6.50065 1.58333C7.21895 1.58333 7.90782 1.86867 8.41573 2.37659C8.92364 2.8845 9.20899 3.57337 9.20899 4.29167V4.83333Z" fill="#B38705"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3547_13228">
                                                    <rect width="13" height="13" fill="white" transform="translate(0 0.5)"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </span>
                                    <img src="/assets/custom/images/2024/06/lock.png" alt="lock" style="height: 25px !important;"/>
                                    <span>Click to lock this job</span>
                                </button>
                                <button v-else class="btn btn-squire btn-outline-golden" type="button" @click="locked(0)">
                                    <span class="svg-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                            <g clip-path="url(#clip0_3547_13228)">
                                                <path d="M10.2923 4.83333V4.29167C10.2923 3.28605 9.89284 2.32163 9.18177 1.61055C8.47069 0.899478 7.50626 0.5 6.50065 0.5C5.49504 0.5 4.53061 0.899478 3.81954 1.61055C3.10846 2.32163 2.70898 3.28605 2.70898 4.29167V4.83333H1.08398V11.875C1.08398 12.306 1.25519 12.7193 1.55994 13.0241C1.86468 13.3288 2.27801 13.5 2.70898 13.5H10.2923C10.7233 13.5 11.1366 13.3288 11.4414 13.0241C11.7461 12.7193 11.9173 12.306 11.9173 11.875V4.83333H10.2923ZM7.04232 10.25H5.95899V8.08333H7.04232V10.25ZM9.20899 4.83333H3.79232V4.29167C3.79232 3.57337 4.07766 2.8845 4.58557 2.37659C5.09348 1.86867 5.78236 1.58333 6.50065 1.58333C7.21895 1.58333 7.90782 1.86867 8.41573 2.37659C8.92364 2.8845 9.20899 3.57337 9.20899 4.29167V4.83333Z" fill="#B38705"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3547_13228">
                                                    <rect width="13" height="13" fill="white" transform="translate(0 0.5)"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </span>
                                    <img src="/assets/custom/images/2024/06/lock.png" alt="lock" style="height: 25px !important;"/>
                                    <span>Click to Unlock this job</span>
                                </button>
                                -->
                                <button
                                    v-if="guideProfileInfo.status_id === 0 && isLastDateExist"
                                    class="btn btn-blue"
                                    type="button"
                                    id="submitGuideRequest"
                                    :disabled="isSubmitLoading"
                                    @click="submitGuideRequest()"
                                >
                                    <span class="svg-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                            <path d="M10.6115 0.898248C10.3405 0.620165 9.93954 0.516565 9.56564 0.625618L0.846649 3.16108C0.452154 3.27068 0.17254 3.5853 0.0972174 3.98497C0.0202692 4.39174 0.289046 4.9081 0.64019 5.12402L3.36643 6.79961C3.64605 6.97137 4.00694 6.92829 4.23833 6.69492L7.36015 3.55367C7.5173 3.39009 7.7774 3.39009 7.93455 3.55367C8.0917 3.7118 8.0917 3.96807 7.93455 4.13165L4.80731 7.27345C4.57538 7.50627 4.53203 7.86887 4.70273 8.15023L6.36849 10.9038C6.56357 11.231 6.89954 11.4163 7.26803 11.4163C7.31138 11.4163 7.36015 11.4163 7.4035 11.4109C7.82617 11.3564 8.16214 11.0674 8.28678 10.6584L10.8716 1.9506C10.9854 1.57983 10.8824 1.17633 10.6115 0.898248Z" fill="white"/>
                                        </svg>
                                    </span>
                                    <span>Submit</span>
                                </button>
                                <!-- <button v-if="guideData.status_id > 0" class="btn btn-danger" type="button" id="cancelGuideRequest"
                                        @click="cancelGuideRequest()">
                                    <span class="svg-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                            <path d="M10.6115 0.898248C10.3405 0.620165 9.93954 0.516565 9.56564 0.625618L0.846649 3.16108C0.452154 3.27068 0.17254 3.5853 0.0972174 3.98497C0.0202692 4.39174 0.289046 4.9081 0.64019 5.12402L3.36643 6.79961C3.64605 6.97137 4.00694 6.92829 4.23833 6.69492L7.36015 3.55367C7.5173 3.39009 7.7774 3.39009 7.93455 3.55367C8.0917 3.7118 8.0917 3.96807 7.93455 4.13165L4.80731 7.27345C4.57538 7.50627 4.53203 7.86887 4.70273 8.15023L6.36849 10.9038C6.56357 11.231 6.89954 11.4163 7.26803 11.4163C7.31138 11.4163 7.36015 11.4163 7.4035 11.4109C7.82617 11.3564 8.16214 11.0674 8.28678 10.6584L10.8716 1.9506C10.9854 1.57983 10.8824 1.17633 10.6115 0.898248Z" fill="white"/>
                                        </svg>
                                    </span>
                                    <span>Cancel Guide Request</span>
                                </button> -->
                                <button
                                    v-if="guideProfileInfo.status_id === 5 && isLastDateExist"
                                    class="btn btn-blue"
                                    type="button"
                                    id="reSubmitGuideRequest"
                                    :disabled="isReSubmitLoading"
                                    @click="reSubmitGuideRequest()"
                                >
                                    <span class="svg-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                            <path d="M10.6115 0.898248C10.3405 0.620165 9.93954 0.516565 9.56564 0.625618L0.846649 3.16108C0.452154 3.27068 0.17254 3.5853 0.0972174 3.98497C0.0202692 4.39174 0.289046 4.9081 0.64019 5.12402L3.36643 6.79961C3.64605 6.97137 4.00694 6.92829 4.23833 6.69492L7.36015 3.55367C7.5173 3.39009 7.7774 3.39009 7.93455 3.55367C8.0917 3.7118 8.0917 3.96807 7.93455 4.13165L4.80731 7.27345C4.57538 7.50627 4.53203 7.86887 4.70273 8.15023L6.36849 10.9038C6.56357 11.231 6.89954 11.4163 7.26803 11.4163C7.31138 11.4163 7.36015 11.4163 7.4035 11.4109C7.82617 11.3564 8.16214 11.0674 8.28678 10.6584L10.8716 1.9506C10.9854 1.57983 10.8824 1.17633 10.6115 0.898248Z" fill="white"/>
                                        </svg>
                                    </span>
                                    <span>Re-Submit</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  Voucher Modal Start  -->
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
                        <h3>আপনার সাথে যে সকল হজ্জযাত্রী যেতে চান তাদের ট্রাকিং নম্বর দিয়ে হজযাত্রী সংযুক্তি করুন ।</h3>
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
    <!--  Voucher Modal End  -->
</template>

<style scoped>
#generateVoucher, #pdfShowBtn {
    margin-right: 300px;
}
#lock-btn, #unlock-btn {
    background-color: #5BC0DE;
    color: white;
}
</style>
