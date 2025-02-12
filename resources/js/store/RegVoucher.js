import Axios from "axios";

export const RegVoucher={
    namespaced: true,
    state:{
        vouchers:[],
        voucherInfo:{},
        payment_transction_info:{},
        pdf_certificate_info:{},
        pdf_voucher_info:{},
        pdf_base_path:{},
        pilgrims:[],
        voucherPilgrims:[],
        message:"",
        packages:[],
        category:[],
        voucherEditInfo:{},
        regPaymentInfo:{},
        laravelData: {
            data: [],
            total: 0
        }

    },
    getters:{
        getVouchers(state){
            return state.vouchers;
        }
    },
    actions:{
        get_vouchers(context){
            Axios.get('/registration/reg-voucher/get-voucher/').then(res=>{
                context.commit('GET_VOUCHERS',res.data.data)
            })
        },
        get_voucher_detail(context,id){
            Axios.get('/registration/reg-voucher/get-voucher-detail/'+id).then(res=>{
                context.commit('GET_VOUCHER_DETAIL',res.data.data.voucherInfo)
                context.commit('GET_REG_PAYMENT_INFO',res.data.data.regPaymentInfo)
                context.commit('GET_VOUCHER_PDF',res.data.data.pdf_info)
                context.commit('GET_VOUCHER_CERTIFICATE',res.data.data.pdf_certificate_info)
                context.commit('GET_VOUCHER_PDF_BASE_PATH',res.data.data.pdf_base_path)
                context.commit('GET_VOUCHER_TRANSCTION_INFO',res.data.data.payment_transction_info)
                context.commit('GET_VOUCHER_PILGRIM',res.data.data.pilgrims)
            })
        },

        get_packages(context, { is_short_package, category }){
            Axios.get('/registration/reg-voucher/get-package', {
                params: {
                    is_short_package,
                    category
                }
            }).then(res=>{
                if (res.data.responseCode === 1) {
                    let hajj_packz = res.data?.data?.hajj_packz;
                    let package_category = res.data?.data?.package_category;
                    if (hajj_packz !== undefined) {
                        const transformedData = Object.keys(hajj_packz).map(key => ({
                            id: parseInt(key),
                            text: hajj_packz[key]
                        }));
                        context.commit('GET_PACKAGE',transformedData);
                    } else {
                        context.commit('GET_PACKAGE',[]);
                    }
                    if (package_category !== undefined) {
                        const transformedCategoryData = package_category.map(item => ({
                            id: item.package_category,
                            is_primary_package: item.is_primary_package,
                            text: item.package_category
                        }));
                        context.commit('GET_CATEGORY', transformedCategoryData);
                    }
                } else {
                    context.commit('GET_PACKAGE',[]);
                    context.commit('GET_CATEGORY',[]);
                }
            })
        },
        /*
        get_pilgrims(context,id){
            Axios.post('/registration/reg-voucher/get-pilgrim').then(res=>{
                context.commit('GET_PILGRIM',res.data.data)
            })
        },
        */
        get_pilgrims(context, { search, limits, page }) {
            Axios.post('/registration/reg-voucher/get-pilgrim', {
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
            Axios.get('/registration/reg-voucher/get-voucher_edit_info/'+id).then(res=>{
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
            return state.payment_transction_info=data
        },

        GET_VOUCHER_PILGRIM(state,data){
            return state.voucherPilgrims=data
        },
        GET_PACKAGE(state,data){
            return state.packages=data
        },
        GET_CATEGORY(state,data){
            return state.category=data
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
        GET_REG_PAYMENT_INFO(state,data){
            return state.regPaymentInfo=data
        },
    }
}
