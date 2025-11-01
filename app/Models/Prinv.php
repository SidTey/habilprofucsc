<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prinv extends Model
{
    use HasFactory;
    
    protected $table = 'prinv';

    protected $primaryKey = 'id_habilitacion';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    // Campos de tu imagen
    protected $fillable = ['id_habilitacion', 'rut_empresa', 'rut_supervisor'];
}