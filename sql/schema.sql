-- Sifer - Esquema de la base de datos
-- MariaDB 10.4 / MySQL 8
--
-- ATENCIÓN: vuelve a crear las tablas de Sifer. Si ya existen, se borran
-- sus datos. Ejecutar solo en una instalación nueva o para reiniciar la demo.

CREATE DATABASE IF NOT EXISTS sifer
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sifer;

SET NAMES utf8mb4;

-- El orden de borrado respeta las claves foráneas.
DROP TABLE IF EXISTS auditoria;
DROP TABLE IF EXISTS intentos_login;
DROP TABLE IF EXISTS movimientos;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

-- Usuarios del sistema.
-- rol: admin (gestiona productos y stock) o user (solo consulta).
CREATE TABLE usuarios (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username      VARCHAR(50)  NOT NULL,
    nombre        VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol           ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    activo        TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Control de intentos fallidos de acceso por IP y nombre de usuario.
CREATE TABLE intentos_login (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ip              VARCHAR(45) NOT NULL,
    username        VARCHAR(50) NOT NULL,
    intentos        TINYINT UNSIGNED NOT NULL DEFAULT 0,
    bloqueado_hasta DATETIME NULL,
    ultimo_intento  DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unico_ip_usuario (ip, username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registro de acciones relevantes realizadas en la aplicación.
CREATE TABLE auditoria (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_id  INT UNSIGNED NULL,
    accion      VARCHAR(50) NOT NULL,
    entidad     VARCHAR(30) NOT NULL,
    entidad_id  INT UNSIGNED NULL,
    detalle     VARCHAR(255) NULL,
    ip          VARCHAR(45) NULL,
    creado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_auditoria_creado_en (creado_en),
    KEY idx_auditoria_entidad_id (entidad, entidad_id),
    CONSTRAINT fk_auditoria_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categorías de productos.
CREATE TABLE categorias (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(80)  NOT NULL,
    descripcion VARCHAR(255) NULL,
    creado_en   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categorias_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Productos de la ferretería.
-- La baja es lógica (activo = 0); nunca se borra un producto con historial.
-- Alerta de stock: activo = 1 AND stock_actual <= stock_minimo.
CREATE TABLE productos (
    id             INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    codigo         VARCHAR(30)    NOT NULL,
    nombre         VARCHAR(120)   NOT NULL,
    descripcion    TEXT           NULL,
    categoria_id   INT UNSIGNED   NULL,
    precio         DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    stock_actual   INT            NOT NULL DEFAULT 0,
    stock_minimo   INT            NOT NULL DEFAULT 0,
    activo         TINYINT(1)     NOT NULL DEFAULT 1,
    creado_en      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_productos_codigo (codigo),
    KEY idx_productos_categoria (categoria_id),
    KEY idx_productos_activo_stock (activo, stock_actual),
    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias (id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_productos_precio CHECK (precio >= 0),
    CONSTRAINT chk_productos_stock CHECK (stock_actual >= 0),
    CONSTRAINT chk_productos_stock_minimo CHECK (stock_minimo >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Movimientos de stock.
-- tipo: entrada (suma), salida (resta) o ajuste (fija el stock en cantidad).
-- Se guardan stock_anterior y stock_nuevo como historial del movimiento.
CREATE TABLE movimientos (
    id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    producto_id    INT UNSIGNED    NOT NULL,
    usuario_id     INT UNSIGNED    NOT NULL,
    tipo           ENUM('entrada', 'salida', 'ajuste') NOT NULL,
    cantidad       INT             NOT NULL,
    stock_anterior INT             NOT NULL,
    stock_nuevo    INT             NOT NULL,
    motivo         VARCHAR(255)    NOT NULL,
    creado_en      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_movimientos_producto (producto_id),
    KEY idx_movimientos_usuario (usuario_id),
    KEY idx_movimientos_creado (creado_en),
    CONSTRAINT fk_movimientos_producto
        FOREIGN KEY (producto_id) REFERENCES productos (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_movimientos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_movimientos_cantidad CHECK (cantidad >= 0),
    CONSTRAINT chk_movimientos_stock_anterior CHECK (stock_anterior >= 0),
    CONSTRAINT chk_movimientos_stock_nuevo CHECK (stock_nuevo >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
