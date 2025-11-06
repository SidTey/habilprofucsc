<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ListadosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginProfesorController;
use App\Http\Middleware\AuthenticateUser;

/*
|--------------------------------------------------------------------------
| Rutas Web
|--------------------------------------------------------------------------
|
| Sistema con React para login y vistas Blade para dashboard.
| La sesión se cierra al cerrar el navegador.
|
*/

// Ruta raíz - Redirige al login o dashboard según autenticación
Route::get('/', function () {
    if (Auth::guard('profesor')->check()) {
        return redirect('/dashboard');
    }
    return view('welcome'); // Login de React
})->name('index');

// Login
Route::get('/login', function () {
    if (Auth::guard('profesor')->check()) {
        return redirect('/dashboard');
    }
    return view('welcome'); // Login de React
})->name('login');

Route::post('/login', [LoginProfesorController::class, 'loginWeb'])->name('login.post');

// Dashboard - Requiere autenticación
Route::get('/dashboard', function () {
    if (!Auth::guard('profesor')->check()) {
        return redirect('/login');
    }
    return view('dashboard');
})->name('dashboard');

// Logout
Route::post('/logout', [LoginProfesorController::class, 'logoutWeb'])->name('logout');
Route::get('/logout', [LoginProfesorController::class, 'logoutWeb']);

// Ejemplo de ruta protegida por RUT admin. Para proteger otras rutas,
// añade ->middleware(\App\Http\Middleware\CheckRutAdmin::class) en la definición.
Route::get('/admin-only', function () {
    return view('habilitacion.test');
})->middleware(\App\Http\Middleware\CheckRutAdmin::class)->name('admin.only');

// Vistas embebidas
Route::get('/habilitacion/agregar-embed', function () {
    return view('AgregarHabilitacionEmbed');
})->name('habilitacion.agregar.embed');

Route::get('/habilitacion/historico-embed', function () {
    return view('ListadoHistoricoEmbed');
})->name('habilitacion.historico.embed');

Route::get('/habilitacion/semestral-embed', function () {
    return view('ListadoSemestralEmbed');
})->name('habilitacion.semestral.embed');

/* Rutas para listados R4 */
Route::get('/habilitacion', function(){ return redirect('/dashboard'); });
Route::get('/habilitacion/listado', function(){ return view('habilitacion.test'); })->name('habilitacion.listado');
Route::post('/habilitacion/listado', [ListadosController::class, 'listadoSemestral'])->name('habilitacion.listado.post');
Route::post('/habilitacion/historico', [ListadosController::class, 'listadoHistorico'])->name('habilitacion.historico');

// Rutas de prueba / API para consumir desde JS
Route::get('/habilitacion/test', function(){ return view('habilitacion.test'); })->name('habilitacion.test');
Route::get('/habilitacion/api/semestral', [ListadosController::class, 'listadoSemestralJson'])->name('habilitacion.api.semestral');
Route::get('/habilitacion/api/historico', [ListadosController::class, 'listadoHistoricoJson'])->name('habilitacion.api.historico');
