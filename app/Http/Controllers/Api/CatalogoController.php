<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\Area;
use App\Models\Prioridad;
use App\Models\Estado;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    /**
     * Listar todos los roles
     */
    public function roles()
    {
        $roles = Rol::all();

        return response()->json([
            'success' => true,
            'data' => $roles,
        ], 200);
    }

    /**
     * Listar todas las áreas
     */
    public function areas()
    {
        $areas = Area::all();

        return response()->json([
            'success' => true,
            'data' => $areas,
        ], 200);
    }

    /**
     * Listar todas las prioridades
     */
    public function prioridades()
    {
        $prioridades = Prioridad::all()->map(function($prioridad) {
            return [
                'id_prioridad' => $prioridad->id_prioridad,
                'nombre' => $prioridad->nombre,
                'nivel' => $prioridad->nivel,
                'color' => $prioridad->color,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $prioridades,
        ], 200);
    }

    /**
     * Listar todos los estados
     */
    public function estados()
    {
        $estados = Estado::all()->map(function($estado) {
            return [
                'id_estado' => $estado->id_estado,
                'nombre' => $estado->nombre,
                'tipo' => $estado->tipo,
                'color' => $estado->color,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $estados,
        ], 200);
    }

    /**
     * Obtener todos los catálogos en una sola petición
     */
    public function todos()
    {
        $catalogos = [
            'roles' => Rol::all(),
            'areas' => Area::all(),
            'prioridades' => Prioridad::all()->map(function($p) {
                return [
                    'id_prioridad' => $p->id_prioridad,
                    'nombre' => $p->nombre,
                    'nivel' => $p->nivel,
                    'color' => $p->color,
                ];
            }),
            'estados' => Estado::all()->map(function($e) {
                return [
                    'id_estado' => $e->id_estado,
                    'nombre' => $e->nombre,
                    'tipo' => $e->tipo,
                    'color' => $e->color,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $catalogos,
        ], 200);
    }
}