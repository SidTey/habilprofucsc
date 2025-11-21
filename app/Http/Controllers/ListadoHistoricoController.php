<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador para el Listado Histórico de Habilitaciones (R4.3)
 * 
 * Implementa la funcionalidad de consultar habilitaciones históricas
 * de un profesor en un semestre específico.
 */
class ListadoHistoricoController extends Controller
{
    /**
     * Valida y parsea el campo semestre_inicio
     * R2.4: Formato YYYY-S donde año [2020,2050] y semestre [1,2]
     */
    private function validarSemestre($semestreInicio)
    {
        if (empty($semestreInicio)) {
            abort(422, 'El valor de Semestre_Inicio no es válido');
        }

        // Intentar parsear formato "YYYY-S"
        if (preg_match('/^(\d{4})-(1|2)$/', trim($semestreInicio), $matches)) {
            $anio = (int) $matches[1];
            $semestre = (int) $matches[2];

            // R2.4.1: Año entre 2020 y 2050
            // R2.4.2: Semestre entre 1 y 2
            if ($anio < 2020 || $anio > 2050) {
                abort(422, 'El valor de Semestre_Inicio no es válido');
            }

            return ['anio' => $anio, 'semestre' => $semestre];
        }

        abort(422, 'El valor de Semestre_Inicio no es válido');
    }

    /**
     * Valida y normaliza el RUT del profesor
     * R1.4: RUT entre 1000000 y 60000000
     */
    private function validarRutProfesor($rutRaw)
    {
        if (empty($rutRaw)) {
            abort(422, 'El valor de Rut_Profesor no es válido');
        }

        // Eliminar puntos, guiones y espacios
        $rutLimpio = preg_replace('/[^0-9]/', '', (string) $rutRaw);

        if (empty($rutLimpio) || !ctype_digit($rutLimpio)) {
            abort(422, 'El valor de Rut_Profesor no es válido');
        }

        $rut = (int) $rutLimpio;

        // R1.4: Validar rango
        if ($rut < 1000000 || $rut > 60000000) {
            abort(422, 'El valor de Rut_Profesor no es válido');
        }

        return $rut;
    }

    /**
     * Determina el tipo de habilitación basándose en las tablas relacionadas
     */
    private function determinarTipoHabilitacion($habilitacion)
    {
        if (!empty($habilitacion->es_pring)) {
            return 'PrIng';
        } elseif (!empty($habilitacion->es_prinv)) {
            return 'PrInv';
        } elseif (!empty($habilitacion->es_prtut)) {
            return 'PrTut';
        }
        return null;
    }

