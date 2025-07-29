<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TurnoController extends Controller
{
    public function listarTodosLosTurnos()
    {
        $user = Auth::user();

        if ($user->roles_id !== 2) {
            return response()->json(['error' => 'Solo los nutricionistas pueden ver sus turnos'], 403);
        }

        $turnos = Turno::where('nutricionista_id', $user->id)
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        // Actualizar dinámicamente el estado
        $now = now();
        foreach ($turnos as $turno) {
            $fechaHoraTurno = \Carbon\Carbon::parse("{$turno->fecha} {$turno->hora_inicio}");
            if ($turno->estado === 'disponible' && $fechaHoraTurno->lt($now)) {
                $turno->estado = 'caducado';
            }
        }

        $agrupadosPorFecha = $turnos->groupBy('fecha');

        return response()->json([
            'total' => $turnos->count(),
            'turnos' => $agrupadosPorFecha
        ]);
    }


    public function obtenerTurnosPorFecha(Request $request)
    {
        $user = Auth::user();

        if ($user->roles_id !== 2) {
            return response()->json(['error' => 'Solo los nutricionistas pueden ver sus turnos'], 403);
        }

        $request->validate([
            'fecha' => 'required|date',
        ]);

        $fecha = $request->fecha;

        $turnos = Turno::where('nutricionista_id', $user->id)
            ->where('fecha', $fecha)
            ->orderBy('hora_inicio')
            ->get();

        $now = now();
        foreach ($turnos as $turno) {
            $fechaHoraTurno = \Carbon\Carbon::parse("{$turno->fecha} {$turno->hora_inicio}");
            if ($turno->estado === 'disponible' && $fechaHoraTurno->lt($now)) {
                $turno->estado = 'caducado';
            }
        }

        return response()->json([
            'fecha' => $fecha,
            'turnos' => $turnos
        ]);
    }


    // Generar turnos para el nutricionista autenticado
    public function generarTurnos(Request $request)
    {
        $user = Auth::user();

        if ($user->roles_id !== 2) {
            return response()->json(['error' => 'Solo los nutricionistas pueden generar turnos'], 403);
        }

        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'descanso_inicio' => 'required|date_format:H:i',
            'descanso_fin' => 'required|date_format:H:i|after:descanso_inicio',
        ]);

        if (Carbon::parse($request->fecha_inicio)->lt(Carbon::today())) {
            return response()->json([
                'message' => 'No se puede generar turnos en fechas pasadas.'
            ], 422);
        }

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        $horaInicio = $request->hora_inicio;
        $horaFin = $request->hora_fin;
        $descansoInicio = $request->descanso_inicio;
        $descansoFin = $request->descanso_fin;

        $turnosCreados = [];

        for ($fecha = $fechaInicio->copy(); $fecha->lte($fechaFin); $fecha->addDay()) {
            $horaActual = Carbon::parse($fecha->format('Y-m-d') . ' ' . $horaInicio);
            $horaFinDelDia = Carbon::parse($fecha->format('Y-m-d') . ' ' . $horaFin);
            $inicioDescanso = Carbon::parse($fecha->format('Y-m-d') . ' ' . $descansoInicio);
            $finDescanso = Carbon::parse($fecha->format('Y-m-d') . ' ' . $descansoFin);

            while ($horaActual->lt($horaFinDelDia)) {
                $horaFinTurno = $horaActual->copy()->addHour();

                // Verificar si el turno cae en el horario de descanso
                if (
                    $horaActual->between($inicioDescanso, $finDescanso->copy()->subSecond()) ||
                    $horaFinTurno->between($inicioDescanso->copy()->addSecond(), $finDescanso)
                ) {
                    $horaActual->addHour();
                    continue;
                }

                // Verificar si ya existe un turno en ese horario
                $existe = Turno::where('nutricionista_id', $user->id)
                    ->where('fecha', $fecha->toDateString())
                    ->where('hora_inicio', $horaActual->format('H:i:s'))
                    ->exists();

                if (!$existe) {
                    $turno = Turno::create([
                        'nutricionista_id' => $user->id,
                        'fecha' => $fecha->toDateString(),
                        'hora_inicio' => $horaActual->format('H:i:s'),
                        'hora_fin' => $horaFinTurno->format('H:i:s'),
                        'estado' => 'disponible',
                    ]);

                    $turnosCreados[] = $turno;
                }
                $horaActual->addHour();
            }
        }

        if (count($turnosCreados) === 0) {
            return response()->json([
                'message' => 'No se generaron nuevos turnos. Todos los turnos en el rango ya existen.',
                'cantidad' => 0,
                'turnos' => [],
            ]);
        }

        return response()->json([
            'message' => 'Turnos generados correctamente',
            'cantidad' => count($turnosCreados),
            'turnos' => $turnosCreados,
        ]);
    }
}
