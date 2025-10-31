<?php
//ESTO ES DE LA BASE DE DATOS FANTASMA (NOMINA DE LOS NOTAS EN LINEA)

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotasEnLinea extends Model
{
    protected $connection = 'db_fantasma';
    protected $table = 'notas_en_linea';
    public $timestamps = false;
}