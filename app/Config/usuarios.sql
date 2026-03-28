-- Desactivamos la revisión de llaves foráneas para poder borrar la tabla con dependencias
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'operador') DEFAULT 'operador',
    estado TINYINT(1) DEFAULT 1,
    ultimo_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertamos al administrador con la contraseña: admin123
INSERT INTO usuarios (id, nombre, usuario, password, rol) 
VALUES (1, 'Administrador', 'admin', '$2y$10$7R8W.u0TNG.wI6L9M3SgVOUYQ7RNC2mE0K2e2/X5oM3nI.o8z1tK6', 'admin');

-- Volvemos a activar la revisión de llaves foráneas
SET FOREIGN_KEY_CHECKS = 1;
