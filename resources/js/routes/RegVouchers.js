

let routes = [

    {
        path: '/reg-voucher-list',
        component: () => import(/* webpackChunkName: "PreRegPilgrimsList" */ '../components/pilgrim/RegVoucher/VoucherList.vue'),
        name: 'RegVoucherList'
    },

    {
        path: '/reg-voucher-detail-view/:id',
        component: () => import(/* webpackChunkName: "HomeContent" */ '../components/pilgrim/RegVoucher/VoucherView.vue'),
        name: 'RegVoucherDetailView'
    },
    {
        path: '/reg-voucher-payment-method-view/:tracking_no',
        component: () => import(/* webpackChunkName: "HomeContent" */ '../components/pilgrim/RegVoucher/PaymentMethodView.vue'),
        name: 'RegPaymentMethodView'
    },

]

export default routes;

