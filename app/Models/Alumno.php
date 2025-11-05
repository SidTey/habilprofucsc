<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumno';
    protected $primaryKey = 'rut_alumno';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';
    
    protected $fillable = [
        'rut_alumno',
        'nombre_alumno', 
        'correo_alumno'
    ];

    public static function validationRules()
    {
        return [
            'rut_alumno' => 'required|integer|min:1000000|max:60000000|unique:alumnos,rut_alumno',
            'nombre_alumno' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'correo_alumno' => 'required|string|max:255|email'
        ];
    }
}
