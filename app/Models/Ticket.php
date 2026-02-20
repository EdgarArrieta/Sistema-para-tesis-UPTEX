<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'id_ticket';
    public $timestamps = true;
    
    protected $fillable = [
        'titulo',
        'descripcion',
        'usuario_id',
        'area_id',
        'prioridad_id',
        'estado_id',
        'tecnico_asignado_id',
        'fecha_creacion',
        'fecha_cierre',
        'solucion',
        'id_estado',
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];
    
    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id_usuario');
    }
    
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id_area');
    }
    
    public function prioridad()
    {
        return $this->belongsTo(Prioridad::class, 'prioridad_id', 'id_prioridad');
    }
    
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id', 'id_estado');
    }
    
    public function tecnicoAsignado()
    {
        return $this->belongsTo(Usuario::class, 'tecnico_asignado_id', 'id_usuario');
    }
    
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'ticket_id', 'id_ticket');
    }
}