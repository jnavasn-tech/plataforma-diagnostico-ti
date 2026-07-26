<?php
require_once __DIR__ . '/env.php';

/**
 * Devuelve una conexión PDO reutilizable a la base de datos MySQL.
 * Usa PDO (en vez de mysqli) para permitir bind de parámetros seguro
 * y una futura migración sencilla a otro motor SQL si fuera necesario.
 */
function obtener_conexion(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: 'localhost';
    $nombreBD = getenv('DB_NAME') ?: 'plataforma_capacitacion';
    $usuario = getenv('DB_USER') ?: 'root';
    $clave = getenv('DB_PASS') ?: '';

    $dsn = "mysql:host={$host};dbname={$nombreBD};charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $usuario, $clave, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        // En producción esto debería registrarse en un log, no mostrarse al usuario.
        die('Error de conexión a la base de datos: ' . $e->getMessage());
    }

    return $pdo;
}
