<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AutentificacionDeUsuario;
use Illuminate\Support\Facades\Hash;

// Buscar el usuario
$user = AutentificacionDeUsuario::where('rut_admin', 12345678)->first();

if (!$user) {
    echo "Usuario no encontrado\n";
    exit(1);
}

echo "Usuario encontrado:\n";
echo "RUT: {$user->rut_admin}\n";
echo "Contraseña (hash): " . substr($user->contraseña, 0, 20) . "...\n";
echo "Longitud del hash: " . strlen($user->contraseña) . "\n";

// Obtener el nombre de la columna de contraseña que usa el modelo
echo "Columna de contraseña: " . $user->getAuthPasswordName() . "\n";

// Probar la verificación de contraseña
$passwordToTest = 'password123';
echo "\nProbando contraseña: '{$passwordToTest}'\n";

// Intentar verificar
try {
    $isValid = Hash::check($passwordToTest, $user->contraseña);
    echo "¿Es válida? " . ($isValid ? "SÍ" : "NO") . "\n";
} catch (\Exception $e) {
    echo "Error al verificar: " . $e->getMessage() . "\n";
}

// También probar obteniendo la contraseña del método del modelo
echo "\nUsando getAuthPassword():\n";
try {
    $authPassword = $user->getAuthPassword();
    echo "Contraseña obtenida: " . substr($authPassword, 0, 20) . "...\n";
    $isValid2 = Hash::check($passwordToTest, $authPassword);
    echo "¿Es válida? " . ($isValid2 ? "SÍ" : "NO") . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
