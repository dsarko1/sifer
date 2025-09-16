<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sifer";  // o sifer_db si ese era el nombre correcto

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
