import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
// Import the CSS or use your own!
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

//Prime vue
import 'primeicons/primeicons.css';
// import 'primevue/resources/primevue.min.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

//Toast options
const options = {
    position: POSITION.TOP_RIGHT,
    timeout: 4000,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
    hideProgressBar: false,
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(Toast, options)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
