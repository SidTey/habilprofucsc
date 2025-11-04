<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UcscApiService
{
    private $baseUrl;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.ucsc.base_url', 'https://api.ucsc.cl');
        $this->timeout = config('services.ucsc.timeout', 30);
    }

    /**
     * Obtener datos de alumno desde sistema UCSC según R1.11
     */
    public function obtenerDatosAlumno($rutAlumno)
    {
        try {
            // Simulación de conexión con sistema UCSC
            // En producción, aquí iría la llamada real a la API de UCSC
            $response = $this->simularConexionUcsc('alumno', $rutAlumno);
            
            if ($response['success']) {
                return [
                    'success' => true,
                    'data' => $response['data']
                ];
            }
            
            return ['success' => false, 'message' => 'Alumno no encontrado en sistema UCSC'];
            
        } catch (\Exception $e) {
            Log::error("Error conectando con sistema UCSC para alumno {$rutAlumno}: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'No se ha podido establecer conexión con los sistemas UCSC'
            ];
        }
    }

    /**
     * Obtener datos de profesor desde sistema UCSC según R1.11
     */
    public function obtenerDatosProfesor($rutProfesor)
    {
        try {
            // Simulación de conexión con sistema UCSC
            // En producción, aquí iría la llamada real a la API de UCSC
            $response = $this->simularConexionUcsc('profesor', $rutProfesor);
            
            if ($response['success']) {
                return [
                    'success' => true,
                    'data' => $response['data']
                ];
            }
            
            return ['success' => false, 'message' => 'Profesor no encontrado en sistema UCSC'];
            
        } catch (\Exception $e) {
            Log::error("Error conectando con sistema UCSC para profesor {$rutProfesor}: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'No se ha podido establecer conexión con los sistemas UCSC'
            ];
        }
    }

    /**
     * Simulación de conexión con sistema UCSC
     * En producción, reemplazar con llamadas HTTP reales
     */
    private function simularConexionUcsc($tipo, $rut)
    {
        // Simular algunos datos de prueba
        if ($tipo === 'alumno') {
            $datosPrueba = [
                12345678 => [
                    'rut_alumno' => 12345678,
                    'nombre_alumno' => 'Juan Pérez García',
                    'correo_alumno' => 'juan.perez@estudiantes.ucsc.cl'
                ],
                23456789 => [
                    'rut_alumno' => 23456789,
                    'nombre_alumno' => 'María González López',
                    'correo_alumno' => 'maria.gonzalez@estudiantes.ucsc.cl'
                ]
            ];
        } else {
            $datosPrueba = [
                11222333 => [
                    'rut_profesor' => 11222333,
                    'nombre_profesor' => 'Dr. Carlos Mendoza',
                    'correo_profesor' => 'carlos.mendoza@ucsc.cl'
                ],
                44555666 => [
                    'rut_profesor' => 44555666,
                    'nombre_profesor' => 'Dra. Ana Rodríguez',
                    'correo_profesor' => 'ana.rodriguez@ucsc.cl'
                ]
            ];
        }

        // Simular latencia de red
        usleep(rand(10000, 50000)); // 0.01 a 0.05 segundos (más rápido para desarrollo)

        // Simular falla de conexión ocasional solo en producción
        if (env('APP_ENV') !== 'local' && rand(1, 100) <= 2) { // 2% en producción, 0% en desarrollo
            throw new \Exception('Timeout de conexión con UCSC');
        }

        if (isset($datosPrueba[$rut])) {
            return [
                'success' => true,
                'data' => $datosPrueba[$rut]
            ];
        }

        return ['success' => false];
    }

    /**
     * Verificar conexión con sistema UCSC
     */
    public function verificarConexion()
    {
        try {
            // En producción, hacer ping al sistema UCSC
            // Por ahora, simular verificación más confiable en desarrollo
            return env('APP_ENV') === 'local' ? true : (rand(1, 100) > 10); // 100% en local
        } catch (\Exception $e) {
            return false;
        }
    }
}
