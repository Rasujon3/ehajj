
let routes = [
    {
        path: '/reg-pilgrims-list',
        component: () => import(/* webpackChunkName: "RegPilgrimsList" */ '../components/pilgrim/registartion/PilgrimsList.vue'),
        name: 'RegPilgrimsList'
    },
    {
        path: '/reg-pilgrim-edit/:id/:trackingNo',
        component: () => import(/* webpackChunkName: "RegPilgrimEdit" */ '../components/pilgrim/registartion/Edit.vue'),
        name: 'EditRegPilgrim'
    },
    {
        path: '/reg-pilgrim-view/:id',
        component: () => import(/* webpackChunkName: "RegPilgrimView" */ '../components/pilgrim/registartion/View.vue'),
        name: 'ViewRegPilgrim'
    },
    {
        path: '/reg-pilgrim/passport-verify-request',
        component: () => import(/* webpackChunkName: "RegPilgrimView" */ '../components/pilgrim/registartion/PassportComponent/PassportVerifyRequest.vue'),
        name: 'RegPassportVerifyReq'
    }
]

export default routes;
