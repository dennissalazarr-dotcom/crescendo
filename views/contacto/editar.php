<?php require __DIR__ . '/../layout/header.php'; ?>
  <link rel="stylesheet" href="admin.css">

  <main>
    <section id="editar">
      <div class="titulo-seccion">
        <h2>Editar mensaje #<?= (int) $m['id'] ?></h2>
        <a href="index.php?c=contacto&amp;a=listar">← Volver a la lista</a>
      </div>

<?php if ($errores): ?>
      <div class="aviso aviso-error">
        <ul class="errores">
<?php foreach ($errores as $mensajeError): ?>
          <li><?= e($mensajeError) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
<?php endif; ?>

<?php if (!empty($m['creado_en'])): ?>
      <p class="conteo">Recibido el <?= e(date('d/m/Y H:i', strtotime($m['creado_en']))) ?></p>
<?php endif; ?>

      <form class="formulario" action="index.php?c=contacto&amp;a=actualizar" method="post">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" maxlength="80" value="<?= e($m['nombre']) ?>" required>
          </div>
          <div class="col-md-6">
            <label for="telefono">Teléfono (opcional)</label>
            <input type="tel" id="telefono" name="telefono" maxlength="30" value="<?= e($m['telefono'] ?? '') ?>">
          </div>
          <div class="col-12">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" maxlength="120" value="<?= e($m['correo']) ?>" required>
          </div>
          <div class="col-12">
            <label for="producto">Producto de interés</label>
            <select id="producto" name="producto">
<?php foreach ($opciones as $op): ?>
              <option value="<?= e($op) ?>"<?= $op === $m['producto'] ? ' selected' : '' ?>><?= e($op) ?></option>
<?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label for="mensaje">Mensaje</label>
            <textarea id="mensaje" name="mensaje" maxlength="2000" required><?= e($m['mensaje']) ?></textarea>
          </div>
          <div class="col-12">
            <button type="submit">Guardar cambios</button>
          </div>
          <div class="col-12">
            <a class="boton oscuro" href="index.php?c=contacto&amp;a=listar">Cancelar</a>
          </div>
        </div>
      </form>
    </section>
  </main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
