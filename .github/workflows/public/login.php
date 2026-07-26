<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/auth.php';
require_once __DIR__ . '/../src/models/Usuario.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    $pdo = obtener_conexion();
    $usuario = Usuario::buscarPorCorreo($pdo, $correo);

    if ($usuario && password_verify($contrasena, $usuario['contrasena_hash'])) {
        $_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
        $_SESSION['nombre_usuario'] = $usuario['nombre_completo'];
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Correo o contraseña incorrectos.';
}

$tituloPagina = 'Iniciar sesión';
require __DIR__ . '/../src/includes/header.php';
?>
<section class="formulario">
    <h1>Iniciar sesión</h1>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post" novalidate>
        <label>Correo electrónico
            <input type="email" name="correo" required>
        </label>
        <label>Contraseña
            <input type="password" name="contrasena" required>
        </label>
        <button type="submit" class="btn">Entrar</button>
    </form>
    <p>¿No tenés cuenta? <a href="registro.php">Registrate gratis</a></p>
</section>
<?php require __DIR__ . '/../src/includes/footer.php'; ?>
