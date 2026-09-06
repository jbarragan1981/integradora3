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

-- -------------------------------------------------------------
-- Datos de prueba: ocho incidencias repartidas entre los cuatro
-- estados del tablero, con categorías y prioridades variadas.
-- -------------------------------------------------------------
INSERT INTO incidencias
    (titulo, descripcion, categoria_id, prioridad, estado, reportante, correo, area_codigo, fecha_reporte)
VALUES
    ('La impresora del laboratorio B no responde',
     'La impresora HP del laboratorio de sistemas B no enciende desde esta mañana. Ya se revisó el cable de poder y sigue sin encender.',
     1, 'Media', 'Nuevo', 'Carlos Mendoza', 'carlos.mendoza@ecotec.edu.ec', 203,
     '2026-09-03 08:15:00'),

    ('Solicitud de acceso a carpeta compartida de Secretaría',
     'El nuevo asistente de Secretaría Académica necesita permisos de lectura y escritura sobre la carpeta compartida de actas de grado.',
     4, 'Baja', 'Nuevo', 'Estefanía Rojas', 'estefania.rojas@ecotec.edu.ec', 101,
     '2026-09-04 09:40:00'),

    ('Caída intermitente de la red inalámbrica en biblioteca',
     'Los estudiantes reportan que el WiFi de la biblioteca se desconecta cada 10 o 15 minutos, especialmente en el segundo piso.',
     3, 'Alta', 'En proceso', 'Luis Andrade', 'luis.andrade@ecotec.edu.ec', 305,
     '2026-08-30 11:20:00'),

    ('Error al generar reportes en el sistema de calificaciones',
     'Al exportar el reporte de notas del segundo parcial, el sistema muestra un error 500 y no genera el archivo PDF.',
     2, 'Crítica', 'En proceso', 'Marcela Vera', 'marcela.vera@ecotec.edu.ec', 110,
     '2026-08-29 14:05:00'),

    ('Proyector del aula 204 con imagen distorsionada',
     'El proyector del aula 204 muestra la imagen con líneas de color y parpadeo constante, dificulta las clases con diapositivas.',
     1, 'Media', 'En proceso', 'Diego Salas', 'diego.salas@ecotec.edu.ec', 204,
     '2026-09-01 10:00:00'),

    ('Reinicio del servidor de correo institucional',
     'El correo institucional dejó de enviar mensajes durante la mañana. Se coordinó con soporte para reiniciar el servicio de correo.',
     5, 'Alta', 'Resuelto', 'Paola Iturralde', 'paola.iturralde@ecotec.edu.ec', 401,
     '2026-08-27 07:30:00'),

    ('Instalación de antivirus en equipos nuevos de Admisiones',
     'Se recibieron cinco computadoras nuevas para el área de Admisiones y requieren la instalación del antivirus corporativo.',
     2, 'Baja', 'Resuelto', 'Jorge Ponce', 'jorge.ponce@ecotec.edu.ec', 102,
     '2026-08-25 13:45:00'),

    ('Cambio de contraseña del router principal del campus',
     'Por política de seguridad se solicitó actualizar la contraseña de administrador del router principal antes de fin de mes.',
     3, 'Media', 'Cerrado', 'Andrea Torres', 'andrea.torres@ecotec.edu.ec', 300,
     '2026-08-18 16:10:00');