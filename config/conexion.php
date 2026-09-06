<?php
/**
 * Conexión a MySQL.
 *
 * Archivo independiente: ningún otro archivo del proyecto define credenciales.
 * Base de datos: integradora — usuario root sin contraseña (entorno local XAMPP).
 */

declare(strict_types=1);

const DB_HOST   = 'localhost';
const DB_NOMBRE = 'integradora';
const DB_USUARIO = 'root';
const DB_CLAVE  = '';
const DB_CHARSET = 'utf8mb4';

/**
 * Devuelve una única instancia de PDO para toda la petición.
 *
 * La primera llamada abre la conexión y las siguientes reutilizan el mismo
 * objeto, de modo que una petición nunca abre dos conexiones.
 */
function conexion(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NOMBRE . ';charset=' . DB_CHARSET;

    $opciones = [
        // Cualquier fallo de SQL lanza una excepción en lugar de pasar en silencio.
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        // Los resultados llegan como arreglos asociativos.
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Consultas preparadas reales en el servidor MySQL.
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USUARIO, DB_CLAVE, $opciones);
    } catch (PDOException $error) {
        http_response_code(500);
        exit(
            'No se pudo conectar con la base de datos "' . DB_NOMBRE . '". '
            . 'Revisa que MySQL esté iniciado en el panel de XAMPP y que la base '
            . 'haya sido importada desde database/integradora.sql. '
            . 'Detalle: ' . $error->getMessage()
        );
    }

    return $pdo;
}
