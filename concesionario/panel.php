<?php
// Panel personal: datos del usuario y catálogo de autos activos.
require 'conexion.php';
require 'funciones.php';
requerir_login();

$stmt = $conexion->prepare(
    'SELECT nombre, apellido, dni, correo, rol FROM usuarios
     WHERE id = :id AND fecha_archivado IS NULL LIMIT 1'
);
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Si el usuario fue archivado mientras tenía sesión abierta, se cierra
if (!$usuario) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit;
}

$autos = $conexion->query(
    'SELECT id, marca, modelo, anio, precio, estado, imagen FROM autos
     WHERE fecha_archivado IS NULL ORDER BY creado_en DESC'
)->fetchAll();

// Compras de este usuario
$stmt = $conexion->prepare(
    'SELECT c.precio, c.fecha_compra, a.marca, a.modelo, a.patente
     FROM compras c JOIN autos a ON a.id = c.auto_id
     WHERE c.usuario_id = :id ORDER BY c.fecha_compra DESC'
);
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$misCompras = $stmt->fetchAll();

$titulo = 'Mi panel';
require 'encabezado.php';
?>
<h1>Hola, <?= escapar($usuario['nombre']) ?></h1>
<section class="tarjeta">
  <h2>Mis datos</h2>
  <p><strong>Nombre:</strong> <?= escapar($usuario['nombre'] . ' ' . $usuario['apellido']) ?></p>
  <p><strong>DNI:</strong> <?= escapar($usuario['dni']) ?></p>
  <p><strong>Correo:</strong> <?= escapar($usuario['correo']) ?></p>
  <p><strong>Rol:</strong> <?= escapar($usuario['rol']) ?></p>
</section>

<h2>Autos del concesionario</h2>
<?php if (!$autos): ?>
  <p>Todavía no hay autos cargados.</p>
<?php else: ?>
  <div class="catalogo">
    <?php foreach ($autos as $a): ?>
      <article class="auto">
        <?php if ($a['imagen']): ?>
          <img src="<?= escapar($a['imagen']) ?>" alt="<?= escapar($a['marca'] . ' ' . $a['modelo']) ?>">
        <?php else: ?>
          <div class="sin-foto">Sin foto</div>
        <?php endif; ?>
        <h3><?= escapar($a['marca'] . ' ' . $a['modelo']) ?></h3>
        <p>Año <?= (int) $a['anio'] ?> · <?= escapar($a['estado']) ?></p>
        <p class="precio">$ <?= number_format((float) $a['precio'], 0, ',', '.') ?></p>
        <?php if ($a['estado'] === 'disponible'): ?>
          <form method="POST" action="comprar_auto.php"
                onsubmit="return confirm('¿Confirmás la compra de este auto?');">
            <?= csrf_campo() ?>
            <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
            <button type="submit">Comprar</button>
          </form>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<h2>Mis compras</h2>
<?php if (!$misCompras): ?>
  <p>Todavía no compraste ningún auto.</p>
<?php else: ?>
<div class="tabla-scroll">
<table>
  <thead><tr><th>Fecha</th><th>Auto</th><th>Patente</th><th>Precio</th></tr></thead>
  <tbody>
  <?php foreach ($misCompras as $c): ?>
    <tr>
      <td><?= escapar($c['fecha_compra']) ?></td>
      <td><?= escapar($c['marca'] . ' ' . $c['modelo']) ?></td>
      <td><?= escapar($c['patente']) ?></td>
      <td>$ <?= number_format((float) $c['precio'], 0, ',', '.') ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>
<?php require 'pie.php'; ?>
