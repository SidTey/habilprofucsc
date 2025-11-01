<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Página de inicio - Aquí irá el login';
});

Route::get('/habilitacion/agregar', function () {
    return view('AgregarHabilitacion');
});
