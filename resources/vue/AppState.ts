import axios from 'axios';
import { defineStore } from 'pinia';

export type ColorMode = 'light' | 'dark' | 'system';

export interface AccessMenuControl {
    id: string;
    code: string;
    name: string;
}

export interface AccessMenuItem {
    id: string;
    name: string;
    url: string | null;
    icon: string | null;
    code: number;
    name_code: string;
    active: boolean;
    submenu: AccessMenuItem[];
    acc_controls: AccessMenuControl[];
}

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
        getProfileMenuAccess: '/api/v1/masterdata/profile/get-menu-access/', // + id
        postProfileCreate: '/api/v1/masterdata/profile/create',
        postProfileUpdate: '/api/v1/masterdata/profile/update/', // + id
        getFuncProfileList: '/api/v1/masterdata/func-profile/list',
        getFuncProfileDetail: '/api/v1/masterdata/func-profile/detail/', // + id
        postFuncProfileCreate: '/api/v1/masterdata/func-profile/create',
        postFuncProfileUpdate: '/api/v1/masterdata/func-profile/update/', // + id
        getMerchStructDivCatList: '/api/v1/masterdata/merch-struct/list-merch-struct-div-cat',
        getUserList: '/api/v1/masterdata/user/list',
        getUserDetail: '/api/v1/masterdata/user/detail/', // + id
        postUserCreate: '/api/v1/masterdata/user/create',
        postUserUpdate: '/api/v1/masterdata/user/update/', // + id
        postResetPassword: '/api/v1/masterdata/user/reset-password/', // + id
        postUnlockUser: '/api/v1/masterdata/user/unlock/', // + id
        getApprovalFlowList: '/api/v1/masterdata/approval-flow/list',
        getApprovalFlowDetail: '/api/v1/masterdata/approval-flow/detail/', // + id
        postApprovalFlowCreate: '/api/v1/masterdata/approval-flow/create',
        postApprovalFlowUpdate: '/api/v1/masterdata/approval-flow/update/', // + id
        getSiteList: '/api/v1/masterdata/site/list',
        getUserGuideList: '/api/v1/masterdata/user-guide/list',
        getUserGuideDetail: '/api/v1/masterdata/user-guide/detail/', // + id
        postUserGuideCreate: '/api/v1/masterdata/user-guide/create',
        postUserGuideUpdate: '/api/v1/masterdata/user-guide/update/', // + id
        getPoStatusList: '/api/v1/masterdata/status/po-status-list',
        getCompanyList: '/api/v1/masterdata/company/list',
        getUserStatusList: '/api/v1/masterdata/status/user-status-list',
        postUserGuideDelete: '/api/v1/masterdata/user-guide/delete/', // + id
        getUserGuideDownload: '/api/v1/masterdata/user-guide/download/', // + id
    }),
});

