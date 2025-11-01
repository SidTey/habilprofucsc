import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react({
            // Disable fast refresh to avoid the preamble detection error in this environment
            fastRefresh: false,
            // Some plugin versions use `refresh` option name
            refresh: false,
        }),
    ],
});
