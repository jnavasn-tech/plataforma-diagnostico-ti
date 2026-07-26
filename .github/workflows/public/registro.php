<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/auth.php';
require_once __DIR__ . '/../src/models/Usuario.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $edad = $_POST['edad'] !== '' ? (int) $_POST['edad'] : null;
    $departamento = trim($_POST['departamento'] ?? '') ?: null;
    $nivel = trim($_POST['nivel_diversificado'] ?? '') ?: null;

    if ($nombre === '' || $correo === '' || $contrasena === '') {
        $errores[] = 'Nombre, correo y contraseña son obligatorios.';
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido.';
    }
    if (strlen($contrasena) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }

    if (!$errores) {
        $pdo = obtener_conexion();
        if (Usuario::buscarPorCorreo($pdo, $correo)) {
            $errores[] = 'Ya existe una cuenta registrada con ese correo.';
        } else {
            $idUsuario = Usuario::crear($pdo, $nombre, $correo, $contrasena, $edad, $departamento, $nivel);
            $_SESSION['id_usuario'] = $idUsuario;
            $_SESSION['nombre_usuario'] = $nombre;
            header('Location: diagnostico.php');
            exit;
        }
    }
}

$tituloPagina = 'Registro';
require __DIR__ . '/../src/includes/header.php';
?>
<section class="formulario">
    <h1>Crear cuenta</h1>
    <?php foreach ($errores as $error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>
    <form method="post" novalidate>
        <label>Nombre completo
            <input type="text" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
        </label>
        <label>Correo electrónico
            <input type="email" name="correo" value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" required>
        </label>
        <label>Contraseña (mínimo 8 caracteres)
            <input type="password" name="contrasena" required>
        </label>
        <label>Edad
            <input type="number" name="edad" min="15" max="99">
        </label>
        <label>Departamento
            <input type="text" name="departamento">
        </label>
        <label>Carrera del diversificado
            <input type="text" name="nivel_diversificado">
        </label>
        <button type="submit" class="btn">Registrarme</button>
    </form>
    <p>¿Ya tenés cuenta? <a href="login.php">Iniciar sesión</a></p>
</section>
<?php require __DIR__ . '/../src/includes/footer.php'; ?>
