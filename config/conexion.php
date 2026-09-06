<?php
/**
 * Conexión a MySQL.
 *
 * Archivo independiente: ningún otro archivo del proyecto define credenciales.
 * Base de datos: integradora - usuario root sin contraseña (entorno local XAMPP).
 *
 * Estado: esqueleto inicial
 */

declare(strict_types=1);

const DB_HOST   = 'localhost';
const DB_NOMBRE = 'integradora';
const DB_USUARIO = 'root';
const DB_CLAVE  = '';
const DB_CHARSET = 'utf8mb4';

/**
 * Devuelve una única instancia de PDO para toda la petición.
 */
function conexion(): PDO
{
    // TODO crear el PDO con DSN, modo de errores en excepción
    // y fetch mode asociativo - manejar el fallo de conexión con un mensaje claro.
    throw new RuntimeException('Conexión pendiente de implementar.');
}
