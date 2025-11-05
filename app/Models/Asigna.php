<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asigna extends Model
{
    protected $table = 'asigna';
    protected $primaryKey = ['id_habilitacion', 'rut_profesor'];
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'id_habilitacion',
        'rut_profesor',
        'rol'
    ];

    public function habilitacion()
    {
        return $this->belongsTo(HabilitacionProfesional::class, 'id_habilitacion', 'id_habilitacion');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'rut_profesor', 'rut_profesor');
    }
}