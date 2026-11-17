<?php require __DIR__ . '/../layout/header.php'; ?>
  <link rel="stylesheet" href="admin.css">

  <main>
    <section id="acceso" class="acceso">
      <div class="titulo-seccion">
        <h2>Administración de mensajes</h2>
        <a href="index.php">← Volver al sitio</a>
      </div>
<?php if ($error): ?>
      <p class="aviso aviso-error"><?= e($error) ?></p>
<?php endif; ?>
      <form class="formulario" action="index.php?c=contacto&amp;a=entrar" method="post">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <label for="clave">Clave de administrador</label>
        <input type="password" id="clave" name="clave" autocomplete="current-password" required>
        <button type="submit">Entrar</button>
      </form>
    </section>
  </main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
