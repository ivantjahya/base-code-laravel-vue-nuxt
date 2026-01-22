const landingPage = '/';
const dashboard = '/dashboard';
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
        path: dashboard,
        name: 'dashboard',
        component: () => import('./DashboardPages/PgDashboard.vue'),
    },
    {
        path: exploreNuxt,
        name: 'exploreNuxt',
        component: () => import('./DashboardPages/PgExploreNuxt.vue'),
    },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});

export const useWebStore = defineStore('web', {
    state: () => ({
        /** Define route here because if not defined and get from XHR it will be race condition */
        landingPage: landingPage,
        dashboard: dashboard,
        exploreNuxt: exploreNuxt,
    }),
});
