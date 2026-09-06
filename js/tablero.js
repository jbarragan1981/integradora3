/**
 * Tablero de incidencias: arrastrar tarjetas entre columnas de estado.
 * Al soltar una tarjeta envía el cambio al controlador con fetch y,
 * si el servidor responde con error, la devuelve a su columna original.
 */

document.addEventListener('DOMContentLoaded', () => {
    const tablero = document.querySelector('#tablero');
    if (!tablero) return;

    const RUTA_MOVER = document.body.dataset.urlBase + '/index.php?ruta=mover';
    const avisador = document.querySelector('#aviso-tablero');

    let tarjetaArrastrada = null;

    /** Mensaje temporal debajo del encabezado del tablero. */
    function avisar(texto, esError) {
        if (!avisador) return;
        avisador.textContent = texto;
        avisador.classList.toggle('aviso-tablero--error', Boolean(esError));
        window.clearTimeout(avisar.temporizador);
        avisar.temporizador = window.setTimeout(() => {
            avisador.textContent = '';
            avisador.classList.remove('aviso-tablero--error');
        }, 4000);
    }

    /** Recalcula el contador y el texto "Sin incidencias" de cada columna. */
    function actualizarColumnas() {
        tablero.querySelectorAll('.columna').forEach((columna) => {
            const lista = columna.querySelector('.columna_lista');
            const total = lista.querySelectorAll('.tarjeta').length;

            const contador = columna.querySelector('.columna_contador');
            if (contador) contador.textContent = String(total);

            const vacio = lista.querySelector('.columna_vacio');
            if (vacio) vacio.hidden = total > 0;
        });
    }

    /** Envía el nuevo estado al controlador. */
    async function guardarEstado(id, estado) {
        const cuerpo = new URLSearchParams({ id: id, estado: estado });

        const respuesta = await fetch(RUTA_MOVER, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: cuerpo,
        });

        return respuesta.json();
    }

    // ------------------------------------------------------------- Tarjetas

    tablero.addEventListener('dragstart', (evento) => {
        const tarjeta = evento.target.closest('.tarjeta');
        if (!tarjeta) return;

        tarjetaArrastrada = tarjeta;
        tarjeta.classList.add('tarjeta--arrastrando');
        evento.dataTransfer.effectAllowed = 'move';
        evento.dataTransfer.setData('text/plain', tarjeta.dataset.id);
    });

    tablero.addEventListener('dragend', () => {
        if (tarjetaArrastrada) tarjetaArrastrada.classList.remove('tarjeta--arrastrando');
        tarjetaArrastrada = null;
        tablero.querySelectorAll('.columna_lista--activa')
            .forEach((lista) => lista.classList.remove('columna_lista--activa'));
    });

    // ------------------------------------------------------------- Columnas

    tablero.querySelectorAll('.columna_lista').forEach((lista) => {
        lista.addEventListener('dragover', (evento) => {
            if (!tarjetaArrastrada) return;
            evento.preventDefault();
            evento.dataTransfer.dropEffect = 'move';
            lista.classList.add('columna_lista--activa');
        });

        lista.addEventListener('dragleave', (evento) => {
            if (!lista.contains(evento.relatedTarget)) {
                lista.classList.remove('columna_lista--activa');
            }
        });

        lista.addEventListener('drop', async (evento) => {
            evento.preventDefault();
            lista.classList.remove('columna_lista--activa');

            const tarjeta = tarjetaArrastrada;
            if (!tarjeta) return;

            const estadoNuevo = lista.dataset.estado;
            const estadoPrevio = tarjeta.dataset.estado;
            if (estadoNuevo === estadoPrevio) return;

            // Se mueve primero en pantalla para que la respuesta sea inmediata.
            const listaPrevia = tarjeta.parentElement;
            lista.appendChild(tarjeta);
            tarjeta.dataset.estado = estadoNuevo;
            actualizarColumnas();

            try {
                const resultado = await guardarEstado(tarjeta.dataset.id, estadoNuevo);

                if (resultado.ok) {
                    avisar('«' + tarjeta.querySelector('.tarjeta_titulo').textContent.trim()
                        + '» pasó a ' + estadoNuevo + '.', false);
                    return;
                }

                throw new Error(resultado.mensaje || 'No se pudo guardar el cambio.');
            } catch (error) {
                // Si el servidor rechaza el cambio, la tarjeta regresa a su columna.
                listaPrevia.appendChild(tarjeta);
                tarjeta.dataset.estado = estadoPrevio;
                actualizarColumnas();
                avisar('No se pudo actualizar el estado: ' + error.message, true);
            }
        });
    });

    actualizarColumnas();
});
