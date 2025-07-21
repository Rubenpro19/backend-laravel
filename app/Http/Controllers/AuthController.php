<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Función para iniciar sesión y generar token
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }
        $user = Auth::user();
        $token = $user->createToken('AppToken')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    // Función para obtener el perfil del usuario autenticado
    public function perfil(Request $request)
    {
        return response()->json($request->user());
    }

    // Función para registrar un nuevo usuario
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json([
            'user' => $user,
        ], 201);
    }

    // Función para actualizar los datos del usuario autenticado
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $user
        ]);
    }

    // Función para listar todos los usuarios (solo para uso administrativo)
    public function verUsuarios()
    {
        $usuarios = User::all();
        return response()->json($usuarios);
    }
}