    /**
     * R4.3: Listado Histórico
     * 
     * Entrada: rut_profesor (obligatorio), semestre_inicio (obligatorio)
     * Salida: JSON con listado de habilitaciones donde participó el profesor
     */
    public function obtenerListado(Request $request)
    {
        // R4.6: Validar rut_profesor
        $rutProfesorRaw = $request->input('rut_profesor');
        $rutProfesor = $this->validarRutProfesor($rutProfesorRaw);

        // R4.7: Verificar que el profesor existe
        $profesorExiste = DB::table('profesor')
            ->where('rut_profesor', $rutProfesor)
            ->exists();

        if (!$profesorExiste) {
            abort(404, 'El valor de Rut_Profesor no se encuentra en registrado en el sistema "Habilprof"');
        }

        // R4.5: Validar semestre_inicio
        $semestreInicio = $request->input('semestre_inicio');
        $datosSemestre = $this->validarSemestre($semestreInicio);

        $anio = $datosSemestre['anio'];
        $semestre = $datosSemestre['semestre'];

        // Obtener datos del profesor
        $profesor = DB::table('profesor')
            ->where('rut_profesor', $rutProfesor)
            ->select('rut_profesor', 'nombre_profesor')
            ->first();

        // R4.10: Consultar habilitaciones donde participó el profesor en el semestre
        $habilitaciones = DB::table('asigna as asig')
            ->join('habilitacion_profesional as hp', 'hp.id_habilitacion', '=', 'asig.id_habilitacion')
            ->join('alumno as a', 'a.rut_alumno', '=', 'hp.rut_alumno')
            
            // Marcadores para determinar el tipo
            ->leftJoin('pring', 'pring.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prinv', 'prinv.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prtut', 'prtut.id_habilitacion', '=', 'hp.id_habilitacion')
            
            // Todos los profesores asignados
            ->leftJoin('asigna as asigna_guia', function($join) {
                $join->on('asigna_guia.id_habilitacion', '=', 'hp.id_habilitacion')
                     ->where('asigna_guia.rol', '=', 'Profesor_Guia');
            })
            ->leftJoin('profesor as prof_guia', 'prof_guia.rut_profesor', '=', 'asigna_guia.rut_profesor')
            
            ->leftJoin('asigna as asigna_coguia', function($join) {
                $join->on('asigna_coguia.id_habilitacion', '=', 'hp.id_habilitacion')
                     ->where('asigna_coguia.rol', '=', 'Profesor_Co_Guia');
            })
            ->leftJoin('profesor as prof_coguia', 'prof_coguia.rut_profesor', '=', 'asigna_coguia.rut_profesor')
            
            ->leftJoin('asigna as asigna_comision', function($join) {
                $join->on('asigna_comision.id_habilitacion', '=', 'hp.id_habilitacion')
                     ->where('asigna_comision.rol', '=', 'Profesor_Comision');
            })
            ->leftJoin('profesor as prof_comision', 'prof_comision.rut_profesor', '=', 'asigna_comision.rut_profesor')
            
            ->leftJoin('asigna as asigna_tutor', function($join) {
                $join->on('asigna_tutor.id_habilitacion', '=', 'hp.id_habilitacion')
                     ->where('asigna_tutor.rol', '=', 'Profesor_Tutor');
            })
            ->leftJoin('profesor as prof_tutor', 'prof_tutor.rut_profesor', '=', 'asigna_tutor.rut_profesor')
            
            // Supervisor y Empresa (para PrTut)
            ->leftJoin('supervisor as sup', 'sup.rut_supervisor', '=', 'prtut.rut_supervisor')
            ->leftJoin('empresa as emp', 'emp.rut_empresa', '=', 'prtut.rut_empresa')
            
            ->where('asig.rut_profesor', $rutProfesor)
            ->where('hp.año_semestre', $anio)
            ->where('hp.numero_semestre', $semestre)
            
            ->select(
                'hp.id_habilitacion',
                'hp.rut_alumno',
                'a.nombre_alumno',
                'hp.descripcion_habilitacion',
                'hp.nota_final',
                'hp.fecha_nota',
                'asig.rol as rol_profesor_consultado',
                
                // Marcadores de tipo
                DB::raw('CASE WHEN pring.id_habilitacion IS NOT NULL THEN 1 ELSE 0 END as es_pring'),
                DB::raw('CASE WHEN prinv.id_habilitacion IS NOT NULL THEN 1 ELSE 0 END as es_prinv'),
                DB::raw('CASE WHEN prtut.id_habilitacion IS NOT NULL THEN 1 ELSE 0 END as es_prtut'),
                
                // Datos para PrIng/PrInv
                'prof_guia.rut_profesor as rut_profesor_guia',
                'prof_guia.nombre_profesor as nombre_profesor_guia',
                'prof_coguia.rut_profesor as rut_profesor_coguia',
                'prof_coguia.nombre_profesor as nombre_profesor_coguia',
                'prof_comision.rut_profesor as rut_profesor_comision',
                'prof_comision.nombre_profesor as nombre_profesor_comision',
                DB::raw('COALESCE(pring.titulo_proy, prinv.titulo_proy) as titulo_proyecto_practica'),
                
                // Datos para PrTut
                'prof_tutor.rut_profesor as rut_profesor_tutor',
                'prof_tutor.nombre_profesor as nombre_profesor_tutor',
                'sup.rut_supervisor',
                'sup.nombre_supervisor',
                'emp.rut_empresa',
                'emp.nombre_empresa'
            )
            ->get();

        // Procesar resultados
        $resultados = $habilitaciones->map(function($hab) {
            $tipo = $this->determinarTipoHabilitacion($hab);
            
            $datos = [
                'id_habilitacion' => $hab->id_habilitacion,
                'rut_alumno' => $hab->rut_alumno,
                'nombre_alumno' => $hab->nombre_alumno,
                'tipo_habilitacion' => $tipo,
                'rol_profesor' => $hab->rol_profesor_consultado,
                'descripcion_habilitacion' => $hab->descripcion_habilitacion,
                'nota_final' => $hab->nota_final,
                'fecha_nota' => $hab->fecha_nota,
            ];

            // Datos según tipo
            if ($tipo === 'PrIng' || $tipo === 'PrInv') {
                $datos['profesor_guia'] = [
                    'rut_profesor' => $hab->rut_profesor_guia,
                    'nombre_profesor' => $hab->nombre_profesor_guia
                ];
                
                if ($hab->rut_profesor_coguia) {
                    $datos['profesor_coguia'] = [
                        'rut_profesor' => $hab->rut_profesor_coguia,
                        'nombre_profesor' => $hab->nombre_profesor_coguia
                    ];
                }
                
                $datos['profesor_comision'] = [
                    'rut_profesor' => $hab->rut_profesor_comision,
                    'nombre_profesor' => $hab->nombre_profesor_comision
                ];
                
                $datos['titulo_proyecto_practica'] = $hab->titulo_proyecto_practica;
            }
            
            if ($tipo === 'PrTut') {
                $datos['profesor_tutor'] = [
                    'rut_profesor' => $hab->rut_profesor_tutor,
                    'nombre_profesor' => $hab->nombre_profesor_tutor
                ];
                
                $datos['supervisor'] = [
                    'rut_supervisor' => $hab->rut_supervisor,
                    'nombre_supervisor' => $hab->nombre_supervisor
                ];
                
                $datos['empresa'] = [
                    'rut_empresa' => $hab->rut_empresa,
                    'nombre_empresa' => $hab->nombre_empresa
                ];
            }

            return $datos;
        });

        return response()->json([
            'success' => true,
            'profesor' => [
                'rut_profesor' => $profesor->rut_profesor,
                'nombre_profesor' => $profesor->nombre_profesor
            ],
            'semestre' => sprintf('%04d-%d', $anio, $semestre),
            'total' => $resultados->count(),
            'habilitaciones' => $resultados
        ]);
    }

    /**
     * Vista para el listado histórico
     */
    public function vista()
    {
        return view('ListadoHistoricoEmbed');
    }
}
