<script setup>
import {defineProps, defineEmits, toRefs, ref, onMounted, computed} from 'vue';
import axios from "axios";
import { useStore } from 'vuex';
import {useRouter} from "vue-router";

const router = useRouter();

const store = useStore();
const props = defineProps({
    previousClick: Function,
    voucherRowDetailsDataClick: Function,
    guideData: Object,
    allerros: Array,
    hajjPackages: Array,
    activeSessionCaption: String
});
const { guideData, allerros } = toRefs(props);

onMounted(async () => {
    await addVoucherModalData();
    await getVoucherAddedPilgrims();
});

let activeSessionCaption = ref('');
let hajjPackages = ref([]);
let voucherInfo = ref([]);
let isLocked = ref(guideData.value.is_locked);
let pdfLink = ref(guideData.value.guide_form_link);
const guideId = guideData.value.id;

const emit = defineEmits(['previous-click']);

const emitPreviousClick = () => {
    emit('previous-click');
};
const voucherRowDetailsData = async () => {
    try {
        if (guideData.value.hajj_packages === "") {
            alert('Please select haj Package.');
            return false;
        }
        if (guideData.value.voucher_number === "") {
            alert('Please add voucher number.');
            return false;
        }

        if (guideData.value.hajj_packages !== "" && guideData.value.voucher_number !== "") {
            const response = await axios.get(`/guides/get-voucher-row-details-data`, {
                params: {
                    tracking_no_list: guideData.value.voucher_number,
                    package_id: guideData.value.hajj_packages
                }
            });
            if (response.data?.responseCode === 1) {
                guideData.value.voucher_row_details_data = [...response.data?.data];
                await getAlreadyAddedVoucherList();
            }
            if (response.data?.responseCode === -1) {
                alert(response.data?.msg)
                return false;
            }
        }
    } catch (error) {
        alert('Something went wrong!!! [EPI:011]');
    }
}

const getAlreadyAddedVoucherList = async () => {
    try {
        const response = await axios.get(`/guides/get-already-added-voucher-list`, {
            params: {
                tracking_no_list: guideData.value.voucher_number,
                package_id: guideData.value.hajj_packages
            }
        });
        let voucher_info = "";
        if (response.data?.responseCode === 1) {
            if (response.data?.data.length > 0) {
                voucher_info = response.data?.data[0]?.vouchers + 'vouchers already added with Guide: ' + response.data?.data[0]?.guide_name;
            }
            guideData.value.already_added_voucher = voucher_info;
        }
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg)
            return false;
        }
    } catch (error) {
        alert('Something went wrong!!! [EPI:012]');
    }
}
const addVoucherModalData = async () => {
    try {
        const response = await axios.get(`/guides/add-voucher-modal-data`);
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg)
            return false;
        }
        if (response.data?.responseCode === 1) {
            activeSessionCaption.value = response.data?.data[0];
            hajjPackages.value = [...response.data?.data[1]];
        }
    } catch (error) {
        alert('Something went wrong!!! [EPI:013]');
    }
}
const addVoucherToGuide = async () => {
    try {
        let voucher_row_details_data = ref(guideData.value.voucher_row_details_data || []);
        if (voucher_row_details_data.length === 0) {
            alert('No data found');
            return false;
        }
        if (voucherInfo.value.length > 0 && voucher_row_details_data.value.length > 0) {
            voucherInfo.value.map((voucher_tracking_no) => {
                voucher_row_details_data.value.map((voucher) => {
                    if (voucher_tracking_no.tracking_no == voucher.voucher_tracking_no) {
                        alert(voucher_tracking_no.tracking_no + 'tracking no already added.')
                        voucher_row_details_data.value = voucher_row_details_data.value.filter(voucher => voucher.voucher_tracking_no !== voucher_tracking_no.tracking_no);
                    }
                })
            })
        }

        let vouchers_id = [];
        voucher_row_details_data.value.map((voucher) => {
            vouchers_id.push(voucher.voucher_id)
        })
        const response = await axios.get(`/guides/add-voucher-to-guide`, {
            params: {
                vouchers_id: vouchers_id,
                guidId: guideId,
            },
        });
        guideData.value.voucher_row_details_data = [];
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg)
            return false;
        }
        if (response.data?.responseCode === 1) {
            await getVoucherAddedPilgrims();
            guideData.value.hajj_packages = ""
            guideData.value.voucher_number = ""
            const modal = document.getElementById('pilgrimAddVoucher');
            // This is not Proper way , it have to change another time
            if (modal) {
                $(modal).modal('hide');
            }
        }
    } catch (error) {
        alert('Something went wrong!!! [EPI:014]');
    }
}

