import { createApp, App } from 'vue';
import { createPinia, Pinia } from 'pinia';
import AppComponent from '../js/App.vue';
import ui from '@nuxt/ui/vue-plugin';
import axios from 'axios';
import { useMainStore } from './AppState';

const pinia: Pinia = createPinia();

/** Vue router needed for navigation menu */
import { router } from './AppRouter';

// Mount Application Instances
const MainApp: App<Element> = createApp(AppComponent)
    .use(router)
    .use(pinia)
    .use(ui);

// Setup axios interceptor AFTER pinia is initialized
axios.interceptors.request.use((config) => {
    const mainStore = useMainStore(pinia);
    config.headers['Accept-Language'] = mainStore.locale;
    return config;
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
    MainApp.mount('#app');
});
