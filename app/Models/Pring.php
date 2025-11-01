<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pring extends Model
{
    use HasFactory;
    
    protected $table = 'pring';

    protected $primaryKey = 'id_habilitacion'; // La llave primaria es el ID de la habilitación

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false; // Asumo que no tienes created_at/updated_at aquí

    protected $fillable = ['id_habilitacion', 'titulo_proy']; // Campos de tu imagen
}