-- MODULO DE ROLES Y PERMISOS - SERVIREC
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Crear tabla de ROLES
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL UNIQUE,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Crear tabla de PERMISOS
CREATE TABLE IF NOT EXISTS `permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL UNIQUE,
  `descripcion` varchar(255) DEFAULT NULL,
  `modulo` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla intermedia ROLES_PERMISOS
CREATE TABLE IF NOT EXISTS `roles_permisos` (
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  PRIMARY KEY (`rol_id`, `permiso_id`),
  CONSTRAINT `fk_rp_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Modificar tabla USUARIOS para usar el nuevo sistema
ALTER TABLE `usuarios` ADD COLUMN `rol_id` int(11) DEFAULT NULL AFTER `rol`;
ALTER TABLE `usuarios` ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

-- 5. SEMILLAS (Datos Iniciales)
INSERT IGNORE INTO `roles` (id, nombre, descripcion) VALUES 
(1, 'Administrador', 'Acceso total a todos los módulos del sistema'),
(2, 'Operador', 'Gestión de simuladores y caja básica');

-- Insertar Permisos Base
INSERT IGNORE INTO `permisos` (nombre, descripcion, modulo) VALUES 
('dashboard.view', 'Ver métricas del panel principal', 'Dashboard'),
('simuladores.manage', 'Iniciar, detener y extender sesiones', 'Simuladores'),
('equipos.manage', 'Configurar hardware y mantenimiento', 'Hardware'),
('usuarios.manage', 'Crear, editar y eliminar personal', 'Seguridad'),
('roles.manage', 'Configurar perfiles y permisos', 'Seguridad'),
('pagos.view', 'Ver historial de transacciones', 'Finanzas'),
('reportes.view', 'Generar reportes de ventas', 'Finanzas'),
('ranking.manage', 'Administrar tiempos y publicidad', 'Ranking');

-- Asignar todos los permisos al Administrador (ID 1)
INSERT IGNORE INTO `roles_permisos` (rol_id, permiso_id)
SELECT 1, id FROM `permisos`;

-- Asignar permisos básicos al Operador (ID 2)
INSERT IGNORE INTO `roles_permisos` (rol_id, permiso_id)
SELECT 2, id FROM `permisos` WHERE modulo IN ('Dashboard', 'Simuladores', 'Ranking');

-- Vincular usuarios actuales a los nuevos roles
UPDATE `usuarios` SET `rol_id` = 1 WHERE `rol` = 'admin';
UPDATE `usuarios` SET `rol_id` = 2 WHERE `rol` = 'operador';

SET FOREIGN_KEY_CHECKS = 1;
