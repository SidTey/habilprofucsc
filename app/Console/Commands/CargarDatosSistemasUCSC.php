<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

// --- MODELOS DE LA BD FANTASMA (Sistemas_UCSC_ghost) ---
use App\Models\NominaAlumno;
use App\Models\NominaProfesor;
use App\Models\NotasEnLinea;

// --- MODELOS DE TU BD LOCAL (habilprof_ucsc) ---
use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\HabilitacionProfesional;

class CargarDatosSistemasUCSC extends Command
{
    protected $signature = 'sync:ucsc';
    protected $description = 'Sincroniza Alumnos/Profes y actualiza las notas de habilitaciones existentes';

    public function handle()
    {
        $this->line("==================================================");
        $this->line("Iniciando Sincronización...");
        $this->line("==================================================");

        // Log en archivo específico de sincronización
        $logPath = storage_path('logs/sync_ucsc.log');
        $fecha_ingreso_sync = Carbon::now(); // Fecha de sincronización

        file_put_contents($logPath, "[" . $fecha_ingreso_sync->format('d/m/Y H:i:s') . "] Iniciando Sincronización UCSC\n", FILE_APPEND);

        //Conexion con la base de datos fantasma
        try {
            DB::connection('db_fantasma')->getPdo();
        } catch (\Exception $e) {
            $mensaje = "No se ha podido establecer conexión con los sistemas UCSC";
            file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] " . $mensaje . "\n", FILE_APPEND);
            $this->line("ERROR: " . $mensaje);
            return 1;
        }


        $profesoresExternos = NominaProfesor::all();
        file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Profesores encontrados: " . $profesoresExternos->count() . "\n", FILE_APPEND);

        foreach ($profesoresExternos as $prof) {

            //valida si los datos son correctos en la base de datos fantasma, para poder guardarlos
            $validator = Validator::make($prof->toArray(), [
                'rut_profesor' => ['required', 'integer', 'between:1000000,60000000'],
                'nombre_profesor' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
                'correo_profesor' => ['required', 'email:rfc,dns', 'max:255', 'min:7'],
            ]);
            //valida si los datos son correctos, si no salta al profesor y continua con el siguiente
            if ($validator->fails()) {
                file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Profesor rechazado RUT: " . $prof->rut_profesor . " - Errores: " . implode(", ", $validator->errors()->all()) . "\n", FILE_APPEND);
                continue;
            }
            // crea o actualiza los datos del profesor en la base de datos habilprof
            // si no existe el rut este lo crea con todos sus datos correspondientes, de lo contrario solo actualiza nombre y correo
            Profesor::updateOrCreate(
                ['rut_profesor' => $prof->rut_profesor],
                [
                    'nombre_profesor' => $prof->nombre_profesor,
                    'correo_profesor' => $prof->correo_profesor,
                ]
            );
            file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Profesor guardado: " . $prof->rut_profesor . " | " . $prof->nombre_profesor . " | " . $prof->correo_profesor . "\n", FILE_APPEND);
        }
        file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Profesores sincronizados: " . $profesoresExternos->count() . "\n", FILE_APPEND);




        $alumnosExternos = NominaAlumno::all();
        file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Alumnos encontrados: " . $alumnosExternos->count() . "\n", FILE_APPEND);

