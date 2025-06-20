import { createApp } from 'vue'
import MainLayout from './main-layout.vue'
import { router } from './router'
import '@tabler/core/dist/css/tabler.min.css'

const app = createApp(MainLayout)
app.use(router).mount('#app')