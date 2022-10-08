<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();
        // Validaciones
        $validator = Validator::make($data, [
            'nombres' => 'required|max:255',
            'apellidos' => 'required|max:255',
            'correo' => 'required|email|unique:usuarios',
            'contrasena' => 'required|confirmed',
        ]);
        // Si hay errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 400);
        }
        // Encripatando contraseña
        $data['contrasena'] = bcrypt($data['contrasena']);
        // Creando usuario
        $user = User::create($data);
        $user->save();
        // Retornando respuesta
        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitósamente',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->all();
        // Validaciones
        $validator = Validator::make($data, [
            'correo' => 'required|email',
            'contrasena' => 'required',
        ]);
        // Si hay errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 400);
        }
        // Buscando usuario
        $user = User::where('correo', $data['correo'])->first();
        // Si no existe el usuario
        if (!$user || !Hash::check($data['contrasena'], $user->contrasena)) {
            return response()->json([
                'success' => false,
                'message' => 'Correo o contraseña incorrectos',
            ], 404);
        }
        // Creando token
        $token = $user->createToken('auth_token');
        $token->accessToken->expires_at = Carbon::now()->addHours(3);
        $token->accessToken->save();
        // Retornando respuesta
        return response()->json([
            'success' => true,
            'message' => 'Usuario autenticado exitósamente',
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->accessToken->expires_at)->toDateTimeString()
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        // $request->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitósamente',
        ], 200);
    }
}
