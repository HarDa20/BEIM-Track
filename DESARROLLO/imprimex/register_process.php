<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';

function volverConError($msg) {
    $_SESSION['error_registro'] = $msg;
    $_SESSION['tab_activa'] = 'registro';
    header('Location: index.php');
    exit;
}

if ($nombre === '' || $correo === '' || strlen($password) < 6) {
    volverConError('Completa todos los campos. La contraseña debe tener al menos 6 caracteres.');
}

$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE correo = ?');
$stmt->execute([$correo]);
if ($stmt->fetch()) {
    volverConError('Ya existe una cuenta registrada con ese correo.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('INSERT INTO usuarios (nombre, correo, password_hash, rol) VALUES (?, ?, ?, "cliente")');
$stmt->execute([$nombre, $correo, $hash]);

$_SESSION['usuario'] = [
    'id'     => $pdo->lastInsertId(),
    'nombre' => $nombre,
    'correo' => $correo,
    'rol'    => 'cliente',
    'area'   => null,
];

redirigirSegunRol('cliente');
