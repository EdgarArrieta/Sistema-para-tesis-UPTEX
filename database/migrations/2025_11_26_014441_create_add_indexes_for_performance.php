<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Ya hay índices creados en las migraciones principales
        // Esta migración puede quedarse vacía o ser eliminada
    }

    public function down()
    {
        // Nothing to rollback
    }
};