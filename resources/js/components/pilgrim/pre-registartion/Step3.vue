<script setup>
import {inject, onMounted, ref, watch} from 'vue';
import axios from "axios";

const indexData = inject('indexData');
const pilgrimData = inject('pilgrimData');
const allerros = inject('allerros');
const currentPoliceStationList = inject('currentPSArr');
const permanentPoliceStationList = inject('permanentPSArr');

</script>
<template>
    <div>
        <div class="row form-group">
            <h6 class="control-label col-md-8"><b> Permanent Address (In Bangladesh)</b></h6> <br>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Post Code</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-6', allerros.permanent_post_code ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.permanent_post_code" placeholder="Post Code" class="form-control" />
                <span v-if="allerros.permanent_post_code" class="text-danger">{{ allerros.permanent_post_code }}</span>
            </div>
            <div class="col-md-1"> <button type="button" @click="permanemtDistrictPoliceByPostCode" class="btn btn-primary">Search</button></div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">District</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.permanent_district_id ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.permanent_district_id"
                         :options="indexData.districtListArr"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.permanent_district_id[0] }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Police Station</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.permanent_police_station_id ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.permanent_police_station_id"
                         :options="permanentPoliceStationList"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.permanent_police_station_id }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Address</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.permanent_address ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.permanent_address" placeholder="Address" class="form-control" />
                <span v-if="allerros.permanent_address" class="text-danger">{{ allerros.permanent_address }}</span>
            </div>
        </div>

        <div class="row form-group">
            <h6 class="control-label col-md-8"> <b>Current Address (In Bangladesh)</b></h6> <br>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Post Code</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-6', allerros.present_post_code ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.present_post_code" placeholder="Post Code" class="form-control" />
                <span v-if="allerros.present_post_code" class="text-danger">{{ allerros.present_post_code }}</span>
            </div>
            <div class="col-md-1"> <button type="button" class="btn btn-primary">Search</button></div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">District</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.present_district_id ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.present_district_id"
                         :options="indexData.districtListArr"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.present_district_id }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Police Station</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.present_police_station_id ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.present_police_station_id"
                         :options="currentPoliceStationList"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.present_police_station_id[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Address</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.present_address ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.present_address" placeholder="Address" class="form-control" />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.present_address[0] }}</span>
            </div>
        </div>
    </div>
</template>
