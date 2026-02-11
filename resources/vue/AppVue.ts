import { createApp, App } from 'vue';
import { createPinia, Pinia } from 'pinia';
import AppComponent from '../js/App.vue';
import ui from '@nuxt/ui/vue-plugin';
import axios from 'axios';
import { useMainStore } from './AppState';
import Swal from 'sweetalert2';

// Create custom Swal instance with default styling
const CustomSwal = Swal.mixin({
    customClass: {
        confirmButton: 'swal-orange-button',
        // cancelButton: 'swal-orange-button'
    },
    buttonsStyling: false
});

declare module '@vue/runtime-core' {
    export interface ComponentCustomProperties {
        $swal: typeof import('sweetalert2').default;
    }
}

const pinia: Pinia = createPinia();

/** Vue router needed for navigation menu */
import { router } from './AppRouter';

// Mount Application Instances
const MainApp: App<Element> = createApp(AppComponent)
    .use(router)
    .use(pinia)
    .use(ui);

// Add custom SweetAlert2 as global property
MainApp.config.globalProperties.$swal = CustomSwal;

// Setup axios defaults and interceptors
axios.defaults.withCredentials = true; // Send cookies with requests
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

// Setup axios interceptor AFTER pinia is initialized
axios.interceptors.request.use((config) => {
    const mainStore = useMainStore(pinia);
    config.headers['Accept-Language'] = mainStore.locale;
    
    return config;
}, (error) => {
    return Promise.reject(error);
});

/** Add Sentry */
import * as Sentry from '@sentry/vue';

Sentry.init({
    app: MainApp,
    dsn: import.meta.env.VITE_SENTRY_DSN ?? '',

    integrations: [Sentry.browserTracingIntegration({ router }), Sentry.replayIntegration()],

    tracesSampleRate: 1.0,
    replaysSessionSampleRate: 0.1,
    replaysOnErrorSampleRate: 1.0,
});

router.isReady().then(() => {
    // Initialize color mode
    const colorModeStore = useMainStore(pinia);
    colorModeStore.init();
    
    MainApp.mount('#app');
});
