<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // ¡Importante!
use Illuminate\Notifications\Notifiable;

class AutentificacionDeUsuario extends Authenticatable // ¡Importante!
{
    use Notifiable;

    // Ajuste: el volcado de la BD (database/backup/habilprof.sql) contiene
    // la tabla `autentificacion_de_usuario` (singular) con PK `rut_admin`.
    // Para evitar el error "Undefined table" mapeamos el modelo a esa tabla.
    protected $table = 'autentificacion_de_usuario';
    protected $primaryKey = 'rut_admin';
    public $incrementing = false;
    protected $keyType = 'integer';
    public $timestamps = false; // no hay created_at/updated_at

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'rut_admin',
        'contraseña', // El nombre de la columna en la BD según el dump
    ];

    /**
     * Los atributos que deben ocultarse.
     */
    protected $hidden = [
        'contraseña', // El nombre de tu columna
        'remember_token',
    ];

    /**
     * 1. Le dice a Laravel que tu columna de contraseña se llama 'contraseña'.
     */
    public function getAuthPasswordName()
    {
        return 'contraseña';
    }

    /**
     * 2. (OPCIONAL PERO RECOMENDADO) Le dice a Laravel
     * que el campo 'password' del formulario debe usar 'contraseña' en la BD.
     */
    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    /**
     * 3. Define la relación: Una autenticación pertenece a un Profesor.
     */
    // En el dump la tabla de admins no tiene relación directa con `profesor`.
    // Si más adelante quieres mapearla, descomenta y ajusta las llaves.
    // public function profesor()
    // {
    //     return $this->belongsTo(Profesor::class, 'rut_admin', 'rut_profesor');
    // }
}
