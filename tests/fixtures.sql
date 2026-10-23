-- Datos fijos para la suite de pruebas.

INSERT INTO usuarios (id, username, nombre, password_hash, rol, activo) VALUES
    (1, 'admin', 'Administrador de prueba', '$2y$10$aLgV53KMupbARi8oND6JW.U6wFoRSdA3sTc/F0UyHBBWoL58..cZa', 'admin', 1),
    (2, 'vendedor', 'Vendedor de prueba', '$2y$10$aLgV53KMupbARi8oND6JW.U6wFoRSdA3sTc/F0UyHBBWoL58..cZa', 'user', 1),
    (3, 'inactivo', 'Usuario inactivo', '$2y$10$aLgV53KMupbARi8oND6JW.U6wFoRSdA3sTc/F0UyHBBWoL58..cZa', 'admin', 0);

INSERT INTO categorias (id, nombre, descripcion) VALUES
    (1, 'Herramientas', 'Herramientas de mano'),
    (2, 'Pinturas', 'Pinturas y accesorios');

INSERT INTO productos (id, nombre, codigo, descripcion, categoria_id, precio, stock_actual, stock_minimo, activo) VALUES
    (1, 'Martillo de prueba', 'TEST-001', 'Herramienta', 1, 100.00, 10, 5, 1),
    (2, 'Lata de pintura', 'TEST-002', 'Pintura', 2, 250.50, 2, 5, 1),
    (3, 'Producto discontinuado', 'TEST-003', 'Sin stock', NULL, 50.00, 0, 0, 0);

INSERT INTO movimientos (producto_id, usuario_id, tipo, cantidad, stock_anterior, stock_nuevo, motivo) VALUES
    (1, 1, 'entrada', 10, 0, 10, 'Carga inicial');
