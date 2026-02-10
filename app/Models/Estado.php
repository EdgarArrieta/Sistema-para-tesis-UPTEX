<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'estados';
    protected $primaryKey = 'id_estado';

    protected $fillable = [
        'nombre',
        'tipo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'estado_id', 'id_estado');
    }

    const TIPO_ABIERTO = 'abierto';
    const TIPO_EN_PROCESO = 'en_proceso';
    const TIPO_PENDIENTE = 'pendiente';
    const TIPO_RESUELTO = 'resuelto';
    const TIPO_CERRADO = 'cerrado';
    const TIPO_CANCELADO = 'cancelado';

    public function getColorAttribute()
    {
        return match($this->tipo) {
            self::TIPO_ABIERTO => '#3B82F6',
            self::TIPO_EN_PROCESO => '#F59E0B',
            self::TIPO_PENDIENTE => '#EAB308',
            self::TIPO_RESUELTO => '#10B981',
            self::TIPO_CERRADO => '#6B7280',
            self::TIPO_CANCELADO => '#EF4444',
            default => '#64748B',
        };
    }
}