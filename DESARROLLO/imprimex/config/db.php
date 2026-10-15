<?php
// Configuración de conexión a la base de datos MySQL
// Ajusta estos datos según tu entorno (XAMPP, WAMP, Laragon, hosting, etc.)

define('DB_HOST', 'localhost');
define('DB_NAME', 'imprimex');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . $e->getMessage() .
        '<br>Verifica que MySQL esté corriendo y que hayas importado database/schema.sql');
}
