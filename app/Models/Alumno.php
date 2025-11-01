<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumno';

    protected $primaryKey = 'rut_alumno';

    public $incrementing = false;

    protected $keyType = 'integer';

    protected $fillable = [
        'rut_alumno',
        'nombre_alumno',
        'email_alumno',
    ];
    public static function validationRules()
    {
        return [
            'rut_alumno' => 'required|integer|min:1000000|max:60000000|unique:alumnos,rut_alumno',
            'nombre_alumno' => 'required|string|max:100|regex:/^[a-zA-Z\s\áéíóúÁÉÍÓÚñÑ]+$/',
            'correo_alumno' => 'required|string|max:255|email',
        ];
    }

    /**
     * Relación con registros UCSC (La mantenemos).
     */
    public function registrosUcsc()
    {
        return $this->hasMany(RegistroUcsc::class);
    }

    /**
     * Define la relación inversa: un alumno tiene muchas habilitaciones.
     * (Esto no lo usaremos ahora, pero es bueno tenerlo).
     */
    public function habilitaciones()
    {
        return $this->hasMany(Habilitacion::class, 'rut_alumno', 'rut_alumno');
    }
}

