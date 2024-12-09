<script setup>
import {ref, toRefs, watch} from 'vue';

const props = defineProps({
    guideData: Object,
    allerros: Array,
    permanentDivisions: Array,
    permanentDistricts: Array,
    permanentPoliceStation: Array
});

const { guideData, allerros, permanentDivisions, permanentDistricts, permanentPoliceStation } = toRefs(props);

const emit = defineEmits(['getPermanentPoliceStationList']);

// ------------ Permanent address value -------------------- //
watch(() => guideData.value.permanent_division_id, (newPermanentDivisionId) => {
    const selectedPermanentDivision = permanentDivisions.value.find(division => division.area_id === newPermanentDivisionId);
    guideData.value.permanent_division_name = selectedPermanentDivision ? selectedPermanentDivision.area_nm_ban : '';
});
watch(() => guideData.value.per_district_id, (newPermanentDistrictId) => {
    emit('getPermanentPoliceStationList', newPermanentDistrictId);
    const selectedPermanentDistrict = permanentDistricts.value.find(district => district.id == newPermanentDistrictId);
    guideData.value.per_district = selectedPermanentDistrict ? selectedPermanentDistrict.text : '';
    guideData.value.per_thana_id = "0";
    guideData.value.per_police_station = "";
});
watch(() => guideData.value.per_thana_id, (newPermanentThanaId) => {
    const selectedPermanentThana = permanentPoliceStation.value.find(pps => pps.id == newPermanentThanaId);
    guideData.value.per_police_station = selectedPermanentThana ? selectedPermanentThana.text : '';
});

</script>

<template>
    <div class="col-lg-6">
        <div class="border-card-block mt-4">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <h3>স্থায়ী ঠিকানা</h3>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="user-guide-address">
                    <div class="input-block-item">
                        <label class="input-label required-star">পোষ্ট কোড</label>
                        <input type="text" class="form-control" :class="[{ required: true }, allerros.per_post_code ? 'has-error' : '']" placeholder="পোষ্ট কোড" v-model="guideData.per_post_code" required>
                    </div>
                    <span v-if="allerros.per_post_code" class="text-danger">{{ allerros.per_post_code[0] }}</span>

                    <div class="input-block-item form-select2-item">
                        <label class="input-label required-star">বিভাগ</label>
                        <select class="form-control hajj-select2" name="" id="" v-model="guideData.permanent_division_id" :class="[{ required: true }, allerros.permanent_division_id ? 'has-error' : '']" required>
                            <option value="" disabled>Select One</option>
                            <option v-for="division in permanentDivisions" :key="division.area_id" :value="division.area_id">
                                {{ division.area_nm_ban }}
                            </option>
                        </select>
                        <input hidden="hidden" v-model="guideData.permanent_division_name">
                    </div>
                    <span v-if="allerros.permanent_division_id" class="text-danger">{{ allerros.permanent_division_id[0] }}</span>

                    <div class="input-block-item form-select2-item">
                        <label class="input-label required-star">জেলা</label>
<!--                        <select class="form-control hajj-select2" name="" id="" v-model="guideData.per_district_id" :class="[{ required: true }, allerros.per_district_id ? 'has-error' : '']" required>-->
<!--                            <option value="" disabled>Select One</option>-->
<!--                            <option v-for="district in permanentDistricts" :key="district.area_id" :value="district.area_id">-->
<!--                                {{ district.area_nm_ban }}-->
<!--                            </option>-->
<!--                        </select>-->
                        <div id="permanentDistrict" class="w-100">
                            <Select2
                                v-model="guideData.per_district_id"
                                :options="permanentDistricts"
                                :class="[{ required: true }, allerros.per_district_id ? 'has-error' : '']"
                                required
                            />
                        </div>
                        <input hidden="hidden" v-model="guideData.per_district">
                    </div>
                    <span v-if="allerros.per_district_id" class="text-danger">{{ allerros.per_district_id[0] }}</span>

                    <div class="input-block-item form-select2-item">
                        <label class="input-label required-star">উপজেলা</label>
<!--                        <select class="form-control hajj-select2" name="" id="" v-model="guideData.per_thana_id" :class="[{ required: true }, allerros.per_thana_id ? 'has-error' : '']" required>-->
<!--                            <option value="" disabled>Select One</option>-->
<!--                            <option v-for="policeStation in permanentPoliceStation" :key="policeStation.area_id" :value="policeStation.area_id">-->
<!--                                {{ policeStation.area_nm_ban }}-->
<!--                            </option>-->
<!--                        </select>-->
                        <div id="permanentThana" class="w-100">
                            <Select2
                                v-model="guideData.per_thana_id"
                                :options="permanentPoliceStation"
                                :class="[{ required: true }, allerros.per_thana_id ? 'has-error' : '']"
                                required
                            />
                        </div>
                        <input hidden="hidden" v-model="guideData.per_police_station">
                    </div>
                    <span v-if="allerros.per_thana_id" class="text-danger">{{ allerros.per_thana_id[0] }}</span>

                    <div class="input-block-item">
                        <label class="input-label required-star">ঠিকানা</label>
                        <input type="text" class="form-control" placeholder="ঠিকানা" v-model="guideData.permanent_address" :class="[{ required: true }, allerros.permanent_address ? 'has-error' : '']" required>
                    </div>
                    <span v-if="allerros.permanent_address" class="text-danger">{{ allerros.permanent_address[0] }}</span>

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
