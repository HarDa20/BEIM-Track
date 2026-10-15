<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';
requiereRol('cliente');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/cliente/crear_pedido.php');
    exit;
}

$u = usuarioActual();

$categoria     = $_POST['categoria'] ?? '';
$tipoTrabajo   = trim($_POST['tipo_trabajo'] ?? '');
$cantidad      = (float) ($_POST['cantidad'] ?? 0);
$fechaEntrega  = $_POST['fecha_entrega'] ?? '';
$notas         = trim($_POST['notas'] ?? '');

$categoriasValidas = ['Fachadas', 'Interiores', 'Gran formato'];

if (!in_array($categoria, $categoriasValidas, true) || $tipoTrabajo === '' || $cantidad <= 0 || $fechaEntrega === '') {
    $_SESSION['error_pedido'] = 'Por favor completa todos los campos obligatorios.';
    header('Location: ' . BASE_URL . '/cliente/crear_pedido.php');
    exit;
}

// Calcular costo estimado según precio por categoría
$stmt = $pdo->prepare('SELECT precio_por_unidad FROM categorias_precios WHERE categoria = ?');
$stmt->execute([$categoria]);
$precioUnitario = (float) $stmt->fetchColumn();
$costoEstimado = $precioUnitario * $cantidad;

// Subida de archivo de diseño (opcional)
$rutaArchivo = null;
if (!empty($_FILES['archivo_diseno']['name'])) {
    $extPermitidas = ['pdf', 'ai', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['archivo_diseno']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $extPermitidas, true)) {
        $carpeta = __DIR__ . '/../uploads/';
        if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);
        $nombreArchivo = uniqid('pedido_') . '.' . $ext;
        if (move_uploaded_file($_FILES['archivo_diseno']['tmp_name'], $carpeta . $nombreArchivo)) {
            $rutaArchivo = 'uploads/' . $nombreArchivo;
        }
    }
}

$estadoInicial = isset($_POST['borrador']) ? 'Recibido' : 'Recibido';

$stmt = $pdo->prepare(
    'INSERT INTO pedidos (cliente_id, categoria, tipo_trabajo, cantidad, unidad, fecha_entrega, archivo_diseno, notas, costo_estimado, estado)
     VALUES (?, ?, ?, ?, "m2", ?, ?, ?, ?, ?)'
);
$stmt->execute([$u['id'], $categoria, $tipoTrabajo, $cantidad, $fechaEntrega, $rutaArchivo, $notas, $costoEstimado, $estadoInicial]);
$pedidoId = $pdo->lastInsertId();

$pdo->prepare('INSERT INTO historial_pedido (pedido_id, descripcion) VALUES (?, "Pedido recibido")')
    ->execute([$pedidoId]);

header('Location: ' . BASE_URL . '/cliente/estado_pedido.php?id=' . $pedidoId);
exit;
