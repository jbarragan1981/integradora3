/**
 * Validaciones del formulario de registro.
 * Se ejecutan antes de enviar los datos al controlador.
 *
 * Estado: esqueleto inicial. Se implementa mas adelante *
 * Reglas previstas:
 *  - Campos vacíos: título, categoría, prioridad, reportante, correo y descripción.
 *  - Longitud: título entre 5 y 120 caracteres, descripción mínimo 15.
 *  - Campo numérico: código de área entre 1 y 999.
 *  - Valores permitidos: prioridad dentro del catálogo.
 *  - Correo electrónico con formato válido.
 */

document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.querySelector('#form-incidencia');
    if (!formulario) return;

    // TODO: escuchar el submit, validar cada campo y mostrar los
    // mensajes de error junto al campo correspondiente.
});
