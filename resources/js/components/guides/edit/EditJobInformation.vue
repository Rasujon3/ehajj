<script setup>
import {computed, onMounted, ref, toRefs, watch} from 'vue';

const props = defineProps({
    guideData: Object,
    allerros: Array,
    isJobHolder: String
});

const { guideData, allerros, isJobHolder } = toRefs(props);
onMounted(async () => {
    await getOccupation();
})

let occupations = ref([]);
const emit = defineEmits(['update:isJobHolder']);

const localIsJobHolder = ref(isJobHolder.value);
watch(isJobHolder, (newVal) => {
    localIsJobHolder.value = newVal;
});

watch(() => localIsJobHolder.value, (newVal) => {
    emit('update:isJobHolder', newVal);
});
const getOccupation = async () => {
    let response = await axios.get(`/guides/get-occupation-list`);
    const fetchedOccupations = [...response.data?.data]?.map(occupation => ({
        id: occupation.id,
        text: occupation.name
    }));
    occupations.value = [{ id: '0', text: 'Select One' }, ...fetchedOccupations];
};
</script>

<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="border-card-block">
                <div class="bd-card-head">
                    <div class="bd-card-title">
                        <h3>চাকরিজীবী প্রার্থীদের ক্ষেত্রে প্রাতিষ্ঠানিক ঠিকানা</h3>
                    </div>
                </div>

                <div class="bd-card-content">
                    <div class="card-form-block">
                        <div class="form-flex-inline">
                            <p>আপনি কি চাকরিজীবী ?</p>
                            <div class="from-redio-group">
                                <div class="bs_radio" :class="{ 'radio_checked': localIsJobHolder === 'Yes' }">
                                    <div class="bs_radio_border">
                                        <input type="radio" name="hajj_guide_job_holder" id="hajj_guide_job_holder_yes"
                                               value="Yes" v-model="localIsJobHolder">
                                        <label for="hajj_guide_job_holder_yes">হ্যাঁ</label>
                                    </div>
                                </div>
                                <div class="bs_radio" :class="{ 'radio_checked': localIsJobHolder === 'No' }">
                                    <div class="bs_radio_border">
                                        <input type="radio" name="hajj_guide_job_holder" id="hajj_guide_job_holder_no"
                                               value="No" v-model="localIsJobHolder">
                                        <label for="hajj_guide_job_holder_no">না</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="isJobHolder === 'Yes'">
                        <div class="col-lg-6">
                            <div class="input-block-item form-select2-item">
                                <label class="input-label required-star">পেশা</label>
                                <div id="occupation" class="w-100">
                                    <Select2
                                        v-model="guideData.occupation"
                                        :options="occupations"
                                        :class="[ allerros.occupation ? 'has-error' : '']"
                                    />
                                </div>
                                <span v-if="allerros.occupation" class="text-danger">{{ allerros.occupation[0] }}</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-block-item">
                                <label class="input-label required-star">প্রার্থীর পদবী</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="প্রার্থীর পদবী"
                                    v-model="guideData.designation"
                                    :class="[ allerros.designation ? 'has-error' : '']"
                                    id="designation"
                                />
                            </div>
                            <span v-if="allerros.designation" class="text-danger">{{ allerros.designation[0] }}</span>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-block-item">
                                <label class="input-label required-star">অফিসের নাম</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="অফিসের নাম"
                                    v-model="guideData.office_name"
                                    :class="[allerros.office_name ? 'has-error' : '']"
                                    id="office_name"
                                />
                            </div>
                            <span v-if="allerros.office_name" class="text-danger">{{ allerros.office_name[0] }}</span>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-block-item">
                                <label class="input-label required-star">অফিসের ঠিকানা</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="অফিসের ঠিকানা"
                                    v-model="guideData.office_address"
                                    :class="[allerros.office_address ? 'has-error' : '']"
                                    id="office_address"
                                />
                            </div>
                            <span v-if="allerros.office_address" class="text-danger">{{ allerros.office_address[0] }}</span>
                        </div>
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
