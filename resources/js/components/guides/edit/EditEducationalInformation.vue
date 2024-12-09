<script setup>
import {computed, onMounted, onUnmounted, ref, toRefs, watch} from 'vue';
import { useStore } from 'vuex';

const store = useStore();

const props = defineProps({
    guideData: Object,
    allerros: Array
});

const { guideData, allerros } = toRefs(props);

const sscFileName = ref('');
const hscFileName = ref('');
const honoursFileName = ref('');
const mastersFileName = ref('');
const currentImage = ref('');

const ssc_certificate_link = ref('');
const hsc_certificate_link = ref('');
const honours_certificate_link = ref('');
const masters_certificate_link = ref('');
const isPdf = ref(false);

// Initialize profilePicUrl on mount
onMounted(() => {
    ssc_certificate_link.value = guideData.value.ssc_certificate_link;
    hsc_certificate_link.value = guideData.value.hsc_certificate_link;
    honours_certificate_link.value = guideData.value.honours_certificate_link;
    masters_certificate_link.value = guideData.value.masters_certificate_link;
});

onUnmounted(() => {
    if (isPdf.value && currentImage.value) {
        URL.revokeObjectURL(currentImage.value);
    }
});

watch(() => guideData.value.ssc_certificate_link, (newPic) => {
    if (newPic instanceof File) {
        ssc_certificate_link.value = URL.createObjectURL(newPic);
    } else {
        ssc_certificate_link.value = newPic;
    }
});
watch(() => guideData.value.hsc_certificate_link, (newPic) => {
    if (newPic instanceof File) {
        hsc_certificate_link.value = URL.createObjectURL(newPic);
    } else {
        hsc_certificate_link.value = newPic;
    }
});
watch(() => guideData.value.honours_certificate_link, (newPic) => {
    if (newPic instanceof File) {
        honours_certificate_link.value = URL.createObjectURL(newPic);
    } else {
        honours_certificate_link.value = newPic;
    }
});
watch(() => guideData.value.masters_certificate_link, (newPic) => {
    if (newPic instanceof File) {
        masters_certificate_link.value = URL.createObjectURL(newPic);
    } else {
        masters_certificate_link.value = newPic;
    }
});

function getTruncatedFileName(fileName) {
    if (!fileName) {
        return '';
    }
    const maxLength = 3;
    const extension = fileName.split('.').pop();
    const truncatedName = fileName.slice(0, maxLength) + '...';
    return fileName.length <= maxLength ? fileName : `${truncatedName}.${extension}`;
}

function onSSCFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        guideData.value.ssc_certificate_link = file;
        sscFileName.value = getTruncatedFileName(file.name);
    } else {
        guideData.value.ssc_certificate_link = null;
        sscFileName.value = '';
    }
}
function onHSCFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        guideData.value.hsc_certificate_link = file;
        hscFileName.value = getTruncatedFileName(file.name);
    } else {
        guideData.value.hsc_certificate_link = null;
        hscFileName.value = '';
    }
}
function onHonoursFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        guideData.value.honours_certificate_link = file;
        honoursFileName.value = getTruncatedFileName(file.name);
    } else {
        guideData.value.honours_certificate_link = null;
        honoursFileName.value = '';
    }
}
function onMastersFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        guideData.value.masters_certificate_link = file;
        mastersFileName.value = getTruncatedFileName(file.name);
    } else {
        guideData.value.masters_certificate_link = null;
        mastersFileName.value = '';
    }
}

function showModal(certificate_link, fileName='') {
    if(certificate_link.includes('application/pdf')){
        // Handle PDF as a Blob URL
        const byteCharacters = atob(certificate_link.split(',')[1]); // Decode the base64 string (after the comma)
        const byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        const byteArray = new Uint8Array(byteNumbers);
        const blob = new Blob([byteArray], { type: 'application/pdf' });
        const blobUrl = URL.createObjectURL(blob);
        currentImage.value = blobUrl; // Use Blob URL for the PDF
        isPdf.value = true;
    }else{
        if(fileName && fileName.includes('pdf')){
            isPdf.value = true;
        }else {
            isPdf.value = false;
        }
        currentImage.value = certificate_link;
    }
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}
</script>

