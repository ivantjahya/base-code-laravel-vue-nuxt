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
    },
});
