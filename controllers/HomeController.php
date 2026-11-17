<?php
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Beneficio.php';
require_once __DIR__ . '/../models/Testimonio.php';
require_once __DIR__ . '/../views/helpers.php';

/** Página principal: lee las 4 tablas y se las pasa a la vista. */
class HomeController
{
    public function index(): void
    {
        $productos = $categorias = $beneficios = $testimonios = [];
        $errorBD = null;

        try {
            $productos   = Producto::todos();
            $categorias  = Categoria::todas();
            $beneficios  = Beneficio::todos();
            $testimonios = Testimonio::todos();
        } catch (PDOException $ex) {
            error_log('Crescendo (BD): ' . $ex->getMessage());
            $errorBD = 'No se pudo conectar con la base de datos.';
        }

        require __DIR__ . '/../views/home.php';
    }
}
