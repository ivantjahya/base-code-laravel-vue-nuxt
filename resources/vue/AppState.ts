import axios from 'axios';
import { defineStore } from 'pinia';

export type ColorMode = 'light' | 'dark' | 'system';

export const useWebApiStore = defineStore('webapi', {
    state: () => ({
        /** WEB for API requests */
        postLogin: '/post-login',
        postLogout: '/post-logout',
    }),
});

export const useApiStore = defineStore('api', {
    state: () => ({
        /** API request */
        postTokenLogout: '/api/v1/post-token-revoke',
        appConst: '/api/v1/post-app-const',
        getLimitList: '/api/v1/masterdata/limit/list',
        getLimitDetail: '/api/v1/masterdata/limit/detail/', // + id
        postLimitCreate: '/api/v1/masterdata/limit/create',
        postLimitUpdate: '/api/v1/masterdata/limit/update/', // + id
        postLimitExtend: '/api/v1/masterdata/limit/extend/', // + id
        getMenuList: '/api/v1/masterdata/menu/list',
        getMenuAccControlList: '/api/v1/masterdata/menu/list-menu-acc-control',
        getProfileList: '/api/v1/masterdata/profile/list',
        getProfileDetail: '/api/v1/masterdata/profile/detail/', // + id
        postProfileCreate: '/api/v1/masterdata/profile/create',
        postProfileUpdate: '/api/v1/masterdata/profile/update/', // + id
        getFuncProfileList: '/api/v1/masterdata/func-profile/list',
        getFuncProfileDetail: '/api/v1/masterdata/func-profile/detail/', // + id
        postFuncProfileCreate: '/api/v1/masterdata/func-profile/create',
        postFuncProfileUpdate: '/api/v1/masterdata/func-profile/update/', // + id
        getMerchStructDivCatList: '/api/v1/masterdata/merch-struct/list-merch-struct-div-cat',
        getUserList: '/api/v1/masterdata/user/list',
        getApprovalFlowList: '/api/v1/masterdata/approval-flow/list',
        postApprovalFlowCreate: '/api/v1/masterdata/approval-flow/create',
        getSiteList: '/api/v1/masterdata/site/list',
        getUserGuideList: '/api/v1/masterdata/user-guide/list',
        getPoStatusList: '/api/v1/masterdata/po-status/list',
    }),
});

export const useMainStore = defineStore('main', {
    state: () => {
        // Detect if user is on mobile device
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 768;

        // Get stored value or default based on device type
        const storedCollapsed = localStorage.getItem('sidebarCollapsed');
        const defaultCollapsed = storedCollapsed !== null ? storedCollapsed === 'true' : isMobile;

        return {
            /** Additional data */
            appName: import.meta.env.APP_NAME,
            appVersion: '',
            appDebug: false,
            userName: '',
            userId: '',
            notificationList: [],
            locale: (localStorage.getItem('locale') || 'en') as 'en' | 'id',
            mode: (localStorage.getItem('colorMode') || 'system') as ColorMode,
            isCollapsed: defaultCollapsed,
        };
    },

    getters: {
        /**
         * Get the actual color mode (resolving 'system' to 'light' or 'dark')
         */
        actualMode: (state): 'light' | 'dark' => {
            if (state.mode === 'system') {
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            return state.mode;
        },

        isDark(): boolean {
            return this.actualMode === 'dark';
        },
    },

    actions: {
        setLocale(newLocale: 'en' | 'id') {
            this.locale = newLocale;
            localStorage.setItem('locale', newLocale);
        },

        setMode(newMode: ColorMode) {
            this.mode = newMode;
            localStorage.setItem('colorMode', newMode);
            this.applyMode();
        },

        setLight() {
            this.setMode('light');
        },

        setDark() {
            this.setMode('dark');
        },

        setSystem() {
            this.setMode('system');
        },

        toggle() {
            if (this.actualMode === 'light') {
                this.setDark();
            } else {
                this.setLight();
            }
        },

        toggleSidebar() {
            this.isCollapsed = !this.isCollapsed;
            localStorage.setItem('sidebarCollapsed', String(this.isCollapsed));
        },

        setSidebarCollapsed(collapsed: boolean) {
            this.isCollapsed = collapsed;
            localStorage.setItem('sidebarCollapsed', String(collapsed));
        },

        applyMode() {
            const actualMode = this.actualMode;

            if (actualMode === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        init() {
            const api = useApiStore();

            // Apply initial mode
            this.applyMode();

            // Listen for system preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (this.mode === 'system') {
                    this.applyMode();
                }
            });

            // Check if API token exists and is not expired
            // const token = localStorage.getItem('api_token');
            // const expiresAt = localStorage.getItem('token_expires_at');
            // if (token && expiresAt) {
            //     const expiryDate = new Date(expiresAt);
            //     if (expiryDate < new Date()) {
            //         // Token expired, remove it
            //         console.warn('API token expired, removing...');
            //         localStorage.removeItem('api_token');
            //         localStorage.removeItem('token_expires_at');
            //     }
            // }

            /** Get Constant */
            axios
                .post(api.appConst)
                .then((response) => {
                    this.$patch({
                        appName: response.data.appName,
                    });
                    this.$patch({
                        appVersion: response.data.appVersion,
                    });
                    this.$patch({
                        appDebug: response.data.appDebug,
                    });
                    this.$patch({
                        userName: response.data.userName,
                    });
                    this.$patch({
                        userId: response.data.userId,
                    });
                })
                .catch((error) => {
                    console.error(error.response.data);
                });
        },

        async spaCsrfToken() {
            /**
             * Get new CSRF Token set everytime app is created
             */
            axios.get('/sanctum/csrf-cookie').then(() => {
                console.log('csrf cookie init');
            });
        },

        // /**
        //  * Get API token for authenticated requests
        //  * Call this after user logs in via web session
        //  */
        // async getApiToken(username: string, password: string) {
        //     try {
        //         const response = await axios.post('/api/post-token', {
        //             username,
        //             password,
        //         });

        //         if (response.data.access_token) {
        //             localStorage.setItem('api_token', response.data.access_token);
        //             localStorage.setItem('token_expires_at', response.data.expires_at);
        //             console.log('API token stored successfully');
        //             return response.data;
        //         }
        //     } catch (error) {
        //         console.error('Failed to get API token:', error);
        //         throw error;
        //     }
        // },

        // /**
        //  * Revoke API token on logout
        //  */
        // async revokeApiToken() {
        //     try {
        //         await axios.post('/api/post-token-revoke');
        //         localStorage.removeItem('api_token');
        //         localStorage.removeItem('token_expires_at');
        //         console.log('API token revoked successfully');
        //     } catch (error) {
        //         console.error('Failed to revoke API token:', error);
        //         // Remove token anyway
        //         localStorage.removeItem('api_token');
        //         localStorage.removeItem('token_expires_at');
        //     }
        // },
    },
});
