<?php
/** Cabecera común. Antes de incluirla, definir $titulo. */
$usuario = usuario_actual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Beaver') ?> · Beaver</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<header class="cabecera">
    <div class="contenedor cabecera__fila">
        <a class="logo" href="index.php"><span class="logo__icono">🦫</span> Beaver</a>
        <nav class="menu">
            <?php if ($usuario): ?>
                <span class="menu__saludo">Hola, <?= e($usuario['nombre']) ?></span>
                <a href="logout.php" class="boton boton--borde">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar sesión</a>
                <a href="registro.php" class="boton">Registrarse</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main>
