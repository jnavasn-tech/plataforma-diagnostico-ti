<?php

class Modulo
{
    /** Lista el catálogo completo de módulos de capacitación disponibles. */
    public static function listarTodos(PDO $pdo): array
    {
        $stmt = $pdo->query(
            'SELECT m.id_modulo, m.titulo, m.descripcion, m.nivel_dificultad, h.nombre AS habilidad
             FROM modulos_capacitacion m
             JOIN habilidades h ON h.id_habilidad = m.id_habilidad
             ORDER BY h.nombre, m.orden_secuencia'
        );

        return $stmt->fetchAll();
    }

    /** Progreso de un usuario específico en todos los módulos. */
    public static function progresoDeUsuario(PDO $pdo, int $idUsuario): array
    {
        $stmt = $pdo->prepare(
            'SELECT m.id_modulo, m.titulo, COALESCE(pu.estado, "no_iniciado") AS estado,
                    COALESCE(pu.porcentaje_avance, 0) AS porcentaje_avance
             FROM modulos_capacitacion m
             LEFT JOIN progreso_usuario pu
                    ON pu.id_modulo = m.id_modulo AND pu.id_usuario = :id_usuario
             ORDER BY m.orden_secuencia'
        );
        $stmt->execute([':id_usuario' => $idUsuario]);

        return $stmt->fetchAll();
    }
}
