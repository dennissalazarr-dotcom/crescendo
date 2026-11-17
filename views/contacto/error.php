<?php require __DIR__ . '/../layout/header.php'; ?>
  <link rel="stylesheet" href="admin.css">

  <main>
    <section class="gracias">
      <span class="etiqueta">⚠ <?= e($titulo) ?></span>
      <ul class="errores">
<?php foreach ($errores as $mensajeError): ?>
        <li><?= e($mensajeError) ?></li>
<?php endforeach; ?>
      </ul>
      <a class="boton" href="<?= e($volver) ?>">← Volver</a>
    </section>
  </main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
