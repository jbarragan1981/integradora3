<?php
/**
 * Vista: formulario de registro de incidencias.
 */
$datosPrevios = $datosPrevios ?? [];
?>
<section class="encabezado-seccion">
    <h1>Reportar incidencia</h1>
    <p>Toda incidencia nueva ingresa al tablero en el estado <strong>Nuevo</strong>.</p>
</section>

<form class="formulario" id="form-incidencia" action="<?= URL_BASE ?>/index.php?ruta=guardar" method="post" novalidate>

    <div class="campo">
        <label class="campo_etiqueta" for="titulo">
            Título de la incidencia <span class="campo_obligatorio" aria-hidden="true">*</span>
        </label>
        <input class="campo_control" type="text" id="titulo" name="titulo" maxlength="120"
               value="<?= htmlspecialchars($datosPrevios['titulo'] ?? '') ?>" required>
        <span class="campo_error" id="error-titulo"></span>
    </div>

    <div class="campo">
        <label class="campo_etiqueta" for="categoria_id">
            Categoría <span class="campo_obligatorio" aria-hidden="true">*</span>
        </label>
        <select class="campo_control" id="categoria_id" name="categoria_id" required>
            <option value="">Selecciona una categoría</option>
            <?php foreach ($categorias as $id => $nombre): ?>
                <option value="<?= (int) $id ?>"
                    <?= (int) ($datosPrevios['categoria_id'] ?? 0) === $id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($nombre) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="campo_error" id="error-categoria_id"></span>
    </div>

    <div class="campo campo--corto">
        <label class="campo_etiqueta" for="prioridad">
            Prioridad <span class="campo_obligatorio" aria-hidden="true">*</span>
        </label>
        <select class="campo_control" id="prioridad" name="prioridad" required>
            <?php foreach ($prioridades as $prioridad): ?>
                <option value="<?= htmlspecialchars($prioridad) ?>"
                    <?= ($datosPrevios['prioridad'] ?? 'Media') === $prioridad ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prioridad) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="campo_error" id="error-prioridad"></span>
    </div>

    <div class="campo">
        <label class="campo_etiqueta" for="reportante">
            Persona que reporta <span class="campo_obligatorio" aria-hidden="true">*</span>
        </label>
        <input class="campo_control" type="text" id="reportante" name="reportante" maxlength="100"
               value="<?= htmlspecialchars($datosPrevios['reportante'] ?? '') ?>" required>
        <span class="campo_error" id="error-reportante"></span>
    </div>

    <div class="campo">
        <label class="campo_etiqueta" for="correo">
            Correo electrónico <span class="campo_obligatorio" aria-hidden="true">*</span>
        </label>
        <input class="campo_control" type="email" id="correo" name="correo" maxlength="150"
               value="<?= htmlspecialchars($datosPrevios['correo'] ?? '') ?>" required>
        <span class="campo_error" id="error-correo"></span>
    </div>

    <div class="campo campo--corto">
        <label class="campo_etiqueta" for="area_codigo">
            Código de área <span class="campo_obligatorio" aria-hidden="true">*</span>
        </label>
        <input class="campo_control" type="number" id="area_codigo" name="area_codigo" min="1" max="999"
               value="<?= htmlspecialchars((string) ($datosPrevios['area_codigo'] ?? '')) ?>" required>
        <span class="campo_error" id="error-area_codigo"></span>
    </div>

    <div class="campo">
        <label class="campo_etiqueta" for="descripcion">
            Descripción <span class="campo_obligatorio" aria-hidden="true">*</span>
        </label>
        <textarea class="campo_control" id="descripcion" name="descripcion" rows="5"
                  required><?= htmlspecialchars($datosPrevios['descripcion'] ?? '') ?></textarea>
        <span class="campo_error" id="error-descripcion"></span>
    </div>

    <div class="formulario_acciones">
        <a class="boton boton--claro" href="<?= URL_BASE ?>/index.php?ruta=tablero">Volver al tablero</a>
        <button class="boton" type="submit">Guardar incidencia</button>
    </div>
</form>
