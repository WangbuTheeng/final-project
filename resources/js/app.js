import './bootstrap';

// Import Alpine.js
import Alpine from 'alpinejs';

// Import keyboard shortcuts
import './keyboard-shortcuts';

// Import Vue.js and Inertia.js
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Initialize Inertia.js with Vue.js
createInertiaApp({
    title: (title) => `${title} - College CMS`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4f46e5',
    },
});
