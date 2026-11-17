<?php
/**
 * Punto de entrada (router MVC).
 *   index.php                         -> página principal
 *   index.php?c=contacto&a=gracias    -> controlador "contacto", acción "gracias"
 * Solo se permiten los controladores y acciones de la lista blanca.
 */
$permitidos = [
    'home'     => ['controlador' => 'HomeController',     'acciones' => ['index']],
    'contacto' => ['controlador' => 'ContactoController', 'acciones' => [
        'guardar', 'gracias', 'entrar', 'salir', 'listar', 'editar', 'actualizar', 'eliminar',
    ]],
];

$c = is_string($_GET['c'] ?? null) ? $_GET['c'] : 'home';
$a = is_string($_GET['a'] ?? null) ? $_GET['a'] : 'index';

if (!isset($permitidos[$c]) || !in_array($a, $permitidos[$c]['acciones'], true)) {
    http_response_code(404);
    exit('Página no encontrada.');
}

$clase = $permitidos[$c]['controlador'];
require __DIR__ . '/controllers/' . $clase . '.php';
(new $clase())->$a();
