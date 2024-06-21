import { createApp } from 'vue'
import App from '@/App.vue'
import { registerPlugins } from '@core/utils/plugins'

// Styles
import '@core-scss/template/index.scss'
import '@styles/styles.scss'
import '@styles/custom.css'
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"

// Import font CSS files
import '../assets/vendor/fonts/fontawesome.css'
import '../assets/vendor/fonts/tabler-icons.css'
import '../assets/vendor/fonts/flag-icons.css'

// Create vue app
const app = createApp(App)

// Register plugins
registerPlugins(app)

// Mount vue app
app.mount('#app')
