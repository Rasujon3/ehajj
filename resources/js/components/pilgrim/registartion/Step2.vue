<script setup>
import {inject, defineEmits, ref, onMounted} from 'vue';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import axios from "axios";

const indexData = inject('indexData');
const pilgrimData = inject('pilgrimData');
const allerros = inject('allerros');
const isGovtJob = inject('isGovtJob');
let maharamRelations = ref([]);

const emit = defineEmits(['handleOccupationChange']);
const handleChange = value => {
    emit('handleOccupationChange', value);
}
const getMaharamRelation = async () => {
    try {
        let response = await axios.get(`/pilgrim/reg/get-maharam-relation`);
        if (response.data?.responseCode === 1) {
            maharamRelations.value = [...response.data?.data];
        } else {
            alert(response.data?.msg);
            maharamRelations.value = [];
        }
    } catch (error) {
        alert('Something went wrong!!! [RS2-001]');
        maharamRelations.value = [];
    }
}
let isLoading = ref(false);
const onTrackingNoSubmit = async () => {
    isLoading.value = true;
    if (pilgrimData.value.maharam_tracking_no.trim() === '') {
        alert('Please add maharam tracking number.');
        isLoading.value = false;
        return false;
    }
    if (pilgrimData.value.tracking_no.trim() === '') {
        alert('Tracking number not found.');
        isLoading.value = false;
        return false;
    }
    try {
        isLoading.value = true;
        const postData = {
            "maharam_tracking_no": pilgrimData.value.maharam_tracking_no,
            "self_tracking_no": pilgrimData.value.tracking_no
        }
        const response = await axios.post(`/pilgrim/reg/search-maharam-by-tracking-no`, postData);
        if (response.data.responseCode === 1) {
            pilgrimData.value.maharam_name = response.data?.data?.pilgrim_name;
            pilgrimData.value.maharamID = response.data?.data?.pilgrimID;
            isLoading.value = false;
        }
        if (response.data?.responseCode === -1) {
            pilgrimData.value.maharam_name = '';
            pilgrimData.value.maharamID = '';
            alert(response.data?.message);
            isLoading.value = false;
            return false;
        }
    } catch (error) {
        pilgrimData.value.maharam_name = '';
        pilgrimData.value.maharamID = '';
        alert('Something went wrong!!! [HG:002]');
        isLoading.value = false;
    }
}

onMounted(async () => {
    await getMaharamRelation();
});

</script>

