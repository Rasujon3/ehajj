<script setup>
import axios from "axios";
import {computed, onMounted, ref, watch} from 'vue';
const nidPicUrl = ref('/assets/custom/images/2024/06/nid-photo-front.png');
import {useRouter} from "vue-router";
import { useStore } from 'vuex';

const router = useRouter();
const store = useStore();
let guideData = ref({
    'nid_img' : null,
});
let guideInfo = ref([]);

onMounted(async () => {
    // await isGuideApplicationExist();
});

function onNidPicFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        guideData.value.nid_img = file;
        nidPicUrl.value = URL.createObjectURL(file);
    } else {
        guideData.value.nid_img = null;
        nidPicUrl.value = '/assets/custom/images/2024/06/nid-photo-front.png';
    }
}
const nidPhotoStyle = computed(() => {
    return {
        backgroundImage: `url(${nidPicUrl.value})`,
        backgroundSize: 'cover',   // Ensure the background image covers the entire div
        backgroundPosition: 'center center'  // Center the background image
    };
});

const onSubmit = async () => {
    $("#submit-btn").prop('disabled', true); // Disable the button
    if (guideData.value.nid_img === null) {
        alert('Please upload NID Image');
        $("#submit-btn").prop('disabled', false); // Disable the button
        return false;
    }
    try {
        const form = new FormData();
        form.append('nid_img', guideData.value.nid_img);

        await axios.post('/guides/nid-file-upload', form, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        }).then(function (resp) {
            if (resp.data?.responseCode === -1) {
                alert(resp.data.msg)
                $("#submit-btn").prop('disabled', false); // Disable the button
                return false;
            }
            if (resp.data?.responseCode === 1) {
                guideInfo.value = resp.data?.data;
                 store.dispatch('updateGuideInfo', guideInfo.value);
                 router.push({ name: 'HajjGuideRegistration' });
                $("#submit-btn").prop('disabled', false); // Disable the button
            }
        }).catch((error) => {
            alert('Something went wrong!!! [error:1]')
            $("#submit-btn").prop('disabled', false); // Disable the button
        });
    } catch (error) {
        alert('Something went wrong!!! [error:2]')
        $("#submit-btn").prop('disabled', false); // Disable the button
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
                            <h3>Guide Registration for 2025</h3>
                        </div>
                    </div>

                    <div class="bd-card-content">
                        <div class="nid-guide-container">
                            <div class="sec-title text-center mt-3">
                                <h2>জাতীয় পরিচয়পত্র</h2>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="nid-verify-item">
                                        <div class="nid-verify-head">
                                            <p>আপনার জাতীয় পরিচয়পত্রের সামনের অংশটি</p>
                                            <div class="fileUpload btn btn-fileUpload">
                                                    <span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                            <g clip-path="url(#clip0_3425_4759)">
                                                                <path d="M6.46157 1.72278L6.45778 10.085C6.45778 10.2287 6.51485 10.3665 6.61643 10.4681C6.71802 10.5696 6.85579 10.6267 6.99945 10.6267C7.14311 10.6267 7.28088 10.5696 7.38247 10.4681C7.48405 10.3665 7.54112 10.2287 7.54112 10.085L7.54491 1.73199L9.12224 3.30987C9.22382 3.41141 9.36157 3.46846 9.5052 3.46846C9.64883 3.46846 9.78658 3.41141 9.88816 3.30987C9.98971 3.20829 10.0468 3.07054 10.0468 2.92691C10.0468 2.78328 9.98971 2.64553 9.88816 2.54395L8.14887 0.802492C7.99796 0.651486 7.81877 0.531696 7.62155 0.449967C7.42433 0.368238 7.21294 0.326172 6.99945 0.326172C6.78596 0.326172 6.57457 0.368238 6.37735 0.449967C6.18013 0.531696 6.00094 0.651486 5.85003 0.802492L4.11074 2.54233C4.00919 2.6439 3.95215 2.78165 3.95215 2.92528C3.95215 3.06891 4.00919 3.20666 4.11074 3.30824C4.21232 3.40979 4.35007 3.46683 4.4937 3.46683C4.63733 3.46683 4.77508 3.40979 4.87666 3.30824L6.46157 1.72278Z" fill="white"/>
                                                                <path d="M12.4167 9.53385V11.7005C12.4167 11.8442 12.3596 11.982 12.258 12.0835C12.1564 12.1851 12.0187 12.2422 11.875 12.2422H2.125C1.98134 12.2422 1.84357 12.1851 1.74198 12.0835C1.6404 11.982 1.58333 11.8442 1.58333 11.7005V9.53385C1.58333 9.39019 1.52627 9.25242 1.42468 9.15084C1.3231 9.04926 1.18533 8.99219 1.04167 8.99219C0.898008 8.99219 0.760233 9.04926 0.658651 9.15084C0.557068 9.25242 0.5 9.39019 0.5 9.53385L0.5 11.7005C0.5 12.1315 0.671205 12.5448 0.975952 12.8496C1.2807 13.1543 1.69402 13.3255 2.125 13.3255H11.875C12.306 13.3255 12.7193 13.1543 13.0241 12.8496C13.3288 12.5448 13.5 12.1315 13.5 11.7005V9.53385C13.5 9.39019 13.4429 9.25242 13.3414 9.15084C13.2398 9.04926 13.102 8.99219 12.9583 8.99219C12.8147 8.99219 12.6769 9.04926 12.5753 9.15084C12.4737 9.25242 12.4167 9.39019 12.4167 9.53385Z" fill="white"/>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_3425_4759">
                                                                    <rect width="13" height="13" fill="white" transform="translate(0.5 0.326172)"/>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </span>
                                                <span>আপলোড করুণ</span>
                                                <input id="uploadBtn" type="file" class="uploadInput" @change="onNidPicFileChange" required>
                                            </div>
                                            <!-- <p>অথবা</p>
                                            <button class="btn btn-outline-primary">
                                                    <span class="">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.73467 1.95117C7.96204 1.95123 8.18363 2.02283 8.36805 2.15583C8.55247 2.28883 8.69037 2.47648 8.76222 2.69221L9.05634 3.57625H10.2913C10.7223 3.57625 11.1356 3.74747 11.4404 4.05223C11.7451 4.35699 11.9163 4.77034 11.9163 5.20133V9.53488C11.9163 9.96588 11.7451 10.3792 11.4404 10.684C11.1356 10.9887 10.7223 11.16 10.2913 11.16H2.70801C2.27703 11.16 1.86371 10.9887 1.55896 10.684C1.25421 10.3792 1.08301 9.96588 1.08301 9.53488V5.20133C1.08301 4.77034 1.25421 4.35699 1.55896 4.05223C1.86371 3.74747 2.27703 3.57625 2.70801 3.57625H3.94301L4.23713 2.69221C4.30901 2.47639 4.44699 2.28868 4.63152 2.15567C4.81605 2.02266 5.03775 1.95112 5.26522 1.95117H7.73413H7.73467ZM7.73467 3.03456H5.26467L4.97055 3.9186C4.89867 4.13442 4.76069 4.32214 4.57616 4.45514C4.39164 4.58815 4.16993 4.65969 3.94247 4.65964H2.70801C2.56435 4.65964 2.42657 4.71671 2.32499 4.8183C2.22341 4.91989 2.16634 5.05767 2.16634 5.20133V9.53488C2.16634 9.67855 2.22341 9.81633 2.32499 9.91792C2.42657 10.0195 2.56435 10.0766 2.70801 10.0766H10.2913C10.435 10.0766 10.5728 10.0195 10.6744 9.91792C10.7759 9.81633 10.833 9.67855 10.833 9.53488V5.20133C10.833 5.05767 10.7759 4.91989 10.6744 4.8183C10.5728 4.71671 10.435 4.65964 10.2913 4.65964H9.05634C8.82897 4.65958 8.60738 4.58798 8.42297 4.45498C8.23855 4.32199 8.10065 4.13433 8.0288 3.9186L7.73467 3.03456ZM5.14551 7.09726C5.14551 6.7381 5.28818 6.39364 5.54213 6.13967C5.79609 5.8857 6.14053 5.74303 6.49967 5.74303C6.85882 5.74303 7.20326 5.8857 7.45722 6.13967C7.71117 6.39364 7.85384 6.7381 7.85384 7.09726C7.85384 7.45643 7.71117 7.80088 7.45722 8.05485C7.20326 8.30882 6.85882 8.45149 6.49967 8.45149C6.14053 8.45149 5.79609 8.30882 5.54213 8.05485C5.28818 7.80088 5.14551 7.45643 5.14551 7.09726ZM6.49967 4.65964C5.85321 4.65964 5.23322 4.91646 4.7761 5.3736C4.31898 5.83075 4.06217 6.45076 4.06217 7.09726C4.06217 7.74376 4.31898 8.36378 4.7761 8.82092C5.23322 9.27806 5.85321 9.53488 6.49967 9.53488C7.14614 9.53488 7.76613 9.27806 8.22325 8.82092C8.68037 8.36378 8.93717 7.74376 8.93717 7.09726C8.93717 6.45076 8.68037 5.83075 8.22325 5.3736C7.76613 4.91646 7.14614 4.65964 6.49967 4.65964Z" fill="#0065FF"/>
                                                        </svg>
                                                    </span>
                                                <span>ক্যামেরার সাহায্যে ছবি তলুন</span>
                                            </button> -->
                                        </div>

                                        <div class="nid-verify-photo"  :style="nidPhotoStyle"></div>
<!--                                        <div class="nid-verify-photo" style="background-image: url(/assets/custom/images/2024/06/nid-photo-front.png);"></div>-->
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

                            <button id="submit-btn" class="btn btn-blue" @click="onSubmit()">
                                <span>Next</span>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
                                        <path d="M1.10449 1.20866L4.89616 5.00033L1.10449 8.79199" stroke="white" stroke-width="0.8125" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
