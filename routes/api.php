<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Creada para entorno de prueba para visualizar todos los usarios 
Route::get('/user', [AuthController::class, 'verUsuarios']); 

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user', [AuthController::class, 'update']);
    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::delete('/user/{id}', [AuthController::class, 'destroy']);
    
    // CRUD de Persona
    Route::get('/personas', [\App\Http\Controllers\PersonaController::class, 'index']);
    Route::post('/personas', [\App\Http\Controllers\PersonaController::class, 'store']);
    Route::get('/personas/{id}', [\App\Http\Controllers\PersonaController::class, 'show']);
    Route::put('/personas/{id}', [\App\Http\Controllers\PersonaController::class, 'update']);
    Route::delete('/personas/{id}', [\App\Http\Controllers\PersonaController::class, 'destroy']);
});