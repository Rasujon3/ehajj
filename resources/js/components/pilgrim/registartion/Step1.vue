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
        <label class="control-label col-md-3 required-star">Passport Type</label>
        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
        <div class="col-md-7">
            <label class="passport-radio">
                <input name="pass-type" type="radio" checked v-model="pilgrimData.passport_type" value="E-PASSPORT"/> E-PASSPORT
            </label>
            <label class="passport-radio">
                <input name="pass-type" type="radio" v-model="pilgrimData.passport_type" value="MRP"/> MRP
            </label>
        </div>
    </div>

    <div class="row form-group">
        <label class="control-label col-md-3 required-star">Passport Number</label>
        <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
        <div :class="['col-md-7', allerros.passport_number ? 'has-error' : '']">
            <input type="text" v-model="pilgrimData.passport_number" placeholder="Passport Number" class="form-control" />
            <span v-if="allerros.passport_number" class="text-danger">{{ allerros.passport_number[0] }}</span>
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
</template>

<style scoped>
    .passport-radio {
        margin-right: 20px;
    }
</style>
