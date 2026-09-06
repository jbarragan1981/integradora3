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

require_once RUTA_BASE . '/config/conexion.php';

class IncidenciaController
{
    /** Ruta absoluta de la carpeta de vistas. */
    private string $vistas;

    /** Conexión PDO compartida para toda la petición. */
    private PDO $db;

    public function __construct()
    {
        $this->vistas = RUTA_BASE . '/views';
        $this->db = conexion();
        require_once RUTA_BASE . '/models/Incidencia.php';
        // TODO instanciar el modelo Incidencia con la conexión PDO.
    }

    /** Tablero de incidencias agrupadas por estado. */
    public function tablero(): void
    {
        // TODO: obtener este arreglo desde el modelo, agrupado por estado.
        $incidenciasPorEstado = [
            'Nuevo' => [
                [
                    'id'         => 1,
                    'titulo'     => 'La impresora del segundo piso no responde',
                    'categoria'  => 'Hardware',
                    'prioridad'  => 'Media',
                    'reportante' => 'Carlos Mendoza',
                    'fecha'      => '2026-09-01',
                ],
                [
                    'id'         => 2,
                    'titulo'     => 'Solicitud de acceso a carpeta compartida de Contabilidad',
                    'categoria'  => 'Accesos',
                    'prioridad'  => 'Baja',
                    'reportante' => 'Estefanía Rojas',
                    'fecha'      => '2026-09-02',
                ],
            ],
            'En proceso' => [
                [
                    'id'         => 3,
                    'titulo'     => 'Caída intermitente de la red en bodega',
                    'categoria'  => 'Red',
                    'prioridad'  => 'Alta',
                    'reportante' => 'Luis Andrade',
                    'fecha'      => '2026-08-30',
                ],
                [
                    'id'         => 4,
                    'titulo'     => 'Error al generar reportes en el sistema de ventas',
                    'categoria'  => 'Software',
                    'prioridad'  => 'Crítica',
                    'reportante' => 'Marcela Vera',
                    'fecha'      => '2026-08-29',
                ],
            ],
            'Resuelto' => [
                [
                    'id'         => 5,
                    'titulo'     => 'Reinicio del servidor de correo',
                    'categoria'  => 'Infraestructura',
                    'prioridad'  => 'Alta',
                    'reportante' => 'Diego Salas',
                    'fecha'      => '2026-08-27',
                ],
            ],
            'Cerrado' => [
                [
                    'id'         => 6,
                    'titulo'     => 'Instalación de antivirus en equipos nuevos',
                    'categoria'  => 'Software',
                    'prioridad'  => 'Baja',
                    'reportante' => 'Paola Iturralde',
                    'fecha'      => '2026-08-20',
                ],
                [
                    'id'         => 7,
                    'titulo'     => 'Cambio de contraseña del router principal',
                    'categoria'  => 'Red',
                    'prioridad'  => 'Media',
                    'reportante' => 'Jorge Ponce',
                    'fecha'      => '2026-08-18',
                ],
            ],
        ];

        $titulo = 'Tablero de incidencias';
        $this->render('incidencias/tablero', compact('titulo', 'incidenciasPorEstado'));
    }

    /** Muestra el formulario de registro. */
    public function crear(): void
    {
        // TODO: obtener el catálogo de categorías desde el modelo.
        $categorias = [
            1 => 'Hardware',
            2 => 'Software',
            3 => 'Red',
            4 => 'Accesos',
            5 => 'Infraestructura',
        ];

        $prioridades = ['Baja', 'Media', 'Alta', 'Crítica'];

        $titulo = 'Reportar incidencia';
        $this->render('incidencias/crear', compact('titulo', 'categorias', 'prioridades'));
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
     * Comprobación visible y temporal de la conexión a MySQL.
     *
     * TODO: eliminar esta ruta y este método antes de la entrega final,
     * es solo para verificar la conexión mientras se desarrollan los
     * commits siguientes.
     */
    public function diagnostico(): void
    {
        $version = $this->db->query('SELECT VERSION() AS version')->fetch()['version'];
        $totalCategorias = (int) $this->db->query('SELECT COUNT(*) AS total FROM categorias')->fetch()['total'];
        $totalIncidencias = (int) $this->db->query('SELECT COUNT(*) AS total FROM incidencias')->fetch()['total'];

        header('Content-Type: text/plain; charset=utf-8');
        echo "Conexión a MySQL: OK\n";
        echo "Versión del servidor: {$version}\n";
        echo "Registros en categorias: {$totalCategorias}\n";
        echo "Registros en incidencias: {$totalIncidencias}\n";
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
