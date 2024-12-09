<script setup>
import {inject, onMounted, ref, defineEmits, defineProps} from 'vue';
import axios from "axios";

const indexData = inject('indexData');
const pilgrimData = inject('pilgrimData');
const allerros = inject('allerros');
const bankBranchList = inject('bankBranchListArr');
const pilgrim_img = ref();

const {imagePreview} = defineProps(['imagePreview']);
const emit = defineEmits(['onFileChange']);
const onImageChange = (event) => {
    const file = event.target.files[0]
    if (file && file.type.startsWith('image/')) {
        emit('onFileChange', file);
    } else {
        if (pilgrim_img.value) {
            pilgrim_img.value.value = '';
        }
        alert('Selected file is not an image');
    }
}
const onDOBImageChange = (event) => {
    const file = event.target.files[0]
    if (file && file.type.startsWith('image/')) {
        emit('onDOBFileChange', file);
    } else {
        if (dob_img.value) {
            dob_img.value.value = '';
        }
        alert('Selected file is not an image');
    }
}

</script>

<template>
    <div>
        <div class="row form-group">
            <h6 class="control-label col-md-8"><b> Refundable Account Information</b></h6> <br>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Account Holder Type</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.refund_account_type ? 'has-error' : '']">
                <select v-model="pilgrimData.refund_account_type" class="form-control input-sm">
                    <option value="">Select one</option>
                    <option v-for="(account_owner,index) in indexData.accountHolderType" :key="index" :value="index">
                        {{ account_owner }}
                    </option>
                </select>
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_account_type[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Account Holder Name</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.refund_account_name ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.refund_account_name" placeholder="Account Holder Name" class="form-control" />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_account_name[0] }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Account Number</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.refund_account_number ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.refund_account_number" placeholder="Account Number" class="form-control" />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_account_number[0] }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Bank Name</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.refund_bank_id ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.refund_bank_id"
                         :options="indexData.bankListArr"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_bank_id[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Branch District</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.refund_branch_district ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.refund_branch_district"
                         :options="indexData.bankDistrictListArr"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_branch_district[0] }}</span>
            </div>
        </div>
        <div class="row form-group">
            <label class="control-label col-md-3 required-star">Branch & Routing No</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.refund_routing_no ? 'has-error' : '']">
                <Select2 v-model="pilgrimData.refund_routing_no"
                         :options="bankBranchList"
                         class="input-sm select2Field"
                />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_routing_no[0] }}</span>
            </div>
        </div>

        <div class="row form-group">
            <label class="control-label col-md-3">Photo</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pilgrim_img ? 'has-error' : '']">
                <input type="file" id="pilgrim_img" ref="pilgrim_img" class="form-control" @change="onImageChange" />
                <span v-if="allerros.pilgrim_img" class="text-danger">{{ allerros.pilgrim_img[0] }}</span>
                <span class="help-block">[File Format: *.jpg/ .jpeg/.png/ .gif | Max size 1 MB]</span>
            </div>
        </div>
        <template v-if="pilgrimData.identity === 'DOB'">
            <div class="row form-group">
                <label class="control-label col-md-3">Birth Certificate Image</label>
                <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                <div :class="['col-md-7', allerros.pilgrim_img ? 'has-error' : '']">
                    <input type="file" id="pilgrim_img" ref="dob_img" class="form-control" @change="onDOBImageChange" />
                    <span v-if="allerros.pilgrim_img" class="text-danger">{{ allerros.pilgrim_img[0] }}</span>
                    <span class="help-block">[File Format: *.jpg/ .jpeg/.png/ .gif | Max size 1 MB]</span>
                </div>
            </div>
        </template>
        <div class="row mb-4">
                <div class="col-md-3"></div>
                <div class="col-md-1"></div>
                <div class="col-md-3 mt-3" v-if="imagePreview.profile">
                    <p>Pilgrim Image</p>
                    <img :src="imagePreview.profile" alt="" class="image-preview">
                </div>
                <div class="col-md-4 mt-3" v-if="imagePreview.dob && pilgrimData.identity === 'DOB'">
                    <p>Birth Certificate</p>
                    <img :src="imagePreview.dob" alt="" class="image-preview" style="max-width: 250px; border: 1px solid black; padding: 2px; border-radius: 5px;">
                </div>
        </div>
    </div>
</template>

<style scoped>
.image-preview {
    width: 150px;
    border: 2px solid black;
    margin-top: 5px;
}
</style>
