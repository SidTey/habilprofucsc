<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\RegistroUcsc;
use App\Models\LogSistema;
use App\Services\UcscApiService;
use Illuminate\Support\Facades\Log;

class UcscDataController extends Controller
{
    private $ucscService;

    public function __construct(UcscApiService $ucscService)
    {
        $this->ucscService = $ucscService;
    }

    /**
     * Función principal R1: Carga automática de datos desde sistemas UCSC
     */
    public function procesarCargaAutomatica(Request $request)
    {
        // Validar entrada de datos según requisitos R1.1 - R1.10
        $validator = Validator::make($request->all(), [
            'rut_alumno' => 'required|integer|min:1000000|max:60000000',
            'rut_profesor' => 'required|integer|min:10000000|max:60000000',
            'fecha_ingreso' => 'required|date|after_or_equal:2025-01-01|before_or_equal:2050-12-31',
            'nota_final' => 'nullable|numeric|min:1.00|max:7.00'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        $datos = $request->all();

        try {
            DB::beginTransaction();

            // R1.11 y R1.12: Obtener datos de alumno y profesor desde sistema UCSC
            $resultadoProceso = $this->procesarDatosUcsc($datos);

            if (!$resultadoProceso['success']) {
                LogSistema::crearLogError($datos, $resultadoProceso['message']);
                DB::rollBack();
                return response()->json($resultadoProceso, 400);
            }

            // R1.13: Registrar log del sistema con los datos cargados
            LogSistema::crearLogExitoso($resultadoProceso['datos_procesados']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $resultadoProceso['datos_procesados'],
                'message' => 'Datos cargados exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            LogSistema::crearLogError($datos, $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error interno del sistema'
            ], 500);
        }
    }

    /**
     * Procesar datos desde sistema UCSC según R1.11 y R1.12
     */
    private function procesarDatosUcsc($datos)
    {
        // Obtener datos del alumno desde sistema UCSC
        $responseAlumno = $this->ucscService->obtenerDatosAlumno($datos['rut_alumno']);
        if (!$responseAlumno['success']) {
            return [
                'success' => false,
                'message' => $responseAlumno['message'] ?? 'No se ha podido establecer conexión con los sistemas UCSC'
            ];
        }

        // Obtener datos del profesor desde sistema UCSC
        $responseProfesor = $this->ucscService->obtenerDatosProfesor($datos['rut_profesor']);
        if (!$responseProfesor['success']) {
            return [
                'success' => false,
                'message' => $responseProfesor['message'] ?? 'No se ha podido establecer conexión con los sistemas UCSC'
            ];
        }

        // R1.12.1: Cargar y actualizar datos automáticamente
        $alumno = $this->crearOActualizarAlumno($responseAlumno['data']);
        $profesor = $this->crearOActualizarProfesor($responseProfesor['data']);

        // Crear registro UCSC
        $registroUcsc = RegistroUcsc::create([
            'alumno_id' => $alumno->id,
            'profesor_id' => $profesor->id,
            'fecha_ingreso' => $datos['fecha_ingreso'],
            'nota_final' => $datos['nota_final'] ?? null
        ]);

        // Preparar datos procesados para salida según requisitos
        $datosProcessados = [
            'rut_alumno' => $alumno->rut_alumno,
            'nombre_alumno' => $alumno->nombre_alumno,
            'correo_alumno' => $alumno->correo_alumno,
            'rut_profesor' => $profesor->rut_profesor,
            'nombre_profesor' => $profesor->nombre_profesor,
            'correo_profesor' => $profesor->correo_profesor,
            'fecha_ingreso' => $registroUcsc->fecha_ingreso, // Mantener formato date para logs
            'nota_final' => $registroUcsc->nota_final
        ];

        return [
            'success' => true,
            'datos_procesados' => $datosProcessados
        ];
    }

    /**
     * Crear o actualizar alumno según R1.12.1
     */
    private function crearOActualizarAlumno($datosAlumno)
    {
        return Alumno::updateOrCreate(
            ['rut_alumno' => $datosAlumno['rut_alumno']],
            [
                'nombre_alumno' => $datosAlumno['nombre_alumno'],
                'correo_alumno' => $datosAlumno['correo_alumno']
            ]
        );
    }

    /**
     * Crear o actualizar profesor según R1.12.1
     */
    private function crearOActualizarProfesor($datosProfesor)
    {
        return Profesor::updateOrCreate(
            ['rut_profesor' => $datosProfesor['rut_profesor']],
            [
                'nombre_profesor' => $datosProfesor['nombre_profesor'],
                'correo_profesor' => $datosProfesor['correo_profesor']
            ]
        );
    }

    /**
     * Obtener registros UCSC con información completa
     */
    public function obtenerRegistros()
    {
        Log::info('Iniciando obtenerRegistros');

        $registros = RegistroUcsc::with(['alumno', 'profesor'])
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info('Registros encontrados: ' . $registros->count());

        $registros = $registros->map(function ($registro) {
                return [
                    'id' => $registro->id,
                    'rut_alumno' => $registro->alumno ? $registro->alumno->rut_alumno : null,
                    'nombre_alumno' => $registro->alumno ? $registro->alumno->nombre_alumno : 'Sin nombre',
                    'correo_alumno' => $registro->alumno ? $registro->alumno->correo_alumno : null,
                    'rut_profesor' => $registro->profesor ? $registro->profesor->rut_profesor : null,
                    'nombre_profesor' => $registro->profesor ? $registro->profesor->nombre_profesor : 'Sin nombre',
                    'correo_profesor' => $registro->profesor ? $registro->profesor->correo_profesor : null,
                    'fecha_ingreso' => $registro->fecha_ingreso ? $registro->fecha_ingreso->format('dd/mm/yyyy') : null,
                    'nota_final' => $registro->nota_final,
                    'created_at' => $registro->created_at ? $registro->created_at->format('d/m/Y H:i:s') : null
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $registros
        ]);
    }

    /**
     * Obtener logs del sistema
     */
    public function obtenerLogs()
    {
        $logs = LogSistema::orderBy('created_at', 'desc')
            ->take(100)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'rut_alumno' => $log->rut_alumno,
                    'nombre_alumno' => $log->nombre_alumno,
                    'correo_alumno' => $log->correo_alumno,
                    'rut_profesor' => $log->rut_profesor,
                    'nombre_profesor' => $log->nombre_profesor,
                    'correo_profesor' => $log->correo_profesor,
                    'fecha_ingreso' => $log->fecha_ingreso ? $log->fecha_ingreso->format('d/m/Y') : null,
                    'nota_final' => $log->nota_final,
                    'estado' => $log->estado,
                    'mensaje' => $log->mensaje,
                    'created_at' => $log->created_at->format('d/m/Y H:i:s')
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
