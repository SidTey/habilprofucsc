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
        
        file_put_contents($logPath, "[" . now() . "] Iniciando Sincronización UCSC\n", FILE_APPEND);

        //Conexion con la base de datos fantasma.
        try {
            DB::connection('db_fantasma')->getPdo();
        } catch (\Exception $e) {
            file_put_contents($logPath, "[" . now() . "] No se ha podido establecer conexión con los sistemas UCSC\n", FILE_APPEND);
            return 1;
        }

        
        $profesoresExternos = NominaProfesor::all();
        file_put_contents($logPath, "[" . now() . "] Profesores encontrados: " . $profesoresExternos->count() . "\n", FILE_APPEND);
        
        foreach ($profesoresExternos as $prof) {
            
            //validar si los datos son correctos en la base de datos fantasma, para poder guardarlos
            $validator = Validator::make($prof->toArray(), [
                'rut_profesor' => ['required', 'integer', 'between:1000000,60000000'],
                'nombre_profesor' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
                'correo_profesor' => ['required', 'email:rfc,dns', 'max:255'], // SIN RESTRICCION
                //'correo_profesor' => ['required', 'email:rfc,dns', 'max:255', 'regex:/@ucsc\.cl$/i'], //CON RESTRICCION
            ]);
            //valida si los datos son correctos, si no salta al profesor y continua con el siguiente 
            if ($validator->fails()) {
                file_put_contents($logPath, "[" . now() . "] Profesor rechazado RUT: " . $prof->rut_profesor . "\n", FILE_APPEND);
                continue; 
            }
            // crea o actualiza los datos del profesor en la base de datos habilprof, (depende si el rut ya existe o no)
            // si no existe el rut este lo crea con todos sus datos correspondientes, de lo contrario solo actualiza nombre y correo
            Profesor::updateOrCreate(
                ['rut_profesor' => $prof->rut_profesor], 
                [ 
                    'nombre_profesor' => $prof->nombre_profesor,
                    'correo_profesor' => $prof->correo_profesor,
                ]
            );
            file_put_contents($logPath, "[" . now() . "] Profesor guardado: " . $prof->rut_profesor . " | " . $prof->nombre_profesor . " | " . $prof->correo_profesor . "\n", FILE_APPEND);
        }
        file_put_contents($logPath, "[" . now() . "] Profesores sincronizados: " . $profesoresExternos->count() . "\n", FILE_APPEND);




        $alumnosExternos = NominaAlumno::all(); 
        $fecha_ingreso_sync = Carbon::now();
        file_put_contents($logPath, "[" . now() . "] Alumnos encontrados: " . $alumnosExternos->count() . "\n", FILE_APPEND);

        foreach ($alumnosExternos as $alumno) {
            
     
            $validator = Validator::make($alumno->toArray(), [
                'rut_alumno' => ['required', 'integer', 'between:1000000,60000000'],
                'nombre_alumno' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
                'correo_alumno' => ['required', 'email:rfc,dns', 'max:255'], // SIN RESTRICCION
                //'correo_alumno' => ['required', 'email:rfc,dns', 'max:255', 'regex:/@ing\.ucsc\.cl$/i'], // CON RESTRICCION
            ]);
            if ($validator->fails()) { 
                file_put_contents($logPath, "[" . now() . "] Alumno rechazado RUT: " . $alumno->rut_alumno . "\n", FILE_APPEND);
                //verifica si los datos del alumno son validos, si no lo son salta al siguiente alumno.
                continue; 
            }
            // crea o actualiza los datos del alumno en la base de datos habilprof, (depende si el rut ya existe o no)
            // si no existe el rut este lo crea con todos sus datos correspondientes, de lo contrario
            $localAlumno = Alumno::updateOrCreate(
                ['rut_alumno' => $alumno->rut_alumno], 
                ['nombre_alumno' => $alumno->nombre_alumno, 'correo_alumno' => $alumno->correo_alumno]
            );
            // esto guarda al alumno ingresado en el log
            file_put_contents($logPath, "[" . now() . "] Alumno guardado: " . $alumno->rut_alumno . " | " . $alumno->nombre_alumno . " | " . $alumno->correo_alumno . "\n", FILE_APPEND);

 
            

            $habilitacion = HabilitacionProfesional::where('rut_alumno', $localAlumno->rut_alumno)->first();
            
            if (!$habilitacion) {
                //verifica si ese alumno ingresado tiene una habilitacion asociado y lo guarda en el log
                file_put_contents($logPath, "[" . now() . "] Alumno sin habilitación local: " . $localAlumno->rut_alumno . " | " . $localAlumno->nombre_alumno . "\n", FILE_APPEND);
                continue; 
            }
            
            // obtiene la nota de la base de datos fantasma y las guarda y convierte en objeto, ya que esto hace una consulta preparada
            $notaRelacion = $alumno->notaHabilitacion(); 
            $nota_obj = $notaRelacion->first();
            if (!$nota_obj) {
                continue;
            }
            // extrae la nota final 
            $nota_final = $nota_obj->nota ?? null;

            // valida y convierte la nota final en float
            $nota_final = ($nota_final === null) ? null : (float)$nota_final;
            if ($nota_final !== null && !($nota_final >= 1.0 && $nota_final <= 7.0)) {
                $nota_final = null;
            }
            
            // si la nota final no es nula, actualiza la habilitacion profesional con la nota y la fecha de ingreso.
            if ($nota_final !== null) {

                $affectedRows = DB::connection('pgsql')
                                  ->table('habilitacion_profesional')
                                  ->where('id_habilitacion', $habilitacion->id_habilitacion)
                                  ->update([
                                      'nota_final' => $nota_final,
                                      'fecha_nota' => $fecha_ingreso_sync
                                  ]);
                
                if ($affectedRows == 0) {
                     continue;
                }
                
                file_put_contents($logPath, "[" . now() . "] Nota actualizada - Alumno: " . $localAlumno->nombre_alumno . " | Nota: " . $nota_final . "\n", FILE_APPEND);
            }
        } // <-- Cierre del foreach ($alumnosExternos...)

        file_put_contents($logPath, "[" . now() . "] Sincronización completada\n", FILE_APPEND);
        file_put_contents($logPath, "[" . now() . "] Profesores: " . $profesoresExternos->count() . " | Alumnos: " . $alumnosExternos->count() . " | Notas: " . HabilitacionProfesional::whereNotNull('nota_final')->count() . "\n", FILE_APPEND);
        file_put_contents($logPath, "==================================================\n", FILE_APPEND);
        
        $this->line("==================================================");
        $this->line("Sincronización completada exitosamente.");
        $this->line("Profesores sincronizados: " . $profesoresExternos->count());
        $this->line("Alumnos sincronizados: " . $alumnosExternos->count());
        $this->line("Notas sincronizadas: " . HabilitacionProfesional::whereNotNull('nota_final')->count());
        $this->line("==================================================");
        
        return 0; 

        
        
    } 
}