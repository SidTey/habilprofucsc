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
    // R2.20.3 y R2.21.3: Permite filtrar profesores ya asignados
    public function getProfesoresDisponibles(Request $request)
    {
        $profesores = Profesor::select('rut_profesor', 'nombre_profesor')->get();
        
        // Si se proporciona id_habilitacion, marca profesores ya asignados
        $idHabilitacion = $request->query('id_habilitacion');
        
        if ($idHabilitacion) {
            // Obtener RUTs de profesores ya asignados a esta habilitación
            $profesoresAsignados = Asigna::where('id_habilitacion', $idHabilitacion)
                ->pluck('rut_profesor')
                ->toArray();
            
            // Marcar profesores como asignados o disponibles
            $profesores = $profesores->map(function ($profesor) use ($profesoresAsignados) {
                $profesor->asignado = in_array($profesor->rut_profesor, $profesoresAsignados);
                return $profesor;
            });
            
            // Opcionalmente, filtrar solo disponibles si se solicita
            if ($request->query('solo_disponibles') === 'true') {
                $profesores = $profesores->filter(function ($profesor) {
                    return !$profesor->asignado;
                })->values();
            }
        }
        
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
        // R1.1: Rut_Alumno es un número entero positivo entre 1000000 y 60000000
        // R2.1: Tipo_Habilitación es una cadena de largo 5: {PrIng, PrInv, PrTut}
        // R2.3: Descripción_Habilitacion es texto con máximo 500 caracteres del abecedario español
        // R2.4: Semestre_Inicio compuesto por año [2020,2050] y semestre [1,2]
        $validator = Validator::make($request->all(), [
            'rut_alumno' => 'required|integer|min:1000000|max:60000000',
            'tipo_habilitacion' => 'required|string|size:5|in:PrIng,PrInv,PrTut',
            'descripcion_habilitacion' => 'required|string|max:500',
            'año_semestre' => 'required|integer|min:2020|max:2050',
            'numero_semestre' => 'required|integer|in:1,2',
        ], [
            // Mensajes personalizados según R2.24
            'rut_alumno.required' => 'El campo Rut_Alumno es obligatorio',
            'tipo_habilitacion.required' => 'El campo Tipo_Habilitacion es obligatorio',
            'descripcion_habilitacion.max' => 'Descripción no válida',
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
        // R2.6: Titulo_Proyecto_Practica es string mínimo 3, máximo 500 caracteres
        // R1.4: Rut_Profesor es un número entero positivo entre 10000000 y 60000000
        // R2.7, R2.14, R2.15: Validación de profesores (Guía, Comisión, Co-Guía)
        $validator = Validator::make($request->all(), [
            'titulo_proyecto' => 'required|string|min:3|max:500',
            'rut_profesor_guia' => 'required|integer|min:10000000|max:60000000',
            'rut_profesor_comision' => 'required|integer|min:10000000|max:60000000',
            'rut_profesor_co_guia' => 'nullable|integer|min:10000000|max:60000000',
        ], [
            // Mensajes personalizados según R2.24
            'titulo_proyecto.required' => 'Título de proyecto no válido',
            'titulo_proyecto.min' => 'Título de proyecto no válido',
            'titulo_proyecto.max' => 'Título de proyecto no válido',
            'rut_profesor_guia.required' => 'El campo Profesor_Guía es obligatorio',
            'rut_profesor_comision.required' => 'El campo Profesor_Comision es obligatorio',
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
        // R2.10: Rut_Supervisor es un número entero positivo entre 1000000 y 60000000
        // R2.11: Nombre_Supervisor es string con máximo 100 caracteres del abecedario español
        // R2.12: Rut_Empresa es un número entero positivo entre 1000000 y 60000000
        // R2.13: Nombre_Empresa es string con máximo 100 caracteres del abecedario español
        // R1.4: Rut_Profesor_Tutor es un número entero positivo entre 10000000 y 60000000
        $validator = Validator::make($request->all(), [
            'rut_supervisor' => 'required|integer|min:1000000|max:60000000',
            'nombre_supervisor' => ['required', 'string', 'max:100', 'regex:/^[a-záéíóúñüA-ZÁÉÍÓÚÑÜ\s]+$/u'],
            'rut_empresa' => 'required|integer|min:1000000|max:60000000',
            'nombre_empresa' => ['required', 'string', 'max:100', 'regex:/^[a-záéíóúñüA-ZÁÉÍÓÚÑÜ\s]+$/u'],
            'rut_profesor_tutor' => 'required|integer|min:10000000|max:60000000',
        ], [
            // Mensajes personalizados según R2.24
            'rut_supervisor.required' => 'El Rut de supervisor no es válido',
            'rut_supervisor.integer' => 'El Rut de supervisor no es válido',
            'rut_supervisor.min' => 'El Rut de supervisor no es válido',
            'rut_supervisor.max' => 'El Rut de supervisor no es válido',
            'nombre_supervisor.required' => 'El Nombre del supervisor no es válido',
            'nombre_supervisor.max' => 'El Nombre del supervisor no es válido',
            'nombre_supervisor.regex' => 'El Nombre del supervisor no es válido',
            'rut_empresa.required' => 'El Rut de la empresa no es válido',
            'rut_empresa.integer' => 'El Rut de la empresa no es válido',
            'rut_empresa.min' => 'El Rut de la empresa no es válido',
            'rut_empresa.max' => 'El Rut de la empresa no es válido',
            'nombre_empresa.required' => 'El Nombre de la empresa no es válido',
            'nombre_empresa.max' => 'El Nombre de la empresa no es válido',
            'nombre_empresa.regex' => 'El Nombre de la empresa no es válido',
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