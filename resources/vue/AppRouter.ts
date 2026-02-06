const landingPage = '/';
const home = '/home';
const limitManagement = '/limit-management';
const profileManagement = '/profile-management';
const exploreNuxt = '/explore-nuxt';

import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { defineStore } from 'pinia';

const routes: Array<RouteRecordRaw> = [
    {
        path: landingPage,
        name: 'landingPage',
        component: () => import('./AuthPages/PgLogin.vue'),
    },
    {
        path: home,
        name: 'home',
        component: () => import('./HomePages/PgHome.vue'),
    },
    {
        path: limitManagement,
        name: 'limitManagement',
        component: () => import('./MasterPages/PgLimitMan.vue'),
    },
    {
        path: profileManagement,
        name: 'profileManagement',
        component: () => import('./MasterPages/PgProfileMan.vue'),
    },
    {
        path: exploreNuxt,
        name: 'exploreNuxt',
        component: () => import('./HomePages/PgExploreNuxt.vue'),
    },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
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
        exploreNuxt: exploreNuxt,
    }),
});