<template>
    <div class="row">
        <input hidden="hidden" v-model="guideData.hidden_ssc_certificate_link">
        <input hidden="hidden" v-model="guideData.hidden_hsc_certificate_link">
        <input hidden="hidden" v-model="guideData.hidden_honours_certificate_link">
        <input hidden="hidden" v-model="guideData.hidden_masters_certificate_link">
        <div class="col-lg-12">
            <div class="border-card-block">
                <div class="bd-card-head">
                    <div class="bd-card-title">
                        <h3>শিক্ষাগত যোগ্যতা</h3>
                    </div>
                </div>

                <div class="bd-card-content">
                    <div class="table-responsive edu-table">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>পরীক্ষার নাম</th>
                                <th>শিক্ষা প্রতিষ্ঠান</th>
                                <th>পাসের সন</th>
                                <th>বোর্ড/ বিশ্ববিদ্যালয়</th>
                                <th>গ্রেড/শ্রেনী/ বিভাগ</th>
                                <th>শিক্ষা সনদ</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr :class="[allerros.ssc_certificate_link ? 'has-tr-error' : '']">
                                <td>
                                    <div class="td-form-group">
                                        <select class="form-control" name="" id="">
                                            <option value="0">SSC</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="শিক্ষা প্রতিষ্ঠান" v-model="guideData.ssc_institute_name">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="number" class="form-control" placeholder="পাসের সন" v-model="guideData.ssc_passing_year">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="বোর্ড/ বিশ্ববিদ্যালয়" v-model="guideData.ssc_board_name">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="গ্রেড/শ্রেনী/ বিভাগ" v-model="guideData.ssc_grade">
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-flex-center">
                                        <div class="btn-filebrowse btn btn-upload-outline">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                    <path d="M12.4167 9.16699V11.8753C12.4167 12.019 12.3596 12.1568 12.258 12.2583C12.1564 12.3599 12.0187 12.417 11.875 12.417H2.125C1.98134 12.417 1.84357 12.3599 1.74198 12.2583C1.6404 12.1568 1.58333 12.019 1.58333 11.8753V9.16699H0.5V11.8753C0.5 12.3063 0.671205 12.7196 0.975952 13.0244C1.2807 13.3291 1.69402 13.5003 2.125 13.5003H11.875C12.306 13.5003 12.7193 13.3291 13.0241 13.0244C13.3288 12.7196 13.5 12.3063 13.5 11.8753V9.16699H12.4167Z" fill="#2196F3"/>
                                                    <path d="M6.98217 0.500006C6.76886 0.499418 6.55752 0.540933 6.36028 0.622171C6.16304 0.703409 5.98378 0.822774 5.83275 0.973423L3.70996 3.09622L4.47588 3.86213L6.44429 1.89426L6.45838 10.7917H7.54171L7.52763 1.90184L9.48792 3.86213L10.2538 3.09622L8.13105 0.973423C7.98012 0.82279 7.80095 0.703432 7.6038 0.622192C7.40665 0.540953 7.1954 0.49943 6.98217 0.500006Z" fill="#2196F3"/>
                                                </svg>
                                            </span>
                                            <span>{{ sscFileName || 'Upload' }}</span>
                                            <input id="uploadBtn" type="file" class="uploadInput" @change="onSSCFileChange">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group" v-if="ssc_certificate_link !== ''">
                                        <button class="btn btn-success" type="button" @click="showModal(ssc_certificate_link, sscFileName)">View</button>
                                    </div>
                                </td>
                                <span v-if="allerros.ssc_certificate_link" class="text-danger">{{ allerros.ssc_certificate_link[0] }}</span>
                            </tr>
                            <tr :class="[allerros.hsc_certificate_link ? 'has-tr-error' : '']">
                                <td>
                                    <div class="td-form-group">
                                        <select class="form-control" name="" id="">
                                            <option value="0">HSC</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="শিক্ষা প্রতিষ্ঠান" v-model="guideData.hsc_institute_name">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="number" class="form-control" placeholder="পাসের সন" v-model="guideData.hsc_passing_year">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="বোর্ড/ বিশ্ববিদ্যালয়" v-model="guideData.hsc_board_name">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="গ্রেড/শ্রেনী/ বিভাগ" v-model="guideData.hsc_grade">
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-flex-center">
                                        <div class="btn-filebrowse btn btn-upload-outline">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                    <path d="M12.4167 9.16699V11.8753C12.4167 12.019 12.3596 12.1568 12.258 12.2583C12.1564 12.3599 12.0187 12.417 11.875 12.417H2.125C1.98134 12.417 1.84357 12.3599 1.74198 12.2583C1.6404 12.1568 1.58333 12.019 1.58333 11.8753V9.16699H0.5V11.8753C0.5 12.3063 0.671205 12.7196 0.975952 13.0244C1.2807 13.3291 1.69402 13.5003 2.125 13.5003H11.875C12.306 13.5003 12.7193 13.3291 13.0241 13.0244C13.3288 12.7196 13.5 12.3063 13.5 11.8753V9.16699H12.4167Z" fill="#2196F3"/>
                                                    <path d="M6.98217 0.500006C6.76886 0.499418 6.55752 0.540933 6.36028 0.622171C6.16304 0.703409 5.98378 0.822774 5.83275 0.973423L3.70996 3.09622L4.47588 3.86213L6.44429 1.89426L6.45838 10.7917H7.54171L7.52763 1.90184L9.48792 3.86213L10.2538 3.09622L8.13105 0.973423C7.98012 0.82279 7.80095 0.703432 7.6038 0.622192C7.40665 0.540953 7.1954 0.49943 6.98217 0.500006Z" fill="#2196F3"/>
                                                </svg>
                                            </span>
                                            <span>{{ hscFileName || 'Upload' }}</span>
                                            <input id="uploadBtn" type="file" class="uploadInput" @change="onHSCFileChange">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group" v-if="hsc_certificate_link !== ''">
                                            <button class="btn btn-success" type="button" @click="showModal(hsc_certificate_link, hscFileName)">View</button>
                                    </div>
                                </td>
                                <span v-if="allerros.hsc_certificate_link" class="text-danger">{{ allerros.hsc_certificate_link[0] }}</span>
                            </tr>
                            <tr :class="[allerros.honours_certificate_link ? 'has-tr-error' : '']">
                                <td>
                                    <div class="td-form-group">
                                        <select class="form-control" name="" id="">
                                            <option value="0">Honours</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="শিক্ষা প্রতিষ্ঠান" v-model="guideData.honours_institute_name" >
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="number" class="form-control" placeholder="পাসের সন" v-model="guideData.honours_passing_year">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="বোর্ড/ বিশ্ববিদ্যালয়" v-model="guideData.honours_board_name">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="গ্রেড/শ্রেনী/ বিভাগ" v-model="guideData.honours_grade">
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-flex-center">
                                        <div class="btn-filebrowse btn btn-upload-outline">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                    <path d="M12.4167 9.16699V11.8753C12.4167 12.019 12.3596 12.1568 12.258 12.2583C12.1564 12.3599 12.0187 12.417 11.875 12.417H2.125C1.98134 12.417 1.84357 12.3599 1.74198 12.2583C1.6404 12.1568 1.58333 12.019 1.58333 11.8753V9.16699H0.5V11.8753C0.5 12.3063 0.671205 12.7196 0.975952 13.0244C1.2807 13.3291 1.69402 13.5003 2.125 13.5003H11.875C12.306 13.5003 12.7193 13.3291 13.0241 13.0244C13.3288 12.7196 13.5 12.3063 13.5 11.8753V9.16699H12.4167Z" fill="#2196F3"/>
                                                    <path d="M6.98217 0.500006C6.76886 0.499418 6.55752 0.540933 6.36028 0.622171C6.16304 0.703409 5.98378 0.822774 5.83275 0.973423L3.70996 3.09622L4.47588 3.86213L6.44429 1.89426L6.45838 10.7917H7.54171L7.52763 1.90184L9.48792 3.86213L10.2538 3.09622L8.13105 0.973423C7.98012 0.82279 7.80095 0.703432 7.6038 0.622192C7.40665 0.540953 7.1954 0.49943 6.98217 0.500006Z" fill="#2196F3"/>
                                                </svg>
                                            </span>
                                            <span>{{ honoursFileName || 'Upload' }}</span>
                                            <input id="uploadBtn" type="file" class="uploadInput" @change="onHonoursFileChange">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group" v-if="honours_certificate_link !== ''">
                                        <button class="btn btn-success" type="button" @click="showModal(honours_certificate_link, honoursFileName)">View</button>
                                    </div>
                                </td>
                                <span v-if="allerros.honours_certificate_link" class="text-danger">{{ allerros.honours_certificate_link[0] }}</span>
                            </tr>
                            <tr :class="[allerros.masters_certificate_link ? 'has-tr-error' : '']">
                                <td>
                                    <div class="td-form-group">
                                        <select class="form-control" name="" id="">
                                            <option value="0">Masters</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="শিক্ষা প্রতিষ্ঠান" v-model="guideData.masters_institute_name">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="number" class="form-control" placeholder="পাসের সন" v-model="guideData.masters_passing_year">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="বোর্ড/ বিশ্ববিদ্যালয়" v-model="guideData.masters_board_name">
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group">
                                        <input type="text" class="form-control" placeholder="গ্রেড/শ্রেনী/ বিভাগ" v-model="guideData.masters_grade">
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-flex-center">
                                        <div class="btn-filebrowse btn btn-upload-outline">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                    <path d="M12.4167 9.16699V11.8753C12.4167 12.019 12.3596 12.1568 12.258 12.2583C12.1564 12.3599 12.0187 12.417 11.875 12.417H2.125C1.98134 12.417 1.84357 12.3599 1.74198 12.2583C1.6404 12.1568 1.58333 12.019 1.58333 11.8753V9.16699H0.5V11.8753C0.5 12.3063 0.671205 12.7196 0.975952 13.0244C1.2807 13.3291 1.69402 13.5003 2.125 13.5003H11.875C12.306 13.5003 12.7193 13.3291 13.0241 13.0244C13.3288 12.7196 13.5 12.3063 13.5 11.8753V9.16699H12.4167Z" fill="#2196F3"/>
                                                    <path d="M6.98217 0.500006C6.76886 0.499418 6.55752 0.540933 6.36028 0.622171C6.16304 0.703409 5.98378 0.822774 5.83275 0.973423L3.70996 3.09622L4.47588 3.86213L6.44429 1.89426L6.45838 10.7917H7.54171L7.52763 1.90184L9.48792 3.86213L10.2538 3.09622L8.13105 0.973423C7.98012 0.82279 7.80095 0.703432 7.6038 0.622192C7.40665 0.540953 7.1954 0.49943 6.98217 0.500006Z" fill="#2196F3"/>
                                                </svg>
                                            </span>
                                            <span>{{ mastersFileName || 'Upload' }}</span>
                                            <input id="uploadBtn" type="file" class="uploadInput" @change="onMastersFileChange">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-form-group" v-if="masters_certificate_link !== ''">
                                        <button class="btn btn-success" type="button" @click="showModal(masters_certificate_link, mastersFileName)">View</button>
                                    </div>
                                </td>
                                <span v-if="allerros.masters_certificate_link" class="text-danger">{{ allerros.masters_certificate_link[0] }}</span>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /row -->

    <!-- Modal -->
    <div class="modal ba-modal fade"  id="imageModal" tabindex="-1" aria-labelledby="pilgrimAddVoucher" aria-hidden="true">
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

                    <div class="modal-title" id="imageModalLabel">
                        <h3>Certificate</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <!-- <image :src="currentImage" style="width: 100%; height: auto;"></image> -->
                    <div v-if="!isPdf"
                        :style="{
                                backgroundImage: `url(${currentImage})`,
                                backgroundSize: 'contain',
                                backgroundRepeat: 'no-repeat',
                                backgroundPosition: 'center',
                                width: '100%',
                                height: '500px'
                               }"
                    >
                    </div>
                    <iframe v-else :src="currentImage" width="100%" height="100%" :style="{ minHeight: '460px' }" frameborder="0"></iframe>
                </div>

                <div class="modal-footer">
                    <div class="footer-btn-group">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><span>Close</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->
</template>

<style scoped>
.has-error {
    border: 1px solid red;
}
.has-tr-error {
    border: 2px solid red;
}
</style>
