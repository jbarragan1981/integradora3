<?php
/**
 * Cabecera común de todas las vistas.
 * Recibe la variable $titulo desde el controlador.
 */
$rutaActual = $_GET['ruta'] ?? 'tablero';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'EasyTickets') ?> · EasyTickets</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= URL_BASE ?>/css/estilos.css">
</head>
<body data-url-base="<?= URL_BASE ?>">

<header class="barra">
    <a class="marca" href="<?= URL_BASE ?>/index.php?ruta=tablero">
        <span class="marca_pulso" aria-hidden="true"></span>
        EasyTickets
        <span class="marca_nota">incidencias</span>
    </a>

    <nav class="menu" aria-label="Secciones">
        <a href="<?= URL_BASE ?>/index.php?ruta=tablero"
           class="menu_item <?= $rutaActual === 'tablero' ? 'menu_item--activo' : '' ?>">Tablero</a>
        <a href="<?= URL_BASE ?>/index.php?ruta=listar"
           class="menu_item <?= $rutaActual === 'listar' ? 'menu_item--activo' : '' ?>">Registros</a>
        <a href="<?= URL_BASE ?>/index.php?ruta=crear" class="boton boton--claro">Reportar incidencia</a>
    </nav>
</header>

<main class="contenido">
