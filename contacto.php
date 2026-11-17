<?php
/**
 * El formulario de la página principal envía sus datos (POST) a "contacto.php".
 * Este archivo solo entrega la petición al controlador MVC: ContactoController::guardar().
 */
$_GET['c'] = 'contacto';
$_GET['a'] = 'guardar';
require __DIR__ . '/index.php';
