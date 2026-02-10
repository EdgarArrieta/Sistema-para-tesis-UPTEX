<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('id_ticket');
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->foreignId('usuario_id')->constrained('usuarios', 'id_usuario')->onDelete('restrict');
            $table->foreignId('area_id')->constrained('areas', 'id_area')->onDelete('restrict');
            $table->foreignId('prioridad_id')->constrained('prioridades', 'id_prioridad')->onDelete('restrict');
            $table->foreignId('estado_id')->constrained('estados', 'id_estado')->onDelete('restrict');
            $table->foreignId('tecnico_asignado_id')->nullable()->constrained('usuarios', 'id_usuario')->onDelete('set null');
            $table->dateTime('fecha_creacion');
            $table->dateTime('fecha_cierre')->nullable();
            $table->text('solucion')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('usuario_id');
            $table->index('area_id');
            $table->index('estado_id');
            $table->index('tecnico_asignado_id');
            $table->index('fecha_creacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};