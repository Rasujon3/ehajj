<script setup>
import {inject, onMounted, ref, defineEmits, defineProps} from 'vue';
import '../../../lib/facedetection';
import '../../../lib/croppie';
import '../../../../css/croppie.css';

const indexData = inject('indexData');
const pilgrimData = inject('pilgrimData');
const allerros = inject('allerros');
const bankBranchList = inject('bankBranchListArr');
const pilgrim_img = ref();
const dob_img = ref();
const isImageChange = ref(false);

const imageUploadModalId = 'ImageUploadModal';
let uploadCropArea = '';
let viewportWidth = 300;
let viewportHeight = 300;
let faceDetect = true;
let rawImg = '';
let fileInput = '';

const {imagePreview} = defineProps(['imagePreview']);
const emit = defineEmits([
    'onFileChange',
    'onDOBFileChange'
]);
const onImageChange = (event) => {
    fileInput = event.target;
    const file = event.target.files[0]
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            rawImg = e.target.result;
            initializeModalAndCropArea();
        };
        reader.readAsDataURL(fileInput.files[0]);
        //emit('onFileChange', file);
    } else {
        if (pilgrim_img.value) {
            pilgrim_img.value.value = '';
        }
        alert('Selected file is not an image');
    }
}

const initializeModalAndCropArea = () => {
    // Show modal
    $('#' + imageUploadModalId).modal('show');
    // Reset crop area
    document.getElementById('upload_crop_area').innerHTML = "";
    const imgElem = document.createElement('img');
    imgElem.id = 'detect_image';
    imgElem.src = rawImg;
    imgElem.style.height = '100%';
    imgElem.style.width = '100%';
    document.getElementById('upload_crop_area').appendChild(imgElem);
    uploadCropArea = $('#detect_image');

    if (uploadCropArea.data('croppie')) {
        uploadCropArea.croppie('destroy');
    }

    document.getElementById('upload_crop_area').style.width = (viewportWidth + 50) + "px";
    document.getElementById('upload_crop_area').style.height = (viewportHeight + 50) + "px";

    // Handle face detection
    if (faceDetect) {
        $('#button_area').css('display', 'none');
        $('#cropping_msg').css('display', 'block');

        setTimeout(() => {
            uploadCropArea.faceDetection({
                complete: function (faces) {
                    if (faces.length > 0) {
                        uploadCropArea.croppie({
                            viewport: {
                                width: viewportWidth,
                                height: viewportHeight,
                                type: 'square'
                            },
                            enforceBoundary: true, // Restricts zoom so image cannot be smaller than viewport
                            // enableExif: false,
                            enableResize: true, // Enable or disable support for resizing the viewport area
                        });

                        $('#button_area').css('display', 'block');
                        $('#cropping_msg').css('display', 'none');
                    } else {
                        toastr.error(" ", 'Given image is not valid! (Can\'t recognize any face)', {
                            positionClass: "toast-top-center",
                        });

                        $('#button_area').css('display', 'block');
                        $('#cropping_msg').css('display', 'none');

                        document.getElementById('detect_image').remove();
                        // Hide Modal
                        $('#' + imageUploadModalId).modal('hide');
                    }
                }
            });
        }, 3000);
    } else {
        uploadCropArea.croppie({
            viewport: {
                width: viewportWidth,
                height: viewportHeight,
                type: 'square'
            },
            enforceBoundary: true, // Restricts zoom so image cannot be smaller than viewport
            // enableExif: false,
            enableResize: true, // Enable or disable support for resizing the viewport area
        });
    }
};

const handleSaveCropImg = () => {
    uploadCropArea.croppie('result', {
        type: 'base64',
        format: 'jpeg',
        // size: 'original',
        size: {width: viewportWidth, height: viewportHeight}
    }).then(function (resp) {
        isImageChange.value = true;
        emit('onFileChange', resp);
        closeModal();
    });
    uploadCropArea.croppie('destroy');
}

const handleResetImage = (event) => {
    isImageChange.value = false;
    emit('onFileChange', null);
}
// Closes the modal
const closeModal = () => {
    $('#' + imageUploadModalId).modal('hide');
};


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
            <label class="control-label col-md-3 required-star">Account Number</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.refund_account_number ? 'has-error' : '']">
                <input type="text" v-model="pilgrimData.refund_account_number" placeholder="Account Number" class="form-control" />
                <span v-if="allerros.pilgrimData" class="text-danger">{{ allerros.refund_account_number[0] }}</span>
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

        <div class="row form-group align-items-center">
            <label class="control-label col-md-3">Photo</label>
            <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
            <div :class="['col-md-7', allerros.pilgrim_img ? 'has-error' : '']">
                <div class="row align-items-end mx-0">
                    <img :src="imagePreview.profile || '/assets/images/no_image.png'" alt="" class="image-preview-pilgrim">
                    <button v-if="isImageChange" type="button" class="btn btn-warning mr-2" @click="handleResetImage"><i class="fas fa-times"></i> Reset Image</button>
                    <label v-if="!isImageChange" class="btn btn-primary btn-file mb-0">
                        <i class="fas fa-image"></i> Browse
                        <input type="file" id="pilgrim_img" ref="pilgrim_img" class="form-control custom-file-input" @change="onImageChange" />
                    </label>
                </div>
                <span v-if="allerros.pilgrim_img" class="text-danger">{{ allerros.pilgrim_img[0] }}</span>
                <span class="help-block">[File Format: *.jpg/ .jpeg/.png/ .gif | Max size 1 MB]</span>
            </div>
        </div>
        <template v-if="pilgrimData.identity === 'DOB'">
            <div class="row form-group">
                <label class="control-label col-md-3">Birth Certificate Image</label>
                <div class="col-md-1" style="font-weight: bold;padding-top: 5px">:</div>
                <div :class="['col-md-7', allerros.pilgrim_img ? 'has-error' : '']">
                    <input type="file" id="dob_img" ref="dob_img" class="form-control" @change="onDOBImageChange" />
                    <span v-if="allerros.dob_img" class="text-danger">{{ allerros.dob_img[0] }}</span>
                    <span class="help-block">[File Format: *.jpg/ .jpeg/.png/ .gif | Max size 1 MB]</span>
                </div>
            </div>
        </template>
        <div class="row mb-4">
                <div class="col-md-3"></div>
                <div class="col-md-1"></div>
                <div class="col-md-4 mt-3" v-if="imagePreview.dob && pilgrimData.identity === 'DOB'">
                    <p>Birth Certificate</p>
                    <img :src="imagePreview.dob" alt="" class="image-preview" style="max-width: 250px; border: 1px solid black; padding: 2px; border-radius: 5px;">
                </div>
        </div>
    </div>

    <div class="modal fade" id="ImageUploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">


                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> Photo resize</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="upload_crop_area" class="d-block mx-auto" style="padding-bottom: 25px"></div>
                </div>
                <div class="modal-footer" id="modal-footer">
                    <div id="cropping_msg" class="alert alert-info text-center" style="width: 100%; display: none;">
                        <i class="fa fa-spinner fa-pulse"></i> Please wait, Face detecting
                    </div>
                    <div id="button_area"  style="width: 100%; display: block;">
                        <div class="float-left">
                            <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
                        </div>
                        <div class="float-right">
                            <button type="button" id="cropImageBtn" class="btn btn-primary" @click="handleSaveCropImg()">Save</button>
                        </div>
                    </div>
                </div>
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
.image-preview-pilgrim {
    width: 120px;
    height: 120px;
    display: inline-block;
    margin-right: 0.5rem !important;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    padding: 0.25rem;
}
</style>
