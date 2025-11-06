<?php
/**
 * Generador de Hash Bcrypt para insertar usuarios en la BD
 * Uso: php generar_hash.php <contraseña>
 */

if ($argc < 2) {
    echo "Uso: php generar_hash.php <contraseña>\n";
    echo "Ejemplo: php generar_hash.php miContraseña123\n";
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "\n=== GENERADOR DE HASH BCRYPT ===\n\n";
echo "Contraseña: {$password}\n";
echo "Hash: {$hash}\n\n";

echo "=== QUERY SQL PARA DBEAVER ===\n\n";
echo "-- Insertar nuevo usuario admin\n";
echo "INSERT INTO autentificacion_de_usuario (rut_admin, contraseña)\n";
echo "VALUES (<RUT_AQUI>, '{$hash}');\n\n";

echo "-- Ejemplo con RUT 11111111:\n";
echo "INSERT INTO autentificacion_de_usuario (rut_admin, contraseña)\n";
echo "VALUES (11111111, '{$hash}');\n\n";

echo "=== ACTUALIZAR CONTRASEÑA EXISTENTE ===\n\n";
echo "UPDATE autentificacion_de_usuario\n";
echo "SET contraseña = '{$hash}'\n";
echo "WHERE rut_admin = <RUT_AQUI>;\n\n";
