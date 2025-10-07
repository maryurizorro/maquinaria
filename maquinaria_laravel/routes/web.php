<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return view('welcome');
});

// Las rutas de Swagger se registran automáticamente por el paquete L5-Swagger
// No es necesario definirlas manualmente aquí



