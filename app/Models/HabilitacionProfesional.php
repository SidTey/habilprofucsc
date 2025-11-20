<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HabilitacionProfesional extends Model
{
    public $timestamps = false;
    protected $table = 'habilitacion_profesional';
    protected $primaryKey = 'id_habilitacion';
    public $incrementing = true;
    protected $keyType = 'integer';

    protected $fillable = [
        'rut_alumno',
        'año_semestre',
        'numero_semestre',
        'descripcion_habilitacion',
        'nota_final',
        'fecha_nota'
    ];

    protected $casts = [
        'fecha_nota' => 'date',
        'nota_final' => 'decimal:2'
    ];

    // Relación con Alumno
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'rut_alumno', 'rut_alumno');
    }

    // Relación con Asigna (profesores)
    public function asignaciones()
    {
        return $this->hasMany(Asigna::class, 'id_habilitacion', 'id_habilitacion');
    }

    // Relación con PrIng
    public function PrIng()
    {
        return $this->hasOne(Pring::class, 'id_habilitacion', 'id_habilitacion');
    }

    // Relación con Prinv
    public function PrInv()
    {
        return $this->hasOne(Prinv::class, 'id_habilitacion', 'id_habilitacion');
    }
}