<?php

namespace App\Models; // Ojo: Asegúrate que sea 'App\Models' o 'App'

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habilitacion extends Model
{
    use HasFactory;

    protected $table = 'habilitacion_profesional';

    protected $primaryKey = 'id_habilitacion';

    public $incrementing = false;
    
    public $timestamps = false;

    protected $keyType = 'string';


    protected $fillable = [
        'id_habilitacion',
        'rut_alumno',
        
        // --- ESTOS SÍ ESTÁN EN LA TABLA ---
        'descripcion_habilitacion',
        'año_semestre',
        'numero_semestre',
        'nota_final',
        'fecha_nota',
    ];
/**
    * Obtiene el nombre de la columna que debe usarse para el "Route Model Binding".   
     *
     * @return string
     */
    public function getRouteKeyName()
    {   
        return 'id_habilitacion';
    }
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'rut_alumno', 'rut_alumno');
    }
    public function profesoresAsignados()
    {
        return $this->belongsToMany(Profesor::class, 'asigna', 'id_habilitacion', 'rut_profesor')->withPivot('rol'); // ¡IMPORTANTE! Carga el campo 'rol_profesor' de la tabla pivote
    }
    public function pring()
    {
        return $this->hasOne(Pring::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function prinv()
    {
        return $this->hasOne(Prinv::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function prtut()
    {
        return $this->hasOne(Prtut::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function getTipoAttribute()
    {
    if ($this->pring) return 'PrIng';
    if ($this->prinv) return 'PrInv';
    if ($this->prtut) return 'PrTut';
    return 'N/A';
    }
}