<?php
// Gestión de usuarios: búsqueda, archivado y reactivación. Solo admin.
require 'conexion.php';
require 'funciones.php';
requerir_admin();

// Acciones (solo POST + CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $accion = $_POST['accion'] ?? '';
    if ($id === false || $id === null || $id < 1 || !in_array($accion, ['archivar', 'reactivar'], true)) {
        http_response_code(400);
        exit('Solicitud inválida.');
    }
    if ($accion === 'archivar' && $id === (int) $_SESSION['usuario_id']) {
        mensaje_flash('No podés archivar tu propia cuenta.', 'error');
    } elseif ($accion === 'archivar') {
        $stmt = $conexion->prepare('UPDATE usuarios SET fecha_archivado = CURRENT_TIMESTAMP WHERE id = :id AND fecha_archivado IS NULL');
        $stmt->execute([':id' => $id]);
        mensaje_flash('Usuario archivado.');
    } else {
        $stmt = $conexion->prepare('UPDATE usuarios SET fecha_archivado = NULL WHERE id = :id AND fecha_archivado IS NOT NULL');
        $stmt->execute([':id' => $id]);
        mensaje_flash('Usuario reactivado.');
    }
    header('Location: admin_usuarios.php?ver=' . ($accion === 'archivar' ? 'activos' : 'archivados'));
    exit;
}

$ver = ($_GET['ver'] ?? 'activos') === 'archivados' ? 'archivados' : 'activos';
$buscar = trim($_GET['buscar'] ?? '');

// Nunca se selecciona la contraseña
$sql = 'SELECT id, nombre, apellido, dni, correo, rol, fecha_archivado FROM usuarios WHERE fecha_archivado '
     . ($ver === 'activos' ? 'IS NULL' : 'IS NOT NULL');
$params = [];
if ($buscar !== '') {
    $sql .= ' AND (nombre LIKE :nombre OR apellido LIKE :apellido OR dni LIKE :dni OR correo LIKE :correo)';
    $t = '%' . $buscar . '%';
    $params = [':nombre' => $t, ':apellido' => $t, ':dni' => $t, ':correo' => $t];
}
$sql .= ' ORDER BY id';
$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();

$titulo = 'Administrar usuarios';
require 'encabezado.php';
?>
<h1>Usuarios <?= $ver === 'archivados' ? 'archivados' : 'activos' ?></h1>
<p>
  <a href="admin_usuarios.php?ver=activos">Activos</a> ·
  <a href="admin_usuarios.php?ver=archivados">Archivados</a>
</p>
<form method="GET" class="busqueda">
  <input type="hidden" name="ver" value="<?= escapar($ver) ?>">
  <input type="search" name="buscar" maxlength="100" placeholder="Nombre, apellido, DNI o correo" value="<?= escapar($buscar) ?>">
  <button type="submit">Buscar</button>
</form>

<?php if (!$usuarios): ?>
  <p>No se encontraron usuarios.</p>
<?php else: ?>
<div class="tabla-scroll">
<table>
  <thead><tr><th>ID</th><th>Nombre</th><th>DNI</th><th>Correo</th><th>Rol</th><th>Acción</th></tr></thead>
  <tbody>
  <?php foreach ($usuarios as $u): ?>
    <tr>
      <td><?= (int) $u['id'] ?></td>
      <td><?= escapar($u['nombre'] . ' ' . $u['apellido']) ?></td>
      <td><?= escapar($u['dni']) ?></td>
      <td><?= escapar($u['correo']) ?></td>
      <td><?= escapar($u['rol']) ?></td>
      <td>
        <form method="POST" class="en-linea" onsubmit="return confirm('¿Confirmar la acción?');">
          <?= csrf_campo() ?>
          <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
          <input type="hidden" name="accion" value="<?= $ver === 'activos' ? 'archivar' : 'reactivar' ?>">
          <button type="submit" class="boton-link"><?= $ver === 'activos' ? 'Archivar' : 'Reactivar' ?></button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>
<?php require 'pie.php'; ?>
