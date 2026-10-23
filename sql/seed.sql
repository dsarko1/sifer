-- Sifer - Datos de prueba
-- Ejecutar después de schema.sql.
--
-- Usuarios:
--   admin  / Admin123!   (rol admin)
--   ventas / Ventas123!  (rol user)

USE sifer;

SET NAMES utf8mb4;

-- Usuarios de prueba (contraseñas hasheadas con password_hash de PHP).
INSERT INTO usuarios (id, username, nombre, password_hash, rol, activo) VALUES
(1, 'admin',  'Administrador', '$2y$10$bnMSPltfl9dGJS.KW31JyOwBojsaRhTNdDll8J2dlfuXDayPcUzpi', 'admin', 1),
(2, 'ventas', 'Ventas',        '$2y$10$hJ2vP0bwj.76IailefV2Q.0tvRwo3np55L/BQY4OTXqxg5UMnUfbu', 'user',  1);

-- Categorías.
INSERT INTO categorias (id, nombre, descripcion) VALUES
(1, 'Herramientas', 'Herramientas manuales y eléctricas'),
(2, 'Materiales de construcción', 'Cemento, arena, ladrillos y afines'),
(3, 'Pinturas y químicos', 'Pinturas, esmaltes, pinceles y rodillos');

-- Productos (algunos con stock bajo o sin stock para probar las alertas).
INSERT INTO productos (codigo, nombre, descripcion, categoria_id, precio, stock_actual, stock_minimo, activo) VALUES
('HERR-001', 'Martillo de carpintero 500 g', 'Mango de madera, cabeza de acero forjado.', 1, 8500.00, 12, 5, 1),
('HERR-002', 'Destornillador Phillips #2', 'Punta magnética, mango bimaterial.', 1, 3200.00, 30, 10, 1),
('HERR-003', 'Taladro percutor 650 W', 'Velocidad variable y función de percusión.', 1, 54900.00, 4, 3, 1),
('HERR-004', 'Juego de llaves combinadas 8 piezas', 'Medidas de 8 a 19 mm, acero cromo vanadio.', 1, 18900.00, 7, 5, 1),
('MAT-001', 'Bolsa de cemento 50 kg', 'Cemento de uso general para obra.', 2, 9500.00, 40, 15, 1),
('MAT-002', 'Arena fina (m³)', 'Arena para mezclas y revoques.', 2, 21000.00, 3, 6, 1),
('MAT-003', 'Ladrillo hueco 18x18x33', 'Unidad. Ladrillo cerámico para tabiques.', 2, 450.00, 500, 100, 1),
('MAT-004', 'Cal hidráulica 25 kg', 'Bolsa de cal para mezclas.', 2, 6800.00, 0, 4, 1),
('PINT-001', 'Látex interior blanco 20 L', 'Pintura acrílica lavable para interiores.', 3, 42000.00, 9, 4, 1),
('PINT-002', 'Esmalte sintético negro 1 L', 'Esmalte de secado rápido para metal y madera.', 3, 7900.00, 2, 5, 1),
('PINT-003', 'Rodillo antigota 22 cm', 'Rodillo de felpa con mango ergonómico.', 3, 4100.00, 18, 6, 1),
('PINT-004', 'Pincel de cerda 2"', 'Pincel para pinturas y esmaltes.', 3, 1500.00, 25, 8, 1);
