import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// import { library } from '@fortawesome/fontawesome-svg-core'
// import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
// import { faCaretDown } from '@fortawesome/free-solid-svg-icons'
// import { googleLogo} from '@fortawesome/free-brands-svg-icons'

import VueCal from 'vue-cal'
import 'vue-cal/dist/vuecal.css'

// library.add(faCaretDown) 

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} | ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .component('VueCal', VueCal)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#dd44a2',
        showSpinner: true,
    },
});
