<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prinv extends Model
{
    protected $table = 'prinv';
    public $timestamps = false;
    protected $primaryKey = 'id_habilitacion';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id_habilitacion', 'titulo_proy'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_termino' => 'date',
        'nota' => 'decimal:2'
    ];

    // Relación inversa con HabilitacionProfesional
    public function habilitacionProfesional()
    {
        return $this->belongsTo(HabilitacionProfesional::class, 'id_habilitacion', 'id_habilitacion');
    }
}
