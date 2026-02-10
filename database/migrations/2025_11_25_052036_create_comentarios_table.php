<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id('id_comentario');
            $table->foreignId('ticket_id')->constrained('tickets', 'id_ticket')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios', 'id_usuario')->onDelete('restrict');
            $table->text('contenido');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('ticket_id');
            $table->index('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};