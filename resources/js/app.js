import '../css/app.css'
import { createApp } from 'vue'
import App from './App.vue'
import { router } from '../vue/AppRouter'
import ui from '@nuxt/ui/vue-plugin'

const app = createApp(App)
app.use(router)
app.use(ui)
app.mount('#app')