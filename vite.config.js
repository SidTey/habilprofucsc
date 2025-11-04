import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react'; // <-- (A) Esta línea debe existir

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.jsx'], // <-- (B) Asegúrate que la ruta es correcta
            refresh: true,
        }),
        react(), // <-- (C) Esta línea DEBE estar aquí
    ],
});
