<template>

    <div class="card card-magenta border border-magenta">

        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div style="max-width: 1200px" class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel"><strong>Create Voucher</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="addVoucher">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>Package Type</strong></label>
                                    <div class="col-sm-8">
                                        <Select2 class="custom-select2 input-sm select2Field"
                                                 :options="category"
                                                 v-model="packageCategory"
                                                 placeholder="Package Type"
                                                 :settings="{ width: '100%'}"
                                        />
                                        <span class="text-danger" v-if="errors['category']">{{ errors['category'][0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row" v-if="isPrimaryPackage == '0'" >
                                    <label class="col-sm-6 col-form-label"><strong>হজযাত্রী কি শর্ট প্যাকেজে যেতে চান? :</strong></label>
                                    <div class="col-sm-4">
                                        <label class="radio-inline p-2">
                                            <input type="radio" name="is_short_package" value="1" v-model="is_short_package"> Yes
                                        </label>
                                        <label class="radio-inline p-2">
                                            <input type="radio" name="is_short_package" value="0" checked v-model="is_short_package"> No
                                        </label>
                                        <span class="text-danger" v-if="errors['is_short_package']">{{ errors['is_short_package'][0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"><strong>Package Name</strong></label>
                                    <div class="col-sm-10">
                                        <Select2 class="custom-select2 input-sm select2Field"
                                                 :options="packages"
                                                 v-model="formData.hajj_pcz_id"
                                                 placeholder="Search hajj Package"
                                                 :settings="{ width: '100%'}"
                                        />
                                        <span class="text-danger" v-if="errors['hajj_pcz_id']">{{ errors['hajj_pcz_id'][0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!--
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>Voucher Name</strong></label>
                                    <div class="col-sm-8">
                                        <input v-model="formData.voucher_name" type="text" class="form-control form-control-md" placeholder="Voucher Name">
                                        <span class="text-danger" v-if="errors['voucher_name']">{{ errors['voucher_name'][0] }}</span>
                                    </div>
                                </div>
                            </div>
                            -->
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>Depositor Name</strong></label>
                                    <div class="col-sm-8">
                                        <input v-model="formData.depositor_name" type="text" class="form-control form-control-md" placeholder="Depositor Name">
                                        <span class="text-danger" v-if="errors['depositor_name']">{{ errors['depositor_name'][0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>Deposite Date</strong></label>
                                    <div class="col-sm-8" style="padding-top: 5px">
                                        <Datepicker
                                            v-model="formData.deposit_date"
                                            :enableTimePicker="false"
                                            type="date"
                                            :minDate="new Date()"
                                            format="dd-MMM-yyyy"
                                            placeholder="dd-mm-yyyy"
                                            :text-input="true"
                                            autoApply
                                        />
                                        <span class="text-danger" v-if="errors['deposit_date']">{{ errors['deposit_date'][0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>Mobile Number</strong></label>
                                    <div class="col-sm-8">
                                        <input v-model="formData.mobile_number" type="text" class="form-control form-control-md" placeholder="Mobile Number">
                                        <span class="text-danger" v-if="errors['mobile_number']">{{ errors['mobile_number'][0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>Total Pilgrim</strong></label>
                                    <div class="col-sm-8">
                                        <input v-model="formData.total_pilgrim" min="0" type="number" class="form-control form-control-md" placeholder="Total Pilgrim">
                                        <span class="text-danger" v-if="errors['total_pilgrim']">{{ errors['total_pilgrim'][0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"><strong>Address</strong></label>
                                    <div class="col-sm-10">
                                        <input v-model="formData.address" type="text" class="form-control form-control-md" placeholder="Address">
                                        <span class="text-danger" v-if="errors['address']">{{ errors['address'][0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- End Modal Body -->
                    <div class="modal-header">
                        <button ref="closeModal" type="button" class="btn btn-secondary" @click="closeModal" data-dismiss="modal">
                            Close
                        </button>
                        <button :disabled="loader" type="submit" class="btn custom-bg">
                            <div v-if="loader" class="spinner-border" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            Create
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-header">
            <div class="float-left">
                <h5><strong><strong>Voucher List</strong></strong></h5>
            </div>

            <div class="float-right">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#staticBackdrop"
                        @click="loadDataPackage()">
                    <i class="fa fa-plus text-dark"></i> <b class="text-dark">ভাউচার তৈরি করুন</b>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="datatable" class="table vue-table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Serial No</th>
                            <th>Voucher Name</th>
                            <th>Voucher No</th>
                            <th>Number of pilgrims</th>
                            <th>Management</th>
                            <th>Payment Status</th>
                            <th>Package Name</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(voucher, index) in vouchers" :key="voucher.id">
                            <td>{{ (page - 1) * limits + index + 1 }}</td>
                            <td>{{voucher.name}}</td>
                            <td>{{voucher.tracking_no}}</td>
                            <td>{{voucher.pilgrims}}</td>
                            <td>{{voucher.is_govt}}</td>
                            <td>{{voucher.payment_status}}</td>
                            <td>{{voucher.package}}</td>
                            <td>
                                <router-link :to="{ name: 'RegVoucherDetailView', params: {id: voucher.id} }" class="btn btn-xs btn-success">
                                    <i class="fas fa-solid fa-folder-open"></i> Open
                                </router-link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="col-md-5"><br>
                    </div>
                    <div class="col-md-7 ">
                        <pagination v-model="page" :records="totalData" :per-page="limits ?? 0" @paginate="getVouchers"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions,mapState} from "vuex"
import Datepicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    name:'RegVoucherList',
    components: {Datepicker},
    inject: ['toast'],
    data() {
        return {
            page:1,
            limits:10,
            formData:{},
            errors:{},
            loader:false,
            msg:null,
            vouchers:{},
            totalData:0,
            packageCategory: '',
            is_short_package: 0,
            isPrimaryPackage: '',
            textInputOptions: {
                format: 'dd/MMM/yyyy'
            }

        }
    },
    computed:{
        ...mapState({
            packages:state=>state.RegVoucher.packages,
            category:state=>state.RegVoucher.category,
        })
    },

    mounted(){
       // this.get_vouchers();
        this.getVouchers()
    },

    methods: {
        ...mapActions({
            get_packages:'RegVoucher/get_packages',
        }),

        getVouchers:function (page= 1){
            let maxLimit = `&limit=${this.limits}`;
            axios.get(`/registration/reg-voucher/get-voucher?page=${page}${maxLimit}`).then(res=>{
                if (res.data.responseCode === 1) {
                    this.vouchers = res.data?.data?.data
                    this.totalData = res.data?.data?.totalCount
                } else {
                    this.vouchers = []
                    this.totalData = 0
                }
            })
        },

        closeModal:function (){
                this.errors={}
                this.formData={}
                this.loader=false
        },

        addVoucher: function () {
            if (this.packageCategory === '') {
                alert('Please select hajj package type');
                return false;
            }
            this.loader=true
            axios.post('/registration/reg-voucher/store-voucher',this.formData).then(res=>{
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                this.$router.push({ name: 'RegVoucherDetailView', params: { id: res.data.data.data.insertId }});
                this.getVouchers();
                this.errors={};
                this.loader=false;
                this.$refs.closeModal.click();
            }).catch(err=>{
                this.errors=err.response.data.errors;
                this.loader=false
            })
        },
        loadDataPackage:function (){
            this.get_packages({
                is_short_package: this.is_short_package,
                category: this.packageCategory,
            });
        },
    },
    watch: {
        is_short_package(newVal) {
            this.loadDataPackage();
        },
        packageCategory(newVal) {
            const selectedCategory = this.category.find(
                item => item.id === newVal
            );
            if (selectedCategory) {
                this.isPrimaryPackage = selectedCategory.is_primary_package;
                this.loadDataPackage();
            }
        }
    },
}

</script>
<style scoped>
.custom-bg{
    background: #00684D !important;
    color: white;
}
.spinner-border{
    width: 16px;
    height: 16px;
}

.custom-select2 {
    //width: 300px;
}
</style>

<style>
.select2-container .select2-selection--single {
    height: 39px;
}
.select2-container .select2-selection--single .select2-selection__arrow {
    top: 7px;
}
</style>
