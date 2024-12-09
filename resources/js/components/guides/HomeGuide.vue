<script setup>
import axios from "axios";
import {onMounted, ref} from "vue";
import {useRouter} from "vue-router";
import { useStore } from 'vuex';

const router = useRouter();
const store = useStore();
const hajjExperience = ref('yes'); // Default selection

onMounted(async () => {
    // await isGuideApplicationExist();
});
const trackingNo = ref('');
let guideInfo = ref([]);
const onTrackingNoSubmit = async () => {
    if (trackingNo.value.trim() === '') {
        alert('Please add tracking number.');
        return false;
    }
    try {
        $("#hajj_experience_yes_btn").prop('disabled', true);
        let response = await axios.get(`/guides/get-guide-data`, {
            params: {
                trackingNo: trackingNo.value
            }
        });
        if (response.data.responseCode === 1) {
            guideInfo.value = response.data.data;
            await store.dispatch('updateGuideInfo', guideInfo.value);
            await router.push({ name: 'HajjGuideRegistration' });
        }
        if (response.data?.responseCode === -1) {
            alert(response.data?.msg)
            $("#hajj_experience_yes_btn").prop('disabled', false);
            return false;
        }
    } catch (error) {
        alert('Something went wrong!!! [HG:001]');
    }
}
const isGuideApplicationExist = async () => {
    try {
        const response = await axios.get(`/guides/is-guide-application-exist`);
        if (response.data?.responseCode === -1) {
            await router.push({ name: 'GuideApplicationList'});
        }
    } catch (error) {
        alert('Something went wrong!!! [HG:002]');
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
                            <h3>Guide Registration</h3>
                        </div>
                    </div>

                    <div class="bd-card-content">
                        <div class="row">
                            <div class="col-md-6">
                                <p>বিগত বছরে হজ গাইড কিংবা হজের কোন অভিজ্ঞতা আছে কিনা ?</p>
                            </div>
                            <div class="col-md-6">
                                <div class="card-form-block">
                                    <div class="from-redio-group">
                                        <div class="bs_radio" :class="{ 'radio_checked': hajjExperience === 'yes' }">
                                            <div class="bs_radio_border">
                                                <input type="radio" name="prev_hajj_experience" id="prev_hajj_experience_yes" value="yes" v-model="hajjExperience">
                                                <label for="prev_hajj_experience_yes">হ্যাঁ</label>
                                            </div>
                                        </div>
                                        <div class="bs_radio" :class="{ 'radio_checked': hajjExperience === 'no' }">
                                            <div class="bs_radio_border">
                                                <input type="radio" name="prev_hajj_experience" id="prev_hajj_experience_no" value="no" v-model="hajjExperience">
                                                <label for="prev_hajj_experience_no">না</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="hajjExperience === 'yes'" class="row pt-2">
                            <div class="col-md-6">
                                <p class="required-star">সর্বশেষ ট্রাকিং নম্বর লিখুন</p>
                            </div>
                            <div class="col-md-6">
                                <div class="card-form-block">
                                    <input class="form-control" type="text" placeholder="সর্বশেষ ট্রাকিং নম্বর লিখুন"  v-model="trackingNo" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bd-card-footer">
                        <div class="flex-space-btw info-btn-group">
                            <router-link :to="{ name: 'GuideApplicationList' }">
                                <button class="btn btn-default"><span>Close</span></button>
                            </router-link>

                            <button id="hajj_experience_yes_btn" class="btn btn-blue disable" type="button" v-if="hajjExperience === 'yes'" @click="onTrackingNoSubmit()" >
                                <span>Next</span>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
                                        <path d="M1.10449 1.20866L4.89616 5.00033L1.10449 8.79199" stroke="white" stroke-width="0.8125" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </button>

                            <router-link :to="{name: 'GuideNIDVerify'}" v-else>
                                <button id="hajj_experience_no_btn" class="btn btn-blue disable" type="button">
                                    <span>Next</span>
                                    <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
                                        <path d="M1.10449 1.20866L4.89616 5.00033L1.10449 8.79199" stroke="white" stroke-width="0.8125" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                </button>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
