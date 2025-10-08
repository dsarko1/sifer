<?php
session_start(); 
require 'sifer_db.php';

if (!isset($_SESSION['nombre'])) {

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    
    <title>Sifer</title> 
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="./imgs/logo.png" alt="">
                </span>

                <div class="text logo-text">
                    <span class="name" id="nombre">
    <?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Invitado'; ?>
                    </span>
                    <span class="profession">7°4° 2025</span>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">

                <li class="search-box">
                    <i class='bx bx-search-alt icon'></i>
                    <input type="text" placeholder="Buscar...">
                </li>

                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="./index.php">
                            <i class='bx bx-home-alt-2 icon' ></i>
                            <span class="text nav-text">Inicio</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-bar-chart-square icon' ></i>
                            <span class="text nav-text">Stock</span>
                        </a>
                    </li>    

          <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 1): ?>
            <li class="nav-link">
                <a href="./admin.php">
                    <i class='bx bx-list-ul-square icon'></i>
                    <span class="text nav-text">Administración</span>
                </a>
            </li>
        <?php endif; ?>

                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="login.php">
                        <i class='bx bx-user-hexagon icon' ></i>
                        <span class="text nav-text">Iniciar Sesión</span>
                    </a>
                </li>

                <li class="mode">
                    <div class="sun-moon">
                        <i class='bx bx-moon icon moon'></i>
                        <i class='bx bx-sun icon sun'></i>
                    </div>
                    <span class="mode-text text">Tema</span>

                    <div class="toggle-switch">
                        <span class="switch"></span>
                    </div>
                </li>
                
            </div>
        </div>
    </nav>

    <section class="home">
    <?php
$sql = "SELECT idProducto, nombreProducto, cantidad, descripcion FROM productos";
$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        echo "
        <div class='card'>
            <div class='cont'>
                " . htmlspecialchars($row['nombreProducto']) . "
                <div class='cont2'>
                    Unidades Disponibles: " . htmlspecialchars($row['cantidad']) . "
                </div>
                <div class='desc'>
                    " . htmlspecialchars($row['descripcion']) . "
                </div>
            </div>
        </div>
        ";
    }
} else {
    echo "<p>No hay productos en el stock.</p>";
}
?>

    </section>
  </main>

    <script src="./scripts/script.js"></script>

</body>
</html>