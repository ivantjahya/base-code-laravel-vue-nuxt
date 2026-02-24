const landingPage = '/';
const home = '/home';
const limitManagement = '/limit-management';
const profileManagement = '/profile-management';
const functionalProfileManagement = '/functional-profile-management';
const userManagement = '/user-management';
const regionalSite = '/regional-site';
const approvalFlowManagement = '/approval-flow-management';
const userGuideManagement = '/user-guide-management';
const exploreNuxt = '/explore-nuxt';

import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { defineStore } from 'pinia';
import { useI18n } from './composables/useI18n';

const routes: Array<RouteRecordRaw> = [
    {
        path: landingPage,
        name: 'landingPage',
        component: () => import('./AuthPages/PgLogin.vue'),
        meta: { titleKey: 'page.login', title: 'Login' },
    },
    {
        path: home,
        name: 'home',
        component: () => import('./HomePages/PgHome.vue'),
        meta: { titleKey: 'page.home', title: 'Home' },
    },
    {
        path: limitManagement,
        name: 'limitManagement',
        component: () => import('./MasterPages/PgLimitMan.vue'),
        meta: { titleKey: 'page.limit-management', title: 'Limit' },
    },
    {
        path: profileManagement,
        name: 'profileManagement',
        component: () => import('./MasterPages/PgProfileMan.vue'),
        meta: { titleKey: 'page.profile-management', title: 'Profile' },
    },
    {
        path: functionalProfileManagement,
        name: 'functionalProfileManagement',
        component: () => import('./MasterPages/PgFuncProfileMan.vue'),
        meta: { titleKey: 'page.functional-profile-management', title: 'Functional Profile' },
    },
    {
        path: userManagement,
        name: 'userManagement',
        component: () => import('./MasterPages/PgUserMan.vue'),
        meta: { titleKey: 'page.user-management', title: 'User' },
    },
    {
        path: regionalSite,
        name: 'regionalSite',
        component: () => import('./MasterPages/PgRegionalSite.vue'),
        meta: { titleKey: 'page.regional-site-management', title: 'Regional Site' },
    },
    {
        path: approvalFlowManagement,
        name: 'approvalFlowManagement',
        component: () => import('./MasterPages/PgApprovalFlowMan.vue'),
        meta: { title: 'Approval Flow' },
    },
    {
        path: approvalFlowManagement,
        name: 'approvalFlowManagement',
        component: () => import('./MasterPages/PgApprovalFlowMan.vue'),
        meta: { title: 'Approval Flow' },
    },
    {
        path: userGuideManagement,
        name: 'userGuideManagement',
        component: () => import('./MasterPages/PgUserGuideMan.vue'),
        meta: { titleKey: 'page.user-guide-management', title: 'User Guide' },
    },
    {
        path: exploreNuxt,
        name: 'exploreNuxt',
        component: () => import('./HomePages/PgExploreNuxt.vue'),
        meta: { titleKey: 'page.explore-nuxt', title: 'Explore Nuxt' },
    },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Update page title on route changes
router.beforeEach((to, from, next) => {
    const { t } = useI18n();

    const titleKey = String(to.meta?.titleKey || '');
    const fallbackTitle = String(to.meta?.title || '');
    const translatedTitle = titleKey ? t(titleKey as any) : fallbackTitle;

    if (translatedTitle) {
        document.title = `${translatedTitle} - ${import.meta.env.VITE_APP_NAME}`;
    }
    next();
});

// Clear console on route changes
router.afterEach(() => {
    // Clear console when navigating between pages
    if (console && console.clear) {
        console.clear();
    }
});

export const useWebStore = defineStore('web', {
    state: () => ({
        /** Define route here because if not defined and get from XHR it will be race condition */
        landingPage: landingPage,
        home: home,
        limitManagement: limitManagement,
        profileManagement: profileManagement,
        functionalProfileManagement: functionalProfileManagement,
        userManagement: userManagement,
        regionalSite: regionalSite,
        approvalFlowManagement: approvalFlowManagement,
        userGuideManagement: userGuideManagement,
        exploreNuxt: exploreNuxt,
    }),
});
