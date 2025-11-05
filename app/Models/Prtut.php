<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prtut extends Model
{
    protected $table = 'prtut';
    public $timestamps = false;
    protected $primaryKey = 'id_habilitacion';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_habilitacion',
        'rut_empresa',
        'rut_supervisor'
    ];

    public function habilitacionProfesional()
    {
        return $this->belongsTo(HabilitacionProfesional::class, 'id_habilitacion', 'id_habilitacion');
    }
}