<template>
    <div>
        <div class="row form-group">
            <label class="control-label col-md-3">Tracking No</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7']">
                <input type="text" v-model="pilgrimData.tracking_no" class="form-control" disabled />
            </div>
        </div>
        <!--<div class="row form-group">
            <label class="control-label col-md-3">Date of Birth</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7']">
                <Datepicker
                    v-model="pilgrimData.birth_date"
                    :enableTimePicker="false"
                    type="date"
                    :maxDate="new Date()"
                    format="dd-MMM-yyyy"
                    placeholder="dd-mm-yyyy"
                    :text-input="true"
                    autoApply
                    readonly
                />
            </div>
        </div> -->
        <div class="row form-group">
            <label class="control-label col-md-3">Passport Date of Birth</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7']">
                <Datepicker
                    v-model="pilgrimData.pass_dob"
                    :enableTimePicker="false"
                    type="date"
                    :maxDate="new Date()"
                    format="dd-MMM-yyyy"
                    placeholder="dd-mm-yyyy"
                    :text-input="true"
                    autoApply
                    readonly
                />
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3">Name in Bangla</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.name_bn ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.name_bn" placeholder="Name in Bangla" class="form-control"/>
                <span v-if="allerros.name_bn" class="text-danger">{{ allerros.name_bn[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3">Name in English</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.name_en ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.name_en" placeholder="Name in English" class="form-control" readonly/>
                <span v-if="allerros.name_en" class="text-danger">{{ allerros.name_en[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3">Passport Name</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pass_name ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.pass_name" placeholder="Passport Name" class="form-control" readonly/>
                <span v-if="allerros.pass_name" class="text-danger">{{ allerros.pass_name[0] }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Father Name in English</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.father_name_en ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.father_name_en" placeholder="Father Name" class="form-control" readonly/>
                <span v-if="allerros.father_name_en" class="text-danger">{{ allerros.father_name_en[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3">Mother Name in English</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.mother_name_en ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.mother_name_en" placeholder="Mother Name " class="form-control" readonly/>
                <span v-if="allerros.mother_name_en" class="text-danger">{{ allerros.mother_name_en[0] }}</span>
            </div>
        </div>

        <!-- for requirement change
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Father Name in Bangla</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.father_name_bn ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.father_name_bn" placeholder="Father Name in Bangla" class="form-control" />
                <span v-if="allerros.father_name_bn" class="text-danger">{{ allerros.father_name_bn[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Mother Name in Bangla </label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.mother_name_bn ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.mother_name_bn" placeholder="Mother Name in Bangla " class="form-control" />
                <span v-if="allerros.mother_name_bn" class="text-danger">{{ allerros.mother_name_bn[0] }}</span>
            </div>
        </div>
        -->
        <div class="row form-group">
            <label class="control-label col-md-3">National ID</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7']">
                <input type="text" v-model="pilgrimData.nid_number" class="form-control" disabled />
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3">Gender</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7']">
                <input type="text" v-model="pilgrimData.gender" class="form-control" disabled />
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3">Management</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7']">
                <input type="text" v-model="pilgrimData.is_govt" class="form-control" disabled />
            </div>
        </div>
        <div v-if="new Date().getFullYear() - new Date(pilgrimData.birth_date).getFullYear() < 18 || pilgrimData.gender === 'female'" class="row form-group">
            <label
                class="control-label col-md-3"
                :class="new Date().getFullYear() - new Date(pilgrimData.birth_date).getFullYear() < 18
                        || pilgrimData.maharam_relation !== '' ? 'required-star' : ''">
                Maharam
            </label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7']">
                <div class="d-flex">
                    <input
                        type="text"
                        class="form-control"
                        v-model="pilgrimData.maharam_tracking_no"
                        placeholder="Enter Maharam tracking no"
                        :required="new Date().getFullYear() - new Date(pilgrimData.birth_date).getFullYear() < 18
                                        || pilgrimData.maharam_relation !== ''"
                    />
                    <span class="input-group-btn ml-2">
                        <button
                            class="btn btn-sm btn-default h-100"
                            type="button"
                            id="search_maharam_btn"
                            :disabled="isLoading"
                            @click="onTrackingNoSubmit()"
                        >
                            <i v-if="!isLoading" class="fa fa-search"></i>
                            <i v-if="isLoading" class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                        </button>
                    </span>
                </div>
                <span v-if="isLoading">Loading ...</span>
                <span v-if="!isLoading" class="text-info" id="maharam_name" v-html="pilgrimData.maharam_name"></span>
            </div>
            <br>
            <input type="hidden" id="maharamID" name="maharamID" v-model="pilgrimData.maharamID" />
            <span v-if="allerros.maharam_tracking_no" class="text-danger">{{ allerros.maharam_tracking_no[0] }}</span>
        </div>
        <div v-if="new Date().getFullYear() - new Date(pilgrimData.birth_date).getFullYear() < 18 || pilgrimData.gender === 'female'" class="row form-group">
            <label
                class="control-label col-md-3"
                :class="new Date().getFullYear() - new Date(pilgrimData.birth_date).getFullYear() < 18
                        || pilgrimData.maharam_tracking_no !== '' ? 'required-star' : ''"
            >
                Relation
            </label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7']">
                <div class="input-block-item form-select2-item">
                    <select class="form-control hajj-select2"
                            v-model="pilgrimData.maharam_relation"
                            :class="[allerros.maharam_relation ? 'has-error' : '']"
                            :required="new Date().getFullYear() - new Date(pilgrimData.birth_date).getFullYear() < 18
                                        || pilgrimData.maharam_tracking_no !== ''"
                    >
                        <option value="">Select One</option>
                        <option v-for="maharamRelation in maharamRelations"
                                :key="maharamRelation.id"
                                :value="maharamRelation.id"
                        >
                            {{ maharamRelation.name }}
                        </option>
                    </select>
                </div>
            </div>
            <span v-if="allerros.maharam_relation" class="text-danger">{{ allerros.maharam_relation[0] }}</span>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Occupation</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.occupation ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.occupation"
                         :options="indexData.occupationListArr"
                         class="input-sm select2Field"
                         @select="handleChange($event)"
                />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.occupation[0] }}</span>
            </div>
        </div>
        <template v-if="isGovtJob">
            <div class="row form-group">
                <label class="control-label col-md-3 required-star">Designation</label>
                <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                <div :class="['col-md-7', allerros.designation ? 'has-error' : '']">
                    <input type="text" v-model="pilgrimData.designation" placeholder="Designation" class="form-control" />
                    <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.designation[0] }}</span>
                </div>
            </div>
            <div class="row form-group">
                <label class="control-label col-md-3 required-star">Govt Service Grade</label>
                <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                <div :class="['col-md-7', allerros.serviceGrade ? 'has-error' : '']">
                    <Select2 v-model="pilgrimData.serviceGrade"
                             :options="indexData.govtServiceGradeObj"
                             class="input-sm select2Field"
                    />
                    <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.serviceGrade[0] }}</span>
                </div>
            </div>
        </template>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Mobile Number</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pilgrimData ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.mobile" placeholder="Mobile number" class="form-control" />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.mobile[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Marital Status</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pilgrimData ? 'has-error' : '']">
                <select v-model="pilgrimData.marital_status" class="form-control input-sm">
                    <option value="">Select one</option>
                    <option value="Married">Married</option>
                    <option value="Unmarried">Unmarried</option>
                    <option value="Others">Others</option>
                </select>
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.marital_status[0] }}</span>
            </div>
        </div>
        <template v-if="pilgrimData.marital_status === 'Married'">
            <div class="row form-group">
                <label class="control-label col-md-3 required-star">Spouse Name</label>
                <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                <div :class="['col-md-7', allerros.pilgrimData ? 'has-error' : '']">
                    <input type="text" v-model="pilgrimData.spouse_name" placeholder="Spouse Name" class="form-control" />
                    <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.spouse_name[0] }}</span>
                </div>
            </div>
        </template>

    </div>
</template>
