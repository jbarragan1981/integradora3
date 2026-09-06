<?php
/**
 * Gestión de incidencias
 * Punto de entrada único de la aplicación (front controller).
 *
 * Todas las peticiones entran por aquí: index.php?ruta=nombre
 * El index NO contiene lógica de negocio ni consultas SQL.
 * Su única responsabilidad es identificar la ruta y delegar en el controlador.
 *
 * Flujo:  Navegador -> index.php -> Controlador -> Modelo -> MySQL
 *
 * Autor: Johanna Barragan - ECOTEC
 */

declare(strict_types=1);

// Durante el desarrollo conviene ver los errores en pantalla.
ini_set('display_errors', '1');
error_reporting(E_ALL);

define('RUTA_BASE', __DIR__);
define('URL_BASE', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

require_once RUTA_BASE . '/controllers/IncidenciaController.php';

$ruta = $_GET['ruta'] ?? 'tablero';

$controlador = new IncidenciaController();

switch ($ruta) {
    case 'tablero':          // Vista principal: tablero por estado
        $controlador->tablero();
        break;

    case 'crear':            // Formulario de registro
        $controlador->crear();
        break;

    case 'guardar':          // Recibe el POST del formulario
        $controlador->guardar();
        break;

    case 'listar':           // Tabla HTML con todas las incidencias
        $controlador->listar();
        break;

    case 'mover':            // Cambia el estado desde el tablero (fetch/AJAX)
        $controlador->mover();
        break;

    case 'eliminar':         // Funcionalidad opcional
        $controlador->eliminar();
        break;

    case 'diagnostico':      // TODO eliminar antes de la entrega final
        $controlador->diagnostico();
        break;

    default:
        http_response_code(404);
        $controlador->noEncontrado();
        break;
}
