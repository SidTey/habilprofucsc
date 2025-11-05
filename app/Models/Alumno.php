<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumno';
    protected $primaryKey = 'rut_alumno';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'rut_alumno',
        'nombre_alumno',
        'correo_alumno'
    ];

    /**
     * Reglas de validación según requisitos R1.1, R1.2, R1.3
     */
    public static function validationRules()
    {
        return [
            'rut_alumno' => 'required|integer|min:1000000|max:60000000|unique:alumno,rut_alumno',
            'nombre_alumno' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'correo_alumno' => 'required|string|max:255|email'
        ];
    }

    /**
     * Relación con registros UCSC
     */
    public function registrosUcsc()
    {
        return $this->hasMany(RegistroUcsc::class);
    }
}
