<?php
require __DIR__ . '/../src/sesion.php';
require __DIR__ . '/../src/conexion.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!comprobar_csrf()) {
        $error = 'La sesión ha caducado. Vuelve a intentarlo.';
    } else {
        $consulta = conexion()->prepare('SELECT id, nombre, password, rol FROM usuarios WHERE email = ?');
        $consulta->execute([$email]);
        $usuario = $consulta->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            iniciar_sesion($usuario);
            redirigir('index.php');
        }
        // Mismo mensaje en ambos casos para no revelar qué correos existen.
        $error = 'Correo o contraseña incorrectos.';
    }
}

$titulo = 'Iniciar sesión';
require __DIR__ . '/../src/vistas/cabecera.php';
?>
<section class="contenedor formulario-caja">
    <h1>Iniciar sesión</h1>

    <?php if ($error): ?>
        <div class="aviso aviso--error" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="formulario" id="form-login" novalidate>
        <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required value="<?= e($email) ?>">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="boton boton--ancho">Entrar</button>
    </form>

    <p class="formulario-caja__pie">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
</section>
<?php require __DIR__ . '/../src/vistas/pie.php'; ?>
