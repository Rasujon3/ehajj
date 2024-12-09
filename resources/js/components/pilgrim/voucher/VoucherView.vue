
<template>
    <div class="card card-magenta border border-magenta">
        <div class="card-header">
            <div class="">
                <div class="row py-2">
                    <div class="col-md-4">
                        <b>Voucher Name :</b>{{voucherInfo.name}}
                    </div>
                    <div class="col-md-4">
                        <b>Voucher No :</b>
                        {{voucherInfo.tracking_no}}
                    </div>
                    <div class="col-md-4 button-alignment">
                        <div v-if="voucherPilgrims.length === 0">
                            <button v-if="voucherInfo.is_locked === 0 && voucherInfo.payment_status !== 12" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#updateVoucherNodal"
                                    @click="loadDataDistrict()">
                                <i class="fa fa-edit"></i>
                                <b class="text-dark ml-1">Edit</b>
                            </button>
                        </div>

                        <button v-if="voucherInfo.is_locked === 0 && voucherPilgrims.length > 0 && voucherInfo.payment_status !== 12 " :disabled="locker" class="btn btn-default btn-sm" @click="lockUnlock('lock')">
                            <div  v-if="locker" class="spinner-border" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            <i class="fas fa-solid fa-lock"></i>
                            Lock
                        </button>
                        <button v-if="voucherInfo.is_locked === 1 && voucherInfo.payment_status < 11 && !pdf_certificate_info && (voucherInfo.payment_type == 'online' && transction_info  && transction_info.status != 3? false : true)" :disabled="locker" class="btn btn-default btn-sm" @click="lockUnlock('unlock')">
                            <div  v-if="locker" class="spinner-border" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            <i class="fas fa-solid fa-unlock"></i>
                            Unlock
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-7">
                        <table class="voucher-detail-table table-responsive-sm table-responsive-md">
                            <tr>
                                <th>Voucher Name:</th>
                                <td>{{voucherInfo.name}}</td>
                            </tr>
                            <tr>
                                <th>Depositor Name:</th>
                                <td >{{voucherInfo.depositor_name}}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>{{voucherInfo.depositor_address}}</td>
                            </tr>

                            <tr>
                                <th>Management:</th>
                                <td>{{voucherInfo.is_govt}}</td>
                            </tr>
<!--                            <tr>-->
<!--                                <th>Package :</th>-->
<!--                                <td><code>{{voucherInfo.is_govt}}</code></td>-->
<!--                            </tr>-->
<!--                            <tr>-->
<!--                                <th>Package Amount:</th>-->
<!--                                <td><code>{voucherInfo.amount}} Taka</code></td>-->
<!--                            </tr>-->
<!--                            <tr>-->
<!--                                <th>Bank Name:</th>-->
<!--                                <td>{{voucherInfo.voucher_branch_name}}</td>-->
<!--                            </tr>-->
                             <tr v-if="voucherInfo.voucher_branch_name && voucherInfo.payment_type != 'online'">
                                <th>Branch Name:</th>
                                <td><code>{{voucherInfo.voucher_branch_name}}</code></td>
                            </tr>
