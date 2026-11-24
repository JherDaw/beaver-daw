<?php
/**
 * Conexión a la base de datos con PDO.
 * Se usa una única conexión por petición (patrón singleton sencillo).
 * Los datos de acceso se pueden cambiar con variables de entorno;
 * si no existen, se usan los valores por defecto de XAMPP.
 */

function conexion(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = getenv('DB_HOST') ?: 'localhost';
        $base = getenv('DB_NAME') ?: 'db_beaver';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';

        $pdo = new PDO(
            "mysql:host=$host;dbname=$base;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }

    return $pdo;
}
