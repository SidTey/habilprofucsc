<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LogSistema;

class DebugLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostrar logs del sistema para depuración';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logs = LogSistema::latest()->take(5)->get();
        
        if ($logs->isEmpty()) {
            $this->info('No hay logs en el sistema');
            return;
        }
        
        foreach ($logs as $log) {
            $this->line("ID: {$log->id}");
            $this->line("Estado: {$log->estado}");
            $this->line("Mensaje: {$log->mensaje}");
            $this->line("Fecha: {$log->created_at}");
            $this->line("---");
        }
    }
}
