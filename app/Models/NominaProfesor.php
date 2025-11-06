<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NominaProfesor extends Model
{
    protected $connection = 'db_fantasma';
    protected $table = 'nomina_profesor';
    public $timestamps = false;
    protected $primaryKey = 'rut_profesor';
    public $incrementing = false;
    protected $keyType = 'int';
}