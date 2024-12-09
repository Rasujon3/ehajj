import { checkGuideApplicationExistLastDate } from "../checkAccess.js";

let routes = [
    {
        path: '/home-guide',
        component: () => import(/* webpackChunkName: "Guides" */ '../components/guides/HomeGuide.vue'),
        name: 'HomeGuide',
        beforeEnter: async (to, from, next) => {
            try {
                const response = await checkGuideApplicationExistLastDate();
                if (response) {
                    next();
                } else {
                    next({ name: 'GuideApplicationList' });
                }
            } catch (error) {
                console.error('Access check failed:', error);
                next({ name: 'GuideApplicationList' });
            }
        }
    },
    {
        path: '/guide-nid-verify',
        component: () => import(/* webpackChunkName: "Guides" */ '../components/guides/GuideNIDVerify.vue'),
        name: 'GuideNIDVerify',
        beforeEnter: async (to, from, next) => {
            try {
                const response = await checkGuideApplicationExistLastDate();
                if (response) {
                    next();
                } else {
                    next({ name: 'GuideApplicationList' });
                }
            } catch (error) {
                console.error('Access check failed:', error);
                next({ name: 'GuideApplicationList' });
            }
        }
    },
    {
        path: '/hajj-guide-registration',
        component: () => import(/* webpackChunkName: "Guides" */ '../components/guides/HajjGuideRegistration.vue'),
        name: 'HajjGuideRegistration',
        beforeEnter: async (to, from, next) => {
            try {
                const response = await checkGuideApplicationExistLastDate();
                if (response) {
                    next();
                } else {
                    next({ name: 'GuideApplicationList' });
                }
            } catch (error) {
                console.error('Access check failed:', error);
                next({ name: 'GuideApplicationList' });
            }
        }
    },
    {
        path: '/guide-application-list',
        component: () => import(/* webpackChunkName: "Guides" */ '../components/guides/GuideApplicationList.vue'),
        name: 'GuideApplicationList'
    },
    {
        path: '/guide-application-edit/:id',
        component: () => import(/* webpackChunkName: "Guides" */ '../components/guides/edit/EditHajjGuide.vue'),
        name: 'GuideEdit'
    },
    {
        path: '/guide-profile-view/:id',
        component: () => import(/* webpackChunkName: "Guides" */ '../components/guides/GuideProfileView.vue'),
        name: 'GuideProfileView'
    },
    {
        path: '/edit-guide-voucher/:id',
        component: () => import(/* webpackChunkName: "Guides" */ '../components/guides/edit/GuideVoucherEdit.vue'),
        name: 'GuideVoucherEdit'
    },

]

export default routes;
