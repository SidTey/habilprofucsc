<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginProfesorController;
use App\Http\Controllers\RegisterProfesorController;

/*
|--------------------------------------------------------------------------
| Rutas API
|--------------------------------------------------------------------------
|
| Aquí vamos a añadir manualmente el middleware de "sesión"
| para que tu LoginProfesorController (que usa sesiones) funcione.
|
*/

// --- ¡LA SOLUCIÓN! ---
// Añadimos el middleware de sesión ('web') a todas nuestras rutas.
// Esto soluciona el Error 500.
Route::middleware([
    // Esto es (básicamente) el grupo 'web', pero sin el CSRF (que da el Error 419)
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
])->group(function () {

    // --- RUTAS DE AUTENTICACIÓN ---

    // Ruta de Login: /api/login
    Route::post('/login', [LoginProfesorController::class, 'store'])->name('login');

    // Rutas que requieren estar logueado (auth:profesor)
    Route::middleware('auth:profesor')->group(function () {
        // Ruta de Logout: /api/logout
        Route::post('/logout', [LoginProfesorController::class, 'destroy'])->name('logout');

        // Ruta para verificar quién está logueado: /api/user
        Route::get('/user', [LoginProfesorController::class, 'user'])->name('user');
    });

});
