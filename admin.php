<?php
session_start(); 
require 'sifer_db.php';

// verificacion
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header('Location: index.php');
    exit();
}

// conexion
try {
    $pdo = new PDO("mysql:host=localhost;dbname=sifer;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$usuarios_totales = 0;
$productos_stock = 0;
$productos_bajos = 0;

try {
    // usarios totales
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $usuarios_totales = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // productos en stock
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos");
    $productos_stock = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // productos bajos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE cantidad < 10");
    $productos_bajos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
} catch (PDOException $e) {
    error_log("Error al obtener estadísticas: " . $e->getMessage());
}

$ultimos_registros = [];
try {
    $stmt = $pdo->query("SELECT u.nombre, r.nombreRol, u.id FROM usuarios u 
                         JOIN roles r ON u.rol = r.idRol 
                         ORDER BY u.id DESC LIMIT 5");
    $ultimos_registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener últimos registros: " . $e->getMessage());
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
    
    <title>Administración - Sifer</title> 
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <a href="index.php"><img src="./imgs/logo.png" alt="logo"></a>
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
                        <a href="./stock.php">
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
                    <a href="logout.php">
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">Cerrar Sesión</span>
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
    <section class="banner">
        <h1 class="texto" style="text-shadow: #00000071 1px 0 10px;">Panel de Administración</h1>
    </section>

    <section class="admin-content">
        <div class="cards">
            <div class="card">
                <i class='bx bx-user icon card-icon'></i>
                <div class="card-content">
                    <div class="cont"><?php echo $usuarios_totales; ?></div>
                    <div class="cont2">Usuarios Totales</div>
                </div>
            </div>

            <div class="card">
                <i class='bx bx-package icon card-icon'></i>
                <div class="card-content">
                    <div class="cont"><?php echo $productos_stock; ?></div>
                    <div class="cont2">Productos en Stock</div>
                </div>
            </div>

            <div class="card">
                <i class='bx bx-low-vision icon card-icon'></i>
                <div class="card-content">
                    <div class="cont"><?php echo $productos_bajos; ?></div>
                    <div class="cont2">Productos Bajos</div>
                </div>
            </div>
        </div>

            <div class="admin-section">
                <h2>Gestión de Usuarios</h2>
                <div class="admin-actions">
                    <button class="admin-btn" onclick="abrirModal('agregarUsuario')">
                        <i class='bx bx-user-plus'></i>
                        Agregar Usuario
                    </button>
                    <button class="admin-btn" onclick="mostrarUsuarios()">
                        <i class='bx bx-edit-alt'></i>
                        Editar Usuarios
                    </button>
                    <button class="admin-btn" onclick="mostrarUsuarios()">
                        <i class='bx bx-cog'></i>
                        Configurar Roles
                    </button>
                </div>
                
                <div class="table-container" style="margin-top: 20px;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimos_registros as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nombreRol']); ?></td>
                                <td>
                                    <button class="admin-btn-small" onclick="editarUsuario(<?php echo $usuario['id']; ?>)">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class="admin-btn-small btn-danger" onclick="eliminarUsuario(<?php echo $usuario['id']; ?>)">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-section">
                <h2>Gestión de Productos</h2>
                <div class="admin-actions">
                    <button class="admin-btn" onclick="abrirModal('agregarProducto')">
                        <i class='bx bx-plus-circle'></i>
                        Agregar Producto
                    </button>
                    <button class="admin-btn" onclick="window.location.href='stock.php'">
                        <i class='bx bx-bar-chart-alt-2'></i>
                        Ver Stock Completo
                    </button>
                    <button class="admin-btn">
                        <i class='bx bx-download'></i>
                        Exportar Datos
                    </button>
                </div>
            </div>

            <div class="admin-section">
                <h2>Configuración del Sistema</h2>
                <div class="admin-actions">
                    <button class="admin-btn">
                        <i class='bx bx-backup'></i>
                        Respaldar Datos
                    </button>
                    <button class="admin-btn">
                        <i class='bx bx-reset'></i>
                        Limpiar Registros
                    </button>
                    <button class="admin-btn">
                        <i class='bx bx-stats'></i>
                        Generar Reportes
                    </button>
                </div>
            </div>
        </section>
    </section>

    <!-- hola -->
    <div id="modalAgregarUsuario" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal('agregarUsuario')">&times;</span>
            <h3>Agregar Nuevo Usuario</h3>
            <form id="formAgregarUsuario" method="POST" action="agregar_usuario.php">
                <div class="form-group">
                    <label for="nombreUsuario">Nombre de usuario:</label>
                    <input type="text" id="nombreUsuario" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="contraseña">Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" required>
                </div>
                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select id="rol" name="rol" required>
                        <option value="1">Administrador</option>
                        <option value="2">Usuario común</option>
                    </select>
                </div>
                <button type="submit" class="admin-btn">Agregar Usuario</button>
            </form>
        </div>
    </div>

    <script src="./scripts/script.js"></script>
    <script>
        function abrirModal(tipo) {
            document.getElementById('modal' + tipo.charAt(0).toUpperCase() + tipo.slice(1)).style.display = 'block';
        }

        function cerrarModal(tipo) {
            document.getElementById('modal' + tipo.charAt(0).toUpperCase() + tipo.slice(1)).style.display = 'none';
        }

        function mostrarUsuarios() {
            alert('Función para mostrar todos los usuarios - Pendiente de implementar');
        }

        function editarUsuario(id) {
            alert('Editando usuario ID: ' + id + ' - Pendiente de implementar');
        }

        function eliminarUsuario(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
                alert('Eliminando usuario ID: ' + id + ' - Pendiente de implementar');
            }
        }

        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let modal of modals) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        }
    </script>

</body>
</html>