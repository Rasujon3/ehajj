
let routes = [

    {
        path: '/pilgrims-list',
        component: () => import(/* webpackChunkName: "PreRegPilgrimsList" */ '../components/pilgrim/pre-registartion/PilgrimsList.vue'),
        name: 'PreRegPilgrimsList'
    },
    {
        path: '/pilgrim-create',
        component: () => import(/* webpackChunkName: "PreRegPilgrimCreate" */ '../components/pilgrim/pre-registartion/Create.vue'),
        name: 'AddNewPilgrim'
    },
    {
        path: '/pilgrim-edit/:id',
        component: () => import(/* webpackChunkName: "PreRegPilgrimEdit" */ '../components/pilgrim/pre-registartion/Edit.vue'),
        name: 'EditPreRegPilgrim'
    },
    {
        path: '/pilgrim-view/:id',
        component: () => import(/* webpackChunkName: "PreRegPilgrimView" */ '../components/pilgrim/pre-registartion/View.vue'),
        name: 'ViewPreRegPilgrim'
    },

]

export default routes;
