<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
requiereRol('empleado');

$u = usuarioActual();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/empleado/panel.php');
    exit;
}

$pedidoId = (int) ($_POST['pedido_id'] ?? 0);
$nuevoEstado = $_POST['nuevo_estado'] ?? '';

if (!in_array($nuevoEstado, pasosEstado(), true)) {
    header('Location: ' . BASE_URL . '/empleado/panel.php');
    exit;
}

// Verificar que el pedido pertenece a este empleado
$stmt = $pdo->prepare('SELECT id FROM pedidos WHERE id = ? AND empleado_id = ?');
$stmt->execute([$pedidoId, $u['id']]);
if (!$stmt->fetch()) {
    header('Location: ' . BASE_URL . '/empleado/panel.php');
    exit;
}

$pdo->prepare('UPDATE pedidos SET estado = ? WHERE id = ?')->execute([$nuevoEstado, $pedidoId]);
$pdo->prepare('INSERT INTO historial_pedido (pedido_id, descripcion) VALUES (?, ?)')
    ->execute([$pedidoId, 'Estado actualizado a "' . $nuevoEstado . '" por ' . $u['nombre']]);

header('Location: ' . BASE_URL . '/empleado/panel.php');
exit;
