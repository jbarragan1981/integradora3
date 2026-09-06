<?php
/**
 * Tabla Incidencia.
 *
 * Aquí vive todo el SQL de la aplicación. El modelo no imprime HTML ni
 * conoce las rutas: recibe datos ya validados y devuelve arreglos.
 */

declare(strict_types=1);

class Incidencia
{
    /** Estados válidos del tablero, en el orden en que se muestran. */
    public const ESTADOS = ['Nuevo', 'En proceso', 'Resuelto', 'Cerrado'];

    /** Prioridades válidas, de menor a mayor urgencia. */
    public const PRIORIDADES = ['Baja', 'Media', 'Alta', 'Crítica'];

    /**
     * Columnas comunes a todas las consultas de lectura.
     * El JOIN trae el nombre de la categoría para no consultarla aparte.
     */
    private const SELECCION = 'SELECT i.id,
                                      i.titulo,
                                      i.descripcion,
                                      i.categoria_id,
                                      c.nombre AS categoria,
                                      i.prioridad,
                                      i.estado,
                                      i.reportante,
                                      i.correo,
                                      i.area_codigo,
                                      DATE(i.fecha_reporte) AS fecha,
                                      i.fecha_reporte
                                 FROM incidencias i
                                 INNER JOIN categorias c ON c.id = i.categoria_id';

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Catálogo de categorías como id => nombre, listo para el <select>.
     *
     * @return array<int, string>
     */
    public function categorias(): array
    {
        $sentencia = $this->db->query('SELECT id, nombre FROM categorias ORDER BY nombre');

        $catalogo = [];
        foreach ($sentencia as $fila) {
            $catalogo[(int) $fila['id']] = $fila['nombre'];
        }

        return $catalogo;
    }

    /**
     * Inserta una incidencia y devuelve el id generado.
     *
     * @param array<string, mixed> $datos
     */
    public function crear(array $datos): int
    {
        $sql = 'INSERT INTO incidencias
                    (titulo, descripcion, categoria_id, prioridad, estado,
                     reportante, correo, area_codigo)
                VALUES
                    (:titulo, :descripcion, :categoria_id, :prioridad, :estado,
                     :reportante, :correo, :area_codigo)';

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([
            ':titulo'       => $datos['titulo'],
            ':descripcion'  => $datos['descripcion'],
            ':categoria_id' => (int) $datos['categoria_id'],
            ':prioridad'    => $datos['prioridad'],
            ':estado'       => $datos['estado'] ?? self::ESTADOS[0],
            ':reportante'   => $datos['reportante'],
            ':correo'       => $datos['correo'],
            ':area_codigo'  => (int) $datos['area_codigo'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** Devuelve todas las incidencias ordenadas por fecha de reporte. */
    public function obtenerTodas(): array
    {
        $sentencia = $this->db->query(self::SELECCION . ' ORDER BY i.fecha_reporte DESC, i.id DESC');

        return $sentencia->fetchAll();
    }

    /**
     * Devuelve las incidencias agrupadas por estado para pintar el tablero.
     * Todas las columnas existen aunque no tengan tarjetas.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function obtenerPorEstado(): array
    {
        $agrupadas = array_fill_keys(self::ESTADOS, []);

        $sql = self::SELECCION . ' ORDER BY FIELD(i.prioridad, ' . $this->listaPrioridades() . ') DESC,
                                            i.fecha_reporte DESC';

        foreach ($this->db->query($sql) as $incidencia) {
            $estado = $incidencia['estado'];
            if (isset($agrupadas[$estado])) {
                $agrupadas[$estado][] = $incidencia;
            }
        }

        return $agrupadas;
    }

    /** Cambia el estado de una incidencia al moverla de columna. */
    public function cambiarEstado(int $id, string $estado): bool
    {
        if (!in_array($estado, self::ESTADOS, true)) {
            return false;
        }

        $sentencia = $this->db->prepare('UPDATE incidencias SET estado = :estado WHERE id = :id');
        $sentencia->execute([':estado' => $estado, ':id' => $id]);

        return $sentencia->rowCount() > 0;
    }

    /** Busca incidencias por título, persona que reporta o categoría. */
    public function buscar(string $termino): array
    {
        $patron = '%' . $termino . '%';

        $sql = self::SELECCION . ' WHERE i.titulo LIKE :titulo
                                      OR i.reportante LIKE :reportante
                                      OR c.nombre LIKE :categoria
                                   ORDER BY i.fecha_reporte DESC, i.id DESC';

        $sentencia = $this->db->prepare($sql);
        $sentencia->execute([
            ':titulo'     => $patron,
            ':reportante' => $patron,
            ':categoria'  => $patron,
        ]);

        return $sentencia->fetchAll();
    }

    /** Elimina una incidencia por id. */
    public function eliminar(int $id): bool
    {
        $sentencia = $this->db->prepare('DELETE FROM incidencias WHERE id = :id');
        $sentencia->execute([':id' => $id]);

        return $sentencia->rowCount() > 0;
    }

    /** Lista de prioridades entrecomillada para la función FIELD() de MySQL. */
    private function listaPrioridades(): string
    {
        return "'" . implode("', '", self::PRIORIDADES) . "'";
    }
}
