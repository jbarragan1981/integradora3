<?php
/**
 * Controlador de incidencias.
 *
 * Responsabilidad: recibir las acciones del usuario, validar en servidor,
 * pedir los datos al Modelo y decidir qué Vista se muestra.
 * No escribe HTML ni consultas SQL.
 */

declare(strict_types=1);

class IncidenciaController
{
    /** Mensajes de confirmación que llegan por la URL después de un redirect. */
    private const AVISOS = [
        'creada'     => 'La incidencia se registró correctamente.',
        'eliminada'  => 'La incidencia fue eliminada.',
        'noEliminada' => 'No se encontró la incidencia que intentabas eliminar.',
    ];

    /** Ruta absoluta de la carpeta de vistas. */
    private string $vistas;

    private Incidencia $modelo;

    public function __construct()
    {
        $this->vistas = RUTA_BASE . '/views';

        require_once RUTA_BASE . '/config/conexion.php';
        require_once RUTA_BASE . '/models/Incidencia.php';

        $this->modelo = new Incidencia(conexion());
    }

    /** Tablero de incidencias agrupadas por estado. */
    public function tablero(): void
    {
        $incidenciasPorEstado = $this->modelo->obtenerPorEstado();

        $titulo = 'Tablero de incidencias';
        $aviso  = $this->aviso();
        $this->render('incidencias/tablero', compact('titulo', 'incidenciasPorEstado', 'aviso'));
    }

    /**
     * Muestra el formulario de registro.
     *
     * @param array<string, string> $errores      Mensajes por campo tras un intento fallido
     * @param array<string, mixed>  $datosPrevios Valores escritos por el usuario
     */
    public function crear(array $errores = [], array $datosPrevios = []): void
    {
        $categorias  = $this->modelo->categorias();
        $prioridades = Incidencia::PRIORIDADES;

        $titulo = 'Reportar incidencia';
        $this->render(
            'incidencias/crear',
            compact('titulo', 'categorias', 'prioridades', 'errores', 'datosPrevios')
        );
    }

