<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSistema extends Model
{
    protected $table = 'logs_sistema';
    
    protected $fillable = [
        'rut_alumno',
        'nombre_alumno',
        'correo_alumno',
        'rut_profesor',
        'nombre_profesor',
        'correo_profesor',
        'fecha_ingreso',
        'nota_final',
        'estado',
        'mensaje',
        'datos_adicionales'
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'nota_final' => 'decimal:2',
        'datos_adicionales' => 'array'
    ];

    /**
     * Crear log de carga exitosa según R1.13
     */
    public static function crearLogExitoso($datos)
    {
        return self::create([
            'rut_alumno' => $datos['rut_alumno'],
            'nombre_alumno' => $datos['nombre_alumno'],
            'correo_alumno' => $datos['correo_alumno'],
            'rut_profesor' => $datos['rut_profesor'],
            'nombre_profesor' => $datos['nombre_profesor'],
            'correo_profesor' => $datos['correo_profesor'],
            'fecha_ingreso' => $datos['fecha_ingreso'],
            'nota_final' => $datos['nota_final'] ?? null,
            'estado' => 'exitoso'
        ]);
    }

    /**
     * Crear log de error
     */
    public static function crearLogError($datos, $mensaje)
    {
        return self::create([
            'rut_alumno' => $datos['rut_alumno'] ?? null,
            'nombre_alumno' => $datos['nombre_alumno'] ?? null,
            'correo_alumno' => $datos['correo_alumno'] ?? null,
            'rut_profesor' => $datos['rut_profesor'] ?? null,
            'nombre_profesor' => $datos['nombre_profesor'] ?? null,
            'correo_profesor' => $datos['correo_profesor'] ?? null,
            'fecha_ingreso' => $datos['fecha_ingreso'] ?? null,
            'nota_final' => $datos['nota_final'] ?? null,
            'estado' => 'error',
            'mensaje' => $mensaje
        ]);
    }
}
