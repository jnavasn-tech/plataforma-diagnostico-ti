<?php
/** Redirige a login si no hay sesión activa. Incluir al inicio de páginas protegidas. */
function requerir_login(): void
{
    if (empty($_SESSION['id_usuario'])) {
        header('Location: login.php');
        exit;
    }
}

function usuario_autenticado(): bool
{
    return !empty($_SESSION['id_usuario']);
}
