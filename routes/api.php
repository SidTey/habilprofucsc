<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IngresoHabilitaciones;
use App\Http\Controllers\LoginProfesorController;
use App\Http\Controllers\RegisterProfesorController;

/*
|--------------------------------------------------------------------------
| Rutas API
|--------------------------------------------------------------------------
*/

// --- RUTAS DE AUTENTICACIÓN (de tu amigo) ---
Route::middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
])->group(function () {

    // Ruta de Login: /api/login
    Route::post('/login', [LoginProfesorController::class, 'store'])->name('login');

    // ✅ NUEVA RUTA: Olvidé Contraseña (Paso 1: Solicitud de código)
    Route::post('/forgot-password', [LoginProfesorController::class, 'sendPasswordResetLink']);

    // ✅ NUEVA RUTA: Restablecer Contraseña (Paso 2: Uso del código)
    Route::post('/reset-password', [LoginProfesorController::class, 'resetPassword']); // <-- ¡AGREGADA!

    // Rutas que requieren estar logueado (auth:profesor)
    Route::middleware('auth:profesor')->group(function () {
        // Ruta de Logout: /api/logout
        Route::post('/logout', [LoginProfesorController::class, 'destroy'])->name('api.logout');

        // Ruta para verificar quién está logueado: /api/user
        Route::get('/user', [LoginProfesorController::class, 'user'])->name('api.user');
    });

});

// --- RUTAS ORIGINALES (tus rutas) ---
Route::middleware('auth:sanctum')->get('/user-sanctum', function (Request $request) {
    return $request->user();
});

// Rutas para obtener datos
Route::get('/alumnos-disponibles', [IngresoHabilitaciones::class, 'getAlumnosDisponibles']);
Route::get('/profesores-disponibles', [IngresoHabilitaciones::class, 'getProfesoresDisponibles']);

// Ruta para crear la habilitación
Route::post('/habilitacion-profesional', [IngresoHabilitaciones::class, 'store']);
