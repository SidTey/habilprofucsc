<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IngresoHabilitaciones;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas para obtener datos
Route::get('/alumnos-disponibles', [IngresoHabilitaciones::class, 'getAlumnosDisponibles']);
Route::get('/profesores-disponibles', [IngresoHabilitaciones::class, 'getProfesoresDisponibles']);

// Ruta para crear la habilitación
Route::post('/habilitacion-profesional', [IngresoHabilitaciones::class, 'store']);
