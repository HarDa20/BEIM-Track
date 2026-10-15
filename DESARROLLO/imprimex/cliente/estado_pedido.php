<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
requiereRol('cliente');

$u = usuarioActual();
$activo = 'pedidos';
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT p.*, e.nombre AS empleado_nombre, e.area AS empleado_area
                        FROM pedidos p
                        LEFT JOIN usuarios e ON e.id = p.empleado_id
                        WHERE p.id = ? AND p.cliente_id = ?');
$stmt->execute([$id, $u['id']]);
$pedido = $stmt->fetch();

if (!$pedido) {
    header('Location: ' . BASE_URL . '/cliente/panel.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM historial_pedido WHERE pedido_id = ? ORDER BY fecha ASC');
$stmt->execute([$id]);
$historial = $stmt->fetchAll();

$pasos = pasosEstado();
$pasoActualIdx = array_search($pedido['estado'], $pasos, true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>IMPRIMEX · Estado del pedido</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

<div class="topbar">
  <h1>IMPRIMEX · Plataforma de Gestión de Pedidos</h1>
  <p>Barra de estado del pedido — seguimiento visual del progreso.</p>
</div>

<div class="container">
  <div class="layout">
    <?php include __DIR__ . '/../includes/nav_sidebar.php'; ?>

    <div class="main">
      <p style="margin-bottom:6px;"><a href="<?= BASE_URL ?>/cliente/panel.php">← Volver a mis pedidos</a></p>

      <div class="card">
        <h2 style="margin:0 0 4px;">Pedido #<?= str_pad($pedido['id'], 4, '0', STR_PAD_LEFT) ?> — <?= htmlspecialchars($pedido['categoria']) ?>, <?= htmlspecialchars($pedido['tipo_trabajo']) ?> (<?= rtrim(rtrim(number_format($pedido['cantidad'],2,',','.'),'0'),',') ?> <?= htmlspecialchars($pedido['unidad']) ?>)</h2>
        <span class="badge <?= claseBadgeEstado($pedido['estado']) ?>"><?= htmlspecialchars($pedido['estado']) ?></span>

        <div class="stepper">
          <?php foreach ($pasos as $idx => $paso):
              $done = $idx < $pasoActualIdx;
              $current = $idx === $pasoActualIdx;
          ?>
            <?php if ($idx > 0): ?><div class="step-line <?= $done || $current ? 'done' : '' ?>"></div><?php endif; ?>
            <div class="step <?= $done ? 'done' : ($current ? 'current' : '') ?>">
              <div class="circle"><?= $done ? '✓' : $idx + 1 ?></div>
              <div class="label"><?= $paso ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="layout" style="margin-top:18px; gap:18px;">
        <div class="card" style="flex:1;">
          <h3 style="margin-top:0;">Historial de actividad</h3>
          <?php if (empty($historial)): ?>
            <p style="color:var(--text-muted);">Sin actividad registrada.</p>
          <?php else: foreach ($historial as $h): ?>
            <p style="font-size:14px; margin:6px 0;">
              <?= date('d M, g:i a', strtotime($h['fecha'])) ?> — <?= htmlspecialchars($h['descripcion']) ?>
            </p>
          <?php endforeach; endif; ?>
        </div>

        <div class="card" style="flex:1;">
          <h3 style="margin-top:0;">Empleado asignado</h3>
          <?php if ($pedido['empleado_nombre']): ?>
            <p style="font-size:14px;">Área: <strong><?= htmlspecialchars($pedido['empleado_area']) ?></strong></p>
            <p style="font-size:14px;">Responsable: <strong><?= htmlspecialchars($pedido['empleado_nombre']) ?></strong></p>
          <?php else: ?>
            <p style="color:var(--text-muted); font-size:14px;">Aún no se ha asignado un empleado.</p>
          <?php endif; ?>
          <p style="font-size:14px;">Fecha estimada de finalización: <strong><?= formatoFecha($pedido['fecha_entrega']) ?></strong></p>
        </div>
      </div>
    </div>
  </div>

  <p class="footer-note">Estado visible tanto para el cliente como para empleados y administrador.</p>
</div>
</body>
</html>
