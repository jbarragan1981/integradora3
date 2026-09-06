/**
 * Validaciones del formulario de registro de incidencias.
 * Se ejecutan en el navegador antes de enviar los datos al controlador.
 *
 * Reglas:
 *  - Campos vacíos: título, categoría, prioridad, reportante, correo,
 *    código de área y descripción.
 *  - Longitud: título entre 5 y 120 caracteres, descripción mínimo 15.
 *  - Campo numérico: código de área, entero entre 1 y 999.
 *  - Valores permitidos: prioridad dentro del catálogo.
 *  - Correo electrónico con formato válido.
 *
 * La validación de servidor sigue siendo obligatoria; esto es solo apoyo
 * para el usuario.
 */

document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.querySelector('#form-incidencia');
    if (!formulario) return;

    const PRIORIDADES_VALIDAS = ['Baja', 'Media', 'Alta', 'Crítica'];
    const CORREO_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    /**
     * Reglas por campo. Cada función recibe el valor ya recortado y
     * devuelve un mensaje de error o una cadena vacía si es válido.
     */
    const reglas = {
        titulo(valor) {
            if (valor === '') return 'Ingresa el título de la incidencia.';
            if (valor.length < 5) return 'El título debe tener al menos 5 caracteres.';
            if (valor.length > 120) return 'El título no puede superar los 120 caracteres.';
            return '';
        },
        categoria_id(valor) {
            if (valor === '') return 'Selecciona una categoría.';
            return '';
        },
        prioridad(valor) {
            if (valor === '') return 'Selecciona una prioridad.';
            if (!PRIORIDADES_VALIDAS.includes(valor)) return 'La prioridad seleccionada no es válida.';
            return '';
        },
        reportante(valor) {
            if (valor === '') return 'Indica quién reporta la incidencia.';
            if (valor.length < 3) return 'El nombre debe tener al menos 3 caracteres.';
            if (valor.length > 100) return 'El nombre no puede superar los 100 caracteres.';
            return '';
        },
        correo(valor) {
            if (valor === '') return 'Ingresa un correo electrónico.';
            if (valor.length > 150) return 'El correo no puede superar los 150 caracteres.';
            if (!CORREO_REGEX.test(valor)) return 'El formato del correo electrónico no es válido.';
            return '';
        },
        area_codigo(valor) {
            if (valor === '') return 'Ingresa el código de área.';
            if (!/^\d+$/.test(valor)) return 'El código de área debe ser un número entero.';
            const numero = Number(valor);
            if (numero < 1 || numero > 999) return 'El código de área debe estar entre 1 y 999.';
            return '';
        },
        descripcion(valor) {
            if (valor === '') return 'Describe la incidencia.';
            if (valor.length < 15) return 'La descripción debe tener al menos 15 caracteres.';
            return '';
        },
    };

    /** Pinta o limpia el mensaje de error asociado a un campo. */
    function mostrarError(nombre, mensaje) {
        const campo = formulario.elements[nombre];
        const contenedorError = document.querySelector('#error-' + nombre);
        if (contenedorError) contenedorError.textContent = mensaje;
        if (campo) {
            campo.setAttribute('aria-invalid', mensaje ? 'true' : 'false');
        }
    }

    /** Valida un único campo y devuelve true si es válido. */
    function validarCampo(nombre) {
        const campo = formulario.elements[nombre];
        if (!campo) return true;
        const valor = (campo.value ?? '').trim();
        const mensaje = reglas[nombre](valor);
        mostrarError(nombre, mensaje);
        return mensaje === '';
    }

    // Revalidación en vivo: al salir del campo y al corregir su contenido.
    Object.keys(reglas).forEach((nombre) => {
        const campo = formulario.elements[nombre];
        if (!campo) return;
        campo.addEventListener('blur', () => validarCampo(nombre));
        campo.addEventListener('input', () => {
            const contenedorError = document.querySelector('#error-' + nombre);
            if (contenedorError && contenedorError.textContent !== '') {
                validarCampo(nombre);
            }
        });
    });

    formulario.addEventListener('submit', (evento) => {
        let primerInvalido = null;

        Object.keys(reglas).forEach((nombre) => {
            const valido = validarCampo(nombre);
            if (!valido && primerInvalido === null) {
                primerInvalido = formulario.elements[nombre];
            }
        });

        if (primerInvalido !== null) {
            evento.preventDefault();
            primerInvalido.focus();
        }
    });
});
