<?php
/**
 * Vista: consulta de incidencias en tabla HTML.
 * Recibe $incidencias y $termino desde el controlador.
 */

$clasesPrioridad = [
    'Baja'    => 'baja',
    'Media'   => 'media',
    'Alta'    => 'alta',
    'Crítica' => 'critica',
];

$clasesEstado = [
    'Nuevo'      => 'nuevo',
    'En proceso' => 'proceso',
    'Resuelto'   => 'resuelto',
    'Cerrado'    => 'cerrado',
];
?>
<section class="encabezado-seccion">
    <h1>Incidencias registradas</h1>
    <p>Listado completo con búsqueda y eliminación.</p>
</section>

<?php if (!empty($aviso)): ?>
    <div class="aviso" role="status"><?= htmlspecialchars($aviso) ?></div>
<?php endif; ?>

<form class="buscador" action="<?= URL_BASE ?>/index.php" method="get" role="search">
    <input type="hidden" name="ruta" value="listar">
    <label class="buscador_etiqueta" for="q">Buscar</label>
    <input class="campo_control buscador_campo" type="search" id="q" name="q"
           placeholder="Título, persona que reporta o categoría"
           value="<?= htmlspecialchars($termino) ?>">
    <button class="boton" type="submit">Buscar</button>
    <?php if ($termino !== ''): ?>
        <a class="buscador_limpiar" href="<?= URL_BASE ?>/index.php?ruta=listar">Limpiar</a>
    <?php endif; ?>
</form>

<?php if ($incidencias === []): ?>
    <div class="aviso aviso--neutro">
        <?= $termino === ''
            ? 'Todavía no hay incidencias registradas.'
            : 'Ninguna incidencia coincide con «' . htmlspecialchars($termino) . '».' ?>
    </div>
<?php else: ?>
    <p class="tabla_resumen"><?= count($incidencias) ?> incidencia(s)</p>

    <div class="tabla-envoltura">
        <table class="tabla">
            <caption class="tabla_titulo">Incidencias ordenadas de la más reciente a la más antigua</caption>
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Título</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Prioridad</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Reporta</th>
                    <th scope="col">Fecha</th>
                    <th scope="col"><span class="visualmente-oculto">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incidencias as $incidencia): ?>
                    <tr>
                        <td class="tabla_id"><?= (int) $incidencia['id'] ?></td>
                        <td>
                            <span class="tabla_titulo-celda"><?= htmlspecialchars($incidencia['titulo']) ?></span>
                            <span class="tabla_correo"><?= htmlspecialchars($incidencia['correo']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($incidencia['categoria']) ?></td>
                        <td>
                            <span class="etiqueta etiqueta--<?= $clasesPrioridad[$incidencia['prioridad']] ?>">
                                <?= htmlspecialchars($incidencia['prioridad']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="estado estado--<?= $clasesEstado[$incidencia['estado']] ?>">
                                <?= htmlspecialchars($incidencia['estado']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($incidencia['reportante']) ?></td>
                        <td class="tabla_fecha"><?= htmlspecialchars($incidencia['fecha']) ?></td>
                        <td class="tabla_acciones">
                            <form action="<?= URL_BASE ?>/index.php?ruta=eliminar" method="post"
                                  onsubmit="return confirm('¿Eliminar la incidencia #<?= (int) $incidencia['id'] ?>?');">
                                <input type="hidden" name="id" value="<?= (int) $incidencia['id'] ?>">
                                <button class="boton boton--peligro" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
