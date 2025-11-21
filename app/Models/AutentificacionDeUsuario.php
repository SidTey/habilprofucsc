<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AutentificacionDeUsuario extends Authenticatable
{
    use Notifiable;


    protected $table = 'autentificacion_de_usuario';
    protected $primaryKey = 'rut_admin';
    public $incrementing = false;
    protected $keyType = 'integer';
    public $timestamps = false;

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
        'contraseña',
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
     * 2. Le dice a Laravel
     * que el campo 'password' del formulario debe usar 'contraseña' en la BD.
     */
    public function getAuthPassword()
    {
        return $this->contraseña;
    }


}