        foreach ($alumnosExternos as $alumno) {

            // Validación de datos del alumno
            $validator = Validator::make($alumno->toArray(), [
                'rut_alumno' => ['required', 'integer', 'between:1000000,60000000'],
                'nombre_alumno' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
                'correo_alumno' => ['required', 'email:rfc,dns', 'max:255', 'min:7'],
            ]);
            if ($validator->fails()) {
                file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Alumno rechazado RUT: " . $alumno->rut_alumno . " - Errores: " . implode(", ", $validator->errors()->all()) . "\n", FILE_APPEND);
                //verifica si los datos del alumno son validos, si no lo son salta al siguiente alumno.
                continue;
            }
            // si no existe el rut este lo crea con todos sus datos correspondientes, de lo contrario solo actualiza nombre y correo
            $localAlumno = Alumno::updateOrCreate(
                ['rut_alumno' => $alumno->rut_alumno],
                ['nombre_alumno' => $alumno->nombre_alumno, 'correo_alumno' => $alumno->correo_alumno]
            );
            // esto guarda al alumno ingresado en el log
            file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Alumno guardado: " . $alumno->rut_alumno . " | " . $alumno->nombre_alumno . " | " . $alumno->correo_alumno . "\n", FILE_APPEND);

            $habilitacion = HabilitacionProfesional::where('rut_alumno', $localAlumno->rut_alumno)->first();

            if (!$habilitacion) {
                //verifica si ese alumno ingresado tiene una habilitacion asociado y lo guarda en el log
                file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Alumno sin habilitación local: " . $localAlumno->rut_alumno . " | " . $localAlumno->nombre_alumno . "\n", FILE_APPEND);
                continue;
            }

            // obtiene la nota de la base de datos fantasma
            $notaRelacion = $alumno->notaHabilitacion();
            $nota_obj = $notaRelacion->first();
            if (!$nota_obj) {
                continue;
            }

            // extrae nota final
            $nota_final = $nota_obj->nota ?? null;

            // valida y convierte la nota final en float
            $nota_final = ($nota_final === null) ? null : (float)$nota_final;

            // Validar que la nota esté entre 1.0 y 7.0
            if ($nota_final !== null && !($nota_final >= 1.0 && $nota_final <= 7.0)) {
                $nota_final = null;
                continue;
            }

            // Validar que tenga máximo 1 cifra decimal
            if ($nota_final !== null && !$this->validarNotaConDecimal($nota_final)) {
                file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Nota rechazada - Alumno: " . $localAlumno->nombre_alumno . " | Nota: " . $nota_final . " (más de 1 decimal)\n", FILE_APPEND);
                continue;
            }

            // Obtener fecha_nota del módulo notas en línea
            // Si existe y es válida, se usa esa fecha. Si no, se mantiene nula (por defecto)
            $fecha_nota = $nota_obj->fecha_nota ?? null;
            $fecha_nota_validada = null;

            if ($fecha_nota !== null) {
                $fecha_nota_validada = $this->validarFechaNota($fecha_nota);
                if ($fecha_nota_validada === null) {
                    file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Fecha_nota rechazada - Alumno: " . $localAlumno->nombre_alumno . " | Fecha: " . $fecha_nota . "\n", FILE_APPEND);
                    continue;
                }
            }
            // Si no existe fecha_nota en Notas en línea, se mantiene nula

            // si la nota final no es nula, actualiza la habilitacion profesional con la nota y fecha_nota
            if ($nota_final !== null) {

                $affectedRows = DB::connection('pgsql')
                                  ->table('habilitacion_profesional')
                                  ->where('id_habilitacion', $habilitacion->id_habilitacion)
                                  ->update([
                                      'nota_final' => $nota_final,
                                      'fecha_nota' => $fecha_nota_validada
                                  ]);

                if ($affectedRows == 0) {
                     continue;
                }

                // Log con todos los datos requeridos
                $fechaFormato = $fecha_nota_validada ? $fecha_nota_validada->format('d/m/Y') : 'nula';
                file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] NOTA ACTUALIZADA | Alumno RUT: " . $localAlumno->rut_alumno . " | Nombre: " . $localAlumno->nombre_alumno . " | Correo: " . $localAlumno->correo_alumno . " | Nota: " . $nota_final . " | Fecha_Nota: " . $fechaFormato . " | Fecha_Ingreso: " . $fecha_ingreso_sync->format('d/m/Y') . "\n", FILE_APPEND);
            }
        } // <-- Cierre del foreach ($alumnosExternos...)

        file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Sincronización completada\n", FILE_APPEND);
        file_put_contents($logPath, "[" . now()->format('d/m/Y H:i:s') . "] Profesores: " . $profesoresExternos->count() . " | Alumnos: " . $alumnosExternos->count() . " | Notas: " . HabilitacionProfesional::whereNotNull('nota_final')->count() . "\n", FILE_APPEND);
        file_put_contents($logPath, "==================================================\n", FILE_APPEND);

        $this->line("==================================================");
        $this->line("Sincronización completada exitosamente.");
        $this->line("Profesores sincronizados: " . $profesoresExternos->count());
        $this->line("Alumnos sincronizados: " . $alumnosExternos->count());
        $this->line("Notas sincronizadas: " . HabilitacionProfesional::whereNotNull('nota_final')->count());
        $this->line("==================================================");

        return 0;
    }

    //Valida que la nota tenga máximo 1 cifra decimal
    private function validarNotaConDecimal($nota)
    {
        // Multiplicar por 10 y verificar que sea un número entero
        $multiplicado = $nota * 10;
        return $multiplicado == intval($multiplicado);
    }

    /**
     * Valida fecha_nota
     */
    private function validarFechaNota($fecha)
    {
        try {
            // Intentar parsear la fecha en múltiples formatos
            $fechaCarbon = null;

            // Intentar DD/MM/YYYY
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fecha)) {
                $fechaCarbon = Carbon::createFromFormat('d/m/Y', $fecha);
            }
            // Intentar YYYY-MM-DD
            elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                $fechaCarbon = Carbon::createFromFormat('Y-m-d', $fecha);
            }
            // Intentar parseo automático
            else {
                $fechaCarbon = Carbon::parse($fecha);
            }

            if (!$fechaCarbon) {
                return null;
            }

            $dia = $fechaCarbon->day;
            $mes = $fechaCarbon->month;
            $año = $fechaCarbon->year;

            // Validar año [2000-2050]
            if ($año < 2000 || $año > 2050) {
                return null;
            }

            // Validar mes [1-12]
            if ($mes < 1 || $mes > 12) {
                return null;
            }

            // Validar día según mes
            $diasValidos = false;

            // Meses con 31 días: 1, 3, 5, 7, 8, 10, 12
            if (in_array($mes, [1, 3, 5, 7, 8, 10, 12])) {
                $diasValidos = ($dia >= 1 && $dia <= 31);
            }
            // Meses con 30 días: 4, 6, 9, 11
            elseif (in_array($mes, [4, 6, 9, 11])) {
                $diasValidos = ($dia >= 1 && $dia <= 30);
            }
            // Febrero (en caso de año bisiesto o no)
            elseif ($mes == 2) {
                $esBisiesto = ($año % 4 == 0 && $año % 100 != 0) || ($año % 400 == 0);
                $maxDias = $esBisiesto ? 29 : 28;
                $diasValidos = ($dia >= 1 && $dia <= $maxDias);
            }

            if (!$diasValidos) {
                return null;
            }

            return $fechaCarbon;

        } catch (\Exception $e) {
            return null;
        }
    }
}
