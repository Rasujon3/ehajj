<script setup>
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import { inject, defineEmits } from 'vue';

const indexData = inject('indexData');
const pilgrimData = inject('pilgrimData');
const allerros = inject('allerros');
const isGovtJob = inject('isGovtJob');
</script>

<template>
    <div class="row form-group">
        <label class="control-label col-md-3 required-star">Resident</label>
        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
        <div class="col-md-7">
            <label class="passport-radio">
                <input name="resident" type="radio" :checked="pilgrimData.resident === 'Bangladeshi'" v-model="pilgrimData.resident" value="Bangladeshi" /> Bangladeshi
            </label>
            <label class="passport-radio">
                <input name="resident" type="radio" :checked="pilgrimData.resident === 'NRB'" v-model="pilgrimData.resident" value="NRB" /> NRB
            </label>
        </div>
    </div>
    <template v-if="pilgrimData.resident !== 'Bangladeshi'">
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Nationality</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.nationality2 ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.nationality2"
                         :options="indexData.nationalityArr"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.nationality2[0] }}</span>
            </div>
        </div>
    </template>
    <div class="row form-group">
        <label class="control-label col-md-3 required-star">Gender</label>
        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
        <div class="col-md-7">
            <label class="passport-radio">
                <input name="gender" type="radio" checked="" v-model="pilgrimData.gender" value="male" /> Male
            </label>
            <label class="passport-radio">
                <input name="gender" type="radio" v-model="pilgrimData.gender" value="female" /> Female
            </label>
        </div>
    </div>

    <div class="row form-group">
        <label class="control-label col-md-3 required-star">Birth Date</label>
        <div class="col-md-1" style="font-weight: bold">:</div>
        <div :class="['col-md-7', allerros.birth_date ? 'has-error' : '']">
            <Datepicker
                v-model="pilgrimData.birth_date"
                :enableTimePicker="false"
                type="date"
                :maxDate="new Date()"
                format="dd-MMM-yyyy"
                placeholder="dd-mm-yyyy"
                :text-input="true"
                autoApply
            />
            <span v-if="allerros.birth_date" class="text-danger">{{ allerros.birth_date }}</span>
        </div>
    </div>
    <div class="row form-group">
        <label class="control-label col-md-3 required-star">Identity Type</label>
        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
        <div class="col-md-7">
            <strong>{{ pilgrimData.identity}}</strong>
        </div>
    </div>

    <div v-if="pilgrimData.identity === 'PASSPORT'">
        <div  class="row form-group">
            <label class="control-label col-md-3 required-star">Passport Type</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div class="col-md-7">
                <label class="passport-radio">
                    <input name="pass-type" type="radio" checked v-model="pilgrimData.passport_type" value="E-PASSPORT" /> E-PASSPORT
                </label>
                <label class="passport-radio">
                    <input name="pass-type" type="radio" v-model="pilgrimData.passport_type" value="MRP" /> MRP
                </label>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Passport Number</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.passport_number ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.passport_number" placeholder="Passport number" class="form-control" />
                <span v-if="allerros.passport_number" class="text-danger">{{ allerros.passport_number[0] }}</span>
            </div>
        </div>
    </div>
    <div v-else-if="pilgrimData.identity === 'NID'">
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">NID Number</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.nid_number ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.nid_number" placeholder="NID number" class="form-control" />
                <span v-if="allerros.nid_number" class="text-danger">{{ allerros.nid_number[0] }}</span>
            </div>
        </div>
    </div>

    <div v-else>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Birth Registration Number</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.brn_number ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.brn" placeholder="Birth registration number" class="form-control" />
                <span v-if="allerros.brn_number" class="text-danger">{{ allerros.brn_number[0] }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.passport-radio {
    margin-right: 20px;
}
</style>
