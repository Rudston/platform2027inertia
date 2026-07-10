import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'Platform2027';

createInertiaApp({
    title: (title) => (title ? `${title} — ${appName}` : appName),

    // Eagerly glob all page components so resolution is synchronous.
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        const page = pages[`./Pages/${name}.jsx`];

        if (!page) {
            throw new Error(`Inertia page not found: ./Pages/${name}.jsx`);
        }

        return page;
    },

    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },

    progress: {
        color: '#4f46e5', // indigo-600, matches the existing Explore accent
    },
});
