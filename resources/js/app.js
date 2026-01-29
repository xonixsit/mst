import './bootstrap';
import '../css/theme.css';

import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { Ziggy } from './ziggy.js'

// Make route function available globally
import { route } from 'ziggy-js'

// Force method spoofing for IIS compatibility
const originalVisit = router.visit
router.visit = function(url, options = {}) {
    if (options.method && ['put', 'patch', 'delete'].includes(options.method.toLowerCase())) {
        options.data = options.data || {}
        options.data._method = options.method.toUpperCase()
        options.method = 'post'
    }
    return originalVisit.call(this, url, options)
}

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'MySuperTax'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // Initialize route function with Ziggy config
        window.route = (name, params, absolute) => route(name, params, absolute, Ziggy)
        
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
    progress: {
        color: '#1e40af', // Primary-600
    },
})