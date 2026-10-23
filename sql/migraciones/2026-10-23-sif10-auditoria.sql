CREATE TABLE IF NOT EXISTS auditoria (
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
