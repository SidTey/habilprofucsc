<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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


        try {
            DB::connection('db_fantasma')->getPdo();
        } catch (\Exception $e) {
            $this->error('No se ha podido establecer conexión con los sistemas UCSC.');
            Log::error('Fallo de conexión con db_fantasma.', ['error' => $e->getMessage()]);
            return 1; // Termina el comando con error
        }

   
        $this->info('Sincronizando Profesores...');
        $profesoresExternos = NominaProfesor::all();

        foreach ($profesoresExternos as $prof) {
            
  
            $validator = Validator::make($prof->toArray(), [
                'rut_profesor' => ['required', 'integer', 'between:1000000,60000000'],
                'nombre_profesor' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
                //'correo_profesor' => ['required', 'email:rfc,dns', 'max:255'],
                'correo_profesor' => ['required', 'email:rfc,dns', 'max:255', 'regex:/@ucsc\.cl$/i'],
            ]);

            if ($validator->fails()) {
                Log::warning('R1.4-R1.6: Profesor externo no válido (saltado): ' . $prof->rut_profesor, $validator->errors()->toArray());
                continue; 
            }

            Profesor::updateOrCreate(
                ['rut_profesor' => $prof->rut_profesor], 
                [ 
                    'nombre_profesor' => $prof->nombre_profesor,
                    'correo_profesor' => $prof->correo_profesor,
                ]
            );
        }
        $this->info('Profesores sincronizados: ' . $profesoresExternos->count());



        $this->info('Sincronizando Alumnos y actualizando Notas...');
        
        $alumnosExternos = NominaAlumno::all(); 
        $fecha_ingreso_sync = Carbon::now();

        foreach ($alumnosExternos as $alumno) {
            
     
            $validator = Validator::make($alumno->toArray(), [
                'rut_alumno' => ['required', 'integer', 'between:1000000,60000000'],
                'nombre_alumno' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
                'correo_alumno' => ['required', 'email:rfc,dns', 'max:255', 'regex:/@ing\.ucsc\.cl$/i'],
            ]);
            if ($validator->fails()) { continue; }


            $localAlumno = Alumno::updateOrCreate(
                ['rut_alumno' => $alumno->rut_alumno], 
                ['nombre_alumno' => $alumno->nombre_alumno, 'correo_alumno' => $alumno->correo_alumno]
            );

 
            

            $habilitacion = HabilitacionProfesional::where('rut_alumno', $localAlumno->rut_alumno)->first();
            
            if (!$habilitacion) {
                // $this->info('Debug: Alumno '.$localAlumno->rut.' no tiene habilitación local. Saltando.');
                continue; 
            }
            
            // Obtener la nota de la relación
            $notaRelacion = $alumno->notaHabilitacion(); 
            if (!$notaRelacion) {
                $this->info('⚠️ Alumno ' . $localAlumno->nombre_alumno . ' no tiene función notaHabilitacion()');
                continue;
            }

            $nota_obj = $notaRelacion->first();
            if (!$nota_obj) {
                $this->info('⚠️ No hay nota registrada para ' . $localAlumno->nombre_alumno);
                continue;
            }

            $nota_final = $nota_obj->nota ?? null;

            $nota_final = ($nota_final === null) ? null : (float)$nota_final;
            if ($nota_final !== null && !($nota_final >= 1.0 && $nota_final <= 7.0)) {
                $nota_final = null;
            }
            
            // 🔧 CORREGIDO: Ahora actualiza SIEMPRE si hay una nota válida
            if ($nota_final !== null) {

                $this->info('¡Nota encontrada para ' . $localAlumno->nombre_alumno . ' Con nota: ' . $nota_final);

                $affectedRows = DB::connection('pgsql')
                                  ->table('habilitacion_profesional')
                                  ->where('id_habilitacion', $habilitacion->id_habilitacion)
                                  ->update([
                                      'nota_final' => $nota_final,
                                      'fecha_nota' => $fecha_ingreso_sync
                                  ]);
                
                if ($affectedRows == 0) {
                     Log::warning('R1.11.2: UPDATE falló para habilitacion ' . $habilitacion->id_habilitacion);
                     continue;
                }
                
                $rut_profesor_asignado = DB::connection('pgsql')
                                           ->table('asigna')
                                           ->where('id_habilitacion', $habilitacion->id_habilitacion)
                                           ->value('rut_profesor');
                
                if (!$rut_profesor_asignado) {
                    Log::warning('Habilitación ' . $habilitacion->id_habilitacion . ' no tiene profesor asignado en "asigna". Log incompleto.');
                }

                $profesorLocal = $rut_profesor_asignado ? Profesor::find($rut_profesor_asignado) : null;
            
                Log::info('R1.13: Carga Habilprof Realizada (Nota Actualizada)', [
                    'Rut_Alumno' => $localAlumno->rut_alumno,
                    'Nombre_Alumno' => $localAlumno->nombre_alumno,
                    'Correo_Alumno' => $localAlumno->correo_alumno,
                    'Rut_Profesor' => $profesorLocal->rut_profesor ?? $rut_profesor_asignado ?? 'N/A',
                    'Nombre_Profesor' => $profesorLocal->nombre_profesor ?? 'N/A',
                    'Correo_Profesor' => $profesorLocal->correo_profesor ?? 'N/A',
                    'Fecha_Ingreso' => $fecha_ingreso_sync->toDateTimeString(),
                    'Nota_Final' => $nota_final,
                ]);
            }
        } // <-- Cierre del foreach ($alumnosExternos...)

        // --- Mensajes Finales ---
        $this->info('Alumnos sincronizados: ' . $alumnosExternos->count());
        $this->info('Notas sincronizadas: ' . HabilitacionProfesional::whereNotNull('nota_final')->count());
        $this->line("==================================================");
        $this->line("Sincronización completada exitosamente.");
        $this->line("==================================================");
        return 0; 

        
        
    } 
}