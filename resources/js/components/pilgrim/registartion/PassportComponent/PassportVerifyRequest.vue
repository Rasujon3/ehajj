<script setup>
import {ref, reactive, onMounted, computed} from 'vue';
import axios from "axios";
import { useRouter, useRoute  } from 'vue-router';
import { useStore } from 'vuex';
import { useToast } from 'vue-toast-notification';

const router = useRouter();
const route = useRoute();
const store = useStore();
const toast = useToast();

const errors = reactive({});
const passportImg = ref(null);
const pilgrimTrackingNo = ref('');
const pilgrimId = ref('');
const passportType = ref('');
const passportNo = ref('');
const passportDob = ref('');
const enPassportData = ref('');
const imagePreview = ref('');
const processRequestFlg = ref(false);
const loadingNext = ref(false);
const processRequest = ref( 1); // 1 = View submit btn , 2 = loading submit btn,

const showToast = (message, type = 'error', duration = 3000) => {
    toast.open({
        message,
        type,
        position: 'top-right',
        duration
    });
};
const uploadImage = async () => {
    try {
        if (!passportImg?.value) {
            passportImg.value = null;
            showToast('আপনার পাসপোর্ট এর ছবি টি দিয়ে রিকুয়েস্ট করুন।', 'error', 5000);
            return false;
        }
        if (!pilgrimId?.value && !pilgrimTrackingNo?.value) {
            passportImg.value = null;
            showToast(' পিলগ্রিম এর ট্র্যাকিং নাম্বার এবং আইডি পাওয়া যায়নি ', 'error');
            return false;

        } else {
           const form = new FormData();
           form.append('pilgrim_id', pilgrimId.value);
           form.append('tracking_no', pilgrimTrackingNo.value);
           form.append('image_file', passportImg.value);

           loadingNext.value = true;
           const response = await axios.post('/registration/image/store', form, {
               headers: {
                   'Content-Type': 'multipart/form-data'
               }
           });

           loadingNext.value = false;

           if(response.data.responseCode === 1) {
               processRequest.value = 1;
               processRequestFlg.value = true;
               showToast('আপনার পাসপোর্ট এর কপি টি সংরক্ষণ করা হয়েছে এখন আপনি সাবমিট বাটন এ ক্লিক করে আপনার রিকুয়েস্ট টি পাঠান।', 'success', 3000);

           } else {
               passportImg.value = null;
               processRequest.value = 1;
               processRequestFlg.value = false;
               showToast(response.data.message, 'error', 3000);
           }
       }

    } catch (error) {
        passportImg.value = null;
        showToast(error.response.data.errors, 'error', 3000);
    }
};
const onPassportImgChange = async (event) => {
    const file = event.target.files[0];
    processRequestFlg.value = false;
    if (file && file.type.startsWith('image/')) {
        passportImg.value = file;
        await createImagePreview(file,'passportScanCopy');
        await uploadImage();
    } else {
        passportImg.value = null;
        processRequestFlg.value = false;
    }
};

