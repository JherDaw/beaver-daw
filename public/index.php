<?php
require __DIR__ . '/../src/sesion.php';
require __DIR__ . '/../src/conexion.php';

// Categorías para el buscador y las tarjetas de la portada.
try {
    $categorias = conexion()->query('SELECT id, nombre, descripcion FROM categorias ORDER BY nombre')->fetchAll();
} catch (PDOException $ex) {
    $categorias = [];
}

$titulo = 'Inicio';
require __DIR__ . '/../src/vistas/cabecera.php';
?>
<section class="portada">
    <div class="contenedor">
        <h1>Arreglos en casa con presupuesto cerrado</h1>
        <p class="portada__texto">Elige un profesional, envíale fotos del problema y recibe un precio total antes de que vaya a tu casa.</p>

        <form class="buscador" action="buscar.php" method="get">
            <select name="categoria" aria-label="Categoría">
                <option value="">¿Qué necesitas?</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= (int) $c['id'] ?>"><?= e($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="zona" placeholder="Código postal o municipio" aria-label="Zona">
            <button type="submit" class="boton">Buscar</button>
        </form>
    </div>
</section>

<section class="contenedor seccion">
    <h2>¿Cómo funciona?</h2>
    <ol class="pasos">
        <li><strong>Busca</strong> un profesional por categoría y zona.</li>
        <li><strong>Envía fotos</strong> y describe el problema.</li>
        <li><strong>Recibe un presupuesto cerrado</strong> y decide si lo aceptas.</li>
    </ol>
</section>

<?php if ($categorias): ?>
<section class="contenedor seccion">
    <h2>Categorías</h2>
    <div class="tarjetas">
        <?php foreach ($categorias as $c): ?>
            <a class="tarjeta" href="buscar.php?categoria=<?= (int) $c['id'] ?>">
                <h3><?= e($c['nombre']) ?></h3>
                <p><?= e($c['descripcion']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/../src/vistas/pie.php'; ?>
