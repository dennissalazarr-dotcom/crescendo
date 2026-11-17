<?php
require_once __DIR__ . '/Conexion.php';

/** Modelo de la tabla `mensajes` (CRUD completo: crear, leer, actualizar y eliminar). */
class Mensaje
{
    /** CREATE: guarda un mensaje nuevo y devuelve su id. */
    public static function crear(array $d): int
    {
        $sql = 'INSERT INTO mensajes (nombre, telefono, correo, producto, mensaje)
                VALUES (:nombre, :telefono, :correo, :producto, :mensaje)';
        $pdo = Conexion::obtener();
        $pdo->prepare($sql)->execute(self::parametros($d));
        return (int) $pdo->lastInsertId();
    }

    /** READ: lista los mensajes (los más recientes primero). Con $busqueda filtra por texto. */
    public static function todos(string $busqueda = ''): array
    {
        $pdo = Conexion::obtener();

        if ($busqueda === '') {
            return $pdo->query('SELECT * FROM mensajes ORDER BY creado_en DESC, id DESC LIMIT 200')->fetchAll();
        }

        $sql = 'SELECT * FROM mensajes
                WHERE nombre   LIKE :q1 OR correo  LIKE :q2
                   OR producto LIKE :q3 OR mensaje LIKE :q4
                ORDER BY creado_en DESC, id DESC
                LIMIT 200';
        $like = '%' . addcslashes($busqueda, '%_\\') . '%';
        $st = $pdo->prepare($sql);
        $st->execute([':q1' => $like, ':q2' => $like, ':q3' => $like, ':q4' => $like]);
        return $st->fetchAll();
    }

    /** READ: un mensaje por su id (null si no existe). */
    public static function buscar(int $id): ?array
    {
        $st = Conexion::obtener()->prepare('SELECT * FROM mensajes WHERE id = :id');
        $st->execute([':id' => $id]);
        $fila = $st->fetch();
        return $fila === false ? null : $fila;
    }

    /** UPDATE: modifica un mensaje existente. */
    public static function actualizar(int $id, array $d): bool
    {
        $sql = 'UPDATE mensajes
                SET nombre = :nombre, telefono = :telefono, correo = :correo,
                    producto = :producto, mensaje = :mensaje
                WHERE id = :id';
        $params = self::parametros($d);
        $params[':id'] = $id;
        return Conexion::obtener()->prepare($sql)->execute($params);
    }

    /** DELETE: borra un mensaje. */
    public static function eliminar(int $id): bool
    {
        return Conexion::obtener()->prepare('DELETE FROM mensajes WHERE id = :id')->execute([':id' => $id]);
    }

    /** Arma los parámetros de INSERT/UPDATE (el teléfono vacío se guarda como NULL). */
    private static function parametros(array $d): array
    {
        $telefono = trim((string) ($d['telefono'] ?? ''));
        return [
            ':nombre'   => $d['nombre'],
            ':telefono' => $telefono === '' ? null : $telefono,
            ':correo'   => $d['correo'],
            ':producto' => $d['producto'],
            ':mensaje'  => $d['mensaje'],
        ];
    }
}
