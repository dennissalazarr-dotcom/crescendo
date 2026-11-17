<?php require __DIR__ . '/../layout/header.php'; ?>
  <link rel="stylesheet" href="admin.css">

  <main>
    <section id="mensajes">
      <div class="titulo-seccion">
        <h2>Mensajes de contacto</h2>
        <a href="index.php?c=contacto&amp;a=salir">Cerrar sesión →</a>
      </div>

<?php if ($aviso): ?>
      <p class="aviso"><?= e($aviso) ?></p>
<?php endif; ?>

      <!-- Buscador: usa GET (los datos viajan en la URL: ?c=contacto&a=listar&q=...) -->
      <form class="busqueda" action="index.php" method="get">
        <input type="hidden" name="c" value="contacto">
        <input type="hidden" name="a" value="listar">
        <input type="search" name="q" value="<?= e($q) ?>" maxlength="80"
          placeholder="Buscar por nombre, correo, producto o mensaje" aria-label="Buscar mensajes">
        <button type="submit">Buscar</button>
<?php if ($q !== ''): ?>
        <a class="boton oscuro" href="index.php?c=contacto&amp;a=listar">Limpiar</a>
<?php endif; ?>
      </form>

      <p class="conteo"><?= count($mensajes) ?> mensaje(s)</p>

<?php if (!$mensajes): ?>
      <p class="vacio">No hay mensajes para mostrar.</p>
<?php else: ?>
      <div class="tabla-scroll">
        <table class="tabla">
          <thead>
            <tr>
              <th>#</th>
              <th>Fecha</th>
              <th>Nombre</th>
              <th>Contacto</th>
              <th>Producto</th>
              <th>Mensaje</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
<?php foreach ($mensajes as $m): ?>
            <tr>
              <td><?= (int) $m['id'] ?></td>
              <td><?= e(date('d/m/Y H:i', strtotime($m['creado_en']))) ?></td>
              <td><?= e($m['nombre']) ?></td>
              <td><?= e($m['correo']) ?><small><?= e($m['telefono'] ?? '—') ?></small></td>
              <td><?= e($m['producto']) ?></td>
              <td><?= e(mb_strimwidth($m['mensaje'], 0, 120, '…', 'UTF-8')) ?></td>
              <td>
                <div class="acciones">
                  <a class="boton oscuro mini" href="index.php?c=contacto&amp;a=editar&amp;id=<?= (int) $m['id'] ?>">Editar</a>
                  <form action="index.php?c=contacto&amp;a=eliminar" method="post"
                    onsubmit="return confirm('¿Eliminar este mensaje? Esta acción no se puede deshacer.');">
                    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                    <button type="submit" class="mini rosa-boton">Eliminar</button>
                  </form>
                </div>
              </td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
      </div>
<?php endif; ?>
    </section>
  </main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
