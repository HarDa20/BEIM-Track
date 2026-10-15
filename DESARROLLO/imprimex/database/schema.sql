-- IMPRIMEX · Plataforma de Gestión de Pedidos
-- Esquema de base de datos MySQL

CREATE DATABASE IF NOT EXISTS imprimex CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE imprimex;

-- Usuarios: clientes, empleados y administradores
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('cliente','empleado','administrador') NOT NULL DEFAULT 'cliente',
    area VARCHAR(100) DEFAULT NULL,          -- solo para empleados (ej: Producción, Control de calidad)
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    empleado_id INT DEFAULT NULL,
    categoria ENUM('Fachadas','Interiores','Gran formato') NOT NULL,
    tipo_trabajo VARCHAR(200) NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    unidad VARCHAR(20) NOT NULL DEFAULT 'm2',
    fecha_entrega DATE NOT NULL,
    archivo_diseno VARCHAR(255) DEFAULT NULL,
    notas TEXT,
    costo_estimado DECIMAL(12,2) DEFAULT 0,
    estado ENUM('Recibido','Confirmado','En producción','Control de calidad','Listo para entrega','Entregado') NOT NULL DEFAULT 'Recibido',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (empleado_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Historial de actividad de cada pedido
CREATE TABLE IF NOT EXISTS historial_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Precio referencial por categoría (usado para el costo estimado)
CREATE TABLE IF NOT EXISTS categorias_precios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria ENUM('Fachadas','Interiores','Gran formato') NOT NULL UNIQUE,
    precio_por_unidad DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

INSERT INTO categorias_precios (categoria, precio_por_unidad) VALUES
('Fachadas', 30000),
('Interiores', 25000),
('Gran formato', 45000)
ON DUPLICATE KEY UPDATE precio_por_unidad = VALUES(precio_por_unidad);

-- Usuarios de ejemplo para probar el sistema (los hashes ya están generados con password_hash())
-- Administrador  -> admin@imprimex.com    / admin123
-- Empleado       -> andrea@imprimex.com   / empleado123
-- Cliente        -> cliente@correo.com    / cliente123
INSERT INTO usuarios (nombre, correo, password_hash, rol, area) VALUES
('Administrador IMPRIMEX', 'admin@imprimex.com', '$2b$12$Pb4OfCbg8x57qZMWUX00y.H3DQBlo8qC4NllIsEjohqcAE3d5eNMi', 'administrador', NULL),
('Andrea Gómez', 'andrea@imprimex.com', '$2b$12$1VSm0V6QQ/rXfckUTkhmyOg.adLjwcV4kxh.jwuxpAu5kiAxk.dAq', 'empleado', 'Producción'),
('Harold Hernández', 'cliente@correo.com', '$2b$12$QdjNZbj1j.b6gz/2lP7UMORHo/xz2V8.jKuS8LCE5rBFMzuaIk/Gq', 'cliente', NULL)
ON DUPLICATE KEY UPDATE correo = correo;
