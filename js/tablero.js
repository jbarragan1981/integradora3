/**
 * Tablero de incidencias: arrastrar tarjetas entre columnas de estado.
 * Al soltar una tarjeta envía el cambio al controlador con fetch.
 *
 * Estado: esqueleto inicial. Se implementa progresivamente.
 */

document.addEventListener('DOMContentLoaded', () => {
    const tablero = document.querySelector('#tablero');
    if (!tablero) return;

    // TODO eventos dragstart / dragover / drop sobre las columnas,
    // y POST a index.php?ruta=mover con el id de la incidencia y el nuevo estado.
});
