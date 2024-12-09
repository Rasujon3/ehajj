<script setup>
import {computed, ref, onMounted, watch, toRefs} from 'vue';
import { useStore } from 'vuex';
import '../../../lib/facedetection'
import '../../../lib/croppie'
import '../../../../css/croppie.css'

const store = useStore();

const props = defineProps({
    guideData: Object,
    allerros: Array
});

const { guideData, allerros } = toRefs(props);

const imageUploadModalId = 'ImageUploadModal';
let uploadCropArea = '';
let viewportWidth = 300;
let viewportHeight = 300;
let faceDetect = true;
let rawImg = '';
let fileInput = '';

// Use ref to make profilePicUrl reactive
const profilePicUrl = ref('');

// Initialize profilePicUrl on mount
onMounted(() => {
    profilePicUrl.value = guideData.value.profile_pic;
});

// Watch for changes in guideData.profile_pic and update profilePicUrl
watch(() => guideData.value.profile_pic, (newPic) => {
    if (newPic instanceof File) {
        profilePicUrl.value = URL.createObjectURL(newPic);
    } else {
        profilePicUrl.value = newPic;
    }
});
function onProfilePicFileChange(event) {
    fileInput = event.target;
    const file = event.target?.files[0];
    if (file) {
        // guideData.value.profile_pic = file;
        // profilePicUrl.value = URL.createObjectURL(file);
        const reader = new FileReader();
        reader.onload = (e) => {
            rawImg = e.target.result;
            initializeModalAndCropArea();
        };
        reader.readAsDataURL(fileInput.files[0]);
    }
}

// Dynamically update the background style
const userPhotoStyle = computed(() => {
    return {
        backgroundImage: `url(${profilePicUrl.value})`
    };
});

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
        guideData.value.profile_pic = resp;
        profilePicUrl.value = resp;
        closeModal();
    });
    uploadCropArea.croppie('destroy');
}

const closeModal = () => {
    $('#' + imageUploadModalId).modal('hide');
};

</script>

<template>
    <div class="photo-reset-sec">
        <div class="guide-user-photo">
            <div class="user-photo-bg" :style="userPhotoStyle"></div>
        </div>
        <div class="upload-user-photo"  :class="[allerros.profile_pic ? 'has-error' : '']" >
            <div class="file-upload-box">
                <div class="upload-box-container">
                    <span class="upload-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                            <g clip-path="url(#clip0_3160_30117)">
                                <path d="M19.4382 14.8691V19.2168C19.4382 19.4474 19.3466 19.6685 19.1835 19.8316C19.0204 19.9947 18.7993 20.0863 18.5686 20.0863H2.91717C2.68656 20.0863 2.46539 19.9947 2.30232 19.8316C2.13926 19.6685 2.04765 19.4474 2.04765 19.2168V14.8691H0.308594V19.2168C0.308594 19.9086 0.583425 20.5721 1.07263 21.0613C1.56183 21.5505 2.22533 21.8253 2.91717 21.8253H18.5686C19.2605 21.8253 19.924 21.5505 20.4132 21.0613C20.9024 20.5721 21.1772 19.9086 21.1772 19.2168V14.8691H19.4382Z" fill="#374957"/>
                                <path d="M10.7137 0.958018C10.3713 0.957074 10.0321 1.02372 9.71545 1.15413C9.39882 1.28454 9.11105 1.47615 8.86861 1.71798L5.46094 5.12566L6.69045 6.35517L9.85031 3.19618L9.87291 17.479H11.612L11.5894 3.20835L14.7362 6.35517L15.9657 5.12566L12.558 1.71798C12.3157 1.47618 12.0281 1.28457 11.7116 1.15416C11.3952 1.02375 11.056 0.957093 10.7137 0.958018Z" fill="#374957"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_3160_30117">
                                    <rect width="20.8686" height="20.8686" fill="white" transform="translate(0.308594 0.958008)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>

                    <div class="fileUpload btn btn-filebrowse">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 18 19" fill="none">
                                <path d="M9.46774 4.36132C9.46774 3.96114 9.14332 3.63672 8.74313 3.63672C8.34294 3.63672 8.01853 3.96114 8.01853 4.36132V8.70896H3.67089C3.27071 8.70896 2.94629 9.03337 2.94629 9.43356C2.94629 9.83375 3.27071 10.1582 3.67089 10.1582H8.01853V14.5058C8.01853 14.906 8.34294 15.2304 8.74313 15.2304C9.14332 15.2304 9.46774 14.906 9.46774 14.5058V10.1582H13.8154C14.2156 10.1582 14.54 9.83375 14.54 9.43356C14.54 9.03337 14.2156 8.70896 13.8154 8.70896H9.46774V4.36132Z" fill="white"/>
                            </svg>
                        </span>
                        <!--                        <input id="uploadBtn" type="file" class="uploadInput" :class="[{ required: true }]" @change="onProfilePicFileChange" required>-->
                        <input id="uploadBtn" type="file" class="uploadInput" @change="onProfilePicFileChange">
                    </div>

                    <p class="text-eng"><strong>Photo</strong><br> Browse and chose the files your from your computer<br>
                        <!--                        <span class="photo-reset" @click="photoReset"><strong>Reset</strong></span>-->
                    </p>
                </div>
            </div>
            <input hidden="hidden" v-model="guideData.hidden_profile_pic">
            <span v-if="allerros.profile_pic" class="text-danger">{{ allerros.profile_pic[0] }}</span>
        </div>
    </div><!-- /photo-reset-sec -->

    <!--  Photo resize modal  -->
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
.has-error {
    border: 1px solid red;
}
.has-tr-error {
    border: 2px solid red;
}
</style>
