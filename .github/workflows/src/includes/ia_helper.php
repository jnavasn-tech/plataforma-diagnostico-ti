<?php
/**
 * Punto de integración con el proveedor de IA (OpenAI y/o DeepSeek) para
 * generar la ruta de aprendizaje personalizada a partir del diagnóstico.
 *
 * Por ahora implementa una regla simple (fallback) para que el flujo
 * funcione de extremo a extremo sin depender de una API key configurada.
 * Reemplazar el cuerpo de generar_ruta_aprendizaje() por una llamada real
 * a la API (ver IA_PROVIDER / IA_API_KEY en src/config/config.php) es el
 * siguiente paso pendiente del proyecto.
 */
function generar_ruta_aprendizaje(PDO $pdo, int $idUsuario, int $idEvaluacion): array
{
    // Habilidades donde el usuario falló al menos una pregunta.
    $stmt = $pdo->prepare(
        'SELECT DISTINCT p.id_habilidad
         FROM respuestas_usuario r
         JOIN preguntas_diagnostico p ON p.id_pregunta = r.id_pregunta
         WHERE r.id_evaluacion = :id_evaluacion AND r.es_correcta = 0'
    );
    $stmt->execute([':id_evaluacion' => $idEvaluacion]);
    $habilidadesDebiles = array_column($stmt->fetchAll(), 'id_habilidad');

    if (!$habilidadesDebiles) {
        return [];
    }

    $marcadores = implode(',', array_fill(0, count($habilidadesDebiles), '?'));
    $stmt = $pdo->prepare(
        "SELECT id_modulo, titulo, orden_secuencia FROM modulos_capacitacion
         WHERE id_habilidad IN ($marcadores) ORDER BY orden_secuencia"
    );
    $stmt->execute($habilidadesDebiles);
    $modulos = $stmt->fetchAll();

    if (!$modulos) {
        return [];
    }

    $justificacion = 'Ruta generada de forma automática (regla base) según las habilidades con '
        . 'respuestas incorrectas en el diagnóstico. Pendiente sustituir por generación vía API de IA.';

    $insertRuta = $pdo->prepare(
        'INSERT INTO rutas_aprendizaje (id_usuario, id_evaluacion, justificacion_ia) VALUES (:u, :e, :j)'
    );
    $insertRuta->execute([':u' => $idUsuario, ':e' => $idEvaluacion, ':j' => $justificacion]);
    $idRuta = (int) $pdo->lastInsertId();

    $insertDetalle = $pdo->prepare(
        'INSERT INTO ruta_modulos (id_ruta, id_modulo, orden) VALUES (:r, :m, :o)'
    );
    $insertProgreso = $pdo->prepare(
        'INSERT IGNORE INTO progreso_usuario (id_usuario, id_modulo, estado) VALUES (:u, :m, "no_iniciado")'
    );

    foreach ($modulos as $orden => $modulo) {
        $insertDetalle->execute([':r' => $idRuta, ':m' => $modulo['id_modulo'], ':o' => $orden + 1]);
        $insertProgreso->execute([':u' => $idUsuario, ':m' => $modulo['id_modulo']]);
    }

    return $modulos;
}
