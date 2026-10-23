# Checklist manual de pruebas

Importar `sql/schema.sql` y luego `sql/seed.sql`, iniciar Apache y MySQL en XAMPP, y abrir `http://localhost/sifer`.

## Administrador

- [ ] Iniciar sesión con `admin` / `Admin123!` y comprobar que aparece el panel.
- [ ] Crear un producto de prueba con stock inicial `10` y stock mínimo `5`; comprobar que aparece en Productos.
- [ ] Editar el nombre o precio y comprobar que se guarda el cambio.
- [ ] Registrar una entrada de `3` unidades y comprobar que el stock queda en `13`.
- [ ] Registrar una salida de `2` unidades y comprobar que el stock queda en `11`.
- [ ] Intentar una salida mayor al stock disponible y comprobar que se rechaza sin dejar stock negativo.
- [ ] Registrar un ajuste con stock final `2` y comprobar que el stock queda en `2`.
- [ ] Comprobar que el producto aparece en la alerta de stock bajo al quedar por debajo del mínimo.
- [ ] Dar de baja lógicamente el producto; comprobar que ya no aparece entre los productos activos.
- [ ] Cerrar sesión y comprobar que vuelve a la pantalla de login.

## Usuario

- [ ] Iniciar sesión con `ventas` / `Ventas123!` y comprobar que se muestra el catálogo.
- [ ] Buscar un producto y filtrar por categoría.
- [ ] Comprobar que el catálogo indica disponibilidad o falta de disponibilidad, sin mostrar cantidades exactas de stock.
- [ ] Abrir directamente `http://localhost/sifer/public/dashboard.php` y comprobar que redirige al catálogo.
- [ ] Abrir directamente `http://localhost/sifer/public/productos.php` y comprobar que redirige al catálogo.

## Chequeos de seguridad revisados

- [ ] **Acceso por rol:** revisión estática de los controladores; cada controlador administrativo exige `require_admin()` y el catálogo exige `require_login()`. `require_admin()` también comprueba el rol en el servidor.
- [ ] **SQL:** revisión estática de los modelos; las consultas que incorporan valores variables usan sentencias PDO preparadas y parámetros. Las consultas dinámicas para límites usan valores enteros.
- [ ] **Escape HTML:** revisión estática de las vistas; los datos dinámicos se presentan mediante `e()`.
- [ ] **CSRF:** revisión estática de formularios y controladores; todos los formularios POST incluyen token y los controladores POST lo verifican. Como prueba manual, enviar una petición POST sin token o con token inválido y comprobar que se rechaza con HTTP 403.
- [ ] **Secretos:** revisión de los archivos versionados; no se encontraron credenciales privadas ni `config/config.local.php` o `.env` versionados. `sql/seed.sql` contiene solo cuentas de prueba con contraseñas almacenadas como hashes.
