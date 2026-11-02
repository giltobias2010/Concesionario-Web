-- Base de datos del concesionario de autos
-- Importar desde phpMyAdmin (pestaña "Importar") o ejecutar completo.

CREATE DATABASE IF NOT EXISTS concesionario
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE concesionario;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(60) NOT NULL,
  apellido VARCHAR(60) NOT NULL,
  dni VARCHAR(10) NOT NULL UNIQUE,
  correo VARCHAR(120) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,            -- hash de password_hash()
  rol ENUM('usuario','admin') NOT NULL DEFAULT 'usuario',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_archivado DATETIME NULL DEFAULT NULL   -- NULL = activo
) ENGINE=InnoDB;

-- Tabla de autos
CREATE TABLE IF NOT EXISTS autos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  marca VARCHAR(60) NOT NULL,
  modelo VARCHAR(60) NOT NULL,
  anio SMALLINT UNSIGNED NOT NULL,
  precio DECIMAL(12,2) NOT NULL,
  patente VARCHAR(10) NOT NULL UNIQUE,
  estado ENUM('disponible','reservado','vendido') NOT NULL DEFAULT 'disponible',
  imagen VARCHAR(255) DEFAULT NULL,            -- ruta relativa de la foto
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_archivado DATETIME NULL DEFAULT NULL   -- NULL = activo
) ENGINE=InnoDB;

-- Administrador inicial: correo admin@concesionario.com | contraseña Admin1234
-- (cambiarla después de la primera prueba)
INSERT INTO usuarios (nombre, apellido, dni, correo, contrasena, rol) VALUES
('Admin', 'Principal', '10000000', 'admin@concesionario.com',
 '$2y$12$ctgtI2z4bvcfpwDk/KU9ouP4jRwRh2z6K3cP0PYkjsgVhga0Flcg2', 'admin');

-- Autos de ejemplo (sin imagen)
INSERT INTO autos (marca, modelo, anio, precio, patente, estado) VALUES
('Toyota', 'Corolla', 2022, 28500000.00, 'AB123CD', 'disponible'),
('Volkswagen', 'Gol Trend', 2019, 14200000.00, 'AA456BC', 'disponible'),
('Ford', 'Ranger', 2021, 41000000.00, 'AC789DE', 'reservado');

-- Compras: cada fila registra qué usuario compró qué auto y a qué precio
CREATE TABLE IF NOT EXISTS compras (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  auto_id INT UNSIGNED NOT NULL,
  precio DECIMAL(12,2) NOT NULL,               -- precio al momento de comprar
  fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (auto_id) REFERENCES autos(id)
) ENGINE=InnoDB;
