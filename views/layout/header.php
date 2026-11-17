<?php require_once __DIR__ . '/../helpers.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crescendo - Tienda de audio</title>
  <link rel="stylesheet" href="estilos.css">
  <!-- Swiper (carrusel) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
  <!-- Bootstrap (cuadrícula y utilidades) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css">
  <!-- Font Awesome (íconos) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
  <header class="barra">
    <a class="logo" href="index.php">▮ CRESCENDO</a>
    <nav>
      <a href="index.php">Inicio</a>
      <a href="index.php#productos">Productos</a>
      <a href="index.php#categorias">Categorías</a>
      <a href="index.php#oferta">Ofertas</a>
      <a href="index.php#contacto">Contacto</a>
    </nav>
    <button class="boton-comprar" onclick="mostrarMensaje()">Comprar</button>
  </header>
