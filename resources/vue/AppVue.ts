import { createApp, App } from 'vue';
import { createPinia, Pinia } from 'pinia';
const pinia: Pinia = createPinia();

/** Vue router needed for navigation menu */
import { router } from './AppRouter';

// Mount Application Instances
const MainApp: App<Element> = createApp({})
    .use(router)
    .use(pinia);

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
