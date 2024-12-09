<script setup>
import {inject, onMounted, ref, watch} from 'vue';
import axios from "axios";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const indexData = inject('indexData');
const pilgrimData = inject('pilgrimData');
const allerros = inject('allerros');
const currentPoliceStationList = inject('currentPSArr');
const permanentPoliceStationList = inject('permanentPSArr');

</script>
<template>
    <div>
        <div class="row form-group">
            <h6 class="control-label col-md-8"><b> Passport Information </b></h6> <br>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Passport Type</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_type ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.pass_type" placeholder="pass_type" class="form-control" readonly/>
                <span v-if="allerros.pass_type" class="text-danger">{{ allerros.pass_type }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Passport No</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.passport_no ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.passport_no" placeholder="passport_no" class="form-control" readonly/>
                <span v-if="allerros.passport_no" class="text-danger">{{ allerros.passport_no }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Date of Issue</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_issue_date ? 'has-error' : '']">
                <Datepicker
                    v-model="pilgrimData.pass_issue_date"
                    :enableTimePicker="false"
                    type="date"
                    :maxDate="new Date()"
                    format="dd-MMM-yyyy"
                    placeholder="dd-mm-yyyy"
                    :text-input="true"
                    autoApply
                    readonly
                />
                <span v-if="allerros.pass_issue_date" class="text-danger">{{ allerros.pass_issue_date }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Date of Expiry</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_exp_date ? 'has-error' : '']">
                <Datepicker
                    v-model="pilgrimData.pass_exp_date"
                    :enableTimePicker="false"
                    type="date"
                    :maxDate="new Date()"
                    format="dd-MMM-yyyy"
                    placeholder="dd-mm-yyyy"
                    :text-input="true"
                    autoApply
                    readonly
                />
                <span v-if="allerros.pass_exp_date" class="text-danger">{{ allerros.pass_exp_date }}</span>
            </div>
        </div>

        <div class="row form-group">
            <h6 class="control-label col-md-8"><b> Passport Permanent Address (In Bangladesh)</b></h6> <br>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Post Code</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_per_post_code ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.pass_per_post_code" placeholder="Post Code" class="form-control" />
                <span v-if="allerros.pass_per_post_code" class="text-danger">{{ allerros.pass_per_post_code }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">District</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_per_district ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.pass_per_district"
                         :options="indexData.districtListArr"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pass_per_district" class="text-danger">{{ allerros.pass_per_district[0] }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Police Station</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_per_thana ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.pass_per_thana"
                         :options="permanentPoliceStationList"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pass_per_thana" class="text-danger">{{ allerros.pass_per_thana }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Address</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_per_village ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.pass_per_village" placeholder="Address" class="form-control" />
                <span v-if="allerros.pass_per_village" class="text-danger">{{ allerros.pass_per_village }}</span>
            </div>
        </div>

        <div class="row form-group">
            <h6 class="control-label col-md-8"> <b>Passport Present Address (In Bangladesh)</b></h6> <br>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Post Code</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_post_code ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.pass_post_code" placeholder="Post Code" class="form-control" />
                <span v-if="allerros.pass_post_code" class="text-danger">{{ allerros.pass_post_code }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">District</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_district ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.pass_district"
                         :options="indexData.districtListArr"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pass_district" class="text-danger">{{ allerros.pass_district }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Police Station</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_thana ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.pass_thana"
                         :options="currentPoliceStationList"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pass_thana" class="text-danger">{{ allerros.pass_thana[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Address</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_village ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.pass_village" placeholder="Address" class="form-control" />
                <span v-if="allerros.pass_village" class="text-danger">{{ allerros.pass_village[0] }}</span>
            </div>
        </div>
    </div>
</template>
