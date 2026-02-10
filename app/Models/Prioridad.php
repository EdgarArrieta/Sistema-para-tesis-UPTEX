<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prioridad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prioridades';
    protected $primaryKey = 'id_prioridad';

    protected $fillable = [
        'nombre',
        'nivel',
    ];

    protected $casts = [
        'nivel' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'prioridad_id', 'id_prioridad');
    }

    const NIVEL_BAJA = 1;
    const NIVEL_MEDIA = 2;
    const NIVEL_ALTA = 3;
    const NIVEL_CRITICA = 4;

    public function getColorAttribute()
    {
        return match($this->nivel) {
            self::NIVEL_BAJA => '#10B981',
            self::NIVEL_MEDIA => '#F59E0B',
            self::NIVEL_ALTA => '#EF4444',
            self::NIVEL_CRITICA => '#DC2626',
            default => '#64748B',
        };
    }
}