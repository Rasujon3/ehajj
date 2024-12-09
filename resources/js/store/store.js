import Vuex from 'vuex'
import Auth from "./AuthStore";

import {voucher} from "./voucher";
import Guide from "./GuideStore";
import {RegVoucher} from "./RegVoucher";


const store = new Vuex.Store({
    modules: {
        Auth: Auth,
        voucher:voucher,
        Guide: Guide,
        RegVoucher: RegVoucher,

    },
    state: {
        globalError: false,
    },
    mutations: {
        setGlobalError(state, error) {
            state.globalError = error;
        },
    },
});

export default store;
