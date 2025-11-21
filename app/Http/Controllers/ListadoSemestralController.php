<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador para el Listado Semestral de Habilitaciones (R4.2)
 * 
 * Implementa la funcionalidad de consultar habilitaciones por semestre
 * y mostrar los datos según el tipo de habilitación.
 */
class ListadoSemestralController extends Controller
{
    /**
     * Valida y parsea el campo semestre_inicio
     * R2.4: Formato YYYY-S donde año [2020,2050] y semestre [1,2]
     * R4.1: Mensaje de error específico
     */
    private function validarSemestre($semestreInicio)
    {
        if (empty($semestreInicio)) {
            // R4.1: Mensaje de error cuando Semestre_Inicio no es válido
            abort(422, 'El valor de Semestre_Inicio no es válido');
        }

        // Intentar parsear formato "YYYY-S"
        if (preg_match('/^(\d{4})-(1|2)$/', trim($semestreInicio), $matches)) {
            $anio = (int) $matches[1];
            $semestre = (int) $matches[2];

            // R2.4.1: Año entre 2020 y 2050
            // R2.4.2: Semestre entre 1 y 2
            if ($anio < 2020 || $anio > 2050 || !in_array($semestre, [1, 2], true)) {
                // R4.1: Mensaje de error cuando Semestre_Inicio no es válido
                abort(422, 'El valor de Semestre_Inicio no es válido');
            }

            return ['anio' => $anio, 'semestre' => $semestre];
        }

        // R4.1: Mensaje de error cuando Semestre_Inicio no es válido
        abort(422, 'El valor de Semestre_Inicio no es válido');
    }

    /**
     * Determina el tipo de habilitación basándose en las tablas relacionadas
     * R2.1: Tipo_Habilitación: {PrIng, PrInv, PrTut}
     */
    private function determinarTipoHabilitacion($habilitacion)
    {
        if ($habilitacion->es_pring == 1) {
            return 'PrIng';
        } elseif ($habilitacion->es_prinv == 1) {
            return 'PrInv';
        } elseif ($habilitacion->es_prtut == 1) {
            return 'PrTut';
        }
        return null;
    }

