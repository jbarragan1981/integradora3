<?php
/**
 * Conexión a MySQL.
 *
 * Archivo independiente: ningún otro archivo del proyecto define credenciales.
 * Base de datos: integradora — usuario root sin contraseña (entorno local XAMPP).
 *
 */

declare(strict_types=1);

const DB_HOST   = 'localhost';
const DB_NOMBRE = 'integradora';
const DB_USUARIO = 'root';
const DB_CLAVE  = '';
const DB_CHARSET = 'utf8mb4';

/**
 * Devuelve una única instancia de PDO para toda la petición.
 * Guarda la conexión en una variable estática para no abrir
 * una conexión nueva cada vez que se llama a la función.
 */
function conexion(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NOMBRE . ';charset=' . DB_CHARSET;

    try {
        $pdo = new PDO($dsn, DB_USUARIO, DB_CLAVE, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        // No se expone la excepción original (contiene credenciales/traza):
        // solo un mensaje claro con lo que hay que revisar en el entorno local.
        http_response_code(500);
        die(
            'No se pudo conectar a la base de datos. Verifique que: ' .
            '1) el servicio de MySQL esté activo en XAMPP, ' .
            '2) la base de datos "integradora" haya sido importada ' .
            '(database/integradora.sql).'
        );
    }

    return $pdo;
}
