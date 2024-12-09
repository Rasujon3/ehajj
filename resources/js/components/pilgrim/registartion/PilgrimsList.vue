<script setup>
    import {ref, onMounted, watch} from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';
    import { useToast } from 'vue-toast-notification';

    const router = useRouter();
    const toast = useToast();

    const laravelData = ref({data: [] ,total:0});
    const search = ref('');
    const limits = ref(10);
    const page = ref(1);
    const formData = ref({
        tracking_no: '',
    });
    const loader = ref(false);

    const closeModalButton = ref(null); // Reference to the close button

    const getResults = async (page = 1) => {
        const maxLimit = `&limit=${limits.value}`;
        const isSearch = search.value ? `&search=${search.value}` : '';
        try {
            const response = await axios.get(`/registration/get-list?page=${page}${isSearch}${maxLimit}`);
            if (response.data?.responseCode === -1) {
                laravelData.value.data = [];
                laravelData.value.total = 0;
            }
            if (response.data?.responseCode === 1) {
                if (response.data?.data?.data?.length > 0) {
                    laravelData.value.data = [...response.data?.data?.data];
                    laravelData.value.total = response.data?.data?.totalCount;
                } else {
                    laravelData.value.data = [];
                    laravelData.value.total = 0;
                }
            }
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    const regPilgrimSearch = async () => {
        const form = new FormData();
        form.append('tracking_no', formData.value.tracking_no);
        loader.value = true;
        try {
            const res = await axios.post('/registration/pilgrim-search', form, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            loader.value = false;
            toast.open({
                message: res.data.msg,
                type: res.data.response,
                position: 'top-right',
                duration: 3000,
            });
            if(res.data.responseCode !== 1) {
                return false;
            }
            if (res.data.status === true) {
                formData.value.tracking_no = '';
                closeModal();
                //router.push({ path: '/reg-pilgrim-view/'+ res.data.data.pilgrim_id });
                router.push({ path: '/reg-pilgrim-edit/'+ res.data.data.pilgrim_id +'/'+ res.data.data.pilgrim_tracking_no });
            }
        } catch (error) {
            loader.value = false;
            console.error("Error fetching data [SRNV:001]", error);
        }
    };

    const closeModal = () => {
        formData.value.tracking_no = '';
        loader.value = false;
        // Trigger the close button's click event
        if (closeModalButton.value) {
            closeModalButton.value.click();
        }
    };

    // const keymonitor = () => {
    //     getResults(1);
    // };

    // const limit = () => {
    //     getResults(1);
    // };

    onMounted(() => {
        getResults();
    });

    watch([search, limits], () => {
        getResults(1);
    });
    watch(page, (newPage) => {
        getResults(newPage);
    });
</script>

<template>
    <div class="card card-magenta border border-magenta">
        <div class="card-header">
            <div class="float-left">
                <h5><strong><strong>সরকারি মাধ্যমে হজযাত্রীর নিবন্ধন</strong></strong></h5>
            </div>
            <div class="float-right">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#searchNewRegistration">
                    <i class="fa fa-plus text-dark"></i>  <b class="text-dark"> নতুন হজযাত্রী যুক্ত করুন </b>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="col-md-1 float-left">
                <select class="form-control col-md-12" v-model="limits" >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="col-md-offset-8 col-md-3 float-right">
                <input class="form-control" type="text" placeholder="Search..." v-model="search" >
            </div>
            <br>
            <br>

            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="datatable" class="table vue-table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Tracking ID</th>
                                <th>Name</th>
                                <th>Voucher ID</th>
                                <th>Passport Info</th>
                                <th>Status</th>
                                <th>Package Migration</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="pilgrim in laravelData.data" :key="pilgrim.id">
                                <td>
                                    <template v-if="pilgrim.serial_no == 0 && pilgrim.payment_status != -12">
                                        Draft
                                    </template>
                                    <template v-else-if="pilgrim.payment_status == -12">
                                        Refunded
                                    </template>
                                    <template v-else>
                                        {{ pilgrim.is_govt == 'Private' ? 'NG-' : 'G-' }}{{ pilgrim.serial_no }}
                                    </template>
                                </td>
                                <td>{{ pilgrim.tracking_no }}</td>
                                <td>{{ pilgrim.full_name_english }}
                                    ({{ new Date().getFullYear() - new Date(pilgrim.birth_date).getFullYear() }}Y)
                                </td>
                                <td><strong>Reg: </strong>{{ pilgrim.reg_voucher_tracking_no ? pilgrim.reg_voucher_tracking_no : '' }}</td>
                                <td>
                                    <strong>Pass No: </strong>{{ pilgrim.passport_no ? pilgrim.passport_no : '' }} <br>
                                    <strong>Exp Date: </strong>{{ pilgrim.pass_exp_date ? (pilgrim.pass_exp_date).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '' }}
                                </td>
                                <td>
                                    <template v-if="pilgrim.is_imported == 1 && pilgrim.replace_request_id > 0">
                                        Replaced
                                    </template>
                                    <template v-else-if="pilgrim.is_imported == 1 && pilgrim.replace_request_id == 0">
                                        Imported
                                    </template>
                                    <template v-else-if="pilgrim.pilgrim_listing_id > 0">
                                            <span style="padding:5px 0px;font-weight:bold;" class="label label-success"
                                                  data-toggle="tooltip" data-placement="left"
                                                  :title="'Listed (' + pilgrim.caption + ')'">
                                                Listed
                                            </span>
                                    </template>
                                    <template v-else>
                                        {{ pilgrim.payment_status_name }}
                                    </template>
                                </td>
                                <td>{{ pilgrim.re_reg_payment_status == 12 ? 'Yes' : 'No' }}</td>
                                <td>
                                    <router-link :to="{ name: 'ViewRegPilgrim', params: { id: pilgrim.id } }"
                                                 class="btn btn-xs btn-primary">
                                        <i class="fa fa-file"></i> Open
                                    </router-link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-5"><br> </div>
                <div class="col-md-7 ">
                    <pagination v-model="page" :records="laravelData.total ?? 0" :per-page="limits ?? 0" @paginate="getResults"/>
                </div>
            </div>
        </div>
    </div>

    <!-- pre reg modal search -->
    <div class="modal fade" id="searchNewRegistration" data-backdrop="static" data-keyboard="false" aria-labelledby="searchNewRegistrationLabel" aria-hidden="true">
            <div style="max-width: 850px" class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="searchNewRegistrationLabel">Pilgrim Registration</h4>
                        <button ref="closeModalButton" type="button" class="btn btn-secondary" @click="closeModal" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="regPilgrimSearch">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12 form-group d-flex">
                                    <label class="col-sm-5 col-form-label"><strong>Tracking No:</strong></label>
                                    <div class="col-sm-6">
                                        <input v-model="formData.tracking_no" type="text" class="form-control form-control-md" placeholder="Tracking No">
                                    </div>
                                </div>

                                <div class="container">
                                    <span class="modal-text">
                                        অনুগ্রহ করে হজযাত্রীর জন্য যাবতীয় তথ্য সঠিকভাবে বানান অনুসরণ করে পূরণ করুন। <br/>
                                        কোন তথ্যে ভুল থাকলে তা পরবর্তীকালে সংশোধন করা সম্ভব নাও হতে পারে।
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary custom-bg" :disabled="loader" type="submit">
                                <div v-if="loader" class="spinner-border" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                <i class="fa fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

</template>
<style scoped>
.modal-text {
    color: #8a6d3b !important;
    text-align: center;
}
</style>
