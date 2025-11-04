<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSqlBackup extends Command
{
    protected $signature = 'db:import-sql';
    protected $description = 'Importa el archivo backup.sql desde database/backups/ a la base de datos';

    public function handle()
    {
        $backupPath = database_path('backups/backup.sql');

        // Verificar si el archivo existe
        if (!File::exists($backupPath)) {
            $this->error('Error: El archivo backup.sql no existe en database/backups/');
            return 1;
        }

        try {
            // Limpiar base de datos
            $this->info('Limpiando base de datos...');
            DB::unprepared('DROP SCHEMA public CASCADE; CREATE SCHEMA public;');

            // Leer el contenido del archivo SQL
            $sql = File::get($backupPath);

            // Eliminar las líneas de \restrict y \unrestrict y otras marcas de pgdump
            $sql = preg_replace('/\\\\.*\\n/', '', $sql);

            // Mostrar mensaje de inicio
            $this->info('Restaurando base de datos desde backup...');

            // Extraer las diferentes partes del SQL
            preg_match_all('/CREATE TYPE.*?;/s', $sql, $createTypes);
            preg_match_all('/CREATE TABLE.*?;/s', $sql, $createTables);
            preg_match_all('/INSERT INTO.*?;/s', $sql, $inserts);
            preg_match_all('/ALTER TABLE.*ADD CONSTRAINT.*?;/s', $sql, $constraints);

            // Primero crear los tipos ENUM
            $this->info('Creando tipos...');
            foreach ($createTypes[0] as $statement) {
                if (!empty($statement)) {
                    try {
                        DB::unprepared($statement);
                    } catch (\Exception $e) {
                        if (!str_contains($e->getMessage(), 'already exists')) {
                            throw $e;
                        }
                    }
                }
            }

            // Luego crear las tablas
            $this->info('Creando tablas...');
            foreach ($createTables[0] as $statement) {
                if (!empty($statement)) {
                    try {
                        DB::unprepared($statement);
                    } catch (\Exception $e) {
                        if (!str_contains($e->getMessage(), 'already exists')) {
                            throw $e;
                        }
                    }
                }
            }

            // Luego insertar los datos
            $this->info('Insertando datos...');
            foreach ($inserts[0] as $statement) {
                if (!empty($statement)) {
                    try {
                        DB::unprepared($statement);
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }
            }

            // Finalmente agregar las restricciones
            $this->info('Agregando restricciones...');
            foreach ($constraints[0] as $statement) {
                if (!empty($statement)) {
                    try {
                        DB::unprepared($statement);
                    } catch (\Exception $e) {
                        if (!str_contains($e->getMessage(), 'already exists')) {
                            throw $e;
                        }
                    }
                }
            }

            // Mostrar mensaje de éxito
            $this->info('¡Restauración completada exitosamente!');
            $this->info('La base de datos ha sido restaurada desde el backup.');

            return 0;

        } catch (\Exception $e) {
            $this->error('Error durante la restauración: ' . $e->getMessage());
            $this->error('No se pudo completar la restauración del backup.');
            return 1;
        }
    }
}
