<template>
<div class="card card-magenta border border-magenta">
    <div class="card-header">
        <h5 class="card-title pt-2 pb-2">User Manual Edit Form</h5>
    </div>
    <form @submit.prevent="saveForm()" id="user-manual">
        <div class="card-body">
            <div class="col-md-10">
                <div class="row form-group">
                    <label class="control-label col-md-2  required-star">Type Name:</label>
                    <div :class="['col-md-8', allerros.typeName ? 'has-error' : '']">
                        <input type="text" v-model="usermanual.typeName" class="form-control">
                        <span v-if="allerros.typeName" :class="['text-danger']">{{ allerros.typeName[0] }}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-md-2  required-star">Details:</label>
                    <div :class="['col-md-8', allerros.details ? 'has-error' : '']">
                        <editor
                                v-model="usermanual.details"
                                api-key="5tyznzq0zx85ayto1vep7l7jy3d4hsyf8mxev8jwuq5zqqwk"
                                :init="{
                                 height: 300,
                                 menubar: true,
                                 plugins: [
                                   'advlist autolink lists link image charmap print preview anchor',
                                   'searchreplace visualblocks code fullscreen',
                                   'insertdatetime media table paste code help wordcount'
                                 ],
                                 toolbar:
                                   'undo redo | formatselect | bold italic backcolor | \
                                   alignleft aligncenter alignright alignjustify | \
                                   bullist numlist outdent indent | removeformat | help'
                               }"
                        />
                        <span v-if="allerros.details" :class="['text-danger']">{{ allerros.details[0] }}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-md-2 ">Pdf File:</label>
                    <div :class="['col-md-6', allerros.files ? 'has-error' : '']">
                        <input type="file" id="files" ref="files"  class="form-control" v-on:change="onFileChange"/>
                        <span v-if="allerros.files" :class="['text-danger']">{{ allerros.files[0] }}</span>
                        <span class="help-block">[File Format: *.pdf | Max size 2 MB]</span>
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-info" target="_blank" v-bind:href="'/'+ usermanual.pdfFile"><i
                                class="fa fa-file-image-o"></i> View Photo</a>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-md-2  required-star">Terms & Condition:</label>
                    <div :class="['col-md-8', allerros.termsCondition ? 'has-error' : '']">
                        <textarea v-model="usermanual.termsCondition" class=" form-control"></textarea>
                        <span v-if="allerros.termsCondition" :class="['text-danger']">{{ allerros.termsCondition[0] }}</span>
                    </div>
                </div>
                <div class="row form-group ">
                    <label  class="col-md-2  required-star">Status: </label>
                    <div class="col-md-10">
                        <label><input v-model="usermanual.status" name="status" type="radio"
                                        value="1" > Active&nbsp;</label>
                        <label><input v-model="usermanual.status" name="status" type="radio"
                                        value="0" > Inactive</label>
                        <span v-if="allerros.status" :class="['text-danger']">{{ allerros.usermanual[0] }}</span>
                    </div>
                </div>
                <div class="col-md-10">
                    <router-link to="/home-page/user-manual" class="btn btn-default"><i
                        class="fa fa-chevron-circle-left"></i> Back</router-link>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fa fa-chevron-circle-right"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
</template>

<script>
    // import {API} from '../../../custom.js';
    import Editor from "@tinymce/tinymce-vue";
    // const customClass = new API();
    export default {
        data: function () {
            return {
                $id: null,
                allerros: [],
                usermanual: {
                    typeName: '',
                    files: '',
                    details: '',
                    termsCondition: '',
                    status:'',
                },
            }

        },
        components: {
            'editor': Editor
        },
        mounted() {

            $(document).ready(function () {
                $("#user-manual").validate({
                    errorPlacement: function () {
                        return false;
                    }
                });
            });
        },

        created() {
            let app = this;
            let id = app.$route.params.id;
            console.log(id);
            app.id = id;
            axios.get('/settings/home-page/edit-user-manual/' + id)
                .then(function (resp) {
                    app.usermanual = resp.data;
                })
                .catch(function () {
                    alert("Could not load your usermanual")
                });
        },

        methods: {
            onFileChange(event) {
                this.usermanual.imagefile = event.target.files[0]
                console.log(this.slider.imagefile);
            },
            saveForm() {
                var app = this;
                var newCompany = app.usermanual;
                var form = new FormData();
                form.append('pdfFile',this.usermanual.imagefile);
                form.append('typeName',this.usermanual.typeName);
                form.append('details',this.usermanual.details);
                form.append('termsCondition',this.usermanual.termsCondition);
                form.append('status',this.usermanual.status);
                form.append('id',app.id);
                axios.defaults.headers.post['Content-Type'] = 'multipart/form-data';
                axios.post('/settings/home-page/update-user-manual',form)
                    .then(function (resp) {
                        if (resp.data.status === true) {
                            app.$toast.success('Your data update successfully.');
                            app.$router.replace('/home-page/user-manual');
                        }
                    }).catch((error) => {
                    app.allerros = error.response.data.errors;
                });
            },
        }
    }
</script>
