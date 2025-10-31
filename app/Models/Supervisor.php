<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    protected $table = 'supervisor';
    protected $primaryKey = 'rut_supervisor';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'rut_supervisor',
        'nombre_supervisor',
        'rut_empresa'
    ];
}
