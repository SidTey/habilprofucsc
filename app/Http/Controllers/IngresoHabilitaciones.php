<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\HabilitacionProfesional;
use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\Empresa;
use App\Models\Supervisor;
use App\Models\Asigna;
use App\Models\Pring;
use App\Models\Prinv;
use App\Models\Prtut;

class IngresoHabilitaciones extends Controller
{
    /**
     * Obtener lista de alumnos disponibles (R2.19)
     */
    public function getAlumnosDisponibles()
    {
        $alumnos = Alumno::select('rut_alumno', 'nombre_alumno')->get();
        return response()->json([
            'success' => true,
            'data' => $alumnos
        ]);
    }

    /**
     * Obtener lista de profesores disponibles
     */
    public function getProfesoresDisponibles(Request $request)
    {
        $profesores = Profesor::select('rut_profesor', 'nombre_profesor')->get();
        
        return response()->json([
            'success' => true,
            'data' => $profesores
        ]);
    }

    /**
     * Crear nueva habilitación profesional
     */
    public function store(Request $request)
    {
        // Validación inicial según R2.16 (campos obligatorios)
        $validator = Validator::make($request->all(), [
            'rut_alumno' => 'required|integer|min:1000000|max:60000000',
            'tipo_habilitacion' => 'required|string|size:5|in:PrIng,PrInv,PrTut',
            'descripcion_habilitacion' => 'required|string|min:50|max:500',
            'año_semestre' => 'required|integer|min:2020|max:2050',
            'numero_semestre' => 'required|integer|in:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Crear habilitación profesional (sin id_habilitacion manual)
            $habilitacion = new HabilitacionProfesional();
            $habilitacion->rut_alumno = $request->rut_alumno;
            $habilitacion->descripcion_habilitacion = $request->descripcion_habilitacion;
            $habilitacion->año_semestre = $request->input('año_semestre');
            $habilitacion->numero_semestre = $request->input('numero_semestre');
            $habilitacion->save();

            // Manually construct the ID based on DB generation logic to refetch the model
            $generatedId = $request->input('rut_alumno') . '_' . $request->input('año_semestre') . '-' . $request->input('numero_semestre');
            $habilitacion = HabilitacionProfesional::find($generatedId);

            if (!$habilitacion) {
                throw new \Exception('Error al recuperar la habilitación recién creada.');
            }

            // Procesar según tipo de habilitación
            if (in_array($request->tipo_habilitacion, ['PrIng', 'PrInv'])) {
                $this->procesarPracticaIngenieriaInvestigacion($request, $habilitacion);
            } else {
                $this->ProcesarPracticaTutelada($request, $habilitacion);
            }

            DB::commit();

            // Cargar relaciones para la respuesta
            $habilitacion->load(['alumno', 'asignaciones.profesor']);

            return response()->json([
                'success' => true,
                'message' => 'Se ha ingresado correctamente la Habilitacion Profesional',
                'data' => $habilitacion
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar habilitación tipo PrIng o PrInv
     */
    private function procesarPracticaIngenieriaInvestigacion($request, $habilitacion)
    {
        // Validaciones específicas
        $validator = Validator::make($request->all(), [
            'titulo_proyecto' => 'required|string|min:3|max:100',
            'rut_profesor_guia' => 'required|integer|min:1000000|max:60000000',
            'rut_profesor_comision' => 'required|integer|min:1000000|max:60000000',
            'rut_profesor_co_guia' => 'nullable|integer|min:1000000|max:60000000',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Validar que un profesor no tenga multiples roles
        $profesores = array_filter([
            $request->input('rut_profesor_guia'),
            $request->input('rut_profesor_comision'),
            $request->input('rut_profesor_co_guia')
        ]);

        if (count($profesores) !== count(array_unique($profesores))) {
            throw new \Exception('Un profesor no puede ser asignado a múltiples roles en la misma habilitación.');
        }

        // Crear registro en tabla pring o prniv según corresponda
        if ($request->tipo_habilitacion === 'PrIng') {
            Pring::create([
                'id_habilitacion' => $habilitacion->id_habilitacion,
                'titulo_proy' => $request->titulo_proyecto
            ]);
        } else {
            Prinv::create([
                'id_habilitacion' => $habilitacion->id_habilitacion,
                'titulo_proy' => $request->titulo_proyecto
            ]);
        }

        // Asignar profesores
        $this->asignarProfesor($habilitacion->id_habilitacion, $request->rut_profesor_guia, 'Profesor_Guia');
        $this->asignarProfesor($habilitacion->id_habilitacion, $request->rut_profesor_comision, 'Profesor_Comision');
        
        if ($request->filled('rut_profesor_co_guia')) {
            $this->asignarProfesor($habilitacion->id_habilitacion, $request->rut_profesor_co_guia, 'Profesor_Co_Guia');
        }
    }

    /**
     * Procesar habilitación tipo PrTut
     */
    private function ProcesarPracticaTutelada($request, $habilitacion)
    {
        // Validaciones específicas
        $validator = Validator::make($request->all(), [
            'rut_supervisor' => 'required|integer|min:1000000|max:60000000',
            'nombre_supervisor' => 'required|string|max:100',
            'rut_empresa' => 'required|integer|min:1000000|max:60000000',
            'nombre_empresa' => 'required|string|max:100',
            'rut_profesor_tutor' => 'required|integer|min:1000000|max:60000000',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Crear o actualizar empresa
        $empresa = Empresa::updateOrCreate(
            ['rut_empresa' => $request->rut_empresa],
            ['nombre_empresa' => $request->nombre_empresa]
        );

        // Crear o actualizar supervisor
        $supervisor = Supervisor::updateOrCreate(
            ['rut_supervisor' => $request->rut_supervisor],
            [
                'nombre_supervisor' => $request->nombre_supervisor,
                'rut_empresa' => $empresa->rut_empresa
            ]
        );

        // Crear el registro en la tabla prtut
        Prtut::create([
            'id_habilitacion' => $habilitacion->id_habilitacion,
            'rut_empresa' => $request->rut_empresa,
            'rut_supervisor' => $request->rut_supervisor,
        ]);

        // Asignar profesor tutor
        $this->asignarProfesor($habilitacion->id_habilitacion, $request->rut_profesor_tutor, 'Profesor_Tutor');
    }

    /**
     * Asignar profesor a habilitación
     */
    private function asignarProfesor($idHabilitacion, $rutProfesor, $rol)
    {
        return Asigna::create([
            'id_habilitacion' => $idHabilitacion,
            'rut_profesor' => $rutProfesor,
            'rol' => $rol
        ]);
    }

    /**
     * Obtener lista de habilitaciones profesionales
     */
    public function index()
    {
        $habilitaciones = HabilitacionProfesional::with([
            'alumno',
            'asignaciones.profesor',
            'practica_ingenieria',
            'practica_nivelacion'
        ])->get();

        // Agregar el tipo de habilitación basado en las relaciones
        $habilitaciones = $habilitaciones->map(function ($habilitacion) {
            $data = $habilitacion->toArray();
            if ($habilitacion->practica_ingenieria) {
                $data['tipo_habilitacion'] = 'PrIng';
            } elseif ($habilitacion->practica_nivelacion) {
                $data['tipo_habilitacion'] = 'PrInv';
            } else {
                $data['tipo_habilitacion'] = 'PrTut';
            }
            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => $habilitaciones
        ]);
    }
}