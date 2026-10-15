<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
requiereRol('empleado');

$u = usuarioActual();
$activo = 'asignados';

$hoy = date('Y-m-d');

$stmt = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE empleado_id = ? AND DATE(creado_en) = ?');
$stmt->execute([$u['id'], $hoy]);
$asignadosHoy = $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE empleado_id = ? AND estado = "En producción"');
$stmt->execute([$u['id']]);
$enProduccion = $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE empleado_id = ? AND estado = "Control de calidad"');
$stmt->execute([$u['id']]);
$porRevisar = $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE empleado_id = ? AND estado = "Entregado" AND DATE(creado_en) = ?');
$stmt->execute([$u['id'], $hoy]);
$entregadosHoy = $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT p.*, c.nombre AS cliente_nombre
                        FROM pedidos p JOIN usuarios c ON c.id = p.cliente_id
                        WHERE p.empleado_id = ?
                        ORDER BY p.fecha_entrega ASC');
$stmt->execute([$u['id']]);
$pedidos = $stmt->fetchAll();

$pasos = pasosEstado();

// Determina la siguiente acción/estado disponible para el botón de la fila
function siguienteAccion($estadoActual) {
    $mapa = [
        'Recibido'            => ['label' => 'Iniciar',            'siguiente' => 'En producción'],
        'Confirmado'          => ['label' => 'Iniciar producción', 'siguiente' => 'En producción'],
        'En producción'       => ['label' => 'Enviar a calidad',   'siguiente' => 'Control de calidad'],
        'Control de calidad'  => ['label' => 'Aprobar',            'siguiente' => 'Listo para entrega'],
        'Listo para entrega'  => ['label' => 'Marcar entregado',   'siguiente' => 'Entregado'],
        'Entregado'           => ['label' => null,                 'siguiente' => null],
    ];
    return $mapa[$estadoActual] ?? ['label' => 'Actualizar estado', 'siguiente' => null];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>IMPRIMEX · Panel de empleados</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

<div class="topbar">
  <h1>IMPRIMEX · Plataforma de Gestión de Pedidos</h1>
  <p>Los empleados ven los pedidos asignados a su área y actualizan el estado a medida que avanza el trabajo.</p>
</div>

<div class="container">
  <div class="layout">
    <?php include __DIR__ . '/../includes/nav_sidebar.php'; ?>

    <div class="main">
      <h2 class="page-title" style="margin-top:0;">Panel de empleados</h2>

      <div class="stat-grid">
        <div class="stat"><div class="label">Asignados hoy</div><div class="value"><?= $asignadosHoy ?></div></div>
        <div class="stat"><div class="label">En producción</div><div class="value"><?= $enProduccion ?></div></div>
        <div class="stat"><div class="label">Por revisar</div><div class="value"><?= $porRevisar ?></div></div>
        <div class="stat"><div class="label">Entregados hoy</div><div class="value"><?= $entregadosHoy ?></div></div>
      </div>

      <div class="card">
        <h3 style="margin-top:0;">Cola de trabajo</h3>
        <?php if (empty($pedidos)): ?>
          <p style="color:var(--text-muted);">No tienes pedidos asignados por el momento.</p>
        <?php else: ?>
        <table>
          <thead>
            <tr><th>Pedido</th><th>Cliente</th><th>Trabajo</th><th>Entrega</th><th>Estado</th><th>Acción</th></tr>
          </thead>
          <tbody>
            <?php foreach ($pedidos as $p): $accion = siguienteAccion($p['estado']); ?>
            <tr>
              <td>#<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?></td>
              <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
              <td><?= htmlspecialchars($p['tipo_trabajo']) ?> <?= rtrim(rtrim(number_format($p['cantidad'],2,',','.'),'0'),',') ?> <?= htmlspecialchars($p['unidad']) ?></td>
              <td><?= formatoFecha($p['fecha_entrega']) ?></td>
              <td><span class="badge <?= claseBadgeEstado($p['estado']) ?>"><?= htmlspecialchars($p['estado']) ?></span></td>
              <td>
                <?php if ($accion['siguiente']): ?>
                <form action="<?= BASE_URL ?>/empleado/actualizar_estado.php" method="post" style="display:inline;">
                  <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="nuevo_estado" value="<?= $accion['siguiente'] ?>">
                  <button type="submit" class="btn btn-sm"><?= $accion['label'] ?></button>
                </form>
                <?php else: ?>
                  <span style="color:var(--text-muted); font-size:13px;">Completado</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <p class="footer-note">IMPRIMEX · Plataforma de Gestión de Pedidos</p>
</div>
</body>
</html>
