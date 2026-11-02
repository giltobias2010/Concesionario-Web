-- Ejecutar SOLO si ya habías importado concesionario.sql antes de agregar las compras.
USE concesionario;
CREATE TABLE IF NOT EXISTS compras (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  auto_id INT UNSIGNED NOT NULL,
  precio DECIMAL(12,2) NOT NULL,
  fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (auto_id) REFERENCES autos(id)
) ENGINE=InnoDB;
