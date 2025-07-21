<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Creada para entorno de prueba para visualizar todos los usarios 
Route::get('/user', [AuthController::class, 'verUsuarios']); 

// Rutas protegidas con Laravel Sanctum
Route::middleware('auth:sanctum')->put('/user', [AuthController::class, 'update']);
Route::middleware('auth:sanctum')->get('/perfil', [AuthController::class, 'perfil']);

// CRUD de Persona
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/personas', [\App\Http\Controllers\PersonaController::class, 'index']);
    Route::post('/personas', [\App\Http\Controllers\PersonaController::class, 'store']);
    Route::get('/personas/{id}', [\App\Http\Controllers\PersonaController::class, 'show']);
    Route::put('/personas/{id}', [\App\Http\Controllers\PersonaController::class, 'update']);
    Route::delete('/personas/{id}', [\App\Http\Controllers\PersonaController::class, 'destroy']);
});