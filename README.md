# Sifer

## Requisitos

- XAMPP con Apache, PHP 8.0 o posterior y MySQL/MariaDB (MariaDB 10.4 o MySQL 8).
- Extensión PDO para MySQL habilitada en PHP.

## Instalación en XAMPP

1. Copiar el proyecto a `C:\xampp\htdocs\sifer`.
2. Iniciar Apache y MySQL desde el panel de XAMPP.
3. Abrir phpMyAdmin e importar `sql/schema.sql`.
4. Importar `sql/seed.sql` después del esquema.
5. Abrir `http://localhost/sifer`.

La configuración local es opcional. Si la instalación usa datos distintos a los valores predeterminados de XAMPP, copiar `config/config.example.php` como `config/config.local.php` y ajustar allí la conexión.

## Usuarios de prueba

- Admin: `admin` / `Admin123!`
- Usuario: `ventas` / `Ventas123!`

## Alcance v1

Incluye inicio de sesión por rol, consulta del catálogo, gestión administrativa de productos, movimientos de stock y alertas de stock bajo. Las categorías se cargan con los datos de prueba; no se incluye su gestión desde la aplicación.

## Tests

Instalá las dependencias con `composer install` y ejecutá la suite con `composer test`. En XAMPP, usá `C:\xampp\php\php.exe`: el `php` de `PATH` puede no tener habilitada la extensión `pdo_mysql`. Los tests usan la base aparte `sifer_test`, no la base de la aplicación.
