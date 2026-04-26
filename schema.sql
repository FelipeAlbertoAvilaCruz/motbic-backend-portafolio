CREATE DATABASE IF NOT EXISTS motbic;
USE motbic;

CREATE TABLE modelos (
  id VARCHAR(64) PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  marca VARCHAR(120) NOT NULL,
  anio CHAR(4) NOT NULL,
  tipo_motor ENUM('electrica','gasolina','hibrida') NOT NULL DEFAULT 'gasolina',
  cilindrada VARCHAR(50) NOT NULL,
  precio DECIMAL(12,2) NOT NULL,
  imagen VARCHAR(255),
  colores JSON NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE FULLTEXT INDEX ft_modelos_nombre ON modelos (nombre);
CREATE FULLTEXT INDEX ft_modelos_marca ON modelos (marca);

CREATE TABLE inventario (
  id VARCHAR(64) PRIMARY KEY,
  modelo_id VARCHAR(64) NOT NULL,
  color VARCHAR(60) NOT NULL,
  serie VARCHAR(60) NOT NULL,
  motor VARCHAR(60) NOT NULL,
  vin CHAR(17) NOT NULL,
  estado ENUM('disponible','vendida','reservada','defectuosa') NOT NULL DEFAULT 'disponible',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_inventario_serie (serie),
  UNIQUE KEY uq_inventario_vin (vin),
  CONSTRAINT fk_inventario_modelos FOREIGN KEY (modelo_id) REFERENCES modelos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX idx_inventario_estado ON inventario (estado);

CREATE TABLE clientes (
  id VARCHAR(64) PRIMARY KEY,
  nombres VARCHAR(120) NOT NULL,
  apellidos VARCHAR(150) NOT NULL,
  telefono VARCHAR(20) NOT NULL,
  email VARCHAR(150) NOT NULL,
  rfc CHAR(13) NOT NULL,
  calle VARCHAR(150) NOT NULL,
  colonia VARCHAR(120) NOT NULL,
  ciudad VARCHAR(120) NOT NULL,
  estado VARCHAR(120) NOT NULL,
  codigo_postal CHAR(5) NOT NULL,
  ultima_compra DATE,
  estado_servicios ENUM('al-dia','pendiente','vencido'),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_clientes_telefono (telefono),
  UNIQUE KEY uq_clientes_email (email),
  UNIQUE KEY uq_clientes_rfc (rfc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE FULLTEXT INDEX ft_clientes_nombre ON clientes (nombres, apellidos);

CREATE TABLE ventas (
  id VARCHAR(64) PRIMARY KEY,
  folio VARCHAR(32) NOT NULL,
  cliente_id VARCHAR(64) NOT NULL,
  inventario_id VARCHAR(64) NOT NULL,
  fecha DATETIME NOT NULL,
  metodo_pago ENUM('efectivo','transferencia','tarjeta-credito','tarjeta-debito','cheque','financiamiento','mixto') NOT NULL,
  precio_total DECIMAL(12,2) NOT NULL,
  estado ENUM('completada','pendiente','cancelada') NOT NULL DEFAULT 'completada',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_ventas_folio (folio),
  CONSTRAINT fk_ventas_clientes FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  CONSTRAINT fk_ventas_inventario FOREIGN KEY (inventario_id) REFERENCES inventario(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE servicios (
  id VARCHAR(64) PRIMARY KEY,
  cliente_id VARCHAR(64) NOT NULL,
  inventario_id VARCHAR(64) NOT NULL,
  venta_id VARCHAR(64),
  tipo_servicio ENUM('primer-servicio','segundo-servicio','tercer-servicio','mantenimiento-regular','reparacion') NOT NULL,
  fecha_programada DATE NOT NULL,
  fecha_realizada DATE,
  estado ENUM('programado','completado','cancelado','vencido') NOT NULL DEFAULT 'programado',
  notas TEXT,
  costo DECIMAL(12,2),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_servicios_clientes FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  CONSTRAINT fk_servicios_inventario FOREIGN KEY (inventario_id) REFERENCES inventario(id),
  CONSTRAINT fk_servicios_ventas FOREIGN KEY (venta_id) REFERENCES ventas(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;