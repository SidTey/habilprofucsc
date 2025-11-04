<?php
while (true) {
    $timestamp = date('Y-m-d H:i:s');
    echo "[" . $timestamp . "] Ejecutando sync:ucsc...\n";

    // Ruta al artisan relativa al directorio del script
    $artisanPath = __DIR__ . '/artisan';

    // Ejecutar el comando
    $output = shell_exec("php \"{$artisanPath}\" sync:ucsc");

    // Mostrar la salida
    echo $output;
    echo "\n----------------------------------------\n";

    sleep(60); // espera 60 segundos
}