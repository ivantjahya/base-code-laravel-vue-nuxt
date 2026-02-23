const landingPage = '/';
const home = '/home';
const limitManagement = '/limit-management';
const profileManagement = '/profile-management';
const functionalProfileManagement = '/functional-profile-management';
const userManagement = '/user-management';
const regionalSite = '/regional-site';
const userGuideManagement = '/user-guide-management';
const exploreNuxt = '/explore-nuxt';

import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { defineStore } from 'pinia';

const routes: Array<RouteRecordRaw> = [
    {
        path: landingPage,
        name: 'landingPage',
        component: () => import('./AuthPages/PgLogin.vue'),
        meta: { title: 'Login' },
    },
    {
        path: home,
        name: 'home',
        component: () => import('./HomePages/PgHome.vue'),
        meta: { title: 'Home' },
    },
    {
        path: limitManagement,
        name: 'limitManagement',
        component: () => import('./MasterPages/PgLimitMan.vue'),
        meta: { title: 'Limit' },
    },
    {
        path: profileManagement,
        name: 'profileManagement',
        component: () => import('./MasterPages/PgProfileMan.vue'),
        meta: { title: 'Profile' },
    },
    {
        path: functionalProfileManagement,
        name: 'functionalProfileManagement',
        component: () => import('./MasterPages/PgFuncProfileMan.vue'),
        meta: { title: 'Functional Profile' },
    },
    {
        path: userManagement,
        name: 'userManagement',
        component: () => import('./MasterPages/PgUserMan.vue'),
        meta: { title: 'User' },
    },
    {
        path: regionalSite,
        name: 'regionalSite',
        component: () => import('./MasterPages/PgRegionalSite.vue'),
        meta: { title: 'Regional Site' },
    },
    {
        path: userGuideManagement,
        name: 'userGuideManagement',
        component: () => import('./MasterPages/PgUserGuideMan.vue'),
    },
    {
        path: exploreNuxt,
        name: 'exploreNuxt',
        component: () => import('./HomePages/PgExploreNuxt.vue'),
        meta: { title: 'Explore Nuxt' },
    },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Update page title on route changes
router.beforeEach((to, from, next) => {
    // Update document title if meta.title exists
    if (to.meta.title) {
        document.title = `${to.meta.title} - ${import.meta.env.VITE_APP_NAME}`;
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
        userGuideManagement: userGuideManagement,
        exploreNuxt: exploreNuxt,
    }),
});