const getVoucherAddedPilgrims = async () => {
    try {
        const response = await axios.get(`/guides/get-voucher-added-pilgrims-list`, {
            params: {
                guidId: guideId,
            }
        });
        if (response.data?.responseCode === 1) {
            if (response.data?.data?.length > 0) {
                voucherInfo.value = [...response.data.data];
            }
        }
    } catch (error) {
        alert('Something went wrong!!! [EPI:015]');
    }
}
const locked = async (flag) => {
    try {
        const response = await axios.get(`/guides/lock-pilgrims`, {
            params: {
                guidId: guideId,
                flag: flag,
            }
        });
        if (response.data?.responseCode === 1) {
            isLocked.value = response.data?.lockStatus;
        }
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg)
            return false;
        }
    } catch (error) {
        alert('Something went wrong!!! [EPI:016]');
    }
}

const deleteVoucher = async (id) => {
    if (confirm('Are you sure you want to delete this voucher?')) {
        try {
            const response = await axios.get(`/guides/delete-pilgrim`, {
                params: {
                    guidId: guideId,
                    id: id,
                }
            });

            if (response.data?.responseCode === 1) {
                // Remove the deleted item from the local array
                voucherInfo.value = voucherInfo.value.filter(voucher => voucher.id !== id);
                alert(response.data?.msg);
            } else {
                alert(response.data?.msg);
            }
        } catch (error) {
            alert('Something went wrong!!! EPI:001');
        }
    }
};

const pdfGenerate = async () => {
    try {
        $("#generateVoucher").prop('disabled', true);
        const response = await axios.get(`/guides/pdf-generate`, {
            params: {
                guidId: guideId,
            }
        });

        if (response.data?.responseCode === 1) {
            $("#generateVoucher").prop('disabled', false);
            pdfLink.value = response.data?.guide_form_link;
        } else {
            pdfLink.value = '';
            alert(response.data?.msg);
            $("#generateVoucher").prop('disabled', false);
        }
    } catch (error) {
        alert('Something went wrong!!! EPI:002');
        $("#generateVoucher").prop('disabled', false);
    }
};

