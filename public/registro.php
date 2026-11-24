<?php
require __DIR__ . '/../src/sesion.php';
require __DIR__ . '/../src/conexion.php';

$errores = [];
$datos = ['nombre' => '', 'email' => '', 'telefono' => '', 'rol' => 'cliente'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos['nombre']   = trim($_POST['nombre'] ?? '');
    $datos['email']    = trim($_POST['email'] ?? '');
    $datos['telefono'] = trim($_POST['telefono'] ?? '');
    $datos['rol']      = $_POST['rol'] ?? '';
    $password          = $_POST['password'] ?? '';

    // Validación en el servidor (RNF03): no se confía solo en la del navegador.
    if (!comprobar_csrf()) {
        $errores[] = 'La sesión ha caducado. Vuelve a enviar el formulario.';
    }
    if ($datos['nombre'] === '' || mb_strlen($datos['nombre']) > 100) {
        $errores[] = 'Escribe tu nombre (máximo 100 caracteres).';
    }
    if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido.';
    }
    if ($datos['telefono'] !== '' && !preg_match('/^\+?[0-9 ]{9,20}$/', $datos['telefono'])) {
        $errores[] = 'El teléfono no es válido.';
    }
    if (!in_array($datos['rol'], ['cliente', 'profesional'], true)) {
        $errores[] = 'Elige si eres cliente o profesional.';
    }
    if (mb_strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }

    if (!$errores) {
        $pdo = conexion();
        $existe = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $existe->execute([$datos['email']]);

        if ($existe->fetch()) {
            // Flujo alternativo 4a del CU01.
            $errores[] = 'Ya existe una cuenta con ese correo.';
        } else {
            $alta = $pdo->prepare(
                'INSERT INTO usuarios (nombre, email, password, telefono, rol, fecha_de_registro)
                 VALUES (?, ?, ?, ?, ?, NOW())'
            );
            $alta->execute([
                $datos['nombre'],
                $datos['email'],
                password_hash($password, PASSWORD_DEFAULT), // RNF01: contraseña cifrada
                $datos['telefono'] ?: null,
                $datos['rol'],
            ]);

            iniciar_sesion([
                'id'     => $pdo->lastInsertId(),
                'nombre' => $datos['nombre'],
                'rol'    => $datos['rol'],
            ]);
            redirigir('index.php');
        }
    }
}

$titulo = 'Registro';
require __DIR__ . '/../src/vistas/cabecera.php';
?>
<section class="contenedor formulario-caja">
    <h1>Crear cuenta</h1>

    <?php if ($errores): ?>
        <div class="aviso aviso--error" role="alert">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="formulario" id="form-registro" novalidate>
        <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">

        <fieldset class="tipo-usuario">
            <legend>Soy…</legend>
            <label><input type="radio" name="rol" value="cliente" <?= $datos['rol'] === 'cliente' ? 'checked' : '' ?>> Cliente</label>
            <label><input type="radio" name="rol" value="profesional" <?= $datos['rol'] === 'profesional' ? 'checked' : '' ?>> Profesional</label>
        </fieldset>

        <label for="nombre">Nombre y apellidos</label>
        <input type="text" id="nombre" name="nombre" maxlength="100" required value="<?= e($datos['nombre']) ?>">

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" maxlength="100" required value="<?= e($datos['email']) ?>">

        <label for="telefono">Teléfono <span class="opcional">(opcional)</span></label>
        <input type="tel" id="telefono" name="telefono" maxlength="20" value="<?= e($datos['telefono']) ?>">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" minlength="8" required>
        <small class="ayuda">Mínimo 8 caracteres.</small>

        <button type="submit" class="boton boton--ancho">Crear cuenta</button>
    </form>

    <p class="formulario-caja__pie">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</section>
<?php require __DIR__ . '/../src/vistas/pie.php'; ?>
