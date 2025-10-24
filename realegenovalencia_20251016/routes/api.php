<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZonaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/zonas',[ZonaController::class,'obtenerZonas']);
Route::get('/zona/{idzona}',[ZonaController::class,'obtenerZona']);
Route::get('/zonaspais/{idpais}',[ZonaController::class,'obtenerZonaPais']);
Route::post('/nuevazona',[ZonaController::class,'crearZona']);

