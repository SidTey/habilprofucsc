<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IngresoHabilitaciones;
use App\Http\Controllers\LoginProfesorController;


/*
|--------------------------------------------------------------------------
| Rutas API
|--------------------------------------------------------------------------
|
|
*/

Route::middleware([
    // Esto es (básicamente) el grupo 'web', pero sin el CSRF (que da el Error 419)
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
])->group(function () {

    // Ruta de Login: /api/login
    Route::post('/login', [LoginProfesorController::class, 'store'])->name('login');

    // Rutas que requieren estar logueado (auth:profesor)
    Route::middleware('auth:profesor')->group(function () {
        // Ruta de Logout: /api/logout
        Route::post('/logout', [LoginProfesorController::class, 'destroy'])->name('api.logout');

        // Ruta para verificar quién está logueado: /api/user
        Route::get('/user', [LoginProfesorController::class, 'user'])->name('api.user');
    });

});


Route::middleware('auth:sanctum')->get('/user-sanctum', function (Request $request) {
    return $request->user();
});

// Rutas para obtener datos
Route::get('/alumnos-disponibles', [IngresoHabilitaciones::class, 'getAlumnosDisponibles']);
Route::get('/profesores-disponibles', [IngresoHabilitaciones::class, 'getProfesoresDisponibles']);

// Ruta para crear la habilitación
Route::post('/habilitacion-profesional', [IngresoHabilitaciones::class, 'store']);

// Rutas para buscar, actualizar y eliminar habilitación (R3.3, R3.4)
Route::get('/habilitacion-profesional/{id}', [IngresoHabilitaciones::class, 'show']);
Route::put('/habilitacion-profesional/{id}', [IngresoHabilitaciones::class, 'update']);
Route::delete('/habilitacion-profesional/{id}', [IngresoHabilitaciones::class, 'destroy']);