export interface CompanyOption {
    label: string;
    value: string;
}

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
            profileId: '',
            profileIsInternal: (localStorage.getItem('profileIsInternal') || false),
            notificationList: [],
            locale: (localStorage.getItem('locale') || 'en') as 'en' | 'id',
            mode: (localStorage.getItem('colorMode') || 'system') as ColorMode,
            isCollapsed: defaultCollapsed,
            accessMenuList: (JSON.parse(localStorage.getItem('accessMenuList') || '[]')) as AccessMenuItem[],
            menuCodes: (JSON.parse(localStorage.getItem('menuCodes') || '{}')) as Record<string, number>,
            ctrlCodes: (JSON.parse(localStorage.getItem('ctrlCodes') || '{}')) as Record<string, string>,
            statusCodes: (JSON.parse(localStorage.getItem('statusCodes') || '{}')) as Record<string, string>,
            menuPaths: (JSON.parse(localStorage.getItem('menuPaths') || '{}')) as Record<string, string>,
            companyOptions: (JSON.parse(localStorage.getItem('companyOptions') || '[]')) as CompanyOption[],
            selectedCompanyId: (localStorage.getItem('selectedCompanyId') || '') as string,
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

        setSelectedCompany(companyId: string) {
            this.selectedCompanyId = companyId;
            localStorage.setItem('selectedCompanyId', companyId);
        },

        async getUserCompanies(userId: string) {
            if (!userId) return;

            const api = useApiStore();
            try {
                const response = await axios.get(api.getUserDetail + userId);
                const detail = response?.data?.data || response?.data || {};

                // Set profileIsInternal based on user detail
                this.profileIsInternal = !!detail?.profile?.is_internal;
                localStorage.setItem('profileIsInternal', String(this.profileIsInternal));

                // Support direct `companies` array or extracted from `func_profiles[].company`
                const rawCompanies: any[] = Array.isArray(detail?.company)
                    ? detail.company
                    : Array.isArray(detail?.func_profiles)
                        ? detail.func_profiles.map((fp: any) => fp?.company).filter(Boolean)
                        : [];

                const uniqueMap = new Map<string, CompanyOption>();
                rawCompanies.forEach((c: any) => {
                    const id = String(c?.id || '').trim();
                    if (!id) return;
                    const label = (c?.code ? c.code + ' - ' : '') + String(c?.name || '').trim();
                    uniqueMap.set(id, { label, value: id });
                });

                const options = Array.from(uniqueMap.values());
                this.companyOptions = options;
                localStorage.setItem('companyOptions', JSON.stringify(options));

                // Auto-select first option if stored value is missing or no longer in list
                if (!this.selectedCompanyId || !options.some(o => o.value === this.selectedCompanyId)) {
                    const firstId = options[0]?.value || '';
                    this.selectedCompanyId = firstId;
                    localStorage.setItem('selectedCompanyId', firstId);
                }
            } catch (error) {
                console.error('Failed to fetch user companies:', error);
            }
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
                .then(async (response) => {
                    let accessMenuList = response.data.accessMenuList ?? [];
                    const profileId = response.data.profileId;

                    if (accessMenuList.length === 0 && profileId) {
                        try {
                            const params = {
                                id: profileId,
                            };
                            const menuResponse = await axios.get(api.getProfileMenuAccess + params.id);

                            // Handle various response formats
                            if (Array.isArray(menuResponse.data)) {
                                accessMenuList = menuResponse.data;
                            } else if (menuResponse.data && Array.isArray(menuResponse.data.data)) {
                                accessMenuList = menuResponse.data.data;
                            } else if (menuResponse.data && Array.isArray(menuResponse.data.menu_access)) {
                                accessMenuList = menuResponse.data.menu_access;
                            }
                        } catch (err) {
                            console.error('Failed to fetch profile menu access:', err);
                        }
                    }

                    // Update local storage
                    if (accessMenuList.length > 0) {
                        localStorage.setItem('accessMenuList', JSON.stringify(accessMenuList));
                    } else {
                        // Clear storage if menu is empty (e.g. logged out or no access)
                        localStorage.removeItem('accessMenuList');
                    }

                    if (response.data.menuCodes) {
                        localStorage.setItem('menuCodes', JSON.stringify(response.data.menuCodes));
                    }
                    if (response.data.ctrlCodes) {
                        localStorage.setItem('ctrlCodes', JSON.stringify(response.data.ctrlCodes));
                    }
                    if (response.data.statusCodes) {
                        localStorage.setItem('statusCodes', JSON.stringify(response.data.statusCodes));
                    }
                    if (response.data.menuPaths) {
                        localStorage.setItem('menuPaths', JSON.stringify(response.data.menuPaths));
                    }

                    this.$patch({
                        appName: response.data.appName,
                        appVersion: response.data.appVersion,
                        appDebug: response.data.appDebug,
                        userName: response.data.userName,
                        userId: response.data.userId,
                        profileId: profileId,
                        accessMenuList: accessMenuList,
                        menuCodes: response.data.menuCodes ?? this.menuCodes,
                        ctrlCodes: response.data.ctrlCodes ?? this.ctrlCodes,
                        statusCodes: response.data.statusCodes ?? this.statusCodes,
                        menuPaths: response.data.menuPaths ?? this.menuPaths,
                    });

                    if (response.data.userId) {
                        this.getUserCompanies(response.data.userId);
                    }
                })
                .catch((error) => {
                    console.error(error);
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
