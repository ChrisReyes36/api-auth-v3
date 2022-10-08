<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TareaController extends Controller
{
    public function index()
    {
        // Obteniendo tareas
        $tareas = Tarea::all();
        // Retornando respuesta
        return response()->json([
            'success' => true,
            'message' => 'Lista de tareas',
            'tareas' => $tareas
        ], 200);
    }

    public function store(Request $request)
    {
        // Obteniendo datos
        $data = $request->all();
        // Validaciones
        $validator = Validator::make($data, [
            'nombre' => 'required|max:255|unique:tareas',
            'descripcion' => 'required',
            // 'estado' => 'required|in:pendiente,completada',
        ]);
        // Si hay errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 400);
        }
        // Estado por defecto
        $data['estado'] = 'pendiente';
        // Creando tarea
        $tarea = Tarea::create($data);
        $tarea->save();
        // Retornando respuesta
        return response()->json([
            'success' => true,
            'message' => 'Tarea creada exitósamente',
            'tarea' => $tarea
        ], 201);
    }

    public function show($id)
    {
        // Obteniendo tarea
        $tarea = Tarea::find($id);
        // Verificando si la tarea existe
        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }
        // Retornando respuesta
        return response()->json([
            'success' => true,
            'message' => 'Tarea encontrada',
            'tarea' => $tarea
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Obteniendo datos
        $data = $request->all();
        // Validaciones
        $validator = Validator::make($data, [
            'nombre' => 'required|max:255|unique:tareas,nombre,' . $id,
            'descripcion' => 'required',
            // 'estado' => 'required|in:pendiente,finalizada',
        ]);
        // Si hay errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 400);
        }
        // Actualizando tarea
        $tarea = Tarea::find($id);
        $tarea->nombre = $data['nombre'];
        $tarea->descripcion = $data['descripcion'];
        // $tarea->estado = $data['estado'];
        $tarea->save();
        // Retornando respuesta
        return response()->json([
            'success' => true,
            'message' => 'Tarea actualizada exitósamente',
            'tarea' => $tarea
        ], 200);
    }

    public function destroy($id)
    {
        // Obteniendo tarea
        $tarea = Tarea::find($id);
        // Verificando si la tarea existe
        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }
        // Eliminando tarea
        $tarea->delete();
        // Retornando respuesta
        return response()->json([
            'success' => true,
            'message' => 'Tarea eliminada exitósamente'
        ], 200);
    }
}
