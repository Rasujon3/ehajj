
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
                            <button v-if="voucherInfo.is_locked === 0 && voucherInfo.reg_payment_status !== 12" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#updateVoucherNodal"
                                    @click="loadDataPackage()">
                                <i class="fa fa-edit"></i>
                                <b class="text-dark ml-1">Edit</b>
                            </button>
                        </div>

                        <button v-if="voucherInfo.is_locked === 0 && voucherPilgrims.length > 0 && voucherInfo.reg_payment_status !== 12 " :disabled="locker" class="btn btn-default btn-sm" @click="lockUnlock('lock')">
                            <div  v-if="locker" class="spinner-border" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            <i class="fas fa-solid fa-lock"></i>
                            Lock
                        </button>
                        <button v-if="voucherInfo.is_locked === 1 && voucherInfo.reg_payment_status < 11" :disabled="locker" class="btn btn-default btn-sm" @click="lockUnlock('unlock')">
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
                        <table class="voucher-detail-table">
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
                            <!--  <tr>
                                 <th>Ref Voucher No:</th>
                                 <td>{{voucherInfo.ref_voucher_no}}</td>
                             </tr>-->
                             <tr>
                                 <th>Package:</th>
                                 <td>{{regPaymentInfo.package_caption}}</td>
                             </tr>
                             <tr>
                                 <th>Package Amount:</th>
                                 <td>{{regPaymentInfo.package_amount}} TK</td>
                             </tr>
                             <tr>
                                 <th>Bank Name:</th>
                                 <td><code>{{regPaymentInfo.bank_name}}</code></td>
                             </tr>
                         </table>
                 </div>
                 <div class="col-md-5">
                     <table class="voucher-detail-table">
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
                 <table class="table table-sm table-bordered table-responsive-sm table-responsive-md">
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
                     <router-link :to="{name:'RegVoucherList' }" class="btn btn-default">Close</router-link>
                 </div>

                 <div class="col-md-8 left-button">

                     <div v-if="voucherInfo.is_locked === 1 && voucherInfo.reg_payment_status !== 12 && !pdf_info">
                         <div @click="generateVoucher" class="btn btn-default custom-bg ml-2" >
                             <div  v-if="loader" class="spinner-border" role="status">
                                 <span class="visually-hidden"></span>
                             </div>
                             Generate Voucher
                         </div>
                     </div>

                     <div v-if="voucherInfo.is_locked === 1 && voucherInfo.reg_payment_status !== 12 && (pdf_info && pdf_info.pin === 0)">
                         <div @click="RegenerateVoucher" class="btn btn-default custom-bg ml-2">
                             <div  v-if="loader" class="spinner-border" role="status">
                                 <span class="visually-hidden"></span>
                             </div>
                             Re Generate Voucher
                         </div>
                     </div>

                     <div class="btn btn-default custom-bg ml-2" v-if="voucherInfo.is_locked === 1 && pdf_info != null && pdf_info.pin > 0 && pdf_info.status === 1 && voucherInfo.reg_payment_status !== 12 ">
                         <div @click="pdfPrint">
                             <div  v-if="loader" class="spinner-border" role="status">
                                 <span class="visually-hidden"></span>
                             </div>
                             Print
                         </div>
                     </div>

                     <div class="btn btn-default custom-bg ml-2" v-if="pdf_certificate != null && pdf_certificate.status === 1 && voucherInfo.reg_payment_status === 12 ">
                         <div @click="downloadCertificate">
                             <div  v-if="loader" class="spinner-border" role="status">
                                 <span class="visually-hidden"></span>
                             </div>
                             Download Certificate
                         </div>
                     </div>

                     <button  v-if="voucherInfo.is_locked === 0 & voucherPilgrims.length > 0" :disabled="loader" type="button" @click="deleteVoucherPilgrim" class="btn btn-danger ml-1">
                         <div  v-if="loader" class="spinner-border" role="status">
                             <span class="visually-hidden"></span>
                         </div>
                         <i class="fas fa-solid fa-minus mr-1"></i> Remove All Pilgrims
                     </button>

                  <!--  <button v-if="voucherInfo.is_locked === 0" @click="loadAllPilgrim" type="button" data-toggle="modal"
                             data-target="#staticBackdropSerial" class="btn btn-info ml-1">
                         <i class="fas fa-solid fa-plus"></i> Add Pilgrim By Serial</button> -->

                    <button v-if="voucherInfo.is_locked === 0" @click="loadAllPilgrim" type="button" data-toggle="modal"
                            data-target="#staticBackdrop" class="btn btn-success ml-1">
                        <i class="fas fa-solid fa-plus"></i> Add Pilgrim
                    </button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="staticBackdropSerial" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Pilgrim by Serial</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="addPilgrimBySerialNo">
                    <div class="modal-body">
                            <div class="form-group">
                                <label>Pilgrim Serial No</label>
                                <input type="text" required="" v-model="tracking_no" class="form-control" placeholder="Serial Number">
                            </div>
                    </div>
                    <div class="modal-header">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-primary" :disabled="loader">
                            <div  v-if="loader" class="spinner-border" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            Add Pilgrim By Serial
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="updateVoucherNodal" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div style="max-width: 1200px" class="modal-dialog modal-lg">
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
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label"><strong>Package Type</strong></label>
                                        <div class="col-sm-8">
                                            <Select2 class="custom-select2 input-sm select2Field"
                                                     :options="category"
                                                     v-model="package_category"
                                                     placeholder="Package Type"
                                                     :settings="{ width: '100%'}"
                                            />
                                            <span class="text-danger" v-if="errors['category']">{{ errors['category'][0] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row" v-if="isPrimaryPackage == '0'">
                                        <label class="col-sm-6 col-form-label"><strong>হজযাত্রী কি শর্ট প্যাকেজে যেতে চান? :</strong></label>
                                        <div class="col-sm-4">
                                            <label class="radio-inline p-2">
                                                <input type="radio" name="is_short_package" value="1" v-model="is_short_package"> Yes
                                            </label>
                                            <label class="radio-inline p-2">
                                                <input type="radio" name="is_short_package" value="0" v-model="is_short_package"> No
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
                                                     placeholder="Search hajj package"
                                                     :settings="{ width: '100%'}"
                                            />
                                            <span class="text-danger" v-if="errors['hajj_pcz_id']">{{ errors['hajj_pcz_id'][0] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- <div class="form-group row">
                                        <label class="col-sm-4 col-form-label"><strong>Voucher Name</strong></label>
                                        <div class="col-sm-8">
                                            <input v-model="formData.voucher_name" type="text" class="form-control form-control-md" placeholder="Voucher Name">
                                            <span class="text-danger" v-if="errors['voucher_name']">{{ errors['voucher_name'][0] }}</span>
                                        </div>
                                    </div> -->
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
                        </div>

                            <div class="row">
                                <!-- <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label"><strong>Address</strong></label>
                                        <div class="col-sm-8">
                                            <input v-model="formData.address" type="text" class="form-control form-control-md" placeholder="Address">
                                            <span class="text-danger" v-if="errors['address']">{{ errors['address'][0] }}</span>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="col-md-6" style="display: none">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label"><strong>Another voucher or unit</strong></label>
                                        <div class="col-sm-8">
                                            <select v-model="formData.ref_voucher_no"  class="form-control">
                                                <option value="">Select One</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                            <span class="text-danger" v-if="errors['other_voucher_unit']">{{ errors['other_voucher_unit'][0] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="modal-header">
                            <button ref="closeEditModal" type="button" class="btn btn-secondary" @click="closeModalEdit" data-dismiss="modal">
                                Close
                            </button>
                            <button :disabled="loader" type="submit" class="btn custom-bg">
                                <div v-if="loader" class="spinner-border" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                Update
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
                        <h5 class="modal-title" id="staticBackdropLabel"><strong>Add Pilgrim to voucher</strong></h5>
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
                                        <th>Name</th>
                                        <th>Serial Number</th>
                                        <th>Tracking No</th>
                                        <th>NID/DOB/PASSPORT</th>
                                        <th>Add To Voucher</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(pilgrim,key) in pilgrims" :key="pilgrim.id">
                                        <td>{{pilgrim.full_name_english}}</td>
                                        <td>{{pilgrim.serial_no}}</td>
                                        <td>{{pilgrim.tracking_no}}</td>
                                        <td>{{ pilgrim.national_id }}</td>
                                        <td>
                                            <input v-model="multipleselect.pilgrim_add_id" type="checkbox" :value="pilgrim.id">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="col-md-5"><br>
                                </div>
                                <div class="col-md-7">
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">Close</button>

                        <button :disabled="loader" @click="addPilgrim" v-if="multipleselect.pilgrim_add_id.length>0" type="button" class="btn custom-bg custom-bg">
                            <div v-if="loader" class="spinner-border" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            Add Pilgrim
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
    name:'RegViewVoucherDetail',
    components: {Datepicker},
    inject: ['toast'],
    data() {
        return {
            id:this.$route.params.id,
            loader:false,
            locker:false,
            message:null,
            tracking_no:'',
            multipleselect:{
                pilgrim_delete_id:[],
                pilgrim_add_id:[]
            },
            formData:{},
            errors:{},
            singleDeleteLoader:false,
            pdfGenerateData:{},
            textInputOptions: {
                format: 'dd/MMM/yyyy'
            },
            base_path:'',
            pdf_info:{},
            pdf_certificate:{},
            search: '',
            limits: 10,
            page: 1,
            is_short_package: '0',
            package_category: '',
            isPrimaryPackage: '0',
        }
    },
    computed:{
        ...mapState({
            voucherInfo:state=>state.RegVoucher.voucherInfo,
            regPaymentInfo:state=>state.RegVoucher.regPaymentInfo,
            pdf_certificate_info:state=>state.RegVoucher.pdf_certificate_info,
            pdf_voucher_info:state=>state.RegVoucher.pdf_voucher_info,
            pilgrims:state=>state.RegVoucher.pilgrims,
            voucherPilgrims:state=>state.RegVoucher.voucherPilgrims,
            voucherEditInfo:state=>state.RegVoucher.voucherEditInfo,
            packages:state=>state.RegVoucher.packages,
            pdf_base_path:state=>state.RegVoucher.pdf_base_path,
            laravelData: state => state.RegVoucher.laravelData,
            category: state => state.RegVoucher.category,
        })
    },

    mounted(){
        this.get_voucher_detail(this.id);
        this.loadVoucherDetail();
    },

    methods: {
        ...mapActions({
            get_voucher_detail:'RegVoucher/get_voucher_detail',
            get_pilgrims:'RegVoucher/get_pilgrims',
            get_packages:'RegVoucher/get_packages',
            get_voucher_edit_info:'RegVoucher/get_voucher_edit_info',
        }),

        generateVoucher: function (){
            this.loader=true
            let metaData={};
            metaData.pdf_flag=1;
            metaData.pdf_type=6;
            metaData.is_first_call=1;
            metaData.reg_voucher_id=this.id;
            axios.post('/registration/reg-voucher/pdf-generate-request',metaData).then(res=>{
                this.loadVoucherDetail();
                if (res.data.data.responseCode === 1){
                    this.loadVoucherDetail();
                    this.toast.fire({
                        icon: res.data.response,
                        title: res.data.msg
                    });
                    // Delay the reload by 30 seconds (30000 milliseconds)
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);

                }else{
                    this.toast.fire({
                        icon: res.data.response,
                        title: res.data.msg
                    });
                }
                this.loader=false
            }).catch(err=>{
                this.errors=err.response.data.errors;
                this.loader=false
            })
        },

        RegenerateVoucher: function (){
            this.loader=true
            let metaData={};
            metaData.pdf_flag=1;
            metaData.pdf_type=6;
            metaData.is_first_call=1;
            metaData.reg_voucher_id=this.id;
            axios.post('/registration/reg-voucher/pdf-generate-request',metaData).then(res=>{
                this.loadVoucherDetail();
                this.loader=false
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }).catch(err=>{
                this.errors=err.response.data.errors;
                this.loader=false
            })
        },

        addPilgrimBySerialNo: function (){
            this.loader=true
            axios.post('/registration/reg-voucher/add-pilgrim-by-serial',{
                'tracking_no':this.tracking_no
            }).then(res=>{
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                this.loader=false;

            }).catch(err=>{
                this.errors=err.response.data.errors;
                this.loader=false
            })
        },

        updateVoucher:function (){

            this.loader=true
            axios.post('/registration/reg-voucher/update-voucher',this.formData).then(res=>{
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
                this.loader=false
            })
        },

        loadDataPackage:function () {
            this.get_packages({
                is_short_package: this.is_short_package,
                category: this.package_category,
            })
        },

        loadVoucherDetail: function (){
           axios.get('/registration/reg-voucher/get-voucher-detail/'+this.id).then(res=>{
                let data=res.data.data.voucherInfo;
                const regPaymentInfo = res.data?.data?.regPaymentInfo;
                this.base_path =res.data.data.pdf_base_path;
                this.pdf_certificate =res.data.data.paid_pdf_info;
                this.pdf_info =res.data.data.pdf_info;
                this.formData.voucher_name=data.name;
                this.formData.depositor_name=data.depositor_name;
                this.formData.mobile_number=data.depositor_mobile;
                this.formData.deposit_date=data.voucher_date;
                this.formData.hajj_pcz_id=data.hajj_package_id;
                this.formData.address=data.depositor_address;
                this.formData.total_pilgrim=data.total_pilgrim;
                //this.formData.ref_voucher_no=data.ref_voucher_no;
                this.formData.reg_voucher_id=this.id;
                this.is_short_package = regPaymentInfo.is_short_package;
                this.package_category = regPaymentInfo.package_category;
                this.isPrimaryPackage = regPaymentInfo.is_primary_package ?? '';

            })
        },

        deleteVoucherPilgrim:function (){
            this.loader=true;
            axios.post('/registration/reg-voucher/delete-all-pilgrim', {reg_voucher_id:this.id}).then(res=>{
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                this.get_voucher_detail(this.id)
                this.loader=false;
            }).catch(err=>{
                this.locker=false;
            })
        },

        lockUnlock:function (type){
            this.locker=true
            let metaData={};
            metaData.reg_voucher_id=this.id;
            metaData.type=type
            axios.post('/registration/reg-voucher/lock-unlock',metaData).then(res=>{
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                this.get_voucher_detail(this.id)
                this.locker=false;
                window.location.reload();
            }).catch(err=>{
                this.locker=false;
            })
        },

        singleDetele:function (pilgrim_id){
            this.singleDeleteLoader = pilgrim_id
            let meteData={};
            meteData.request_type = 'remove-pilgrim-from-voucher';
            meteData.reg_voucher_id = this.id;
            meteData.pilgrim_id = pilgrim_id;

            axios.post('/registration/reg-voucher/add-delete-action',meteData).then(res=>{
                this.toast.fire({
                    icon: res.data.response,
                    title: res.data.msg
                });
                meteData={};
                this.get_voucher_detail(this.id);
                this.singleDeleteLoader = false
            }).catch(err=>{
                this.singleDeleteLoader = false
            })
        },

        addPilgrim:function (){
            this.loader=true;
            let meteData={};
            meteData.request_type = 'add-pilgrim';
            meteData.reg_voucher_id = this.id;
            meteData.pilgrims_id = this.multipleselect.pilgrim_add_id;
            axios.post('/registration/reg-voucher/add-delete-action',meteData).then(res=>{
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
                this.loader=false
                this.multipleselect.pilgrim_add_id=[];
            })
        },

        /*
        loadAllPilgrim:function (){
            this.get_pilgrims()
        },
        */
        loadAllPilgrim() {
            this.get_pilgrims({
                search: this.search,
                limits: this.limits,
                page: this.page
            });
        },

        closeModal:function (){
            this.multipleselect.pilgrim_add_id=[]
            this.errors={};
            this.formData={}
        },
        closeModalEdit:function (){
            this.errors={};
        },

        pdfPrint:function (){
            this.loader=true
            if(this.pdf_voucher_info.file_path){
                this.loader=false
                const url2 = this.base_path + this.pdf_info.file_path ;
                window.open(url2, '_blank');
            }else{
                this.loader=false
                this.toast.fire({
                    title: "এই ভাউচারটির  PDF তৈরী হতে একটু সময় নিচ্ছে । অনুগ্রহ করে, পেজ টি রিলোড করুন। [PDF3434]",
                });
            }
        },
        downloadCertificate:function (){
            this.loader=true
            if(this.pdf_certificate.file_path){
                this.loader=false
                const url2 = this.base_path + this.pdf_certificate.file_path ;
                window.open(url2, '_blank');
            }else{
                this.loader=false
                this.toast.fire({
                    title: "এই সনদ টি তৈরী হতে একটু সময় নিচ্ছে । অনুগ্রহ করে, পেজ টি রিলোড করুন। [PDF3437]",
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
        },
        is_short_package(newVal) {
            this.loadDataPackage();
        },
        package_category(newVal) {
            const selectedCategory = this.category.find(
                item => item.id === newVal
            );
            if (selectedCategory) {
                this.isPrimaryPackage = selectedCategory.is_primary_package;
                this.loadDataPackage();
            }
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
