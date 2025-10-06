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
import '../css/app.css';

import { createNotivue } from 'notivue';
import 'notivue/animations.css'; // Only needed if using built-in animations
import 'notivue/notification.css'; // Only needed if using built-in notifications

//Leaflet
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Fix for default icon issues with Vite/Mix
delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: new URL(
        'leaflet/dist/images/marker-icon-2x.png',
        import.meta.url,
    ).href,
    iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url)
        .href,
    shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url)
        .href,
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

//Notivue options
const notivue = createNotivue({
    position: 'top-right',
    limit: 3,
    enqueue: true,
    // avoidDuplicates: true,
    notifications: {
        global: {
            duration: 3000,
        },
    },
});

//Toast options
const options = {
    position: POSITION.TOP_RIGHT,
    timeout: 4000,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
    hideProgressBar: false,

    // apply our custom classes
    // toastClassName: 'custom-toast',
    // bodyClassName: 'custom-toast-body',
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
            .use(notivue)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
