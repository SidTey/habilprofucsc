<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ForceRestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:force-restore {--force : Ejecutar la restauración (requerido)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forzar la restauración limpia de la base de datos habilprof desde database/backup/habilprof.sql';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('force')) {
            $this->error('Operación abortada: debe pasar la opción --force para ejecutar la restauración.');
            $this->line('Ejemplo: php artisan db:force-restore --force');
            return 1;
        }

        $db = env('DB_DATABASE', 'habilprof');
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', 5432);
        $user = env('DB_USERNAME', 'postgres');
        $password = env('DB_PASSWORD', '');
        $psqlPath = env('PSQL_PATH') ?: 'psql';
        $sqlPath = base_path('database/backup/habilprof.sql');

        if (!file_exists($sqlPath)) {
            $this->error("Archivo de dump no encontrado en: {$sqlPath}");
            return 1;
        }

        $this->info("Usando psql: {$psqlPath}");

        // Paso 1: DROP DATABASE IF EXISTS; CREATE DATABASE
        $dropCreate = sprintf('DROP DATABASE IF EXISTS "%s"; CREATE DATABASE "%s";', $db, $db);
        $process1 = new Process([$psqlPath, '-h', $host, '-p', (string)$port, '-U', $user, '-d', 'postgres', '-c', $dropCreate]);
        $process1->setEnv(['PGPASSWORD' => $password]);
        $this->info('Eliminando y creando la base de datos...');
        $process1->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process1->isSuccessful()) {
            $this->error('Falló DROP/CREATE: ' . $process1->getErrorOutput());
            Log::error('db:force-restore drop/create failed: ' . $process1->getErrorOutput());
            return 1;
        }

        $this->info('Base de datos recreada. Iniciando importación del dump...');

        $process2 = new Process([$psqlPath, '-h', $host, '-p', (string)$port, '-U', $user, '-d', $db, '-f', $sqlPath]);
        $process2->setEnv(['PGPASSWORD' => $password]);
        $process2->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process2->isSuccessful()) {
            $this->error('Falló la importación: ' . $process2->getErrorOutput());
            Log::error('db:force-restore import failed: ' . $process2->getErrorOutput());
            return 1;
        }

        $this->info('Restauración completada correctamente. La base ahora coincide con el dump.');
        Log::info('db:force-restore: restauración completada.');
        return 0;
    }
}
