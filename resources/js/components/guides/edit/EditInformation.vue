<script setup>
import {computed, toRefs, watch} from 'vue';
import moment from 'moment';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { format, parse, isValid } from 'date-fns';

const props = defineProps({
    guideData: Object,
    birthPlace: Array,
    allerros: Array
});

const { guideData, birthPlace, allerros } = toRefs(props);
const formattedBirthDate = computed({
    get() {
        const date = new Date(guideData.value.birth_date);
        if (isNaN(date.getTime())) {
            return '';
        }
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    },
    set(newValue) {
        guideData.value.birth_date = format(newValue, 'dd MMM yyyy');
    },
});
watch(() => guideData.value.birth_place_id, (newBirthPlaceId) => {
    if(!birthPlace || birthPlace.value.length === 0){
        //console.log("birthPlace is empty or not loaded yet.");
        return;
    }
    const selectedBirthPlace = birthPlace.value.find(district => district.id == newBirthPlaceId);
    guideData.value.birth_place = selectedBirthPlace ? selectedBirthPlace.text : '';
});
</script>

<template>
    <div class="row pt-2" id="trackingNoDiv">
        <div class="col-lg-6">
            <div class="input-block-item">
                <label class="input-label required-star">সর্বশেষ ট্রাকিং নম্বর লিখুন</label>
                <!--                                        <input type="text" class="form-control" v-model="guideInfo.tracking_no" disabled>-->
                <input disabled type="text" class="form-control" id="trackingNoField" :class="[allerros.tracking_no ? 'has-error' : '']" v-model="guideData.tracking_no">
            </div>
            <span v-if="allerros.tracking_no" class="text-danger">{{ allerros.tracking_no[0] }}</span>
        </div>
    </div><!-- /row -->

    <div class="row">
        <div class="col-lg-6">
            <div class="input-block-item">
                <label class="input-label required-star">প্রার্থীর নাম (বাংলায়)</label>
                <input type="text" class="form-control" :class="[{ required: true }, allerros.full_name_bangla ? 'has-error' : '']" :placeholder="guideData.full_name_bangla" v-model="guideData.full_name_bangla" required>
            </div>
            <span v-if="allerros.full_name_bangla" class="text-danger">{{ allerros.full_name_bangla[0] }}</span>
        </div>
        <div class="col-lg-6">
            <div class="input-block-item">
                <label class="input-label required-star">প্রার্থীর নাম (ইংরেজীতে)</label>
                <input type="text" class="form-control required" :class="[{ required: true }, allerros.full_name_english ? 'has-error' : '']" :placeholder="guideData.full_name_english" v-model="guideData.full_name_english" required>
            </div>
            <span v-if="allerros.full_name_english" class="text-danger">{{ allerros.full_name_english[0] }}</span>
        </div>
        <div class="col-lg-6">
            <div class="input-block-item">
                <label class="input-label required-star">জন্ম তারিখ:</label>
<!--                <input type="text" class="form-control" :class="[{ required: true }, allerros.birth_date ? 'has-error' : '']" :placeholder="guideData.birth_date" v-model="guideData.birth_date" required>-->
                <Datepicker
                    class="w-100"
                    :class="[{ required: true }, allerros.birth_date ? 'has-error' : '']"
                    v-model="formattedBirthDate"
                    :enableTimePicker="false"
                    type="date"
                    :maxDate="new Date()"
                    placeholder="Select Date"
                    format="dd-MMM-yyyy"
                    autoApply
                    required
                />
            </div>
            <span v-if="allerros.birth_date" class="text-danger">{{ allerros.birth_date[0] }}</span>
        </div>
        <div class="col-lg-6">
            <div class="input-block-item">
                <label class="input-label required-star">জাতীয় পরিচয় নম্বর</label>
                <input
                    type="number"
                    class="form-control"
                    :class="[{ required: true }, allerros.national_id ? 'has-error' : '']"
                    placeholder="জাতীয় পরিচয় নম্বর"
                    v-model="guideData.national_id"
                    required
                >
            </div>
            <span v-if="allerros.national_id" class="text-danger">{{ allerros.national_id[0] }}</span>
        </div>
        <div class="col-lg-6">
            <div class="input-block-item">
                <label class="input-label required-star">মাতার নাম</label>
                <input type="text" class="form-control" :class="[{ required: true }, allerros.mother_name ? 'has-error' : '']" :placeholder="guideData.mother_name" v-model="guideData.mother_name" required>
            </div>
            <span v-if="allerros.mother_name" class="text-danger">{{ allerros.mother_name[0] }}</span>
        </div>
<!--        <div class="col-lg-6">-->
<!--            <div class="input-block-item">-->
<!--                <label class="input-label required-star">পিতা/স্বামীর নাম</label>-->
<!--                <input type="text" class="form-control" :class="[{ required: true }, allerros.spouse_name ? 'has-error' : '']" :placeholder="guideData.spouse_name" v-model="guideData.spouse_name" required>-->
<!--            </div>-->
<!--            <span v-if="allerros.spouse_name" class="text-danger">{{ allerros.spouse_name[0] }}</span>-->
<!--        </div>-->
        <div class="col-lg-6">
            <div class="input-block-item">
                <label class="input-label required-star">পিতার নাম</label>
                <input type="text" class="form-control"
                       :class="[{ required: true }, allerros.father_name ? 'has-error' : '']"
                       placeholder="পিতার নাম"
                       v-model="guideData.father_name" required>
            </div>
            <span v-if="allerros.father_name" class="text-danger">{{ allerros.father_name[0] }}</span>
        </div>
        <div class="col-lg-6">
            <div class="input-block-item form-select2-item">
                <label class="input-label required-star">জন্মস্থান জেলা</label>
                <div id="birthPlace" class="w-100">
                    <Select2
                        v-model="guideData.birth_place_id"
                        :options="birthPlace"
                        :class="[{ required: true }, allerros.birth_place_id ? 'has-error' : '']"
                        required
                    />
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="input-block-item">
                <label class="input-label required-star">মোবাইল নম্বর (Whatsapp)</label>
                <input type="text" class="form-control" :class="[{ required: true }, allerros.mobile ? 'has-error' : '']" placeholder="মোবাইল নম্বর (Whatsapp)" v-model="guideData.mobile" required>
            </div>
            <span v-if="allerros.mobile" class="text-danger">{{ allerros.mobile[0] }}</span>
        </div>
        <div class="col-lg-6">
            <div class="input-block-item">
                <label class="input-label required-star">জেন্ডার</label>
                <select class="form-control hajj-select2" name="" id=""
                        v-model="guideData.gender"
                        :class="[{ required: true },
                        allerros.gender ? 'has-error' : '']"
                        required
                >
                    <option value="" disabled>Select One</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="others">Others</option>
                </select>
            </div>
            <span v-if="allerros.mobile" class="text-danger">{{ allerros.gender[0] }}</span>
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
