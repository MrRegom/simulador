CREATE DATABASE IF NOT EXISTS servirec CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE servirec;

-- Tabla de Usuarios (Admins)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'operador') DEFAULT 'operador',
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de Equipos (Cabinas/Simuladores)
CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) UNIQUE,
    mac_address VARCHAR(17) UNIQUE,
    estado ENUM('libre', 'en_uso', 'bloqueado', 'mantenimiento') DEFAULT 'libre',
    ultimo_heartbeat TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de Sesiones
CREATE TABLE sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    tiempo_asignado_min INT NOT NULL,
    hora_inicio DATETIME NOT NULL,
    hora_fin DATETIME NOT NULL,
    estado ENUM('activa', 'finalizada', 'cancelada') DEFAULT 'activa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_sesion_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    CONSTRAINT fk_sesion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Tabla de Pagos
CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia') NOT NULL,
    referencia_pago VARCHAR(100),
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pago_sesion FOREIGN KEY (sesion_id) REFERENCES sesiones(id)
) ENGINE=InnoDB;

-- Tabla de Auditoría Transaccional
CREATE TABLE auditoria_transaccional (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(255) NOT NULL,
    tabla_afectada VARCHAR(50),
    id_registro INT,
    valor_anterior TEXT,
    valor_nuevo TEXT,
    ip_cliente VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Usuario inicial: admin@servirec.cl / Admin123
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Administrador Master', 'admin@servirec.cl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Las 4 cabinas iniciales
INSERT INTO equipos (nombre, ip_address, estado) VALUES 
('Simulador 01', '192.168.1.10', 'libre'),
('Simulador 02', '192.168.1.11', 'libre'),
('Simulador 03', '192.168.1.12', 'libre'),
('Simulador 04', '192.168.1.13', 'libre');