<!--                            <tr>-->
<!--                                <th>Account Holder:</th>-->
<!--                                <td><code>Rasel Rana</code></td>-->
<!--                            </tr>-->
<!--                            <tr>-->
<!--                                <th>Account No:</th>-->
<!--                                <td><code>52555525244</code></td>-->
<!--                            </tr>-->

                        </table>
                </div>
                <div class="col-md-5">
                    <table class="voucher-detail-table">
                        <tr>
                            <th>Payment Type:</th>
                            <td>{{ voucherInfo.payment_type }}</td>
                        </tr>
                        <tr>
                            <th>Deposit Date:</th>
                            <td>{{formattedDate(voucherInfo.voucher_date)}}</td>
                        </tr>
                        <tr>
                            <th>Mobile No:</th>
                            <td>{{voucherInfo.depositor_mobile}}</td>
                        </tr>
                        <tr>
                            <th>Total Pilgrims:</th>
                            <td>{{voucherInfo.total_pilgrim}}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <table class="table table-sm table-bordered table-responsive-sm">
                    <thead>
                    <tr>
                        <th scope="col">Serial No</th>
                        <th scope="col">Tracking No</th>
                        <th scope="col">Name</th>
                        <th scope="col">NID/DOB/PASSPORT</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(voucherPilgrim,key) in voucherPilgrims" :key="voucherPilgrim.id">
                        <th>{{key+1}}</th>
                        <td>{{voucherPilgrim.tracking_no}}</td>
                        <td>{{voucherPilgrim.full_name_english}}</td>
                        <td v-if="voucherPilgrim.identity === 'NID'">{{ voucherPilgrim.national_id }}</td>
                        <td v-else-if="voucherPilgrim.identity === 'PASSPORT'">{{ voucherPilgrim.passport_no }}</td>
                        <td v-else-if="voucherPilgrim.identity === 'DOB'">{{ voucherPilgrim.birth_certificate }}</td>
                        <td v-else>N/A</td>
                        <td>{{voucherPilgrim.mobile}}</td>
                        <td>
                            <div v-if="singleDeleteLoader === voucherPilgrim.id" class="spinner-border mr-2" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            <span :disabled="singleDeleteLoader === voucherPilgrim.id" v-if="voucherInfo.is_locked === 0 && voucherInfo.payment_status !== 12" @click="singleDetele(voucherPilgrim.id)">
                                <i class="fas fa-solid fa-trash text-danger"></i>
                            </span>

                            <span v-else>N\A</span>
                        </td>
                    </tr>

                    </tbody>
                </table>

            </div>
            <div class="row">
                <div class="col-md-4">
                    <router-link :to="{name:'voucherList' }" class="btn btn-default">Close</router-link>
                </div>

                <div class="col-md-8 left-button">
                    <div v-if="voucherInfo.is_locked === 1 && voucherInfo.payment_status !== 12 ">
                        <router-link :to="{ path: '/voucher-payment-method-view/' + voucherInfo.tracking_no }" class="btn btn-default custom-bg ml-2" style="background-color: #4CAF50 !important;"
                                     v-if="(voucherInfo.payment_type == 'online') && (pdf_voucher_info ? pdf_voucher_info.status !== 1: true) && (pdf_certificate_info ? pdf_certificate_info.status !== 1: true) && (transction_info == null || transction_info.status === 3)">
                            Online Payment
                        </router-link>

                        <div class="btn btn-default custom-bg ml-2" style="background-color: #277ce1 !important;"
                             @click="offlinePayment"
                             v-if="(voucherInfo.payment_type != 'online') && pdf_voucher_info==null && (pdf_voucher_info ? pdf_voucher_info.status !== 1: true) && (transction_info == null || transction_info.status === 3) ">
                            <div  v-if="loader" class="spinner-border" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            জেনারেট ভাউচার
                        </div>
                        <div v-if="voucherInfo.payment_type == 'online'">

                            <div class="btn btn-default custom-bg ml-2"
                                  style="background-color: #00684D !important;"
                                  v-if="(transction_info ? transction_info.status === 0 : false) && transction_info.is_counter_payment === 1">
                                <a :href="`/pilgrim/voucher/counter-pay-voucher-request/${transction_info.tracking_no}/${transction_info.id}`"
                                   target="_blank" style="color: white !important;">
                                    Download Voucher
                                </a>
                            </div>

                            <div class="btn btn-default custom-bg ml-2" style="background-color: #0c72e3 !important;"
                                 @click="paymentVerify"
                                 v-if="(transction_info ? transction_info.status === 0: false)">
                                <div  v-if="loader && loader == 'verify'" class="spinner-border" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                Payment Verify
                            </div>
                            <div class="btn btn-default custom-bg ml-2" style="background-color: #15273b !important;"
                                 @click="paymentCancel"
                                 v-if="(transction_info ? transction_info.status === 0: false)">
                                <div  v-if="loader && loader == 'cancel'" class="spinner-border" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                Cancel Payment
                            </div>
                        </div>
                    </div>

                    <div class="btn btn-default custom-bg ml-2"
                         style="background-color: #1c3a5c !important;"
                         v-if="voucherInfo.is_locked === 1 &&
                         voucherInfo.payment_status === 12">
                        <div  v-if="loader" class="spinner-border" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                        <div @click="certificate"
                             v-if="(pdf_certificate_info ? pdf_certificate_info.status === 1: true)">
                            সনদ ডাউনলোড করুন
                        </div>
                        <div title="Please refresh this page for download certificate" v-else>
                            Certificate Generating...
                        </div>
                    </div>
                    <div class="btn btn-default custom-bg ml-2"
                         style="background-color: #00684D !important;"
                         v-if="voucherInfo.is_locked === 1 && voucherInfo.payment_type === 'online' &&
                         voucherInfo.payment_status === 12 &&
                         (transction_info ? transction_info.status === 1 : false)">
                        <a :href="`/pilgrim/voucher/counter-payslip-request/${transction_info.tracking_no}/${transction_info.id}`"
                           target="_blank" style="color: white !important;">
                            Download Payment Slip
                        </a>
                    </div>
                    <div class="btn btn-default custom-bg ml-2"
                         style="background-color: #1c3a5c !important;"
                         v-if="voucherInfo.payment_type != 'online' && voucherInfo.is_locked === 1 &&
                          voucherInfo.payment_status !== 12 && pdf_voucher_info">
                        <div  v-if="loader" class="spinner-border" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                        <div @click="pdfPrint"
                             v-if="(pdf_voucher_info ? pdf_voucher_info.status === 1: true)">
                            প্রিন্ট ভাউচার
                        </div>
                        <div title="Please refresh this page for pdf voucher" v-else>
                            Voucher Generating...
                        </div>
                    </div>
                    <button  v-if="voucherInfo.is_locked === 0 & voucherPilgrims.length > 0" :disabled="loader" type="button" @click="deleteVoucherPilgrim" class="btn btn-danger ml-1">
                        <div  v-if="loader" class="spinner-border" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                        <i class="fas fa-solid fa-minus mr-1"></i> সকল হজযাত্রী রিমুভ করুন
                    </button>
                    <button v-if="voucherInfo.is_locked === 0" @click="loadAllPilgrim" type="button" data-toggle="modal"
                            data-target="#staticBackdrop" class="btn btn-success ml-1">
                        <i class="fas fa-solid fa-plus"></i> হজযাত্রী যোগ করুন</button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="updateVoucherNodal" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div style="max-width: 850px" class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel"><strong>Update Voucher Details</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="updateVoucher">
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
                                            <input  v-model="formData.voucher_name" type="text" class="form-control form-control-md">
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
                                            <input  v-model="formData.address" type="text" class="form-control form-control-md">
                                            <span class="text-danger" v-if="errors['address']">{{ errors['address'][0] }}</span>
                                        </div>
                                    </div>
                                    <template v-if="formData.payment_type != 'online'">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label"><strong>জেলা </strong></label>
                                            <div class="col-sm-8">
                                                <Select2 class="custom-select2 p-1"
                                                         :options="districts"
                                                         v-model="formData.district_id"
                                                         placeholder="Search district"
                                                         :settings="{ width: '100%'}"
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
                                            <input v-model="formData.depositor_name" type="text" class="form-control form-control-md">
                                            <span class="text-danger" v-if="errors['depositor_name']">{{ errors['depositor_name'][0] }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label"><strong>মোবাইল নম্বার</strong></label>
                                        <div class="col-sm-8">
                                            <input v-model="formData.mobile_number" type="text" class="form-control form-control-md">
                                            <span class="text-danger" v-if="errors['mobile_number']">{{ errors['mobile_number'][0] }}</span>
                                        </div>
                                    </div>
                                    <template v-if="formData.payment_type != 'online'">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label"><strong> পুলিশ স্টেশন </strong></label>
                                            <div class="col-sm-8">
                                                <Select2 class="custom-select2 p-1"
                                                         :options="policeStations"
                                                         v-model="formData.thana_id"
                                                         placeholder="Search police station"
                                                         :settings="{ width: '100%'}"
                                                         @select="loadBankBranch"
                                                />
                                                <span class="text-danger" v-if="errors['thana_id']">{{ errors['thana_id'][0] }}</span>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-if="formData.payment_type != 'online'">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label"><strong>ব্যাংক ব্রাঞ্চ </strong></label>
                                            <div class="col-sm-8">
                                                <Select2 class="custom-select2 p-1"
                                                         :options="bankBranches"
                                                         v-model="formData.bank_branch_id"
                                                         placeholder="Search bank branch"
                                                         :settings="{ width: '100%'}"
                                                />
                                                <span class="text-danger" v-if="errors['bank_branch_id']">{{ errors['bank_branch_id'][0] }}</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="modal-header">
                            <button ref="closeEditModal" type="button" class="btn btn-secondary float-left" @click="closeModalEdit" data-dismiss="modal">বন্ধ</button>
                            <button :disabled="loader" type="submit" class="btn custom-bg">
                                <div v-if="loader" class="spinner-border" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                আপডেট
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel"><strong>হজযাত্রী যোগ করুন</strong></h5>
                        <button ref="closeModal" @close="closeModal" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="col-md-offset-8 col-md-3 float-right">
                                <input class="form-control" type="text" placeholder="Search..." v-model="search" >
                            </div>
                            <div class="table-responsive">
                                <table id="datatable" class="table vue-table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Serial No</th>
                                        <th>Tracking No</th>
                                        <th>Name</th>
                                        <th>NID/DOB/PASSPORT</th>
                                        <th>Add To Voucher</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(pilgrim,key) in pilgrims" :key="pilgrim.id">
                                        <td>{{ (page - 1) * limits + key + 1 }}</td>
                                        <td>{{pilgrim.tracking_no}}</td>
                                        <td>{{pilgrim.full_name_english}}</td>
                                        <td v-if="pilgrim.identity === 'NID'">{{ pilgrim.national_id }}</td>
                                        <td v-else-if="pilgrim.identity === 'PASSPORT'">{{ pilgrim.passport_no }}</td>
                                        <td v-else-if="pilgrim.identity === 'DOB'">{{ pilgrim.birth_certificate }}</td>
                                        <td v-else>N/A</td>
                                        <td>
                                            <input v-model="multipleselect.pilgrim_add_id" type="checkbox" :value="pilgrim.id">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="col-md-5"><br>
                                </div>
                                <div class="col-md-7 ">
                                    <pagination
                                        v-model="page"
                                        :records="laravelData.total ?? 0"
                                        :per-page="limits ?? 0"
                                        @paginate="loadAllPilgrim"
                                    />
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-header">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button :disabled="loader" @click="addPilgrim" v-if="multipleselect.pilgrim_add_id.length>0" type="button" class="btn custom-bg custom-bg">
                            <div v-if="loader" class="spinner-border" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            হজযাত্রী যোগ করুন
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions,mapState} from "vuex"
import {thai} from "../../../../../public/assets/plugins/pdfmake/pdfmake";
import Datepicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';
import moment from 'moment';

