<?php
// Se espera que $u = usuarioActual() y $activo = nombre de la sección activa
// estén definidos antes de incluir este archivo.
?>
<div class="sidebar">
  <div class="who">
    <strong><?= htmlspecialchars($u['nombre']) ?></strong>
    <span><?= ucfirst($u['rol']) ?><?= $u['area'] ? ' · ' . htmlspecialchars($u['area']) : '' ?></span>
  </div>

  <?php if ($u['rol'] === 'cliente'): ?>
    <a href="<?= BASE_URL ?>/cliente/panel.php" class="<?= $activo === 'pedidos' ? 'active' : '' ?>">🏠 Mis pedidos</a>
    <a href="<?= BASE_URL ?>/cliente/crear_pedido.php" class="<?= $activo === 'nuevo' ? 'active' : '' ?>">➕ Nuevo pedido</a>
  <?php elseif ($u['rol'] === 'empleado'): ?>
    <a href="<?= BASE_URL ?>/empleado/panel.php" class="<?= $activo === 'asignados' ? 'active' : '' ?>">📋 Pedidos asignados</a>
  <?php elseif ($u['rol'] === 'administrador'): ?>
    <a href="<?= BASE_URL ?>/admin/panel.php" class="<?= $activo === 'general' ? 'active' : '' ?>">📊 Panel general</a>
  <?php endif; ?>

  <a href="<?= BASE_URL ?>/logout.php" class="logout">🚪 Cerrar sesión</a>
</div>
