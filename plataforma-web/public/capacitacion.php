<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/auth.php';
require_once __DIR__ . '/../src/models/Modulo.php';

$pdo = obtener_conexion();
$modulos = Modulo::listarTodos($pdo);

$tituloPagina = 'Catálogo de capacitación';
require __DIR__ . '/../src/includes/header.php';
?>
<section>
    <h1>Catálogo de módulos de capacitación</h1>
    <div class="grid-modulos">
        <?php foreach ($modulos as $modulo): ?>
            <article class="tarjeta-modulo">
                <h3><?= htmlspecialchars($modulo['titulo']) ?></h3>
                <p class="etiqueta"><?= htmlspecialchars($modulo['habilidad']) ?> · <?= htmlspecialchars($modulo['nivel_dificultad']) ?></p>
                <p><?= htmlspecialchars($modulo['descripcion'] ?? '') ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/../src/includes/footer.php'; ?>