    /** Procesa el POST del formulario e inserta mediante el modelo. */
    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('crear');
        }

        $datos = [
            'titulo'       => trim((string) ($_POST['titulo'] ?? '')),
            'categoria_id' => trim((string) ($_POST['categoria_id'] ?? '')),
            'prioridad'    => trim((string) ($_POST['prioridad'] ?? '')),
            'reportante'   => trim((string) ($_POST['reportante'] ?? '')),
            'correo'       => trim((string) ($_POST['correo'] ?? '')),
            'area_codigo'  => trim((string) ($_POST['area_codigo'] ?? '')),
            'descripcion'  => trim((string) ($_POST['descripcion'] ?? '')),
        ];

        $errores = $this->validar($datos);

        // Con errores se vuelve a mostrar el formulario conservando lo escrito.
        if ($errores !== []) {
            $this->crear($errores, $datos);
            return;
        }

        $datos['estado'] = Incidencia::ESTADOS[0];
        $this->modelo->crear($datos);

        $this->redirigir('tablero', ['mensaje' => 'creada']);
    }

    /** Consulta de registros en tabla HTML, con búsqueda opcional. */
    public function listar(): void
    {
        $termino = trim((string) ($_GET['q'] ?? ''));

        $incidencias = $termino === ''
            ? $this->modelo->obtenerTodas()
            : $this->modelo->buscar($termino);

        $titulo = 'Incidencias registradas';
        $aviso  = $this->aviso();
        $this->render('incidencias/listar', compact('titulo', 'incidencias', 'termino', 'aviso'));
    }

    /** Actualiza el estado de una incidencia al arrastrarla en el tablero. */
    public function mover(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
            return;
        }

        $id     = (int) ($_POST['id'] ?? 0);
        $estado = trim((string) ($_POST['estado'] ?? ''));

        if ($id <= 0 || !in_array($estado, Incidencia::ESTADOS, true)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos o estado no válido']);
            return;
        }

        $actualizada = $this->modelo->cambiarEstado($id, $estado);

        echo json_encode([
            'ok'      => $actualizada,
            'mensaje' => $actualizada
                ? 'Estado actualizado'
                : 'La incidencia ya estaba en esa columna o no existe',
        ]);
    }

    /** Elimina una incidencia. */
    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('listar');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $eliminada = $id > 0 && $this->modelo->eliminar($id);

        $this->redirigir('listar', ['mensaje' => $eliminada ? 'eliminada' : 'noEliminada']);
    }

    /** Ruta inexistente. */
    public function noEncontrado(): void
    {
        $titulo = 'Página no encontrada';
        $this->render('404', compact('titulo'));
    }

    /**
     * Reglas de validación del formulario de registro.
     * Refleja las mismas reglas de js/validaciones.js, porque el navegador
     * puede saltarse la validación del cliente.
     *
     * @param  array<string, string> $datos
     * @return array<string, string> Mensajes indexados por campo
     */
    private function validar(array $datos): array
    {
        $errores = [];

        $longitudTitulo = mb_strlen($datos['titulo']);
        if ($datos['titulo'] === '') {
            $errores['titulo'] = 'Ingresa el título de la incidencia.';
        } elseif ($longitudTitulo < 5) {
            $errores['titulo'] = 'El título debe tener al menos 5 caracteres.';
        } elseif ($longitudTitulo > 120) {
            $errores['titulo'] = 'El título no puede superar los 120 caracteres.';
        }

        $categorias = $this->modelo->categorias();
        if ($datos['categoria_id'] === '') {
            $errores['categoria_id'] = 'Selecciona una categoría.';
        } elseif (!isset($categorias[(int) $datos['categoria_id']])) {
            $errores['categoria_id'] = 'La categoría seleccionada no existe.';
        }

        if (!in_array($datos['prioridad'], Incidencia::PRIORIDADES, true)) {
            $errores['prioridad'] = 'La prioridad seleccionada no es válida.';
        }

        $longitudReportante = mb_strlen($datos['reportante']);
        if ($datos['reportante'] === '') {
            $errores['reportante'] = 'Indica quién reporta la incidencia.';
        } elseif ($longitudReportante < 3) {
            $errores['reportante'] = 'El nombre debe tener al menos 3 caracteres.';
        } elseif ($longitudReportante > 100) {
            $errores['reportante'] = 'El nombre no puede superar los 100 caracteres.';
        }

        if ($datos['correo'] === '') {
            $errores['correo'] = 'Ingresa un correo electrónico.';
        } elseif (mb_strlen($datos['correo']) > 150) {
            $errores['correo'] = 'El correo no puede superar los 150 caracteres.';
        } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = 'El formato del correo electrónico no es válido.';
        }

        if ($datos['area_codigo'] === '') {
            $errores['area_codigo'] = 'Ingresa el código de área.';
        } elseif (!ctype_digit($datos['area_codigo'])) {
            $errores['area_codigo'] = 'El código de área debe ser un número entero.';
        } elseif ((int) $datos['area_codigo'] < 1 || (int) $datos['area_codigo'] > 999) {
            $errores['area_codigo'] = 'El código de área debe estar entre 1 y 999.';
        }

        if ($datos['descripcion'] === '') {
            $errores['descripcion'] = 'Describe la incidencia.';
        } elseif (mb_strlen($datos['descripcion']) < 15) {
            $errores['descripcion'] = 'La descripción debe tener al menos 15 caracteres.';
        }

        return $errores;
    }

    /** Traduce el parámetro ?mensaje= de la URL a un texto para la vista. */
    private function aviso(): ?string
    {
        $clave = (string) ($_GET['mensaje'] ?? '');

        return self::AVISOS[$clave] ?? null;
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

    /**
     * Redirección interna a otra ruta de la aplicación.
     *
     * @param array<string, string> $parametros Pares adicionales para la URL
     */
    private function redirigir(string $ruta, array $parametros = []): void
    {
        $consulta = http_build_query(array_merge(['ruta' => $ruta], $parametros));
        header('Location: ' . URL_BASE . '/index.php?' . $consulta);
        exit;
    }
}
