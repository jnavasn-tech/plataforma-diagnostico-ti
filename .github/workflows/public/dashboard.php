<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/auth.php';
require_once __DIR__ . '/../src/models/Modulo.php';

requerir_login();

$pdo = obtener_conexion();
$progreso = Modulo::progresoDeUsuario($pdo, (int) $_SESSION['id_usuario']);

$tituloPagina = 'Mi progreso';
require __DIR__ . '/../src/includes/header.php';
?>
<section>
    <h1>Hola, <?= htmlspecialchars($_SESSION['nombre_usuario']) ?></h1>
    <h2>Tu progreso por módulo</h2>
    <table class="tabla-progreso">
        <thead>
            <tr><th>Módulo</th><th>Estado</th><th>Avance</th></tr>
        </thead>
        <tbody>
        <?php foreach ($progreso as $fila): ?>
            <tr>
                <td><?= htmlspecialchars($fila['titulo']) ?></td>
                <td><?= htmlspecialchars(str_replace('_', ' ', $fila['estado'])) ?></td>
                <td><?= htmlspecialchars((string) $fila['porcentaje_avance']) ?>%</td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$progreso): ?>
            <tr><td colspan="3">Todavía no tenés módulos asignados. Completá el <a href="diagnostico.php">diagnóstico</a> primero.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
<?php require __DIR__ . '/../src/includes/footer.php'; ?>
