<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Web
|--------------------------------------------------------------------------
|
| Aquí solo vamos a dejar la ruta "catch-all" que carga tu
| aplicación de React.
|
*/

// --- RUTA "CATCH-ALL" PARA REACT ---
// Esta ruta carga tu app de React. Debe ir AL FINAL.
Route::get('/{any?}', function () {
    return view('welcome'); // Carga resources/views/welcome.blade.php
})->where('any', '.*');
