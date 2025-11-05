<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'rut_empresa';
    public $incrementing = false;
    protected $table = 'empresa';
    protected $fillable = ['rut_empresa','nombre_empresa'];


    public static function validationRules()
    {
        return [
            'rut_empresa' => 'required|integer|min:1000000|max:60000000|unique:alumnos,rut_alumno',
            'nombre_empresa' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',

        ];
    }
}
