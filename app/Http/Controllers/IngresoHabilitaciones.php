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
    // obtiene lista de alumnos disponibles
    public function getAlumnosDisponibles()
    {
        $alumnos = Alumno::select('rut_alumno', 'nombre_alumno')->get();
        return response()->json([
            'success' => true,
            'data' => $alumnos
        ]);
    }

    // obtiene lista de profesores disponibles
    public function getProfesoresDisponibles(Request $request)
    {
        $profesores = Profesor::select('rut_profesor', 'nombre_profesor')->get();
        // retorna en formato json para que se vea en el frontend bien, y no como un array como se extrae de la base de datos
        return response()->json([
            'success' => true,
            'data' => $profesores
        ]);
    }

    //creacion de una nueva habilitacion profesional
    public function store(Request $request)
    {
        // validacion de los datos de todas las habilitaciones (PrIng, PrInv, PrTut)
        $validator = Validator::make($request->all(), [
            'rut_alumno' => 'required|integer|min:1000000|max:60000000',
            'tipo_habilitacion' => 'required|string|size:5|in:PrIng,PrInv,PrTut',
            'descripcion_habilitacion' => 'required|string|min:50|max:500',
            'año_semestre' => 'required|integer|min:2025|max:2050',
            'numero_semestre' => 'required|integer|in:1,2',
        ]);
        // si la validacion falla, retorna los errores en formato json.
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Crear habilitación profesional (sin id_habilitacion manual, ya que este lo genera de base de datos de habilprof)
            $habilitacion = new HabilitacionProfesional();
            $habilitacion->rut_alumno = $request->rut_alumno;
            $habilitacion->descripcion_habilitacion = $request->descripcion_habilitacion;
            $habilitacion->año_semestre = $request->input('año_semestre');
            $habilitacion->numero_semestre = $request->input('numero_semestre');
            $habilitacion->save();

            // procesar según tipo de habilitación
            if (in_array($request->tipo_habilitacion, ['PrIng', 'PrInv'])) {
                $this->procesarPracticaIngenieriaInvestigacion($request, $habilitacion); //procesa PrIng o PrInv
            } else {
                $this->procesarPracticaTutelada($request, $habilitacion); //procesa PrTut
            }

            DB::commit();  // confirma la transacción si todo salió bien en la base de datos

            // Cargar relaciones para la respuesta
            $habilitacion->load(['alumno', 'asignaciones.profesor']);
            //envio de mensaje de exito del ingreso
            return response()->json([
                'success' => true,
                'message' => 'Se ha ingresado correctamente la Habilitacion Profesional',
                'data' => $habilitacion
            ]);
            // si hay algun error en el proceso, se captura la excepcion y se revierte la transaccion
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Procesar habilitación tipo PrIng o PrInv
    private function procesarPracticaIngenieriaInvestigacion($request, $habilitacion)
    {
        // Validaciones especificas de cada tipo habilitacion
        $validator = Validator::make($request->all(), [
            'titulo_proyecto' => 'required|string|min:3|max:100',
            'rut_profesor_guia' => 'required|integer|min:1000000|max:60000000',
            'rut_profesor_comision' => 'required|integer|min:1000000|max:60000000',
            'rut_profesor_co_guia' => 'nullable|integer|min:1000000|max:60000000',
        ]);
        // si la validacion falla, lanza una excepcion con los errores
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // validacion que un profesor no tenga multiples roles
        $profesores = array_filter([
            $request->input('rut_profesor_guia'),
            $request->input('rut_profesor_comision'),
            $request->input('rut_profesor_co_guia')
        ]);
            // elimina valores nulos y verifica unicidad
            if (count($profesores) !== count(array_unique($profesores))) {
            throw new \Exception('Un profesor no puede ser asignado a múltiples roles en la misma habilitación.');
        }

        // crea registro en tabla pring o prniv según corresponda
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

        // asigna profesores
        $this->asignarProfesor($habilitacion->id_habilitacion, $request->rut_profesor_guia, 'Profesor_Guia');
        $this->asignarProfesor($habilitacion->id_habilitacion, $request->rut_profesor_comision, 'Profesor_Comision');
        // asigna co-guia si se proporciono uno ya que este puede quedar nulo
        if ($request->filled('rut_profesor_co_guia')) {
            $this->asignarProfesor($habilitacion->id_habilitacion, $request->rut_profesor_co_guia, 'Profesor_Co_Guia');
        }
    }

    // Procesar habilitación tipo PrTut
    private function ProcesarPracticaTutelada($request, $habilitacion)
    {
        // Validaciones especificas de PrTut
        $validator = Validator::make($request->all(), [
            'rut_supervisor' => 'required|integer|min:1000000|max:60000000',
            'nombre_supervisor' => 'required|string|max:100',
            'rut_empresa' => 'required|integer|min:1000000|max:60000000',
            'nombre_empresa' => 'required|string|max:100',
            'rut_profesor_tutor' => 'required|integer|min:1000000|max:60000000',
        ]);
        // si la validacion falla, lanza una excepcion con los errores
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // crear o actualizar empresa
        $empresa = Empresa::updateOrCreate(
            ['rut_empresa' => $request->rut_empresa],
            ['nombre_empresa' => $request->nombre_empresa]
        );

        // crear o actualizar supervisor
        $supervisor = Supervisor::updateOrCreate(
            ['rut_supervisor' => $request->rut_supervisor],
            [
                'nombre_supervisor' => $request->nombre_supervisor,
                'rut_empresa' => $empresa->rut_empresa
            ]
        );

        // crear el registro en la tabla prtut
        Prtut::create([
            'id_habilitacion' => $habilitacion->id_habilitacion,
            'rut_empresa' => $request->rut_empresa,
            'rut_supervisor' => $request->rut_supervisor,
        ]);

        // asignar profesor tutor
        $this->asignarProfesor($habilitacion->id_habilitacion, $request->rut_profesor_tutor, 'Profesor_Tutor');
    }

    // asigna un profesor a una habilitacion con un rol especifico
    private function asignarProfesor($idHabilitacion, $rutProfesor, $rol)
    {
        return Asigna::create([
            'id_habilitacion' => $idHabilitacion,
            'rut_profesor' => $rutProfesor,
            'rol' => $rol
        ]);
    }

    // obtiene todas las habilitaciones profesionales con sus relaciones
    public function index()
    {
        $habilitaciones = HabilitacionProfesional::with([
            'alumno',
            'asignaciones.profesor',
            'practica_ingenieria',
            'practica_nivelacion'
        ])->get();

        // agregar el tipo de habilitación basado en las relaciones
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
        // retorna las habilitaciones en formato json para el frontend
        return response()->json([
            'success' => true,
            'data' => $habilitaciones
        ]);
    }
}