<script setup>
import {computed, onMounted, ref, toRefs, watch} from 'vue';

const props = defineProps({
    guideData: Object,
    allerros: Array
});

const { guideData, allerros } = toRefs(props);

const sscFileName = ref('');
const hscFileName = ref('');
const honoursFileName = ref('');
const mastersFileName = ref('');
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

</script>

<template>
    <div class="row">
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
                                <span v-if="allerros.masters_certificate_link" class="text-danger">{{ allerros.masters_certificate_link[0] }}</span>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /row -->
</template>

<style scoped>
.has-error {
    border: 1px solid red;
}
.has-tr-error {
    border: 2px solid red;
}
</style>
