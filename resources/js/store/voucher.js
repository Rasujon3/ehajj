import Axios from "axios";

export const voucher={
    namespaced: true,
    state:{
        vouchers:[],
        voucherInfo:{},
        payment_transction_info:{},
        pdf_certificate_info:{},
        pdf_voucher_info:{},
        pilgrims:[],
        voucherPilgrims:[],
        message:"",
        districts:[],
        policeStations:[],
        bankBranches:[],
        voucherEditInfo:{},
        laravelData: {
            data: [],
            total: 0
        },
    },
    getters:{
        getVouchers(state){
            return state.vouchers;
        }
    },
    actions:{
        get_vouchers(context){
            Axios.get('/pilgrim/voucher/get-voucher/').then(res=>{
                context.commit('GET_VOUCHERS',res.data.data)
            })
        },
        get_voucher_detail(context,id){
            Axios.get('/pilgrim/voucher/get-voucher-detail/'+id).then(res=>{
                context.commit('GET_VOUCHER_DETAIL',res.data.data.voucherInfo)
                context.commit('GET_VOUCHER_PDF',res.data.data.pdf_voucher_info)
                context.commit('GET_VOUCHER_CERTIFICATE',res.data.data.pdf_certificate_info)
                context.commit('GET_VOUCHER_PDF_BASE_PATH',res.data.data.pdf_base_path)
                context.commit('GET_VOUCHER_TRANSCTION_INFO',res.data.data.payment_transction_info)
                context.commit('GET_VOUCHER_PILGRIM',res.data.data.pilgrims)
            })
        },

        get_districts(context){
            Axios.get('/pilgrim/voucher/get-district').then(res=>{
                let datas = res.data.data
                const transformedData = Object.keys(datas).map(key => ({
                    id: parseInt(key),
                    text: datas[key]
                }));
                context.commit('GET_DISTRICT',transformedData)
            })
        },
        get_police_stations(context,id){
            Axios.get('/pilgrim/voucher/get-police-station/'+id).then(res=>{
                let datas = res.data.data
                const transformedData = Object.keys(datas).map(key => ({
                    id: parseInt(key),
                    text: datas[key]
                }));
                context.commit('GET_POLICE_STATION',transformedData)
            })
        },

        get_bank_branches(context,id){
            Axios.get('/pilgrim/voucher/get-bank_branch/'+id).then(res=>{

                let datas = res.data.data

                const transformedData = datas.map((value)=> ({
                    id: parseInt(value['bank_branch_id']),
                    text: value['bank_branch_name']
                }));
                context.commit('GET_BANK_BRANCH',transformedData)
            })
        },

        get_pilgrims(context,{ id, search, limits, page }){
            Axios.post('/pilgrim/voucher/get-pilgrim', {
                id: id,
                search: search,
                limits: limits,
                page: page
            }).then(res => {
                if (res.data.responseCode === 1) {
                    context.commit('GET_PILGRIM', res.data?.data);
                } else {
                    context.commit('GET_PILGRIM', []);
                }
            });
        },
        get_voucher_edit_info(context,id){
            Axios.get('/pilgrim/voucher/get-voucher_edit_info/'+id).then(res=>{
                context.commit('GET_VOUCHER_EDIT_INFO',res.data.data.groupPaymentInfo)
            })
        }

    },
    mutations:{

        GET_VOUCHERS(state,data){
            return state.vouchers=data
        },

        GET_VOUCHER_DETAIL(state,data){
            return state.voucherInfo=data
        },

        GET_VOUCHER_PDF(state,data){
            return state.pdf_voucher_info=data
        },
        GET_VOUCHER_CERTIFICATE(state,data){
            return state.pdf_certificate_info=data
        },

        GET_VOUCHER_PDF_BASE_PATH(state,data){
            return state.pdf_base_path=data
        },

        GET_VOUCHER_TRANSCTION_INFO(state,data){
            //console.log(data)
            return state.payment_transction_info=data
        },

        GET_VOUCHER_PILGRIM(state,data){
            return state.voucherPilgrims=data
        },
        GET_DISTRICT(state,data){
            return state.districts=data
        },
        GET_POLICE_STATION(state,data){
            return state.policeStations=data
        },
        GET_BANK_BRANCH(state,data){
            return state.bankBranches=data
        },

        /*
        GET_PILGRIM(state,data){
            return state.pilgrims=data
        },
        */
        GET_PILGRIM(state, payload) {
            if (Array.isArray(payload)) {
                state.pilgrims = [];
                state.laravelData.data = [];
                state.laravelData.total = 0;
            } else {
                state.pilgrims = payload.data || [];
                state.laravelData.data = payload.data || [];
                state.laravelData.total = payload.totalCount || 0;
            }
        },

        GET_VOUCHER_EDIT_INFO(state,data){
            return state.voucherEditInfo=data
        },
    }
}
