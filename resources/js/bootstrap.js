import axios from 'axios';
window.axios = axios;

// --- CONFIGURACIÓN CENTRAL DE AXIOS ---

// 1. URL Base: Apunta a /api
const baseUrl = window.location.origin;
window.axios.defaults.baseURL = `${baseUrl}/api`;

// 2. Encabezados estándar
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';

// 3. Cookies (¡Muy importante para el login!)
window.axios.defaults.withCredentials = true;

// 4. Token CSRF (¡Muy importante para la seguridad!)
// Obtenemos el token que Laravel pone en welcome.blade.php
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
