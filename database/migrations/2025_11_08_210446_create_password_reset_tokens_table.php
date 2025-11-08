<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usaremos 'rut_admin' como el identificador del usuario.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('rut_admin')->primary(); // Clave primaria: el rut del usuario
            $table->string('token'); // El código de seguridad o token hasheado
            $table->timestamp('created_at')->nullable(); // Para saber cuándo expira
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
