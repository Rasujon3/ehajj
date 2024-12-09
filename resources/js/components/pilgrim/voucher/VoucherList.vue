<template>

    <div class="card card-magenta border border-magenta">

        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div style="max-width: 850px" class="modal-dialog modal-lg">
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
                            <div class="col-md-10">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>আপনি কিভাবে পেমেন্ট করবেন</strong></label>
                                    <div class="col-sm-8" style="padding-top: 5px">
                                        <label>
                                            <input name="payment_type" type="radio" class="passport-radio" checked v-model="formData.payment_type" value="online"/> অনলাইন
                                        </label>
                                        <label style="margin-left: 10%;">
                                            <input name="payment_type" type="radio" class="passport-radio" v-model="formData.payment_type" value="offline"/> ব্যাংক ব্রাঞ্চ
                                        </label>

                                        <span class="text-danger" v-if="errors['payment_type']">{{ errors['payment_type'][0] }}</span>
                                    </div>
                                </div>
                                <div v-if="formData.payment_type === 'online'" class="form-group row">
                                    <div class="col-sm-12 text-danger"><b>অনলাইন পেমেন্ট পরিষেবায় সংশ্লিষ্ট পেমেন্ট গেটওয়ের সেবা গ্রহণের জন্য অতিরিক্ত সার্ভিস চার্জ প্রদান করতে হবে।</b></div>
                                </div>
                                <div v-else class="form-group row">
                                    <div class="col-sm-12 text-danger"><b>যে ব্যাংক এর মাধ্যমে পেমেন্ট করবেন তা সিলেক্ট করুন এবং উক্ত ব্যাংক এ টাকা জমা করুন।</b></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <!--<div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>Voucher Name</strong></label>
                                    <div class="col-sm-8">
                                        <input v-model="formData.voucher_name" type="text" class="form-control form-control-md" placeholder="Voucher Name">
                                        <span class="text-danger" v-if="errors['voucher_name']">{{ errors['voucher_name'][0] }}</span>
                                    </div>
                                </div>-->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>জমা প্রদানের তারিখ</strong></label>
                                    <div class="col-sm-8">
                                        <Datepicker
                                            v-model="formData.deposite_date"
                                            :enableTimePicker="false"
                                            type="date"
                                            :minDate="new Date()"
                                            format="dd-MMM-yyyy"
                                            placeholder="dd-mm-yyyy"
                                            :text-input="true"
                                            autoApply
                                        />
                                        <span class="text-danger" v-if="errors['deposite_date']">{{ errors['deposite_date'][0] }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>ঠিকানা</strong></label>
                                    <div class="col-sm-8">
                                        <input v-model="formData.address" type="text" class="form-control form-control-md" placeholder="Address">
                                        <span class="text-danger" v-if="errors['address']">{{ errors['address'][0] }}</span>
                                    </div>
                                </div>
                                <template v-if="formData.payment_type != 'online'">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label"><strong>জেলা</strong></label>
                                        <div class="col-sm-8">
                                            <Select2 class="custom-select2 input-sm select2Field"
                                                     :options="districts"
                                                     v-model="formData.district_id"
                                                     placeholder="Search District"
                                                     :settings="{ width: '80%'}"
                                                     @select="loadPoliceStation"
                                            />
                                            <span class="text-danger" v-if="errors['district_id']">{{ errors['district_id'][0] }}</span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>জমাদানকারীর নাম</strong></label>
                                    <div class="col-sm-8">
                                        <input v-model="formData.depositor_name" type="text" class="form-control form-control-md" placeholder="Depositor Name">
                                        <span class="text-danger" v-if="errors['depositor_name']">{{ errors['depositor_name'][0] }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"><strong>মোবাইল নম্বার</strong></label>
                                    <div class="col-sm-8">
                                        <input v-model="formData.mobile_number" type="text" class="form-control form-control-md" placeholder="Mobile Number">
                                        <span class="text-danger" v-if="errors['mobile_number']">{{ errors['mobile_number'][0] }}</span>
                                    </div>
                                </div>
                                <template v-if="formData.payment_type != 'online'">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label"><strong>পুলিশ স্টেশন</strong></label>
                                        <div class="col-sm-8">
                                            <Select2 class="custom-select2 input-sm select2Field"
                                                     :options="policeStations"
                                                     v-model="formData.thana_id"
                                                     placeholder="Search Station"
                                                     :settings="{ width: '80%'}"
                                                     @select="loadBankBranch"
                                            />
                                            <span class="text-danger" v-if="errors['thana_id']">{{ errors['thana_id'][0] }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label"><strong>ব্যাংক ব্রাঞ্চ</strong></label>
                                        <div class="col-sm-8">
                                            <Select2 class="custom-select2 input-sm select2Field"
                                                     :options="bankBranches"
                                                     v-model="formData.bank_branch_id"
                                                     placeholder="Search Bank Branch"
                                                     :settings="{ width: '80%'}"
                                            />
                                            <span class="text-danger" v-if="errors['bank_branch_id']">{{ errors['bank_branch_id'][0] }}</span>
                                        </div>
                                    </div>
                                </template>

                            </div>
                        </div>
                    </div>
                    <div class="modal-header">
                        <button ref="closeModal" type="button" class="btn btn-secondary" @click="closeModal" data-dismiss="modal">
                            বন্ধ
                        </button>

                        <button :disabled="loader" type="submit" class="btn custom-bg">
                            <div v-if="loader" class="spinner-border" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            তৈরি করুন
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
                        @click="loadDataDistrict()">
                    <i class="fa fa-plus text-dark"></i> <b class="text-dark">ভাউচার তৈরি করুন</b>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="col-md-1 float-left">
                <select class="form-control col-md-12" v-model="limits" style="display: none">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="col-md-offset-8 col-md-3 float-right">
                <input class="form-control" type="text" placeholder="Search..."  style="display: none" >
            </div>
            <br>
            <br>

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
                            <td>
                                <router-link :to="{ name: 'voucherDetailView', params: {id: voucher.id} }" class="btn btn-xs btn-success">
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
    name:'VoucherList',
    components: {Datepicker},
    inject: ['toast'],
    data() {
        return {
            page:1,
            limits:10,
            formData:{
                "payment_type" : 'online'
            },
            errors:{},
            loader:false,
            msg:null,
            vouchers:{},
            totalData:0,
            textInputOptions: {
                format: 'dd/MMM/yyyy'
            }

        }
    },
    computed:{
        ...mapState({
           // vouchers:state=>state.voucher.vouchers,
            districts:state=>state.voucher.districts,
            policeStations:state=>state.voucher.policeStations,
            bankBranches:state=>state.voucher.bankBranches,
        })
    },

    mounted(){
       // this.get_vouchers();
        this.getVouchers()
    },

    methods: {
        ...mapActions({
           // get_vouchers:'voucher/get_vouchers',
            get_districts:'voucher/get_districts',
            get_police_stations:'voucher/get_police_stations',
            get_bank_branches:'voucher/get_bank_branches',
        }),

        getVouchers:function (page= 1){
            let maxLimit = `&limit=${this.limits}`;
            axios.get(`/pilgrim/voucher/get-voucher?page=${page}${maxLimit}`).then(res=>{
                this.vouchers = res.data.data
                this.totalData = res.data.msg
            })
        },

        closeModal:function (){
                this.errors={}
                this.formData={  "payment_type" : 'online' }
                this.loader=false
        },

        addVoucher: function () {
            this.loader=true
            axios.post('/pilgrim/voucher/store-voucher',this.formData).then(res=>{

                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                this.getVouchers();
                this.formData={  "payment_type" : 'online' };
                this.errors={};
                this.loader=false;
                this.$refs.closeModal.click();
            }).catch(err=>{
                this.errors=err.response.data.errors;
                console.log(err)
                this.loader=false
            })
        },
        loadDataDistrict:function (){
            this.get_districts()
        },
        loadPoliceStation:function (){
            let district_id=this.formData.district_id
            this.get_police_stations(district_id)
        },
        loadBankBranch:function (){
            let thana_id=this.formData.thana_id
            this.get_bank_branches(thana_id)
        },

    }
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
    width: 300px;
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
