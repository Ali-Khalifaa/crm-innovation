import { createApp } from 'vue'
import { i18nVue } from 'laravel-vue-i18n'
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'
import App from './App.vue'
import router from './router/index.js'
import store from './store/index.js'
import en from '../../../lang/en.json'
import ar from '../../../lang/ar.json'

// laravel-vue-i18n stores keys FLAT in activeMessages.
// Nested JSON like { nav: { dashboard: "X" } } must be flattened
// to { "nav.dashboard": "X" } for trans('nav.dashboard') to work.
function flatten(obj, prefix = '') {
    return Object.keys(obj).reduce((acc, key) => {
        const fullKey = prefix ? `${prefix}.${key}` : key
        if (typeof obj[key] === 'object' && obj[key] !== null && !Array.isArray(obj[key])) {
            Object.assign(acc, flatten(obj[key], fullKey))
        } else {
            acc[fullKey] = obj[key]
        }
        return acc
    }, {})
}

const translations = {
    en: flatten(en),
    ar: flatten(ar),
}

const app = createApp(App)
app.use(store)
app.use(router)
app.use(PrimeVue, {
  theme: {
    preset: Aura,
    options: {
      darkModeSelector: '[data-bs-theme="dark"]',
      cssLayer: false,
    },
  },
  ripple: false,
})

app.use(i18nVue, {
    lang: localStorage.getItem('crm_locale') || 'en',
    resolve: lang => translations[lang] || translations.en,
})

app.mount('#crm-app')
