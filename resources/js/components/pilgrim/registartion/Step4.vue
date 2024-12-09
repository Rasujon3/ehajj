<script setup>
import {inject, onMounted, ref, watch} from 'vue';
import axios from "axios";

const indexData = inject('indexData');
const pilgrimData = inject('pilgrimData');
const allerros = inject('allerros');
const currentPoliceStationList = inject('currentPSArr');
const permanentPoliceStationList = inject('permanentPSArr');
const venueList = inject('venueListArr');

</script>
<template>
    <div>
        <div class="row form-group">
            <h6 class="control-label col-md-8"><b> হজযাত্রী যেখানে ট্রেনিং করতে চান তার  তথ্য দিন </b></h6> <br>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">District</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.training_district ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.training_district"
                         :options="indexData.trainingDistrictArr"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.training_district" class="text-danger">{{ allerros.training_district[0] }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Training Venue</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.training_venue ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.training_venue"
                         :options="venueList"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.training_venue" class="text-danger">{{ allerros.training_venue }}</span>
            </div>
        </div>

        <div class="row form-group">
            <h6 class="control-label col-md-8"> <b>হজযাত্রী যে জেলায় ভ্যাকসিন নিতে চায় তার তথ্য দিন</b></h6> <br>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">District</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.vaccine_district ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.vaccine_district"
                         :options="indexData.trainingDistrictArr"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.vaccine_district" class="text-danger">{{ allerros.vaccine_district }}</span>
            </div>
        </div>

        <div class="row form-group">
            <h6 class="control-label col-md-8"><b> সৌদি আরবে অবস্থানকালে জরুরী প্রয়োজনে বাংলাদেশে যে ব্যক্তির সঙ্গে যোগাযোগ
                করবেন (তার তথ্য ইংরেজিতে পূরন করুন) </b></h6> <br>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Name</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.reliable_bd_person ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.reliable_bd_person" placeholder="Name" class="form-control" />
                <span v-if="allerros.reliable_bd_person" class="text-danger">{{ allerros.reliable_bd_person }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Mobile</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.reliable_bd_person_mobile ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.reliable_bd_person_mobile" placeholder="Mobile" class="form-control" />
                <span v-if="allerros.reliable_bd_person_mobile" class="text-danger">{{ allerros.reliable_bd_person_mobile }}</span>
            </div>
        </div>

        <div class="row form-group">
            <h6 class="control-label col-md-8"><b> জরুরী প্রয়োজনে সৌদি আরবে যে ব্যক্তির (যদি থাকে) সঙ্গে যোগাযোগ করবেন (তার
                তথ্য ইংরেজিতে পূরন করুন) </b></h6> <br>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Name</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.reliable_ksa_person ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.reliable_ksa_person" placeholder="Name" class="form-control" />
                <span v-if="allerros.reliable_ksa_person" class="text-danger">{{ allerros.reliable_ksa_person }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Mobile</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.reliable_ksa_person_mobile ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.reliable_ksa_person_mobile" placeholder="Mobile" class="form-control" />
                <span v-if="allerros.reliable_ksa_person_mobile" class="text-danger">{{ allerros.reliable_ksa_person_mobile }}</span>
            </div>
        </div>

        <div class="row form-group">
            <h6 class="control-label col-md-8"><b> সৌদি আরবে মৃত্যুবরণ করলে টাকা ও মালামাল যিনি গ্রহণ করবেন (তার তথ্য
                ইংরেজিতে পূরন করুন) </b></h6> <br>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Name</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.nominee ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.nominee" placeholder="Name" class="form-control" />
                <span v-if="allerros.nominee" class="text-danger">{{ allerros.nominee }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Mobile</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.nominee_mobile ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.nominee_mobile" placeholder="Mobile" class="form-control" />
                <span v-if="allerros.nominee_mobile" class="text-danger">{{ allerros.nominee_mobile }}</span>
            </div>
        </div>
    </div>
</template>
