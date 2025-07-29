<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']); //usada en el front
Route::post('/register', [AuthController::class, 'register']); //usada en el front
Route::post('/logout', [AuthController::class, 'logout']); //usada en el front

//Ruta para llamar al controlador de roles
Route::get('/roles', [RoleController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'index']); //usada en el front
    Route::get('/perfil', [UserController::class, 'perfil']); //usada en el front
    Route::put('/user', [UserController::class, 'update']); //usada en el front
    Route::put('/user/admin/{id}', [UserController::class, 'updateByAdmin']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    // CRUD de Persona
    Route::get('/personas', [PersonaController::class, 'index']);
    Route::post('/personas', [PersonaController::class, 'store']);
    Route::get('/personas/{id}', [PersonaController::class, 'show']);
    Route::put('/personas/{id}', [PersonaController::class, 'update']);
    Route::delete('/personas/{id}', [PersonaController::class, 'destroy']);

    Route::post('/nutricionista/turnos/generar', [TurnoController::class, 'generarTurnos']);
    Route::get('/nutricionista/turnos/fecha', [TurnoController::class, 'obtenerTurnosPorFecha']);
    Route::get('/nutricionista/turnos', [TurnoController::class, 'listarTodosLosTurnos']);
});
