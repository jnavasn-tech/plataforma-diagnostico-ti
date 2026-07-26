<?php

class Diagnostico
{
    /** Devuelve todas las preguntas activas del banco de diagnóstico. */
    public static function obtenerPreguntas(PDO $pdo): array
    {
        $stmt = $pdo->query(
            'SELECT p.id_pregunta, p.enunciado, p.opcion_a, p.opcion_b, p.opcion_c, p.opcion_d,
                    p.respuesta_correcta, p.id_habilidad, h.nombre AS habilidad
             FROM preguntas_diagnostico p
             JOIN habilidades h ON h.id_habilidad = p.id_habilidad
             ORDER BY p.id_pregunta'
        );

        return $stmt->fetchAll();
    }

    /** Crea una nueva evaluación en progreso para un usuario. */
    public static function iniciarEvaluacion(PDO $pdo, int $idUsuario): int
    {
        $stmt = $pdo->prepare(
            'INSERT INTO evaluaciones_diagnostico (id_usuario, estado) VALUES (:id_usuario, "en_progreso")'
        );
        $stmt->execute([':id_usuario' => $idUsuario]);

        return (int) $pdo->lastInsertId();
    }

    /** Registra la respuesta de un usuario a una pregunta específica. */
    public static function registrarRespuesta(PDO $pdo, int $idEvaluacion, int $idPregunta, string $opcionSeleccionada, bool $esCorrecta): void
    {
        $stmt = $pdo->prepare(
            'INSERT INTO respuestas_usuario (id_evaluacion, id_pregunta, opcion_seleccionada, es_correcta)
             VALUES (:id_evaluacion, :id_pregunta, :opcion, :correcta)'
        );
        $stmt->execute([
            ':id_evaluacion' => $idEvaluacion,
            ':id_pregunta' => $idPregunta,
            ':opcion' => $opcionSeleccionada,
            ':correcta' => $esCorrecta ? 1 : 0,
        ]);
    }

    /** Calcula el puntaje total y cierra la evaluación. */
    public static function finalizarEvaluacion(PDO $pdo, int $idEvaluacion): float
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS total, SUM(es_correcta) AS correctas
             FROM respuestas_usuario WHERE id_evaluacion = :id'
        );
        $stmt->execute([':id' => $idEvaluacion]);
        $fila = $stmt->fetch();

        $total = (int) ($fila['total'] ?? 0);
        $correctas = (int) ($fila['correctas'] ?? 0);
        $puntaje = $total > 0 ? round(($correctas / $total) * 100, 2) : 0.0;

        $update = $pdo->prepare(
            'UPDATE evaluaciones_diagnostico
             SET puntaje_total = :puntaje, estado = "completada"
             WHERE id_evaluacion = :id'
        );
        $update->execute([':puntaje' => $puntaje, ':id' => $idEvaluacion]);

        return $puntaje;
    }
}
