<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
requiereRol('cliente');

$u = usuarioActual();
$activo = 'pedidos';

$stmt = $pdo->prepare('SELECT * FROM pedidos WHERE cliente_id = ? ORDER BY creado_en DESC');
$stmt->execute([$u['id']]);
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>IMPRIMEX · Mis pedidos</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

<div class="topbar">
  <h1>IMPRIMEX · Plataforma de Gestión de Pedidos</h1>
  <p>Vista principal tras iniciar sesión: resumen de pedidos activos, historial y acceso rápido a crear un nuevo pedido.</p>
</div>

<div class="container">
  <div class="layout">
    <?php include __DIR__ . '/../includes/nav_sidebar.php'; ?>

    <div class="main">
      <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
          <h2 style="margin:0;">Mis pedidos</h2>
          <a href="<?= BASE_URL ?>/cliente/crear_pedido.php" class="btn btn-primary">+ Crear nuevo pedido</a>
        </div>

        <?php if (empty($pedidos)): ?>
          <p style="color:var(--text-muted);">Aún no tienes pedidos. Crea el primero con el botón de arriba.</p>
        <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>N° Pedido</th>
              <th>Categoría</th>
              <th>Cantidad</th>
              <th>Entrega</th>
              <th>Estado</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pedidos as $p): ?>
            <tr>
              <td>#<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?></td>
              <td><?= htmlspecialchars($p['categoria']) ?> — <?= htmlspecialchars($p['tipo_trabajo']) ?></td>
              <td><?= rtrim(rtrim(number_format($p['cantidad'], 2, ',', '.'), '0'), ',') ?> <?= htmlspecialchars($p['unidad']) ?></td>
              <td><?= formatoFecha($p['fecha_entrega']) ?></td>
              <td><span class="badge <?= claseBadgeEstado($p['estado']) ?>"><?= htmlspecialchars($p['estado']) ?></span></td>
              <td><a href="<?= BASE_URL ?>/cliente/estado_pedido.php?id=<?= $p['id'] ?>">Ver</a></td>
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
