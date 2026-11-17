<?php
require_once __DIR__ . '/Conexion.php';

/** Modelo de la tabla `productos` (solo lectura). */
class Producto
{
    public static function todos(): array
    {
        return Conexion::obtener()->query('SELECT * FROM productos ORDER BY id')->fetchAll();
    }
}
