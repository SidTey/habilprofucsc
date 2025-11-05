<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // ¡Importante!
use Illuminate\Notifications\Notifiable;

class AutentificacionDeUsuario extends Authenticatable // ¡Importante!
{
    use Notifiable;

    protected $table = 'autentificacion_de_usuarios';
    protected $primaryKey = 'rut_profesor';
    public $incrementing = false;
    protected $keyType = 'integer';
    public $timestamps = false; // Asumiendo que no tienes created_at/updated_at

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'rut_profesor',
        'correo_profesor',
        'contraseña', // El nombre de tu columna en la BD
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
    public function profesor()
    {
        // La llave foránea es 'rut_profesor', la llave local es 'rut_profesor'
        return $this->belongsTo(Profesor::class, 'rut_profesor', 'rut_profesor');
    }
}
