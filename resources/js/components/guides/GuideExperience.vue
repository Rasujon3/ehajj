<script setup>
import { toRefs } from 'vue';

const props = defineProps({
    guideData: Object,
    allerros: Array
});

const { guideData, allerros } = toRefs(props);
</script>

<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="border-card-block">
                <div class="bd-card-head">
                    <div class="bd-card-title">
                        <h3>অভিজ্ঞতার বিবরণ</h3>
                    </div>
                </div>

                <div class="bd-card-content">
                    <div class="form-group mb-0">
                        <textarea
                            class="form-control"
                            name=""
                            id=""
                            cols="30"
                            rows="4"
                            placeholder="অভিজ্ঞতার বিবরণ"
                            v-model="guideData.experience"
                            :class="[allerros.experience ? 'has-error' : '']"
                        >
                        </textarea>
                    </div>
                </div>
                <span v-if="allerros.experience" class="text-danger">{{ allerros.experience[0] }}</span>
            </div>
        </div>
    </div><!-- /row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="border-card-block">
                <div class="bd-card-head">
                    <div class="bd-card-title">
                        <h3>অতিরিক্ত যোগ্যতা (যদি কিছু থাকে)</h3>
                    </div>
                </div>

                <div class="bd-card-content">
                    <div class="form-group mb-0">
                        <textarea
                            class="form-control"
                            name=""
                            id=""
                            cols="30"
                            rows="4"
                            placeholder="অতিরিক্ত যোগ্যতা (যদি কিছু থাকে)"
                            v-model="guideData.additional_experience"
                            :class="[allerros.additional_experience ? 'has-error' : '']">
                        </textarea>
                    </div>
                </div>
                <span v-if="allerros.additional_experience" class="text-danger">{{ allerros.additional_experience[0] }}</span>
            </div>
        </div>
    </div><!-- /row -->
</template>

<style scoped>

</style>
