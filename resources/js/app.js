import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import VueSmoothScroll from 'vue3-smooth-scroll'
import VueCal from 'vue-cal'
import 'vue-cal/dist/vuecal.css'
import routes from './routes';
import { createRouter, createWebHistory } from 'vue-router';
import { InertiaProgress } from '@inertiajs/progress';

const router = createRouter({
    history: createWebHistory(),
    routes,
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} | ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(router)
            .use(VueSmoothScroll)
            .component('VueCal', VueCal)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#dd44a2',
        showSpinner: true,
    },
});

InertiaProgress.init();
