<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs/openapi.json', function () {
    $path = storage_path('api-docs/api-docs.json');
    abort_unless(File::exists($path), 404, 'openapi.json no encontrado');
    return response()->file($path, ['Content-Type' => 'application/json']);
})->name('docs.openapi');

