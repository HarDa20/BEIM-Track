<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
requiereRol('administrador');

$u = usuarioActual();
$activo = 'general';

$pedidosActivos = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado != 'Entregado'")->fetchColumn();
$retrasados = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado != 'Entregado' AND fecha_entrega < CURDATE()")->fetchColumn();
$ingresosMes = $pdo->query("SELECT COALESCE(SUM(costo_estimado),0) FROM pedidos WHERE MONTH(creado_en) = MONTH(CURDATE()) AND YEAR(creado_en) = YEAR(CURDATE())")->fetchColumn();
$empleadosActivos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'empleado'")->fetchColumn();

$busqueda = trim($_GET['q'] ?? '');
$sql = "SELECT p.*, c.nombre AS cliente_nombre, e.nombre AS empleado_nombre
        FROM pedidos p
        JOIN usuarios c ON c.id = p.cliente_id
        LEFT JOIN usuarios e ON e.id = p.empleado_id";
$params = [];
if ($busqueda !== '') {
    $sql .= " WHERE p.id = ? OR c.nombre LIKE ?";
    $params = [(int) ltrim($busqueda, '#'), '%' . $busqueda . '%'];
}
$sql .= " ORDER BY p.creado_en DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pedidos = $stmt->fetchAll();

$empleados = $pdo->query("SELECT id, nombre, area FROM usuarios WHERE rol = 'empleado' ORDER BY nombre")->fetchAll();

function formatoM($v) {
    if ($v >= 1000000) return '$' . rtrim(rtrim(number_format($v / 1000000, 1, '.', ''), '0'), '.') . 'M';
    return '$' . number_format($v, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>IMPRIMEX · Panel de administrador</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

<div class="topbar">
  <h1>IMPRIMEX · Plataforma de Gestión de Pedidos</h1>
  <p>Visión global: todos los pedidos, clientes, empleados, asignación de trabajos y métricas del negocio.</p>
</div>

<div class="container">
  <div class="layout">
    <?php include __DIR__ . '/../includes/nav_sidebar.php'; ?>

    <div class="main">
      <h2 class="page-title" style="margin-top:0;">Panel de administrador</h2>

      <div class="stat-grid">
        <div class="stat"><div class="label">Pedidos activos</div><div class="value"><?= $pedidosActivos ?></div></div>
        <div class="stat"><div class="label">Retrasados</div><div class="value"><?= $retrasados ?></div></div>
        <div class="stat"><div class="label">Ingresos del mes</div><div class="value"><?= formatoM($ingresosMes) ?></div></div>
        <div class="stat"><div class="label">Empleados activos</div><div class="value"><?= $empleadosActivos ?></div></div>
      </div>

      <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
          <h3 style="margin:0;">Todos los pedidos</h3>
          <form method="get" style="display:flex; gap:8px;">
            <input type="text" name="q" placeholder="Buscar pedido..." value="<?= htmlspecialchars($busqueda) ?>" style="width:200px;">
            <button type="submit" class="btn">🔍 Filtrar</button>
          </form>
        </div>

        <table>
          <thead>
            <tr><th>Pedido</th><th>Cliente</th><th>Categoría</th><th>Asignado a</th><th>Entrega</th><th>Estado</th><th>Acción</th></tr>
          </thead>
          <tbody>
            <?php foreach ($pedidos as $p): ?>
            <tr>
              <td>#<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?></td>
              <td><?= htmlspecialchars($p['cliente_nombre']) ?></td>
              <td><?= htmlspecialchars($p['categoria']) ?></td>
              <td><?= $p['empleado_nombre'] ? htmlspecialchars($p['empleado_nombre']) : '<span style="color:var(--text-muted);">Sin asignar</span>' ?></td>
              <td><?= formatoFecha($p['fecha_entrega']) ?></td>
              <td><span class="badge <?= claseBadgeEstado($p['estado']) ?>"><?= htmlspecialchars($p['estado']) ?></span></td>
              <td>
                <form action="<?= BASE_URL ?>/admin/asignar_empleado.php" method="post" style="display:flex; gap:6px;">
                  <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                  <select name="empleado_id" style="padding:6px;">
                    <option value="">Sin asignar</option>
                    <?php foreach ($empleados as $emp): ?>
                      <option value="<?= $emp['id'] ?>" <?= $p['empleado_id'] == $emp['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($emp['nombre']) ?> (<?= htmlspecialchars($emp['area']) ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="btn btn-sm">Asignar</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($pedidos)): ?>
              <tr><td colspan="7" style="text-align:center; color:var(--text-muted);">No se encontraron pedidos.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <p class="footer-note">Boceto funcional convertido a proyecto PHP — estructura y flujo completos, listo para conectar a tu base de datos.</p>
</div>
</body>
</html>
