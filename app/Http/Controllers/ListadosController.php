<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador para los listados de habilitaciones (R4)
 *
 * Implementa:
 * - listadoSemestral: recibe `semestre_inicio` y devuelve los registros del semestre
 * - listadoHistorico: recibe `rut_profesor` y `semestre_inicio` y devuelve el histórico
 *
 * Validaciones y mensajes según R4.4, R4.5 y R4.6.
 */
class ListadosController extends Controller
{
    /**
     * Valida y parsea el campo `semestre_inicio`.
     *
     * Formatos aceptados: "YYYY-S" (ej. 2025-1) o dos campos separados enviados desde el formulario.
     * Retorna un array [anio, semestre] o lanza una \Illuminate\Validation\ValidationException
     * con el mensaje apropiado.
     */
    protected function parseSemestre(Request $request)
    {
        $value = $request->input('semestre_inicio');

        if (!$value) {
            // Será responsabilidad del caller marcar como obligatorio
            return null;
        }

        // Soportar formato "AAAA-N" donde N ∈ {1,2}
        if (preg_match('/^(\d{4})-(1|2)$/', trim($value), $m)) {
            $anio = (int) $m[1];
            $sem = (int) $m[2];
        } else {
            // intentar parsear como array separado: año y semestre
            $anio = (int) $request->input('anio');
            $sem = (int) $request->input('semestre');
        }

        // R4.5: Validación de formato y rango de semestre
        // Validaciones según R2.5
        if (!is_int($anio) || $anio < 2025 || $anio > 2050 || !in_array($sem, [1,2], true)) {
            // R4.5: Mensaje de error cuando Semestre_Inicio no es válido
            abort(422, 'El valor de Semestre_Inicio no es válido');
        }

        return [$anio, $sem];
    }

    /**
     * Valida formato básico de RUT esperado por la base de datos.
     * En la BD los rut se guardan como bigint (sin puntos ni DV). Aquí normalizamos y
     * validamos que el valor resultante sea un número entero positivo.
     *
     * Si no cumple, aborta con 422 y mensaje R4.6.
     */
    protected function normalizeRut($rutRaw)
    {
        // R4.6: Validación de RUT nulo
        if (is_null($rutRaw)) {
            // R4.6: Mensaje de error cuando Rut_Profesor no es válido
            abort(422, 'El valor de Rut_Profesor no es válido');
        }

        // Eliminar puntos, guiones y espacios, tomar sólo dígitos.
        $digits = preg_replace('/[^0-9]/', '', (string) $rutRaw);

        // R4.6: Validación de formato de RUT
        if ($digits === '' || !ctype_digit($digits)) {
            // R4.6: Mensaje de error cuando Rut_Profesor no es válido
            abort(422, 'El valor de Rut_Profesor no es válido');
        }

        // Convertir a entero (la BD usa bigint)
        return (int) $digits;
    }

