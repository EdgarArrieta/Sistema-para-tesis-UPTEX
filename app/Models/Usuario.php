<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    // AQUI ESTÁ EL CAMBIO: Se agregó HasApiTokens al final de esta lista
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'password',
        'id_rol',
        'activo',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'usuario_id', 'id_usuario');
    }

    public function ticketsAsignados()
    {
        return $this->hasMany(Ticket::class, 'tecnico_asignado_id', 'id_usuario');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'usuario_id', 'id_usuario');
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function esAdministrador()
    {
        return $this->rol->nombre === Rol::ROL_ADMINISTRADOR;
    }

    public function esTecnico()
    {
        return $this->rol->nombre === Rol::ROL_TECNICO;
    }

    public function esUsuarioNormal()
    {
        return $this->rol->nombre === Rol::ROL_USUARIO_NORMAL;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    public function username()
    {
        return 'correo';
    }
}