<?php
session_start();
require 'sifer_db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['usuario']);
    $password = ($_POST['contraseña']);
    $default_rol = 2;

    if(strlen($username) < 3 || strlen($password) < 4){
        $_SESSION['register_error'] = "Usuario o contraseña demasiado cortos.";
        header("Location: registro.php");
        exit();
    }

    $check = $conn->prepare("SELECT id FROM usuarios WHERE nombre = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows > 0){
        $_SESSION['register_error'] = "Ese nombre ya está ocupado.";
        header("Location: registro.php");
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, contraseña, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $hashed, $default_rol);

    if($stmt->execute()){
        $_SESSION['register_success'] = "Registro exitoso. ¡Ya puedes iniciar sesión!";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['register_error'] = "Error al registrar el usuario.";
        header("Location: registro.php");
        exit();
    }
}
?>
