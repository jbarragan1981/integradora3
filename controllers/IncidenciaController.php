<?php
/**
 * Controlador de incidencias.
 *
 * Responsabilidad: recibir las acciones del usuario, validar en servidor,
 * pedir los datos al Modelo y decidir qué Vista se muestra.
 * No escribe HTML ni consultas SQL.
 *
 * Estado: esqueleto inicial. La lógica se implementa más adelante.
 */

declare(strict_types=1);

class IncidenciaController
{
    /** Ruta absoluta de la carpeta de vistas. */
    private string $vistas;

    public function __construct()
    {
        $this->vistas = RUTA_BASE . '/views';
        // TODO instanciar el modelo Incidencia con la conexión PDO.
    }

    /** Tablero de incidencias agrupadas por estado. */
    public function tablero(): void
    {
        // TODO: pedir al modelo las incidencias agrupadas por estado.
        $titulo = 'Tablero de incidencias';
        $this->render('incidencias/tablero', compact('titulo'));
    }

    /** Muestra el formulario de registro. */
    public function crear(): void
    {
        // TODO: enviar a la vista el catálogo de categorías.
        $titulo = 'Reportar incidencia';
        $this->render('incidencias/crear', compact('titulo'));
    }

    /** Procesa el POST del formulario e inserta mediante el modelo. */
    public function guardar(): void
    {
        // TODO: validar en servidor y llamar a Incidencia::crear().
        $this->redirigir('tablero');
    }

    /** Consulta de registros en tabla HTML. */
    public function listar(): void
    {
        // TODO: $incidencias = $this->modelo->obtenerTodas();
        $titulo = 'Incidencias registradas';
        $this->render('incidencias/listar', compact('titulo'));
    }

    /** Actualiza el estado de una incidencia al arrastrarla en el tablero. */
    public function mover(): void
    {
        // TODO: leer id y estado, llamar a Incidencia::cambiarEstado()
        // y responder en JSON para el fetch del tablero.
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'mensaje' => 'Función pendiente']);
    }

    /** Elimina una incidencia. */
    public function eliminar(): void
    {        
        //TODO
        $this->redirigir('listar');
    }

    /** Ruta inexistente. */
    public function noEncontrado(): void
    {
        $titulo = 'Página no encontrada';
        $this->render('404', compact('titulo'));
    }

    /**
     * Carga una vista dentro del layout general.
     *
     * @param string               $vista Ruta relativa dentro de /views (sin .php)
     * @param array<string, mixed> $datos Variables disponibles en la vista
     */
    private function render(string $vista, array $datos = []): void
    {
        extract($datos, EXTR_SKIP);
        require $this->vistas . '/layouts/header.php';
        require $this->vistas . '/' . $vista . '.php';
        require $this->vistas . '/layouts/footer.php';
    }

    /** Redirección interna a otra ruta de la aplicación. */
    private function redirigir(string $ruta): void
    {
        header('Location: ' . URL_BASE . '/index.php?ruta=' . $ruta);
        exit;
    }
}
