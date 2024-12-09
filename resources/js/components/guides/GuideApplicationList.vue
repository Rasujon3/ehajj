<script setup>
import {defineProps, defineEmits, toRefs, ref, onMounted, computed} from 'vue';
import axios from "axios";
import { useStore } from 'vuex';
import { useRouter } from "vue-router";

const router = useRouter();

const store = useStore();
onMounted(async () => {
    await getApplicationList();
    await checkGuideApplicationLastDate();
});

let applicationList = ref([]);
let isLoading = ref(false);
let isLastDateExist = ref(false);
const getApplicationList = async () => {
    try {
        const response = await axios.get(`/guides/get-application-list`);
        if (response.data?.responseCode === 1) {
            applicationList.value = response.data?.data;
        }
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg);
        }
    } catch (error) {
        alert('Something went wrong!!! GAL:001');
    }
};

function getStatusText(statusId) {
    let statusText = '';
    if (statusId === 0) {
        statusText = 'Save as draft';
    } else if (statusId === -1) {
        statusText = 'Cancelled';
    } else if (statusId === 1) {
        statusText = 'Submitted';
    } else if (statusId === 2) {
        statusText = 'Recommended';
    } else if (statusId === 3) {
        statusText = 'Approved';
    } else if (statusId === 4) {
        statusText = 'Rejected';
    } else if (statusId === 5) {
        statusText = 'Shortfall';
    } else if (statusId === 6) {
        statusText = 'Re-submitted';
    } else {
        statusText = 'Unknown Status';
    }
    return statusText;
}
const openApplication = async (id) => {
    await router.push({ name: 'GuideProfileView', params: { id } });
};
const newApplicationBtnClick = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(`/guides/is-guide-application-exist`);

        if (response.data?.responseCode === 1) {
            isLoading.value = false;
            await router.push({ name: 'HomeGuide'});
        } else {
            isLoading.value = false;
            alert(response.data?.msg);
        }
    } catch (error) {
        isLoading.value = false;
        alert('Something went wrong!!! [GE:002]');
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
};
</script>

<template>
    <div class="hajj-main-content">
        <div class="container">
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
                            <h3>List of Application</h3>
                        </div>

                        <div class="title-btn-group" id="newApplicationBtn" v-if="isLastDateExist" :class="{ 'disabled-div': isLoading }" @click="!isLoading && newApplicationBtnClick()">
                            <div class="list-btn-white">
                                <span class="list-btn-icon flex-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5001 10.4C7.93015 10.4 9.9001 8.43006 9.9001 6.00001C9.9001 3.56995 7.93015 1.60001 5.5001 1.60001C3.07005 1.60001 1.1001 3.56995 1.1001 6.00001C1.1001 8.43006 3.07005 10.4 5.5001 10.4ZM6.0501 4.35001C6.0501 4.04625 5.80385 3.80001 5.5001 3.80001C5.19634 3.80001 4.9501 4.04625 4.9501 4.35001V5.45001H3.8501C3.54634 5.45001 3.3001 5.69625 3.3001 6.00001C3.3001 6.30376 3.54634 6.55001 3.8501 6.55001H4.9501V7.65001C4.9501 7.95376 5.19634 8.20001 5.5001 8.20001C5.80385 8.20001 6.0501 7.95376 6.0501 7.65001V6.55001H7.1501C7.45386 6.55001 7.7001 6.30376 7.7001 6.00001C7.7001 5.69625 7.45386 5.45001 7.1501 5.45001H6.0501V4.35001Z" fill="#0F6849"></path>
                                    </svg>
                                </span>
                                <span class="list-btn-text">নতুন আবেদন</span>
                            </div>
                        </div>
                    </div>

                    <div class="bd-card-content" v-if="applicationList.length !== 0">
                        <div class="table-responsive app-list-table">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th width="100">Serial No.</th>
                                    <th>Tracking Number</th>
                                    <th>Year of Application</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody v-for="(application, index) in applicationList" :key="application.id">
                                <tr>
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ application.tracking_no }}</td>
                                    <td>{{ application.haj_year }}</td>
                                    <td>{{ getStatusText(application.status_id) }}</td>
                                    <td>
                                        <div class="btn-flex-center">
                                            <button class="btn-outline-green" type="button" @click="openApplication(application.id)">
                                                Open
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="no-data-box" v-else>
                        <span>No application found.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
#newApplicationBtn:hover {
    cursor: pointer !important;
}
.disabled-div {
    opacity: 0.5 !important;
    pointer-events: none !important;
}
</style>