export default {
    name:'ViewVoucherDetail',
    components: {Datepicker},
    inject: ['toast'],
    data() {
        return {
            id:this.$route.params.id,
            loader:false,
            locker:false,
            message:null,
            multipleselect:{
                pilgrim_delete_id:[],
                pilgrim_add_id:[]
            },
            formData:{},
            errors:{},
            singleDeleteLoader:false,
            pdfGenerateData:{},
            tye:'18x415',
            textInputOptions: {
                format: 'dd/MMM/yyyy'
            },
            search: '',
            limits: 10,
            page: 1,

        }
    },
    computed:{
        ...mapState({
            voucherInfo:state=>state.voucher.voucherInfo,
            pdf_certificate_info:state=>state.voucher.pdf_certificate_info,
            pdf_voucher_info:state=>state.voucher.pdf_voucher_info,
            pdf_base_path:state=>state.voucher.pdf_base_path,
            transction_info:state=>state.voucher.payment_transction_info,
            pilgrims:state=>state.voucher.pilgrims,
            voucherPilgrims:state=>state.voucher.voucherPilgrims,
            districts:state=>state.voucher.districts,
            policeStations:state=>state.voucher.policeStations,
            bankBranches:state=>state.voucher.bankBranches,
            voucherEditInfo:state=>state.voucher.voucherEditInfo,
            laravelData: state => state.voucher.laravelData,
        })
    },

    mounted(){
        this.get_voucher_detail(this.id);
        this.loadVoucherDetail();
    },

    methods: {
        ...mapActions({
            get_voucher_detail:'voucher/get_voucher_detail',
            get_pilgrims:'voucher/get_pilgrims',
            get_districts:'voucher/get_districts',
            get_police_stations:'voucher/get_police_stations',
            get_bank_branches:'voucher/get_bank_branches',
            get_voucher_edit_info:'voucher/get_voucher_edit_info',
        }),

        updateVoucher:function (){

            this.loader=true
            axios.post('/pilgrim/voucher/update-voucher',this.formData).then(res=>{
                if(res.data.status == 200){
                    this.$refs.closeEditModal.click();
                }
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                 this.errors={};
                 this.loader=false;
                this.get_voucher_detail(this.id);

            }).catch(err=>{
                this.errors=err.response.data.errors;
                console.log(err)
                this.loader=false
            })
        },

        loadDataDistrict:function (){
            this.get_districts()
            this.loadPoliceStation()
            this.loadBankBranch()

        },

        loadVoucherDetail: function (){
           axios.get('/pilgrim/voucher/get-voucher_edit_info/'+this.id).then(res=>{

               let data=(res.data.data.groupPaymentInfo);
               this.formData.voucher_name=data.name;
               this.formData.payment_type=data.payment_type;
               this.formData.depositor_name=data.depositor_name;
               this.formData.mobile_number=data.depositor_mobile;
               this.formData.deposite_date=data.voucher_date;
               this.formData.district_id=data.district_id;
               this.formData.thana_id=data.thana_id;
               this.formData.bank_branch_id=data.voucher_branch_id;
               this.formData.address=data.depositor_address;
               this.formData.group_payment_id=this.id;

            })
        },

        loadPoliceStation:function (){
            let district_id=this.formData.district_id
            this.get_police_stations(district_id)
        },

        loadBankBranch:function (event){
            let thana_id=this.formData.thana_id
            this.get_bank_branches(thana_id)
        },

        deleteVoucherPilgrim:function (){
            this.loader=true;
            axios.post('/pilgrim/voucher/delete-all-pilgrim', {group_payment_id:this.id}).then(res=>{
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                this.get_voucher_detail(this.id)
                this.loader=false;
            }).catch(err=>{
                console.log(err)
                this.locker=false;
            })
        },

        lockUnlock:function (type){
            this.locker=true
            let metaData={};
            metaData.groupPaymentId=this.id;
            metaData.type=type
            axios.post('/pilgrim/voucher/lock-unlock',metaData).then(res=>{
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                this.get_voucher_detail(this.id)
                this.locker=false;
            }).catch(err=>{
                console.log(err)
                this.locker=false;
            })
        },

        singleDetele:function (pilgrim_id){
            this.singleDeleteLoader = pilgrim_id
            let meteData={}
            meteData.operationType = 'remove-pilgrim-from-voucher';
            meteData.groupPaymentId = this.id;
            meteData.pilgrimId = pilgrim_id;
            axios.post('/pilgrim/voucher/add-delete-action',meteData).then(res=>{
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                meteData={};
                this.get_voucher_detail(this.id);
                this.singleDeleteLoader = false
            }).catch(err=>{
                console.log(err)
                this.singleDeleteLoader = false
            })
        },

        addPilgrim:function (){
            this.loader=true;
            let meteData={};
            meteData.operationType = 'add-pilgrim';
            meteData.groupPaymentId = this.id;
            meteData.pilgrimId = this.multipleselect.pilgrim_add_id;
            axios.post('/pilgrim/voucher/add-delete-action',meteData).then(res=>{
                this.$refs.closeModal.click();
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                meteData={};
                this.get_voucher_detail(this.id);
                this.loader=false;
                this.multipleselect.pilgrim_add_id=[];

            }).catch(err=>{
                console.log(err)
                this.loader=false
                this.multipleselect.pilgrim_add_id=[];
            })
        },

        loadAllPilgrim:function (){
            this.get_pilgrims({
                id: this.id,
                search: this.search,
                limits: this.limits,
                page: this.page
            })
        },

        closeModal:function (){
            this.multipleselect.pilgrim_add_id=[]
            this.errors={};
            this.formData={}
        },
        closeModalEdit:function (){
            this.errors={};
        },

        certificate:function (){
            this.loader=true
            if(this.pdf_certificate_info.file_path){
                this.loader=false
                const url = this.pdf_base_path + this.pdf_certificate_info.file_path ;
                window.open(url, '_blank');
            }else{
                this.loader=false
                this.toast.fire({
                    title: "এই ভাউচারটির  PDF তৈরী হতে একটু সময় নিচ্ছে । অনুগ্রহ করে, পেজ টি রিলোড করুন। [PDF3433]",
                });
            }
        },
        pdfPrint:function (){
            this.loader=true
            if(this.pdf_voucher_info.file_path){
                this.loader=false
                const url2 = this.pdf_base_path + this.pdf_voucher_info.file_path ;
                window.open(url2, '_blank');
            }else{
                this.loader=false
                this.toast.fire({
                    title: "এই ভাউচারটির  PDF তৈরী হতে একটু সময় নিচ্ছে । অনুগ্রহ করে, পেজ টি রিলোড করুন। [PDF3434]",
                });
            }
        },
        offlinePayment:function (){
            this.loader=true
            if(this.voucherInfo.tracking_no){
                this.loader=true;
                let pdfGenerateData={};
                pdfGenerateData.group_payment_id = this.id;
                pdfGenerateData.pdf_type = 2;
                pdfGenerateData.pdf_flag = 1;
                axios.post('/pilgrim/voucher/pdf-generate-request',pdfGenerateData).then(res=>{
                    this.toast.fire({
                        icon: res.data.response,
                        title: res.data.msg
                    });
                    this.loader=false;
                    window.location.reload();
                }).catch(err=>{
                    this.loader=false
                    this.toast.fire({
                        title: "এই ভাউচারটির  PDF তৈরী হতে সমস্যা হচ্ছে। অনুগ্রহ করে, পেজ টি রিলোড করুন।[PDF3434]"
                    });
                })
            }else{
                this.loader=false
                this.toast.fire({
                    title: "Tracking Number Not Found!",
                });
            }

        },

        paymentVerify:function (){
            this.loader=true
            if(this.voucherInfo.tracking_no){
                this.loader='verify';
                let payVerify={};
                payVerify.tracking_no = this.voucherInfo.tracking_no;
                payVerify.wallet_reg_key = this.transction_info.wallet_reg_key;
                payVerify.pay_unique_id = this.transction_info.pay_unique_id;
                axios.post('/pilgrim/voucher/pay-verify-request',payVerify).then(res=>{
                    this.toast.fire({
                        icon: res.data.response,
                        title: res.data.msg
                    });
                    this.loader=false;
                    if(res.data.status == 200){
                        window.location.reload();
                    }
                }).catch(err=>{
                    this.loader=false
                    this.toast.fire({
                        title: "এই Payment হতে সমস্যা হচ্ছে। অনুগ্রহ করে, পেজ টি রিলোড করুন।[PDF-884]"
                    });
                })
            }else{
                this.loader=false
                this.toast.fire({
                    title: "Tracking Number Not Found!",
                });
            }

        },
        paymentCancel:function (){
            this.loader=true
            if(this.voucherInfo.tracking_no){
                this.loader='cancel';
                let payVerify={};
                payVerify.tracking_no = this.voucherInfo.tracking_no;
                payVerify.wallet_reg_key = this.transction_info.wallet_reg_key;
                payVerify.pay_unique_id = this.transction_info.pay_unique_id;
                axios.post('/pilgrim/voucher/pay-cancel-request',payVerify).then(res=>{
                    this.toast.fire({
                        icon: res.data.response,
                        title: res.data.msg
                    });
                    this.loader=false;
                    if(res.data.status == 200){
                        window.location.reload();
                    }
                }).catch(err=>{
                    this.loader=false
                    this.toast.fire({
                        title: "এই Payment হতে সমস্যা হচ্ছে। অনুগ্রহ করে, পেজ টি রিলোড করুন।[PDF-884]"
                    });
                })
            }else{
                this.loader=false
                this.toast.fire({
                    title: "Tracking Number Not Found!",
                });
            }

        },
        counterPaymentVoucher:function (){
            this.loader=true
            if(this.voucherInfo.tracking_no){
                this.loader='cancel';
                let counterPayVerify={};
                counterPayVerify.tracking_no = this.voucherInfo.tracking_no;
                counterPayVerify.wallet_reg_key = this.transction_info.wallet_reg_key;
                counterPayVerify.pay_unique_id = this.transction_info.pay_unique_id;
                axios.post('/pilgrim/voucher/counter-pay-voucher-request',counterPayVerify).then(res=>{
                    this.toast.fire({
                        icon: res.data.response,
                        title: res.data.msg
                    });
                    this.loader=false;
                    if(res.data.status == 200){
                        window.location.reload();
                    }
                }).catch(err=>{
                    this.loader=false
                    this.toast.fire({
                        title: "এই Payment হতে সমস্যা হচ্ছে। অনুগ্রহ করে, পেজ টি রিলোড করুন।[PDF-899]"
                    });
                })
            }else{
                this.loader=false
                this.toast.fire({
                    title: "Tracking Number Not Found!",
                });
            }

        },

        formattedDate: function(date)
        {
            return moment(date).format('DD-MMM-YYYY');
        }
    },
    watch: {
        search(newSearch) {
            this.loadAllPilgrim();
        }
    }
}

</script>

<style scoped>

.voucher-detail-table tr th{
    text-align: end;
}
.voucher-detail-table tr td{
    padding-left: 12px;
}
.left-button{
    text-align: end;
}
.custom-bg{
    background: #00684D !important;
    color: white;
}
.spinner-border{
    width: 16px;
    height: 16px;
}
.button-alignment button{
    float: right;
    margin-left: 2px;
}
</style>

<style>
.select2-container .select2-selection--single {
    height: 39px;
}
.select2-container .select2-selection--single .select2-selection__arrow {
    top: 7px;
}
.select2-container .select2-selection--single .select2-selection__rendered{
    padding-left: 0px;
}

</style>
