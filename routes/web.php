<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HabilitacionController;

Route::get('/', function () {
    return 'Página de inicio - Aquí irá el login';
});

Route::get('/habilitacion/agregar', function () {
    return view('AgregarHabilitacion');
});

/* Rutas para listados R4 */
Route::get('/habilitacion', function(){ return redirect('/habilitacion/listado'); });
// Mostrar la vista de prueba como página definitiva para el listado
Route::get('/habilitacion/listado', function(){ return view('habilitacion.test'); })->name('habilitacion.listado');
Route::post('/habilitacion/listado', [HabilitacionController::class, 'listadoSemestral'])->name('habilitacion.listado.post');
Route::post('/habilitacion/historico', [HabilitacionController::class, 'listadoHistorico'])->name('habilitacion.historico');

// Rutas de prueba / API para consumir desde JS en entorno de desarrollo
Route::get('/habilitacion/test', function(){ return view('habilitacion.test'); })->name('habilitacion.test');
Route::get('/habilitacion/api/semestral', [HabilitacionController::class, 'listadoSemestralJson'])->name('habilitacion.api.semestral');
Route::get('/habilitacion/api/historico', [HabilitacionController::class, 'listadoHistoricoJson'])->name('habilitacion.api.historico');
