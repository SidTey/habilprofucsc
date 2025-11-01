<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    use HasFactory;

    // 1. El nombre de tu tabla
    protected $table = 'profesor';

    // 2. La llave primaria de esta tabla
    protected $primaryKey = 'rut_profesor';

    // 3. No es un número que incrementa (es un RUT)
    public $incrementing = false;

    // 4. El tipo de la llave es un entero
    protected $keyType = 'integer';

    // 5. Campos que permitimos rellenar
    protected $fillable = [
        'rut_profesor',
        'nombre_profesor',
        'correo_profesor', // Asumo que tienes campos así
    ];
    public function habilitacionesAsignadas()
    {
    return $this->belongsToMany(Habilitacion::class, 'asigna', 'rut_profesor', 'id_habilitacion')
        ->withPivot('rol_profesor');
    }
}
