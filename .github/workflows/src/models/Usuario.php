<?php

class Usuario
{
    public static function crear(PDO $pdo, string $nombre, string $correo, string $contrasena, ?int $edad, ?string $departamento, ?string $nivelDiversificado): int
    {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            'INSERT INTO usuarios (nombre_completo, correo, contrasena_hash, edad, departamento, nivel_diversificado)
             VALUES (:nombre, :correo, :hash, :edad, :departamento, :nivel)'
        );
        $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':hash' => $hash,
            ':edad' => $edad,
            ':departamento' => $departamento,
            ':nivel' => $nivelDiversificado,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function buscarPorCorreo(PDO $pdo, string $correo): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public static function buscarPorId(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id_usuario = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }
}
