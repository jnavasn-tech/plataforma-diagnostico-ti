<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/auth.php';

$tituloPagina = 'Inicio';
require __DIR__ . '/../src/includes/header.php';
?>
<section class="hero">
    <h1>Diagnóstico y capacitación técnica en informática, gratuita y personalizada con IA</h1>
    <p>
        Plataforma dirigida a jóvenes egresados del nivel diversificado en Guatemala que buscan
        fortalecer sus competencias informáticas para conseguir un puesto de trabajo. Un diagnóstico
        inicial identifica tus fortalezas y debilidades; a partir de ahí, la Inteligencia Artificial
        arma una ruta de aprendizaje personalizada.
    </p>
    <?php if (!usuario_autenticado()): ?>
        <a class="btn" href="registro.php">Comenzar diagnóstico gratuito</a>
    <?php else: ?>
        <a class="btn" href="diagnostico.php">Ir al diagnóstico</a>
    <?php endif; ?>
</section>

<section class="pasos">
    <div class="paso">
        <h3>1. Diagnóstico</h3>
        <p>Respondé una evaluación corta sobre ofimática y ciberseguridad básica.</p>
    </div>
    <div class="paso">
        <h3>2. Ruta personalizada</h3>
        <p>La IA identifica tus brechas y arma una secuencia de módulos a tu medida.</p>
    </div>
    <div class="paso">
        <h3>3. Capacitación</h3>
        <p>Avanzá módulo por módulo y seguí tu progreso desde tu panel.</p>
    </div>
</section>
<?php require __DIR__ . '/../src/includes/footer.php'; ?>
