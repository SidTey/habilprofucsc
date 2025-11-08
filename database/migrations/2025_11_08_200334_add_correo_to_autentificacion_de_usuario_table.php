<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCorreoToAutentificacionDeUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('autentificacion_de_usuario', function (Blueprint $table) {
            // Añadimos la columna 'correo'. Debe ser única si es el identificador de reseteo.
            $table->string('correo')->unique()->nullable()->after('contraseña');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('autentificacion_de_usuario', function (Blueprint $table) {
            $table->dropColumn('correo');
        });
    }
};
