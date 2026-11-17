<?php
require_once __DIR__ . '/Conexion.php';

/** Modelo de la tabla `categorias` (solo lectura). */
class Categoria
{
    public static function todas(): array
    {
        return Conexion::obtener()->query('SELECT * FROM categorias ORDER BY id')->fetchAll();
    }
}
