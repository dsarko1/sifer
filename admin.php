<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>Panel de Control - Administrador</h1>
        <nav>
            <a href="admin.php">Inicio</a>
            <a href="usuarios.php">Usuarios</a>
            <a href="configuracion.php">Configuración</a>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>
    <main>
        <section>
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_admin']); ?></h2>
            <p>Selecciona una opción del menú para administrar el sitio.</p>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Panel de Administrador</p>
    </footer>
</body>
</html>