    /**
     * R4.2: Listado Semestral
     * 
     * Entrada: semestre_inicio (obligatorio) formato "YYYY-S"
     * Salida: JSON con listado de habilitaciones según tipo
     * 
     * VALIDACIONES DE CAMPOS (según requisitos):
     * - R1.1: Rut_Alumno: entero positivo [1000000, 60000000] - validado por BD
     * - R1.2: Nombre_Alumno: string max 100 caracteres alfabeto español - validado por BD
     * - R1.4: Rut_Profesor: entero positivo [1000000, 60000000] - validado por BD
     * - R1.5: Nombre_Profesor: string max 100 caracteres alfabeto español - validado por BD
     * - R1.8: Nota_Final: real positivo [1.0, 7.0] con 1 decimal - validado por BD (CHECK)
     * - R1.9: Fecha_nota: date (día/mes/año) validado por tipo date de PostgreSQL
     * - R2.1: Tipo_Habilitación: {PrIng, PrInv, PrTut} - determinado por tablas relacionadas
     * - R2.3: Descripción_Habilitación: text max 500 caracteres - validado por BD
     * - R2.4: Semestre_Inicio: año [2020,2050] semestre [1,2] - validado en validarSemestre()
     * - R2.6: Titulo_Proyecto_Practica: text max 500 caracteres - validado por BD
     * - R2.7/R2.9/R2.14/R2.15: Datos compuestos profesor - obtenidos por JOINs
     * - R2.8: Rol_Profesor: ENUM {Profesor_Guia, Profesor_Comision, Profesor_Co_Guia, Profesor_Tutor}
     * - R2.10: Rut_Supervisor: entero positivo [1000000, 60000000] - validado por BD
     * - R2.11: Nombre_Supervisor: string max 100 caracteres - validado por BD
     * - R2.12: Rut_Empresa: entero positivo [1000000, 60000000] - validado por BD
     * - R2.13: Nombre_Empresa: string max 100 caracteres - validado por BD
     */
    public function obtenerListado(Request $request)
    {
        // R4.1: Validar que venga semestre_inicio
        $semestreInicio = $request->input('semestre_inicio');
        $datosSemestre = $this->validarSemestre($semestreInicio);

        $anio = $datosSemestre['anio'];
        $semestre = $datosSemestre['semestre'];

        // Consultar habilitaciones del semestre con todos los datos necesarios
        $habilitaciones = DB::table('habilitacion_profesional as hp')
            ->join('alumno as a', 'a.rut_alumno', '=', 'hp.rut_alumno')
            
            // Marcadores para determinar el tipo
            ->leftJoin('pring', 'pring.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prinv', 'prinv.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prtut', 'prtut.id_habilitacion', '=', 'hp.id_habilitacion')
            
            // Profesor Guía (para PrIng y PrInv)
            ->leftJoin('asigna as asigna_guia', function($join) {
                $join->on('asigna_guia.id_habilitacion', '=', 'hp.id_habilitacion')
                     ->where('asigna_guia.rol', '=', 'Profesor_Guia');
            })
            ->leftJoin('profesor as prof_guia', 'prof_guia.rut_profesor', '=', 'asigna_guia.rut_profesor')
            
            // Profesor Co-Guía (opcional, para PrIng y PrInv)
            ->leftJoin('asigna as asigna_coguia', function($join) {
                $join->on('asigna_coguia.id_habilitacion', '=', 'hp.id_habilitacion')
                     ->where('asigna_coguia.rol', '=', 'Profesor_Co_Guia');
            })
            ->leftJoin('profesor as prof_coguia', 'prof_coguia.rut_profesor', '=', 'asigna_coguia.rut_profesor')
            
            // Profesor Comisión (para PrIng y PrInv)
            ->leftJoin('asigna as asigna_comision', function($join) {
                $join->on('asigna_comision.id_habilitacion', '=', 'hp.id_habilitacion')
                     ->where('asigna_comision.rol', '=', 'Profesor_Comision');
            })
            ->leftJoin('profesor as prof_comision', 'prof_comision.rut_profesor', '=', 'asigna_comision.rut_profesor')
            
            // Profesor Tutor (para PrTut)
            ->leftJoin('asigna as asigna_tutor', function($join) {
                $join->on('asigna_tutor.id_habilitacion', '=', 'hp.id_habilitacion')
                     ->where('asigna_tutor.rol', '=', 'Profesor_Tutor');
            })
            ->leftJoin('profesor as prof_tutor', 'prof_tutor.rut_profesor', '=', 'asigna_tutor.rut_profesor')
            
            // Supervisor y Empresa (para PrTut)
            ->leftJoin('supervisor as sup', 'sup.rut_supervisor', '=', 'prtut.rut_supervisor')
            ->leftJoin('empresa as emp', 'emp.rut_empresa', '=', 'prtut.rut_empresa')
            
            ->where('hp.año_semestre', $anio)
            ->where('hp.numero_semestre', $semestre)
            
            ->select(
                'hp.id_habilitacion',
                'hp.rut_alumno',
                'a.nombre_alumno',
                'hp.descripcion_habilitacion',
                'hp.nota_final',
                'hp.fecha_nota',
                
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

        // Procesar resultados y agregar tipo de habilitación
        $resultados = $habilitaciones->map(function($hab) {
            $tipo = $this->determinarTipoHabilitacion($hab);
            
            $datos = [
                'id_habilitacion' => $hab->id_habilitacion,
                'rut_alumno' => $hab->rut_alumno,
                'nombre_alumno' => $hab->nombre_alumno,
                'tipo_habilitacion' => $tipo,
                'descripcion_habilitacion' => $hab->descripcion_habilitacion,
                'nota_final' => $hab->nota_final,
                'fecha_nota' => $hab->fecha_nota,
            ];

            // R4.2.1: Datos para PrIng o PrInv
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
            
            // R4.2.2: Datos para PrTut
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
            'semestre' => sprintf('%04d-%d', $anio, $semestre),
            'total' => $resultados->count(),
            'habilitaciones' => $resultados
        ]);
    }

    /**
     * Vista para el listado semestral
     */
    public function vista()
    {
        return view('ListadoSemestralEmbed');
    }
}
