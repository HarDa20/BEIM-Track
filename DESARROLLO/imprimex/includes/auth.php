<?php
// Manejo de sesión y control de acceso por rol
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detecta automáticamente la URL base del proyecto, sin importar si está en
// http://localhost/  o en una subcarpeta como  http://localhost/imprimex/
if (!defined('BASE_URL')) {
    $raizProyecto = str_replace('\\', '/', dirname(__DIR__));           // ej: C:/xampp/htdocs/imprimex
    $raizServidor = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/')); // ej: C:/xampp/htdocs
    $base = '';
    if ($raizServidor !== '' && stripos($raizProyecto, $raizServidor) === 0) {
        $base = substr($raizProyecto, strlen($raizServidor));           // ej: /imprimex
    }
    define('BASE_URL', rtrim($base, '/')); // '' si el proyecto está en la raíz, '/imprimex' si está en subcarpeta
}

function usuarioActual() {
    return $_SESSION['usuario'] ?? null;
}

function requiereLogin() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function requiereRol($rolesPermitidos) {
    requiereLogin();
    if (!in_array($_SESSION['usuario']['rol'], (array) $rolesPermitidos, true)) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function redirigirSegunRol($rol) {
    switch ($rol) {
        case 'cliente':
            header('Location: ' . BASE_URL . '/cliente/panel.php');
            break;
        case 'empleado':
            header('Location: ' . BASE_URL . '/empleado/panel.php');
            break;
        case 'administrador':
            header('Location: ' . BASE_URL . '/admin/panel.php');
            break;
        default:
            header('Location: ' . BASE_URL . '/index.php');
    }
    exit;
}