    /**
     * R4.1: Listado semestral (R4.9)
     *
     * Entrada: semestre_inicio (obligatorio)
     * Salida: vista con listado según tipo de habilitación
     */
    public function listadoSemestral(Request $request)
    {
        // R4.2: Validación de presencia de semestre_inicio
        // R4.5: Validar que venga semestre_inicio
        if (!$request->filled('semestre_inicio')) {
            // R4.5: Mensaje de error cuando Semestre_Inicio no es válido
            abort(422, 'El valor de Semestre_Inicio no es válido');
        }

        [$anio, $sem] = $this->parseSemestre($request);

        // R4.9: Consultar habilitaciones del semestre
        // R4.9.1: Para Proyecto de Grado e Investigación - incluye profesores guía, co-guía y comisión
        // R4.9.2: Para Práctica Profesional - incluye profesor tutor, supervisor y empresa
        $habilitaciones = DB::table('habilitacion_profesional as hp')
            ->join('alumno as a', 'a.rut_alumno', '=', 'hp.rut_alumno')
            ->leftJoin('asigna as guia', function($join){
                $join->on('guia.id_habilitacion', '=', 'hp.id_habilitacion')
                    ->where('guia.rol', '=', 'Profesor_Guia');
            })
            ->leftJoin('profesor as pg', 'pg.rut_profesor', '=', 'guia.rut_profesor')
            ->leftJoin('asigna as co', function($join){
                $join->on('co.id_habilitacion', '=', 'hp.id_habilitacion')
                    ->where('co.rol', '=', 'Profesor_Co_Guia');
            })
            ->leftJoin('profesor as pcg', 'pcg.rut_profesor', '=', 'co.rut_profesor')
            ->leftJoin('asigna as com', function($join){
                $join->on('com.id_habilitacion', '=', 'hp.id_habilitacion')
                    ->where('com.rol', '=', 'Profesor_Comision');
            })
            ->leftJoin('asigna as tut', function($join){
                $join->on('tut.id_habilitacion', '=', 'hp.id_habilitacion')
                    ->where('tut.rol', '=', 'Profesor_Tutor');
            })
            ->leftJoin('profesor as pt', 'pt.rut_profesor', '=', 'tut.rut_profesor')
            ->leftJoin('profesor as pcom', 'pcom.rut_profesor', '=', 'com.rut_profesor')
            // unir tablas de títulos (pring/prinv)
            ->leftJoin('pring as pring', 'pring.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prinv as prinv', 'prinv.id_habilitacion', '=', 'hp.id_habilitacion')
            // unir prtut para datos de tutor/empresa/supervisor
            ->leftJoin('prtut as prtut', 'prtut.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('supervisor as s', 's.rut_supervisor', '=', 'prtut.rut_supervisor')
            ->leftJoin('empresa as e', 'e.rut_empresa', '=', 'prtut.rut_empresa')
            ->where('hp.año_semestre', $anio)
            ->where('hp.numero_semestre', $sem)
            ->select(
                'hp.id_habilitacion',
                'hp.rut_alumno',
                'a.nombre_alumno',
                'pg.rut_profesor as rut_guia',
                'pg.nombre_profesor as nombre_guia',
                'pcg.rut_profesor as rut_co_guia',
                'pcg.nombre_profesor as nombre_co_guia',
                'pcom.rut_profesor as rut_comision',
                'pcom.nombre_profesor as nombre_comision',
                'pt.rut_profesor as rut_tutor',
                'pt.nombre_profesor as nombre_tutor',
                DB::raw('COALESCE(pring.titulo_proy, prinv.titulo_proy) as titulo_proyecto'),
                'hp.descripcion_habilitacion',
                'hp.nota_final',
                's.nombre_supervisor',
                's.rut_supervisor',
                'e.rut_empresa',
                'e.nombre_empresa'
            )
            ->get();

        // Devolver vista con pestaña seleccionada 'semestral'
        return view('habilitacion.index', [
            'tab' => 'semestral',
            'results' => $habilitaciones,
            'semestre' => sprintf('%04d-%d', $anio, $sem),
        ]);
    }

    /**
     * R4.1: Listado histórico (R4.10)
     *
     * Entrada: rut_profesor (obligatorio), semestre_inicio (obligatorio)
     */
    public function listadoHistorico(Request $request)
    {
        // R4.3: Validación de presencia de rut_profesor y semestre_inicio
        // R4.6: Validar presencia de rut_profesor
        if (!$request->filled('rut_profesor')) {
            // R4.6: Mensaje de error cuando Rut_Profesor no es válido
            abort(422, 'El valor de Rut_Profesor no es válido');
        }

        // R4.5: Validar presencia de semestre_inicio
        if (!$request->filled('semestre_inicio')) {
            // R4.5: Mensaje de error cuando Semestre_Inicio no es válido
            abort(422, 'El valor de Semestre_Inicio no es válido');
        }

        $rut = $this->normalizeRut($request->input('rut_profesor'));

        // R4.7: Comprobar que el profesor existe en el sistema
        $exists = DB::table('profesor')->where('rut_profesor', $rut)->exists();
        if (!$exists) {
            // R4.7: Mensaje de error cuando Rut_Profesor no se encuentra registrado
            abort(404, 'El valor de Rut_Profesor no se encuentra en registrado en el sistema "Habilprof"');
        }

        [$anio, $sem] = $this->parseSemestre($request);

        // R4.10: Consultar habilitaciones donde participa ese profesor en el semestre
        $rows = DB::table('asigna as asg')
            ->join('habilitacion_profesional as hp', 'hp.id_habilitacion', '=', 'asg.id_habilitacion')
            ->join('alumno as a', 'a.rut_alumno', '=', 'hp.rut_alumno')
            ->leftJoin('profesor as p', 'p.rut_profesor', '=', 'asg.rut_profesor')
            ->leftJoin('pring as pring', 'pring.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prinv as prinv', 'prinv.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prtut as prtut', 'prtut.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('supervisor as s', 's.rut_supervisor', '=', 'prtut.rut_supervisor')
            ->leftJoin('empresa as e', 'e.rut_empresa', '=', 'prtut.rut_empresa')
            ->where('asg.rut_profesor', $rut)
            ->where('hp.año_semestre', $anio)
            ->where('hp.numero_semestre', $sem)
            ->select(
                'hp.id_habilitacion',
                'hp.rut_alumno',
                'a.nombre_alumno',
                'asg.rol',
                'p.rut_profesor as rut_profesor',
                'p.nombre_profesor',
                DB::raw('COALESCE(pring.titulo_proy, prinv.titulo_proy) as titulo_proyecto'),
                'hp.descripcion_habilitacion',
                'hp.nota_final',
                's.nombre_supervisor',
                's.rut_supervisor',
                'e.rut_empresa',
                'e.nombre_empresa'
            )
            ->get();

        return view('habilitacion.index', [
            'tab' => 'historico',
            'results' => $rows,
            'semestre' => sprintf('%04d-%d', $anio, $sem),
            'rut_profesor' => $rut,
        ]);
    }

    /**
     * R4.8: Endpoint JSON para pruebas: listado semestral
     * Uso: GET /habilitacion/api/semestral?semestre_inicio=2025-1
     */
    public function listadoSemestralJson(Request $request)
    {
        // R4.2: Validación de presencia de semestre_inicio
        if (!$request->filled('semestre_inicio')) {
            return response()->json(['error' => 'Semestre_Inicio es obligatorio'], 422);
        }

        [$anio, $sem] = $this->parseSemestre($request);

        $habilitaciones = DB::table('habilitacion_profesional as hp')
            ->join('alumno as a', 'a.rut_alumno', '=', 'hp.rut_alumno')
            ->leftJoin('asigna as guia', function($join){
                $join->on('guia.id_habilitacion', '=', 'hp.id_habilitacion')
                    ->where('guia.rol', '=', 'Profesor_Guia');
            })
            ->leftJoin('profesor as pg', 'pg.rut_profesor', '=', 'guia.rut_profesor')
            ->leftJoin('asigna as co', function($join){
                $join->on('co.id_habilitacion', '=', 'hp.id_habilitacion')
                    ->where('co.rol', '=', 'Profesor_Co_Guia');
            })
            ->leftJoin('profesor as pcg', 'pcg.rut_profesor', '=', 'co.rut_profesor')
            ->leftJoin('asigna as com', function($join){
                $join->on('com.id_habilitacion', '=', 'hp.id_habilitacion')
                    ->where('com.rol', '=', 'Profesor_Comision');
            })
            ->leftJoin('asigna as tut', function($join){
                $join->on('tut.id_habilitacion', '=', 'hp.id_habilitacion')
                    ->where('tut.rol', '=', 'Profesor_Tutor');
            })
            ->leftJoin('profesor as pt', 'pt.rut_profesor', '=', 'tut.rut_profesor')
            ->leftJoin('profesor as pcom', 'pcom.rut_profesor', '=', 'com.rut_profesor')
            ->leftJoin('pring as pring', 'pring.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prinv as prinv', 'prinv.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prtut as prtut', 'prtut.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('supervisor as s', 's.rut_supervisor', '=', 'prtut.rut_supervisor')
            ->leftJoin('empresa as e', 'e.rut_empresa', '=', 'prtut.rut_empresa')
            ->where('hp.año_semestre', $anio)
            ->where('hp.numero_semestre', $sem)
            ->select(
                'hp.id_habilitacion',
                'hp.rut_alumno',
                'a.nombre_alumno',
                'pg.rut_profesor as rut_guia',
                'pg.nombre_profesor as nombre_guia',
                'pcg.rut_profesor as rut_co_guia',
                'pcg.nombre_profesor as nombre_co_guia',
                'pcom.rut_profesor as rut_comision',
                'pcom.nombre_profesor as nombre_comision',
                'pt.rut_profesor as rut_tutor',
                'pt.nombre_profesor as nombre_tutor',
                DB::raw('COALESCE(pring.titulo_proy, prinv.titulo_proy) as titulo_proyecto'),
                'hp.descripcion_habilitacion',
                'hp.nota_final',
                's.nombre_supervisor',
                's.rut_supervisor',
                'e.rut_empresa',
                'e.nombre_empresa'
            )
            ->get();

        return response()->json([ 'semestre' => sprintf('%04d-%d', $anio, $sem), 'results' => $habilitaciones ]);
    }

    /**
     * R4.8: Endpoint JSON para pruebas: listado histórico
     * Uso: GET /habilitacion/api/historico?rut_profesor=11111111&semestre_inicio=2025-1
     */
    public function listadoHistoricoJson(Request $request)
    {
        // R4.3: Validación de presencia de rut_profesor
        if (!$request->filled('rut_profesor')) {
            return response()->json(['error' => 'Rut_Profesor es obligatorio'], 422);
        }
        // R4.3: Validación de presencia de semestre_inicio
        if (!$request->filled('semestre_inicio')) {
            return response()->json(['error' => 'Semestre_Inicio es obligatorio'], 422);
        }

        $rut = $this->normalizeRut($request->input('rut_profesor'));
        // R4.7: Verificación de existencia del profesor
        $exists = DB::table('profesor')->where('rut_profesor', $rut)->exists();
        if (!$exists) {
            return response()->json(['error' => 'Rut no encontrado en el sistema'], 404);
        }

        [$anio, $sem] = $this->parseSemestre($request);

        $rows = DB::table('asigna as asg')
            ->join('habilitacion_profesional as hp', 'hp.id_habilitacion', '=', 'asg.id_habilitacion')
            ->join('alumno as a', 'a.rut_alumno', '=', 'hp.rut_alumno')
            ->leftJoin('profesor as p', 'p.rut_profesor', '=', 'asg.rut_profesor')
            ->leftJoin('pring as pring', 'pring.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prinv as prinv', 'prinv.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('prtut as prtut', 'prtut.id_habilitacion', '=', 'hp.id_habilitacion')
            ->leftJoin('supervisor as s', 's.rut_supervisor', '=', 'prtut.rut_supervisor')
            ->leftJoin('empresa as e', 'e.rut_empresa', '=', 'prtut.rut_empresa')
            ->where('asg.rut_profesor', $rut)
            ->where('hp.año_semestre', $anio)
            ->where('hp.numero_semestre', $sem)
            ->select(
                'hp.id_habilitacion',
                'hp.rut_alumno',
                'a.nombre_alumno',
                'asg.rol',
                'p.rut_profesor as rut_profesor',
                'p.nombre_profesor',
                DB::raw('COALESCE(pring.titulo_proy, prinv.titulo_proy) as titulo_proyecto'),
                'hp.descripcion_habilitacion',
                'hp.nota_final',
                's.nombre_supervisor',
                's.rut_supervisor',
                'e.rut_empresa',
                'e.nombre_empresa'
            )
            ->get();

        return response()->json([ 'semestre' => sprintf('%04d-%d', $anio, $sem), 'rut_profesor' => $rut, 'results' => $rows ]);
    }
}
