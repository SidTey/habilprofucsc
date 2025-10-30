<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Evitar cualquier operación de creación/modificación de tablas a menos que
        // se habilite explícitamente mediante la variable de entorno RESTORE_BACKUP=true
        // Esto protege contra creación accidental de tablas (sessions, cache, users, etc.).
        if (env('RESTORE_BACKUP', false) !== true && env('RESTORE_BACKUP', 'false') !== 'true') {
            Log::info('RESTORE_BACKUP no está habilitado; se omite cualquier restauración/creación de base de datos.');
            return;
        }

        // Si llegamos aquí, la restauración está autorizada por la variable de entorno.
        try {
            $targetDb = 'habilprof';
            $sqlPath = database_path('backup/habilprof.sql');

            if (!File::exists($sqlPath)) {
                Log::warning("SQL de backup no encontrada en: {$sqlPath}. Se omite restauración.");
                return;
            }

            // Buscar una conexión con driver pgsql en la configuración
            $connections = Config::get('database.connections', []);
            $pgConnName = null;
            foreach ($connections as $name => $conf) {
                if (isset($conf['driver']) && $conf['driver'] === 'pgsql') {
                    $pgConnName = $name;
                    break;
                }
            }

            if (!$pgConnName) {
                Log::warning('No se encontró una conexión con driver pgsql en config/database.php.');
                return;
            }

            $pg = $connections[$pgConnName];

            // Crear una conexión temporal al sistema (base 'postgres') para poder crear la DB
            $systemConf = $pg;
            $systemConf['database'] = 'postgres';
            Config::set("database.connections.{$pgConnName}_system", $systemConf);
            DB::purge("{$pgConnName}_system");
            $systemConn = DB::connection("{$pgConnName}_system");

            // Comprobar si la base de datos ya existe
            $exists = $systemConn->select('SELECT 1 FROM pg_database WHERE datname = ?', [$targetDb]);
            if (empty($exists)) {
                // Crear la base de datos
                $systemConn->statement('CREATE DATABASE "' . $targetDb . '"');
                Log::info("Base de datos {$targetDb} creada correctamente.");
            } else {
                Log::info("Base de datos {$targetDb} ya existe.");
            }

            // Restauración automática usando el cliente psql si está disponible.
            // Preferimos usar psql porque los dumps generados por pg_dump pueden
            // contener metacomandos y extensiones que DB::unprepared() no entiende.
            $psqlPath = env('PSQL_PATH') ?: null;
            if (!$psqlPath) {
                // Intentar localizar psql en PATH (Windows 'where')
                try {
                    $where = trim(shell_exec('where psql 2>&1')) ?: trim(shell_exec('which psql 2>&1'));
                    if ($where) {
                        $lines = preg_split('/\r?\n/', $where);
                        $psqlPath = $lines[0] ?: null;
                    }
                } catch (Exception $e) {
                    // ignore
                }
            }

            if ($psqlPath && File::exists($psqlPath)) {
                $pgUser = $pg['username'] ?? ($pg['user'] ?? 'postgres');
                $pgHost = $pg['host'] ?? '127.0.0.1';
                $pgPort = $pg['port'] ?? 5432;
                $pgPassword = $pg['password'] ?? env('DB_PASSWORD');

                $cmd = sprintf(
                    '"%s" -h %s -p %s -U %s -d %s -f %s',
                    $psqlPath,
                    escapeshellarg($pgHost),
                    escapeshellarg($pgPort),
                    escapeshellarg($pgUser),
                    escapeshellarg($targetDb),
                    escapeshellarg($sqlPath)
                );

                // Preparar entorno con PGPASSWORD para no pedir interactivo
                $env = array_merge($_ENV, $_SERVER);
                if (!empty($pgPassword)) {
                    $env['PGPASSWORD'] = $pgPassword;
                }

                $descriptors = [
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ];

                $process = proc_open($cmd, $descriptors, $pipes, null, $env);
                if (is_resource($process)) {
                    $stdout = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);

                    $stderr = stream_get_contents($pipes[2]);
                    fclose($pipes[2]);

                    $status = proc_get_status($process);
                    $exit = proc_close($process);

                    if ($exit === 0) {
                        Log::info("Restauración completa de {$targetDb} usando psql. output: " . substr($stdout, 0, 1000));
                    } else {
                        Log::error("Error al restaurar {$targetDb} con psql (exit {$exit}). stderr: " . substr($stderr, 0, 2000));
                        // Intentar fallback con DB::unprepared() leyendo el archivo (solo si es SQL plano)
                        $sql = File::get($sqlPath);
                        if (!empty(trim($sql))) {
                            try {
                                DB::connection($pgConnName)->unprepared($sql);
                                Log::info("Fallback: restauración mediante DB::unprepared() completada.");
                            } catch (Exception $ex) {
                                Log::error('Fallback fallido: ' . $ex->getMessage());
                            }
                        }
                    }
                } else {
                    Log::error('No se pudo iniciar el proceso psql.');
                }
            } else {
                Log::warning('psql no encontrado en sistema. Intentando DB::unprepared() como fallback.');
                // Fallback: intentar ejecutar con DB::unprepared() (puede fallar para dumps con metacomandos)
                $sql = File::get($sqlPath);
                if (!empty(trim($sql))) {
                    try {
                        DB::connection($pgConnName)->unprepared($sql);
                        Log::info("Restauración completa de {$targetDb} desde {$sqlPath} (via DB::unprepared).");
                    } catch (Exception $ex) {
                        Log::error('Error en DB::unprepared fallback: ' . $ex->getMessage());
                    }
                } else {
                    Log::warning("El archivo SQL {$sqlPath} está vacío. No se ejecutó nada.");
                }
            }
        } catch (Exception $e) {
            Log::error('Error creando/restaurando la base de datos habilprof: ' . $e->getMessage());
        }
    }
}
