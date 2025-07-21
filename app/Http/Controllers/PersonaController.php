<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonaController extends Controller
{
    // Listar todas las personas
    public function index()
    {
        return response()->json(Persona::with('user')->get());
    }

    // Crear una nueva persona
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cedula' => 'nullable|string|unique:persona,cedula',
            'fecha_nacimiento' => 'nullable|date',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
        ]);

        $persona = Persona::create(array_merge([
            'user_id' => Auth::id()
        ], $validated));

        return response()->json($persona, 201);
    }

    // Mostrar una persona específica
    public function show($id)
    {
        $persona = Persona::with('user')->findOrFail($id);
        return response()->json($persona);
    }

    // Actualizar una persona
    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);
        $validated = $request->validate([
            'cedula' => 'string|unique:persona,cedula,' . $id,
            'fecha_nacimiento' => 'nullable|date',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
        ]);
        $persona->update($validated);
        return response()->json($persona);
    }

    // Eliminar una persona
    public function destroy($id)
    {
        $persona = Persona::findOrFail($id);
        $persona->delete();
        return response()->json(['message' => 'Persona eliminada correctamente']);
    }
}
