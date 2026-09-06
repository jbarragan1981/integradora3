<?php
/**
 * Vista: tablero de incidencias por estado.
 * Solo presenta datos; no consulta la base de datos.
 */

$clasesEstado = [
    'Nuevo'      => 'nuevo',
    'En proceso' => 'proceso',
    'Resuelto'   => 'resuelto',
    'Cerrado'    => 'cerrado',
];

$clasesPrioridad = [
    'Baja'    => 'baja',
    'Media'   => 'media',
    'Alta'    => 'alta',
    'Crítica' => 'critica',
];

$totalIncidencias = array_sum(array_map('count', $incidenciasPorEstado));
?>
<section class="encabezado-seccion">
    <h1>Tablero de incidencias</h1>
    <p>Arrastra una tarjeta hacia otra columna para actualizar su estado.</p>
</section>

<?php if (!empty($aviso)): ?>
    <div class="aviso" role="status"><?= htmlspecialchars($aviso) ?></div>
<?php endif; ?>

<p class="aviso-tablero" id="aviso-tablero" role="status" aria-live="polite"></p>

<?php if ($totalIncidencias === 0): ?>
    <div class="aviso aviso--neutro">
        Todavía no hay incidencias registradas.
        <a href="<?= URL_BASE ?>/index.php?ruta=crear">Reporta la primera</a>.
    </div>
<?php endif; ?>

<div class="tablero" id="tablero">
    <?php foreach (Incidencia::ESTADOS as $estado): ?>
        <?php $tarjetas = $incidenciasPorEstado[$estado] ?? []; ?>
        <section class="columna columna--<?= $clasesEstado[$estado] ?>">
            <header class="columna_cabecera">
                <h2 class="columna_titulo"><?= htmlspecialchars($estado) ?></h2>
                <span class="columna_contador"><?= count($tarjetas) ?></span>
            </header>

            <div class="columna_lista" data-estado="<?= htmlspecialchars($estado) ?>">
                <p class="columna_vacio"<?= empty($tarjetas) ? '' : ' hidden' ?>>Sin incidencias</p>

                <?php foreach ($tarjetas as $tarjeta): ?>
                    <article class="tarjeta"
                             draggable="true"
                             tabindex="0"
                             data-id="<?= (int) $tarjeta['id'] ?>"
                             data-estado="<?= htmlspecialchars($estado) ?>">
                        <h3 class="tarjeta_titulo"><?= htmlspecialchars($tarjeta['titulo']) ?></h3>

                        <div class="tarjeta_meta">
                            <span class="etiqueta etiqueta--<?= $clasesPrioridad[$tarjeta['prioridad']] ?>">
                                <?= htmlspecialchars($tarjeta['prioridad']) ?>
                            </span>
                            <span class="tarjeta_categoria"><?= htmlspecialchars($tarjeta['categoria']) ?></span>
                        </div>

                        <div class="tarjeta_pie">
                            <span class="tarjeta_reportante"><?= htmlspecialchars($tarjeta['reportante']) ?></span>
                            <span class="tarjeta_fecha"><?= htmlspecialchars($tarjeta['fecha']) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>
