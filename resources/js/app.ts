import { createInertiaApp } from '@inertiajs/vue3';
import AppLayout from './layouts/AppLayout.vue';
import AuthLayout from './layouts/AuthLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('errors/'):
                return;
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
});
