<script setup>
    import {ref, onMounted, watch} from 'vue';
    import axios from 'axios';

    const laravelData = ref({data: [] ,total:0});
    const search = ref('');
    const limits = ref(10);
    const page = ref(1);

    const getResults = async (page = 1) => {
        const maxLimit = `&limit=${limits.value}`;
        const isSearch = search.value ? `&search=${search.value}` : '';
        try {
            const response = await axios.get(`/pilgrim/pre-reg/get-list?page=${page}${isSearch}${maxLimit}`);
            if (response.data?.responseCode === 1) {
                if (response.data?.data?.data?.length > 0) {
                    laravelData.value.data = [...response.data?.data?.data];
                    laravelData.value.total = response.data?.data?.totalCount;
                } else {
                    laravelData.value.data = [];
                    laravelData.value.total = 0;
                }
            } else {
                laravelData.value.data = [];
                laravelData.value.total = 0;
            }
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    // const keymonitor = () => {
    //     getResults(1);
    // };

    // const limit = () => {
    //     getResults(1);
    // };

    onMounted(() => {
        getResults(1);
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
                <h5><strong><strong>সরকারি মাধ্যমে হজযাত্রীর প্রাক নিবন্ধন</strong></strong></h5>
            </div>
            <div class="float-right">
                <router-link :to="{name: 'AddNewPilgrim'}" class="btn btn-default">
                    <i class="fa fa-plus text-dark"></i> <b class="text-dark">নতুন হজযাত্রী যুক্ত করুন</b>
                </router-link>
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
                                <th>Gender</th>
                                <th>Status</th>
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
                                <td>{{ pilgrim.gender }}</td>
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
                                <td>
                                    <router-link :to="{ name: 'ViewPreRegPilgrim', params: { id: pilgrim.id } }"
                                                 class="btn btn-xs btn-primary">
                                        <i class="fa fa-eye"></i> View
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
</template>
