<?php 
//ESTO ES DE LA BASE DE DATOS FANTASMA (NOMINA DE LOS ALUMNOS)
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NominaAlumno extends Model
{
    protected $connection = 'db_fantasma'; 
    protected $table = 'nomina_alumno';    
    public $timestamps = false;
    protected $primaryKey = 'rut_alumno';  
    public $incrementing = false;
    protected $keyType = 'int';            

    public function notaHabilitacion()
    {
        return $this->hasOne(NotasEnLinea::class, 'rut_alumno_n', 'rut_alumno')
                   ->where('asignatura', 'Habilitacion Profesional');
    }
}