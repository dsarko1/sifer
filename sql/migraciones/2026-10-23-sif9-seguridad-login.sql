CREATE TABLE IF NOT EXISTS intentos_login (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ip              VARCHAR(45) NOT NULL,
    username        VARCHAR(50) NOT NULL,
    intentos        TINYINT UNSIGNED NOT NULL DEFAULT 0,
    bloqueado_hasta DATETIME NULL,
    ultimo_intento  DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unico_ip_usuario (ip, username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
