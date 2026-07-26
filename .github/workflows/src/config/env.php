<?php
/**
 * Carga muy simple de variables de entorno desde un archivo .env
 * (evita depender de Composer/vlucas para este scaffold inicial).
 */
function cargar_env(string $rutaEnv): void
{
    if (!file_exists($rutaEnv)) {
        return;
    }

    foreach (file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || str_starts_with($linea, '#')) {
            continue;
        }
        [$clave, $valor] = array_pad(explode('=', $linea, 2), 2, '');
        $clave = trim($clave);
        $valor = trim($valor);
        if ($clave !== '' && getenv($clave) === false) {
            putenv("{$clave}={$valor}");
            $_ENV[$clave] = $valor;
        }
    }
}

cargar_env(__DIR__ . '/../../.env');
