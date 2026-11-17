<?php
require_once __DIR__ . '/Conexion.php';

/** Modelo de la tabla `beneficios` (solo lectura). */
class Beneficio
{
    public static function todos(): array
    {
        return Conexion::obtener()->query('SELECT * FROM beneficios ORDER BY id')->fetchAll();
    }
}
