<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? NOMBRE_PLATAFORMA) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="contenedor">
        <a href="index.php" class="logo">Plataforma TI</a>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="capacitacion.php">Capacitación</a>
            <?php if (usuario_autenticado()): ?>
                <a href="diagnostico.php">Diagnóstico</a>
                <a href="dashboard.php">Mi progreso</a>
                <a href="logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar sesión</a>
                <a href="registro.php" class="btn-nav">Registrarme</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="contenedor">
