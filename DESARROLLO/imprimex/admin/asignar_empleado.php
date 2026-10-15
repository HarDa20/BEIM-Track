<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';
requiereRol('administrador');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/panel.php');
    exit;
}

$pedidoId = (int) ($_POST['pedido_id'] ?? 0);
$empleadoId = $_POST['empleado_id'] !== '' ? (int) $_POST['empleado_id'] : null;

$stmt = $pdo->prepare('UPDATE pedidos SET empleado_id = ?, estado = IF(estado = "Recibido" AND ? IS NOT NULL, "Confirmado", estado) WHERE id = ?');
$stmt->execute([$empleadoId, $empleadoId, $pedidoId]);

if ($empleadoId) {
    $nombreEmp = $pdo->query('SELECT nombre FROM usuarios WHERE id = ' . (int) $empleadoId)->fetchColumn();
    $pdo->prepare('INSERT INTO historial_pedido (pedido_id, descripcion) VALUES (?, ?)')
        ->execute([$pedidoId, 'Asignado a ' . $nombreEmp]);
}

header('Location: ' . BASE_URL . '/admin/panel.php');
exit;
