<?php require __DIR__ . '/../layout/header.php'; ?>
  <link rel="stylesheet" href="admin.css">

  <main>
    <section class="gracias">
      <span class="etiqueta">✔ Mensaje enviado</span>
      <h1>¡Gracias<?= $nombre !== '' ? ', <span>' . e($nombre) . '</span>' : '' ?>!</h1>
      <p>Recibimos tu mensaje. Te responderemos por correo en el horario de atención: lunes a viernes, de 8:00 a. m. a 6:00 p. m.</p>
      <a class="boton" href="index.php">← Volver al inicio</a>
      <a class="boton oscuro" href="index.php#productos">Ver productos</a>
    </section>
  </main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
