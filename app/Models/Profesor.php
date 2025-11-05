<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AutentificacionDeUsuario;
use App\Models\RegistroUcsc;

class Profesor extends Model
{

    protected $table = 'profesor';
    protected $primaryKey = 'rut_profesor';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'rut_profesor',
        'nombre_profesor',
        'correo_profesor',
        'password',

    ];
    /**
     * Reglas de validación según requisitos R1.4, R1.5, R1.7
     */

    public function username()
    {
        return 'rut_profesor';
    }
    public static function validationRules()
    {
        return [
            'rut_profesor' => 'required|integer|min:10000000|max:60000000|unique:profesor,rut_profesor',
            'nombre_profesor' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',

        ];
    }

    /**
     * Relación con registros UCSC
     */

    public function autenticacion()
    {
        return $this->hasOne(AutentificacionDeUsuario::class, 'rut_profesor', 'rut_profesor');
    }
    public function registrosUcsc()
    {
        // hasMany(Modelo, 'llave_foránea_en_registros_ucsc', 'llave_local_en_profesor')
        return $this->hasMany(RegistroUcsc::class, 'profesor_id', 'rut_profesor');
    }
}
