<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HabilitacionController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AuthenticateUser;

// Rutas públicas (sin autenticación)
Route::get('/', function () {
    return view('habilitacion.test');
})->name('index');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

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
Route::post('/habilitacion/listado', [HabilitacionController::class, 'listadoSemestral'])->name('habilitacion.listado.post');
Route::post('/habilitacion/historico', [HabilitacionController::class, 'listadoHistorico'])->name('habilitacion.historico');

// Rutas de prueba / API para consumir desde JS
Route::get('/habilitacion/test', function(){ return view('habilitacion.test'); })->name('habilitacion.test');
Route::get('/habilitacion/api/semestral', [HabilitacionController::class, 'listadoSemestralJson'])->name('habilitacion.api.semestral');
Route::get('/habilitacion/api/historico', [HabilitacionController::class, 'listadoHistoricoJson'])->name('habilitacion.api.historico');
