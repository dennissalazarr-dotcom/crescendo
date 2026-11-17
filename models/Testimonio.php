<?php
require_once __DIR__ . '/Conexion.php';

/** Modelo de la tabla `testimonios` (solo lectura). */
class Testimonio
{
    public static function todos(): array
    {
        return Conexion::obtener()->query('SELECT * FROM testimonios ORDER BY id')->fetchAll();
    }
}
