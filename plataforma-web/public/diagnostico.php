<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/auth.php';
require_once __DIR__ . '/../src/includes/ia_helper.php';
require_once __DIR__ . '/../src/models/Diagnostico.php';

requerir_login();
$pdo = obtener_conexion();
$idUsuario = (int) $_SESSION['id_usuario'];

$resultado = null;
$rutaGenerada = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $preguntas = Diagnostico::obtenerPreguntas($pdo);
    $idEvaluacion = Diagnostico::iniciarEvaluacion($pdo, $idUsuario);

    foreach ($preguntas as $pregunta) {
        $campo = 'pregunta_' . $pregunta['id_pregunta'];
        $seleccion = $_POST[$campo] ?? null;
        if ($seleccion === null) {
            continue;
        }
        $esCorrecta = $seleccion === $pregunta['respuesta_correcta'];
        Diagnostico::registrarRespuesta($pdo, $idEvaluacion, (int) $pregunta['id_pregunta'], $seleccion, $esCorrecta);
    }

    $resultado = Diagnostico::finalizarEvaluacion($pdo, $idEvaluacion);
    $rutaGenerada = generar_ruta_aprendizaje($pdo, $idUsuario, $idEvaluacion);
}

$preguntas = Diagnostico::obtenerPreguntas($pdo);
$tituloPagina = 'Diagnóstico de habilidades';
require __DIR__ . '/../src/includes/header.php';
?>
<section>
    <h1>Diagnóstico de habilidades informáticas</h1>

    <?php if ($resultado !== null): ?>
        <div class="resultado">
            <p>Puntaje obtenido: <strong><?= htmlspecialchars((string) $resultado) ?>%</strong></p>
            <?php if ($rutaGenerada): ?>
                <p>Se generó tu ruta de aprendizaje personalizada con los siguientes módulos:</p>
                <ul>
                    <?php foreach ($rutaGenerada as $modulo): ?>
                        <li><?= htmlspecialchars($modulo['titulo']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>¡Buen resultado! No se detectaron brechas que requieran módulos adicionales por ahora.</p>
            <?php endif; ?>
            <a class="btn" href="dashboard.php">Ver mi progreso</a>
        </div>
    <?php else: ?>
        <form method="post">
            <?php foreach ($preguntas as $i => $pregunta): ?>
                <fieldset>
                    <legend><?= ($i + 1) . '. ' . htmlspecialchars($pregunta['enunciado']) ?></legend>
                    <?php foreach (['A', 'B', 'C', 'D'] as $letra): ?>
                        <label class="opcion">
                            <input type="radio" name="pregunta_<?= $pregunta['id_pregunta'] ?>" value="<?= $letra ?>" required>
                            <?= htmlspecialchars($pregunta['opcion_' . strtolower($letra)]) ?>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
            <button type="submit" class="btn">Enviar diagnóstico</button>
        </form>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../src/includes/footer.php'; ?>