const submitGuideRequest = async () => {
    try {
        // if (isLocked.value === 0) {
        //     alert('Please lock first.');
        //     $("#submitGuideRequest").prop('disabled', false);
        //     return false;
        // }
        // if (voucherInfo.value.length === 0) {
        //     alert('No voucher found.');
        //     $("#submitGuideRequest").prop('disabled', false);
        //     return false;
        // }

        $("#submitGuideRequest").prop('disabled', true);
        const response = await axios.post(`/guides/submit-guide-request/${guideId}`);

        if (response.data?.responseCode === 1) {
            alert(response.data?.msg);
            $("#submitGuideRequest").prop('disabled', false);
            await router.push({ name: 'GuideApplicationList' });
        }
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg);
            $("#submitGuideRequest").prop('disabled', false);
            return false;
        }
    } catch (error) {
        alert('Something went wrong!!! EPI:003');
        $("#submitGuideRequest").prop('disabled', false);
    }
};
const cancelGuideRequest = async () => {
    try {
        $("#cancelGuideRequest").prop('disabled', true);
        const response = await axios.post(`/guides/cancel-guide-request/${guideId}`);

        if (response.data?.responseCode === 1) {
            alert(response.data?.msg);
            $("#cancelGuideRequest").prop('disabled', false);
            await router.push({ name: 'GuideApplicationList' });
        } else {
            alert(response.data?.msg);
            $("#cancelGuideRequest").prop('disabled', false);
        }
    } catch (error) {
        alert('Something went wrong!!! EPI:004');
        $("#submitGuideRequest").prop('disabled', false);
    }
};
const emitVoucherRowDetailsDataClick = () => {
    voucherRowDetailsData();
};
const closeModal = () => {
    const modal = document.getElementById('pilgrimAddVoucher');
    if (modal) {
        $(modal).modal('hide');
    }
};
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
                                <div v-if="guideData.status_id == 0" class="title-btn-group">
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
                                <div class="table-responsive app-list-table">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th width="70">SL No</th>
                                            <th>Voucher Name</th>
                                            <th>Voucher Number</th>
                                            <th>Package</th>
                                            <th>Number of Pilgrim</th>
                                            <th v-if="guideData.status_id == 0">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(info,index) in voucherInfo" :key="info.id">
                                                <td>{{index + 1}}</td>
                                                <td>{{info.name}}</td>
                                                <td>{{info.tracking_no }}</td>
                                                <td>{{info.package_caption }}</td>
                                                <td>{{info.total_pilgrim }}</td>
                                                <td v-if="guideData.status_id == 0">
                                                    <div class="btn-flex-center">
                                                        <button @click="deleteVoucher(info.id)" type="button" class="btn btn-outline-danger">Delete</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="no-data-box" v-if="voucherInfo.length === 0">
                                    <span>Member not added</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bd-card-footer">
                        <div class="flex-space-btw info-btn-group">
                            <button class="btn btn-default" @click="emitPreviousClick"><span>Previous</span></button>

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
                                <button v-if="guideData.status_id == 0" class="btn btn-blue" type="button"
                                        id="submitGuideRequest" @click="submitGuideRequest()"
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  Voucher Modal Start  -->
    <div class="modal ba-modal fade"  id="pilgrimAddVoucher" tabindex="-1" aria-labelledby="pilgrimAddVoucher" aria-hidden="true">
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
                        <h3>Add Voucher</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Package <span class="clr-red">*</span></label>
                        <div class="form-select2-item">
                            <select class="form-control hajj-select2" v-model="guideData.hajj_packages" :class="[allerros.hajj_packages ? 'has-error' : '']" required>
                                <option value="" selected disabled>Select Package( {{activeSessionCaption}} )</option>
                                <option v-for="hajjPackage in hajjPackages" :key="hajjPackage.id" :value="hajjPackage.id">
                                    {{ hajjPackage.caption }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Voucher Number <span class="clr-red">*</span></label>
                        <div class="voucher-src">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Registration on voucher Number" v-model="guideData.voucher_number">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" @click="emitVoucherRowDetailsDataClick">
                                        <span class="svg-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66667 3.33339C5.78261 3.33339 4.93477 3.6846 4.30964 4.30975C3.68452 4.9349 3.33333 5.78279 3.33333 6.66689C3.33333 7.55098 3.68452 8.39887 4.30964 9.02402C4.93477 9.64918 5.78261 10.0004 6.66667 10.0004C7.55072 10.0004 8.39857 9.64918 9.02369 9.02402C9.64881 8.39887 10 7.55098 10 6.66689C10 5.78279 9.64881 4.9349 9.02369 4.30975C8.39857 3.6846 7.55072 3.33339 6.66667 3.33339ZM2 6.66689C2 5.92769 2.17558 5.19907 2.51229 4.54102C2.84899 3.88297 3.33719 3.31431 3.93667 2.88189C4.53615 2.44946 5.22978 2.16562 5.96042 2.05375C6.69107 1.94189 7.43784 2.00518 8.13923 2.23844C8.84063 2.47169 9.47659 2.86823 9.99474 3.39539C10.5129 3.92255 10.8984 4.56527 11.1196 5.2706C11.3407 5.97594 11.3912 6.72372 11.2668 7.45237C11.1424 8.18102 10.8466 8.8697 10.404 9.46169L13.8047 12.8619C13.9298 12.9869 14.0001 13.1565 14.0001 13.3333C14.0002 13.5102 13.93 13.6798 13.805 13.8049C13.68 13.93 13.5104 14.0003 13.3336 14.0004C13.1567 14.0004 12.9871 13.9302 12.862 13.8052L9.462 10.4051C8.76834 10.924 7.94395 11.2396 7.08111 11.3165C6.21828 11.3934 5.35105 11.2287 4.57654 10.8407C3.80202 10.4527 3.15079 9.85675 2.69574 9.1196C2.24068 8.38245 1.99978 7.53319 2 6.66689Z" fill="white"/>
                                            </svg>
                                        </span>
                                        <span>Search</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="no-data-box" v-if="guideData.voucher_row_details_data.length === 0 && guideData.already_added_voucher === ''">
                        <span>No data found.</span>
                    </div>

                    <div class="table-responsive app-list-table" v-else-if="guideData.voucher_row_details_data.length !== 0">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Voucher Name</th>
                                <th>Voucher Number</th>
                                <th>Number of Pilgrim</th>
                            </tr>
                            </thead>
                            <tbody v-for="(voucher, i) in guideData.voucher_row_details_data" :key="voucher.voucher_id">
                            <tr>
                                <td>{{ voucher.voucher_name }}</td>
                                <td>{{ voucher.voucher_tracking_no }}</td>
                                <td>{{ voucher.no_of_pilgrims }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <p class="text-danger text-center">{{ guideData.already_added_voucher }}</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="footer-btn-group">
                        <button type="button" class="btn btn-default" data-dismiss="modal" @click="closeModal"><span>Close</span></button>
                        <button type="button" class="btn btn-blue" :disabled="guideData.voucher_row_details_data.length === 0" @click="addVoucherToGuide()"><span>Add</span></button>
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
