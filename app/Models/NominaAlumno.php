<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NominaAlumno extends Model
{
    protected $connection = 'db_fantasma'; // 1. Apunta a la BD fantasma
    protected $table = 'nomina_alumno';    // 2. Nombre de la tabla fantasma
    public $timestamps = false;
    protected $primaryKey = 'rut_alumno';  // 3. Su ID es el RUT
    public $incrementing = false;
    protected $keyType = 'int';            // 4. Es un entero

    // R1.11.1: Define la relación con la nota de Habilitación Profesional
    public function notaHabilitacion()
    {
        return $this->hasOne(NotasEnLinea::class, 'rut_alumno_n', 'rut_alumno')
                   ->where('asignatura', 'Habilitacion Profesional');
    }
}