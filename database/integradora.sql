-- =============================================================
-- Gestión de incidencias
-- Base de datos: integradora
-- Motor: MySQL / MariaDB 
--
-- Ejecutar desde phpMyAdmin (Importar) o desde consola:
--   mysql -u root < database/integradora.sql
-- =============================================================

CREATE DATABASE IF NOT EXISTS integradora
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE integradora;

-- -------------------------------------------------------------
-- Catálogo de categorías de incidencia
-- -------------------------------------------------------------
DROP TABLE IF EXISTS incidencias;
DROP TABLE IF EXISTS categorias;

CREATE TABLE categorias (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL UNIQUE,
    color  VARCHAR(7)  NOT NULL DEFAULT '#2C8C8C'
) ENGINE = InnoDB;

INSERT INTO categorias (nombre, color) VALUES
    ('Hardware',        '#EC7A69'),
    ('Software',        '#2C8C8C'),
    ('Red',             '#14274E'),
    ('Accesos',         '#F2B035'),
    ('Infraestructura', '#5FC2A4');

-- -------------------------------------------------------------
-- Tabla principal: incidencias reportadas
-- El campo estado alimenta las columnas del tablero.
-- -------------------------------------------------------------
CREATE TABLE incidencias (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    titulo       VARCHAR(120) NOT NULL,
    descripcion  TEXT         NOT NULL,
    categoria_id INT          NOT NULL,
    prioridad    ENUM('Baja', 'Media', 'Alta', 'Crítica')            NOT NULL DEFAULT 'Media',
    estado       ENUM('Nuevo', 'En proceso', 'Resuelto', 'Cerrado')  NOT NULL DEFAULT 'Nuevo',
    reportante   VARCHAR(100) NOT NULL,
    correo       VARCHAR(150) NOT NULL,
    area_codigo  SMALLINT     NOT NULL,
    fecha_reporte       DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_incidencia_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias (id),

    INDEX idx_estado (estado),
    INDEX idx_prioridad (prioridad)
) ENGINE = InnoDB;