const createImagePreview = async (file, flag) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (e) => {
        if(flag === 'passportScanCopy') {
            imagePreview.value = e.target.result;
        }
    };
};
const submit = async () => {
    try {

        if (!passportImg.value) {
            showToast('আপনার পাসপোর্ট এর স্ক্যান কপিটি দিয়ে রিকুয়েস্ট করুন।', 'error', 5000);
            return false;
        }
        if (processRequestFlg.value === false) {
            showToast('আপনার পাসপোর্ট এর স্ক্যান কপিটি আপলোড হচ্ছে, অনুগ্রহ করে কিছুক্ষণ অপেক্ষা করুন।', 'error', 5000);
            return false;
        }
        if (!pilgrimId?.value || !pilgrimTrackingNo?.value || !passportDob?.value || !passportType?.value) {
            showToast(' পিলগ্রিম এর সঠিক তথ্য পাওয়া যায়নি ', 'error');
            return false;
        }
        const form = new FormData();
        form.append('pilgrimId', pilgrimId.value);
        form.append('pilgrimTrackingNo', pilgrimTrackingNo.value);
        form.append('passportDob', passportDob.value);
        form.append('passportNo', passportNo.value);
        form.append('passportType', passportType.value);
        form.append('image_file', passportImg.value);
        processRequest.value = 2;
        const response = await axios.post('/registration/passport-verify-request', form, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

       await handleResponse(response.data);

    } catch (error) {
        processRequest.value = 1;
        passportImg.value = null;
        processRequestFlg.value = false;
        handleError(error);
    }
}

// Handle successful and error responses
const handleResponse = async (data) => {
    if (data.responseCode === 1 && data.status === true) {
        showToast(data.message, 'success');
    } else {
        showToast(data.message, 'error');
    }
   await router.push({ path: '/reg-pilgrims-list' });
}

// Error handling function
const handleError = (error) => {
    if (error.response && error.response.data && error.response.data.errors) {
        allerros.value = error.response.data.errors;
    } else {
        toast.open({
            message: 'An unexpected error occurred.',
            type: 'error',
            position: 'top-right',
            duration: 3000
        });
    }
}

onMounted(()  => {
    pilgrimTrackingNo.value = route.query.pilgrimTrackingNo;
    pilgrimId.value = route.query.pilgrimId;
    passportType.value = route.query.passportType;
    passportNo.value = route.query.passportNo;
    passportDob.value = route.query.dob;
});
</script>

<template>
    <div class="card card-magenta border border-magenta">
        <div class="card-header">
            <h5 class="card-title pt-2 pb-2"> পাসপোর্ট ভেরিফিকেশন</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="btn-group btn-breadcrumb">
                                        <span class="btn btn-primary custom-font">Submission of Application with PASSPORT</span>
                                        <span class="btn btn-warning custom-font"> Registration Payment amount deposit to designated bank</span>
                                        <span class="btn btn-default custom-font">Confirmation of registration by SMS</span>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12"
                                             style="padding-left: 0;padding-top: 10px;">
                                            <div class="form-group col-md-12 ">
                                                <span class="text-danger"><b>পাসপোর্ট নম্বর সাবধানতার সাথে নির্ভুলভাবে এন্ট্রি করতে হবে। এন্ট্রিকৃত পাসপোর্ট নম্বরটি পাসপোর্ট অধিদপ্তরের ডাটাবেজের সাথে যাচাই করা হবে। ভুল তথ্য প্রদানের জন্য সৃষ্ট জটিলতার দায়িত্ব ডাটা এন্ট্রিকারিকেই বহন করতে হবে।</b></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div v-if="passportType === 'E-PASSPORT'">
                                            <ul style="list-style-type: none; font-size: 13px; color:darkred">
                                                <li><b>ই-পাসপোর্ট সম্পর্কিত প্রয়োজনীয় নির্দেশনা!!</b></li>
                                                <li>&nbsp;</li>
                                                <li class="text-justify">
                                                    ১. ই-পাসপোর্ট সংক্রান্ত সকল তথ্য ই-পাসপোর্টের ফটোকপি থেকে OCR এর মাধ্যমে কনভার্ট
                                                    করা হয়েছে।
                                                </li>

                                                <li class="text-justify">
                                                    ২. ই-পাসপোর্টের ফটোকপি থেকে যে সকল তথ্য পাওয়া গেছে তা ভালোভাবে ই-পাসপোর্টের সাথে
                                                    মিলিয়ে নিবেন।
                                                </li>
                                                <li class="text-justify">
                                                    ৩. যদি তথ্যের কোন অসঙ্গতি থাকে তাহলে "Modify" বাটনে ক্লিক করে পরিবর্তন করে নিতে
                                                    হবে।
                                                </li>
                                                <li class="text-justify">
                                                    ৪. পাসপোর্টের ফটোকপি থেকে যে সকল তথ্য পাওয়া যায় নাই তা এন্ট্রি করতে হবে, যেমন
                                                    ইসু তারিখ, পিতার নাম, মাতার নাম ও (স্বামী বা স্ত্রী নাম যদি থাকে)।

                                                </li>
                                                <li class="text-justify">
                                                    ৫. ছবি যদি না দেখা যায় তাহলে তা আপলোড করে দিতে হবে।
                                                </li>
                                                <li class="text-justify">
                                                    ৬. ই-পাসপোর্ট ভেরিফিকেশন প্রসেস সংক্রান্ত যেকোন প্রশ্নে, হজ তথ্য সেবাকেন্দ্রে
                                                    (ফোন নম্বর: ১৬১৩৬, +৮৮০৯৬০২৬৬৬৭০৭) যোগাযোগ করুন।
                                                </li>
                                            </ul>
                                        </div>
                                        <div v-else>
                                            <ul style="list-style-type: none; font-size: 13px;">
                                                <li class="text-justify">
                                                    ১. প্রাক-নিবন্ধিত ব্যক্তিদের নিবন্ধনের জন্য পাসপোর্টের তথ্য প্রদান বাধ্যতামূলক।
                                                    পাসপোর্ট অধিদপ্তরের সঙ্গে আন্তঃ সংযোগ স্থাপিত হওয়ায় এবছর পাসপোর্টের তথ্য
                                                    পাসপোর্ট
                                                    অধিদপ্তরের ডাটাবেজ হতে গ্রহণ করা হবে। পাসপোর্ট অধিদপ্তরের নির্দেশনা অনুযায়ী দিনে
                                                    ২০০
                                                    টি এবং রাতে ১০,০০০ পর্যন্ত পাসপোর্টের তথ্য গ্রহণের সুযোগ থাকবে। এমতাবস্থায়, হজ
                                                    এজেন্সিরা NID তথ্য যাচাইয়ের ন্যায় নিবন্ধনযোগ্য হজযাত্রীদের পাসপোর্ট নম্বর তথ্য
                                                    সাবমিট করতে পারবেন। পাসপোর্ট অধিদপ্তর হতে পাসপোর্টের তথ্য প্রাপ্তি স্বাপেক্ষে
                                                    নিবন্ধনের পরবর্তী কাজ সম্পাদন করতে পারবেন। পাসপোর্ট অধিদপ্তর হতে পাসপোর্টের তথ্য
                                                    হালনাগাদ হয়েছে কিনা, তা নিবন্ধন সিস্টেমের "পাসপোর্ট স্ট্যাটাস" রিপোর্টে দেখা
                                                    যাবে।
                                                </li>
                                                <li>&nbsp;</li>
                                                <li class="text-justify">
                                                    ২. পাসপোর্ট ভেরিফিকেশন প্রসেস সংক্রান্ত যেকোন প্রশ্নে, হজ তথ্য সেবাকেন্দ্রে (ফোন
                                                    নম্বর: ১৬১৩৬, +৮৮০৯৬০২৬৬৬৭০৭) যোগাযোগ করুন।
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="col-md-12">
                                            <label class="control-label"><b>পাসপোর্ট এর ছবি:</b></label>
                                            <div>
                                                <input type="file" class="form-control" @change="onPassportImgChange" />
                                                <span class="help-block text-green">[File Format: *.jpg/ .jpeg/.png | Max size 1 MB]</span>
                                            </div>
                                        </div>
                                        <div class="col-md-10 flex" v-if="imagePreview">
                                            <div>
                                                <img :src="imagePreview" alt="" style="width: 50%;border-radius: 5px; margin-bottom: 10px">
                                            </div>
                                        </div>
                                        <button v-if="processRequest === 1" @click="submit" type="button" style="background-color:#00684D !important; color:white !important;" class="btn float-right">
                                            সাবমিট
                                        </button>
                                        <button v-if="processRequest === 2" type="button" style="background-color:#00684D !important; color:white !important;"  class="btn float-right">
                                            সাবমিট হচ্ছে অপেক্ষা করুন...
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
