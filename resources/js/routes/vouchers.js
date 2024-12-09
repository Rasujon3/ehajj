

let routes = [

    {
        path: '/voucher-list',
        component: () => import(/* webpackChunkName: "PreRegPilgrimsList" */ '../components/pilgrim/voucher/VoucherList.vue'),
        name: 'voucherList'
    },

    {
        path: '/voucher-detail-view/:id',
        component: () => import(/* webpackChunkName: "HomeContent" */ '../components/pilgrim/voucher/VoucherView.vue'),
        name: 'voucherDetailView'
    },
    {
        path: '/voucher-payment-method-view/:tracking_no',
        component: () => import(/* webpackChunkName: "HomeContent" */ '../components/pilgrim/voucher/PaymentMethodView.vue'),
        name: 'PaymentMethodView'
    },

]

export default routes;

