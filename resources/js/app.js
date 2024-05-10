import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'
import Toast, { TYPE } from "vue-toastification";
import "vue-toastification/dist/index.css";
import { VueQueryPlugin } from '@tanstack/vue-query'

const appName =
    window.document.getElementsByTagName('title')[0]?.innerText || 'K UI'

const options = {
    toastDefaults: {
        // ToastOptions object for each type of toast
        [TYPE.DEFAULT]: {
            toastClassName: "default-background-color",
            closeButtonClassName: "default-close-button",
            position: "top-center",
            closeOnClick: false,
            timeout: 5000,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: true,
            closeButton: 'button',
            icon: false,
        }    
    }
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Toast, options)
            .use(VueQueryPlugin)
            .mount(el)
    },
    progress: false
})
