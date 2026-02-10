<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (!$usuario || !$usuario->activo) {
            throw ValidationException::withMessages([
                'correo' => ['Las credenciales son incorrectas o el usuario está inactivo.'],
            ]);
        }

        if (!Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'correo' => ['Las credenciales son incorrectas.'],
            ]);
        }

        $usuario->load('rol');
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'user' => [
                    'id' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'nombre_completo' => $usuario->nombre_completo,
                    'correo' => $usuario->correo,
                    'rol' => [
                        'id' => $usuario->rol->id_rol,
                        'nombre' => $usuario->rol->nombre,
                    ],
                    'activo' => $usuario->activo,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|max:150|unique:usuarios,correo',
            'password' => 'required|string|min:6|confirmed',
            'id_rol' => 'nullable|exists:roles,id_rol',
        ]);

        if (!isset($validated['id_rol'])) {
            $validated['id_rol'] = 3;
        }

        $usuario = Usuario::create([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'correo' => $validated['correo'],
            'password' => $validated['password'],
            'id_rol' => $validated['id_rol'],
            'activo' => true,
        ]);

        $usuario->load('rol');
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => [
                    'id' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'nombre_completo' => $usuario->nombre_completo,
                    'correo' => $usuario->correo,
                    'rol' => [
                        'id' => $usuario->rol->id_rol,
                        'nombre' => $usuario->rol->nombre,
                    ],
                    'activo' => $usuario->activo,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente',
        ], 200);
    }

    public function me(Request $request)
    {
        $usuario = $request->user();
        $usuario->load('rol');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'nombre_completo' => $usuario->nombre_completo,
                    'correo' => $usuario->correo,
                    'rol' => [
                        'id' => $usuario->rol->id_rol,
                        'nombre' => $usuario->rol->nombre,
                    ],
                    'activo' => $usuario->activo,
                    'email_verified_at' => $usuario->email_verified_at,
                    'created_at' => $usuario->created_at,
                ],
            ],
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $usuario = $request->user();

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'correo' => 'sometimes|email|max:150|unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario',
            'password' => 'sometimes|string|min:6|confirmed',
        ]);

        if (isset($validated['nombre'])) {
            $usuario->nombre = $validated['nombre'];
        }
        if (isset($validated['apellido'])) {
            $usuario->apellido = $validated['apellido'];
        }
        if (isset($validated['correo'])) {
            $usuario->correo = $validated['correo'];
        }
        if (isset($validated['password'])) {
            $usuario->password = $validated['password'];
        }

        $usuario->save();
        $usuario->load('rol');

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado exitosamente',
            'data' => [
                'user' => [
                    'id' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'nombre_completo' => $usuario->nombre_completo,
                    'correo' => $usuario->correo,
                    'rol' => [
                        'id' => $usuario->rol->id_rol,
                        'nombre' => $usuario->rol->nombre,
                    ],
                    'activo' => $usuario->activo,
                ],
            ],
        ], 200);
    }

    public function refresh(Request $request)
    {
        $usuario = $request->user();
        $request->user()->currentAccessToken()->delete();
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token renovado exitosamente',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }
}