<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListadosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginProfesorController;
use App\Http\Middleware\AuthenticateUser;

/*
|--------------------------------------------------------------------------
| Rutas Web
|--------------------------------------------------------------------------
|
| Aquí definimos las rutas específicas ANTES del catch-all de React.
|
*/

// Ruta raíz - Redirige al login o dashboard según estado de autenticación
Route::get('/', [LoginProfesorController::class, 'showLogin'])->name('index');

// Login/Logout con LoginProfesorController (unificado con sistema del amigo)
Route::get('/login', [LoginProfesorController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginProfesorController::class, 'loginWeb'])->name('login.post');

// Cerrar sesión (unificado)
Route::post('/logout', [LoginProfesorController::class, 'logoutWeb'])->name('logout');
Route::get('/logout', [LoginProfesorController::class, 'logoutWeb']);

// Dashboard principal
Route::get('/dashboard', function () {
    return view('habilitacion.test');
})->name('dashboard');

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
Route::get('/habilitacion/api/semestral', [HabilitacionController::class, 'listadoSemestralJson'])->name('habilitacion.api.semestral');
Route::get('/habilitacion/api/historico', [HabilitacionController::class, 'listadoHistoricoJson'])->name('habilitacion.api.historico');
