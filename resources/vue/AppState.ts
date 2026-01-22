import axios from 'axios';
import { defineStore } from 'pinia';

export const useWebApiStore = defineStore('webapi', {
    state: () => ({
        /** WEB for API requests */
        postLogin: '/post-login',
    }),
});

export const useApiStore = defineStore('api', {
    state: () => ({
        /** API request */
        postTokenLogout: '/api/post-token-revoke',
        appConst: '/api/v1/post-app-const',
    }),
});

export const useMainStore = defineStore('main', {
    state: () => ({
        /** Additional data */
        appName: import.meta.env.APP_NAME,
        appVersion: '',
        appDebug: false,
        userName: '',
        userId: '',
        notificationList: [],
    }),

    actions: {
        init() {
            const api = useApiStore();
            const toast = useToast();

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
