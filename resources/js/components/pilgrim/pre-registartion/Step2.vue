<script setup>
import { inject, defineEmits } from 'vue';

const indexData = inject('indexData');
const pilgrimData = inject('pilgrimData');
const allerros = inject('allerros');
const isGovtJob = inject('isGovtJob');

const emit = defineEmits(['handleOccupationChange']);
const handleChange = value => {
    emit('handleOccupationChange', value);
}

</script>

<template>
    <div>
        <div  v-if="pilgrimData.identity !== 'PASSPORT'" class="row form-group">
            <label class="control-label col-md-3 required-star">Name in Bangla</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.name_bn ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.name_bn" placeholder="Name in Bangla" class="form-control" />
                <span v-if="allerros.name_bn" class="text-danger">{{ allerros.name_bn[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Name in English</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.name_en ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.name_en" placeholder="Name in English" class="form-control" />
                <span v-if="allerros.name_en" class="text-danger">{{ allerros.name_en[0] }}</span>
            </div>
        </div>
        <template v-if="pilgrimData.identity === 'PASSPORT'">
            <div class="row form-group">
                <label class="control-label col-md-3 required-star">Father Name</label>
                <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                <div :class="['col-md-7', allerros.father_name_en ? 'has-error' : '']">
                    <input type="text" v-model="pilgrimData.father_name_en" placeholder="Father Name" class="form-control" />
                    <span v-if="allerros.father_name_en" class="text-danger">{{ allerros.father_name_en[0] }}</span>
                </div>
            </div>
            <div class="row form-group">
                <label class="control-label col-md-3 required-star">Mother Name </label>
                <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                <div :class="['col-md-7', allerros.mother_name_en ? 'has-error' : '']">
                    <input type="text" v-model="pilgrimData.mother_name_en" placeholder="Mother Name " class="form-control" />
                    <span v-if="allerros.mother_name_en" class="text-danger">{{ allerros.mother_name_en[0] }}</span>
                </div>
            </div>
        </template>
        <template v-else>
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
        </template>
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
