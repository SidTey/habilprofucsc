<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RegistroUcsc extends Model
{
    protected $table = 'registros_ucsc';
    
    protected $fillable = [
        'alumno_id',
        'profesor_id',
        'fecha_ingreso',
        'nota_final'
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'nota_final' => 'decimal:2'
    ];

    /**
     * Reglas de validación según requisitos R1.8, R1.9
     */
    public static function validationRules()
    {
        return [
            'alumno_id' => 'required|exists:alumnos,id',
            'profesor_id' => 'required|exists:profesores,id',
            'fecha_ingreso' => 'required|date|after_or_equal:2025-01-01|before_or_equal:2050-12-31',
            'nota_final' => 'nullable|numeric|min:1.00|max:7.00'
        ];
    }

    /**
     * Relación con Alumno
     */
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    /**
     * Relación con Profesor
     */
    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }
}
