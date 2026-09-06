<?php
/**
 * Tabla Incidencia.
 */

declare(strict_types=1);

class Incidencia
{
    /** Estados válidos del tablero, en el orden en que se muestran. */
    public const ESTADOS = ['Nuevo', 'En proceso', 'Resuelto', 'Cerrado'];

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Inserta una incidencia y devuelve el id generado.
     *
     * @param array<string, mixed> $datos
     */
    public function crear(array $datos): int
    {
        // TODO
        return 0;
    }

    /** Devuelve todas las incidencias ordenadas por fecha de reporte. */
    public function obtenerTodas(): array
    {
        // TODO
        return [];
    }

    /** Devuelve las incidencias agrupadas por estado para pintar el tablero. */
    public function obtenerPorEstado(): array
    {
        // TODO
        return [];
    }

    /** Cambia el estado de una incidencia al moverla de columna. */
    public function cambiarEstado(int $id, string $estado): bool
    {
        // TODO UPDATE incidencias SET estado = :estado WHERE id = :id.
        return false;
    }

    /** Busca incidencias por título o por persona que reporta (opcional). */
    public function buscar(string $termino): array
    {
        // TODO
        return [];
    }

    /** Elimina una incidencia por id (opcional). */
    public function eliminar(int $id): bool
    {
        // TODO
        return false;
    }
}
