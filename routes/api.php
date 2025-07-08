<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/user', [AuthController::class, 'verUsuarios']);
Route::middleware('auth:sanctum')->put('/user', [AuthController::class, 'update']);
Route::middleware('auth:sanctum')->get('/perfil', [AuthController::class, 'perfil']);
