<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prioridades', function (Blueprint $table) {
            $table->id('id_prioridad');
            $table->string('nombre', 50)->unique();
            $table->integer('nivel')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prioridades');
    }
};