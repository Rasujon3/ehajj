<script setup>
import {ref, toRefs, watch} from 'vue';

const props = defineProps({
    guideData: Object,
    allerros: Array,
    presentDivisions: Array,
    presentDistricts: Array,
    presentPoliceStation: Array,
    isSameAsPermanent: Boolean
});

const { guideData, allerros, presentDivisions, presentDistricts, presentPoliceStation, isSameAsPermanent } = toRefs(props);
let skipWatch = ref(false);
const emit = defineEmits(['getPresentPoliceStationList', 'toggleSameAsPermanent']);

// ------------ Present address value -------------------- //
watch(() => guideData.value.present_division_id, (newPresentDivisionId) => {
    const selectedPresentDivision = presentDivisions.value.find(division => division.area_id == newPresentDivisionId);
    guideData.value.present_division_name = selectedPresentDivision ? selectedPresentDivision.area_nm_ban : '';
});
watch(() => guideData.value.district_id, (newPresentDistrictId) => {
    emit('getPresentPoliceStationList', newPresentDistrictId);
    const selectedPresentDistrict = presentDistricts.value.find(district => district.id == newPresentDistrictId);
    guideData.value.district = selectedPresentDistrict ? selectedPresentDistrict.text : '';
    if (skipWatch.value) {
        skipWatch.value = false; // Reset the flag after skipping
        return;
    }
    guideData.value.thana_id = "0";
    guideData.value.police_station = "";
});
watch(() => guideData.value.thana_id, (newPresentThanaId) => {
    const selectedPermanentThana = presentPoliceStation.value.find(pps => pps.id == newPresentThanaId);
    guideData.value.police_station = selectedPermanentThana ? selectedPermanentThana.text : '';
});
const handleCheckboxChange = () => {
    emit('toggleSameAsPermanent', !isSameAsPermanent.value);
};
// Store previous present address data
const previousPresentAddress = ref({
    post_code: guideData.value.post_code,
    division_id: guideData.value.present_division_id,
    division_name: guideData.value.present_division_name,
    district: guideData.value.district,
    district_id: guideData.value.district_id,
    thana_id: guideData.value.thana_id,
    police_station: guideData.value.police_station,
    address: guideData.value.present_address,
});

watch(isSameAsPermanent, (newValue) => {
    if (newValue) {
        // Copy Permanent Address to Present Address
        skipWatch.value = true; // Skip the watch
        guideData.value.post_code = guideData.value.per_post_code;
        guideData.value.present_division_id = guideData.value.permanent_division_id;
        guideData.value.present_division_name = guideData.value.permanent_division_name;
        guideData.value.district_id = guideData.value.per_district_id;
        guideData.value.district = guideData.value.per_district;
        guideData.value.thana_id = guideData.value.per_thana_id;
        guideData.value.police_station = guideData.value.per_police_station;
        guideData.value.present_address = guideData.value.permanent_address;
    } else {
        // Revert to previous Present Address
        skipWatch.value = true; // Skip the watch
        guideData.value.post_code = previousPresentAddress.value.post_code;
        guideData.value.present_division_id = previousPresentAddress.value.division_id;
        guideData.value.present_division_name = previousPresentAddress.value.division_name;
        guideData.value.district_id = previousPresentAddress.value.district_id;
        guideData.value.district = previousPresentAddress.value.district;
        guideData.value.thana_id = previousPresentAddress.value.thana_id;
        guideData.value.police_station = previousPresentAddress.value.police_station;
        guideData.value.present_address = previousPresentAddress.value.address;
    }
});

</script>

<template>
    <div class="col-lg-6">
        <div class="border-card-block mt-4">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <h3>বর্তমান ঠিকানা</h3>
                </div>
                <div class="title-btn-group">
                    <div class="form-group m-0">
                        <input class="checkbox" type="checkbox" id="same_address" :checked="isSameAsPermanent" @change="handleCheckboxChange">
                        <label class="text-sm m-0" for="same_address">স্থায়ী ঠিকানা অনুযায়ী একই</label>
                    </div>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="user-guide-address">
                    <div class="input-block-item">
                        <label class="input-label required-star">পোষ্ট কোড</label>
                        <input type="text" class="form-control" placeholder="পোষ্ট কোড" v-model="guideData.post_code" :class="[{ required: true }, allerros.post_code ? 'has-error' : '']" required>
                    </div>
                    <span v-if="allerros.post_code" class="text-danger">{{ allerros.post_code[0] }}</span>

                    <div class="input-block-item form-select2-item">
                        <label class="input-label required-star">বিভাগ</label>
                        <select class="form-control hajj-select2" name="" id="" v-model="guideData.present_division_id" :class="[{ required: true }, allerros.present_division_id ? 'has-error' : '']" required>
                            <option value="" disabled>Select One</option>
                            <option v-for="division in presentDivisions" :key="division.area_id" :value="division.area_id">
                                {{ division.area_nm_ban }}
                            </option>
                        </select>
                        <input hidden="hidden" v-model="guideData.present_division_name">
                    </div>
                    <span v-if="allerros.present_division_id" class="text-danger">{{ allerros.present_division_id[0] }}</span>

                    <div class="input-block-item form-select2-item">
                        <label class="input-label required-star">জেলা</label>
<!--                        <select class="form-control hajj-select2" name="" id="" v-model="guideData.district_id" :class="[{ required: true }, allerros.district_id ? 'has-error' : '']" required>-->
<!--                            <option value="" disabled>Select One</option>-->
<!--                            <option v-for="district in presentDistricts" :key="district.area_id" :value="district.area_id">-->
<!--                                {{ district.area_nm_ban }}-->
<!--                            </option>-->
<!--                        </select>-->
                        <div id="presentDistrict" class="w-100">
                            <Select2
                                v-model="guideData.district_id"
                                :options="presentDistricts"
                                :class="[{ required: true }, allerros.district_id ? 'has-error' : '']"
                                required
                            />
                        </div>
                        <input hidden="hidden" v-model="guideData.district">
                    </div>
                    <span v-if="allerros.district_id" class="text-danger">{{ allerros.district_id[0] }}</span>

                    <div class="input-block-item form-select2-item">
                        <label class="input-label required-star">উপজেলা</label>
<!--                        <select class="form-control hajj-select2" name="" id="" v-model="guideData.thana_id" :class="[{ required: true }, allerros.thana_id ? 'has-error' : '']" required>-->
<!--                            <option value="" disabled>Select One</option>-->
<!--                            <option v-for="policeStation in presentPoliceStation" :key="policeStation.area_id" :value="policeStation.area_id">-->
<!--                                {{ policeStation.area_nm_ban }}-->
<!--                            </option>-->
<!--                        </select>-->
                        <div id="presentThana" class="w-100">
                            <Select2
                                v-model="guideData.thana_id"
                                :options="presentPoliceStation"
                                :class="[{ required: true }, allerros.thana_id ? 'has-error' : '']"
                                required
                            />
                        </div>
                        <input hidden="hidden" v-model="guideData.police_station">
                    </div>
                    <span v-if="allerros.thana_id" class="text-danger">{{ allerros.thana_id[0] }}</span>

                    <div class="input-block-item">
                        <label class="input-label required-star">ঠিকানা</label>
                        <input type="text" class="form-control" placeholder="ঠিকানা" v-model="guideData.present_address" :class="[{ required: true }, allerros.present_address ? 'has-error' : '']" required>
                    </div>
                    <span v-if="allerros.present_address" class="text-danger">{{ allerros.present_address[0] }}</span>

                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.has-error {
    border: 1px solid red;
}
.has-tr-error {
    border: 2px solid red;
}
</style>
