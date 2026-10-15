<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$correo = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare('SELECT id, nombre, correo, password_hash, rol, area FROM usuarios WHERE correo = ?');
$stmt->execute([$correo]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
    $_SESSION['error_login'] = 'Correo o contraseña incorrectos.';
    $_SESSION['tab_activa'] = 'login';
    header('Location: index.php');
    exit;
}

$_SESSION['usuario'] = [
    'id'     => $usuario['id'],
    'nombre' => $usuario['nombre'],
    'correo' => $usuario['correo'],
    'rol'    => $usuario['rol'],
    'area'   => $usuario['area'],
];

redirigirSegunRol($usuario['rol']);
