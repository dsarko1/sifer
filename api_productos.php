<?php
// ✅ CONFIGURACIÓN INICIAL - Headers para permitir CORS
header('Content-Type: application/json');           // Decimos que devolvemos JSON
header('Access-Control-Allow-Origin: *');           // Permitir acceso desde cualquier origen (apps, otros sitios)
header('Access-Control-Allow-Methods: GET, POST');  // Métodos HTTP permitidos
header('Access-Control-Allow-Headers: Content-Type'); // Headers permitidos

// ✅ INCLUIR LA CONEXIÓN A LA BASE DE DATOS
require 'sifer_db.php';  // Tu archivo de conexión existente

try {
    // ✅ PREPARAR Y EJECUTAR LA CONSULTA SQL
    $sql = "SELECT idProducto, nombreProducto, cantidad, descripcion FROM productos";
    $resultado = $pdo->query($sql);  // Ejecutar la consulta
    
    // ✅ OBTENER TODOS LOS RESULTADOS COMO ARRAY ASOCIATIVO
    $productos = $resultado->fetchAll(PDO::FETCH_ASSOC);
    // PDO::FETCH_ASSOC = Cada fila como array asociativo ['idProducto' => 1, 'nombreProducto' => 'Laptop', ...]
    
    // ✅ VERIFICAR SI HAY PRODUCTOS
    if ($productos) {
        // ✅ ÉXITO: Convertir el array de productos a JSON y enviarlo
        echo json_encode([
            'success' => true,       // Indicador de éxito
            'data' => $productos,    // Los productos en formato JSON
            'count' => count($productos) // Cantidad total de productos
        ]);
    } else {
        // ✅ NO HAY PRODUCTOS: Enviar respuesta vacía pero exitosa
        echo json_encode([
            'success' => true,
            'data' => [],           // Array vacío
            'count' => 0,
            'message' => 'No hay productos en el stock'
        ]);
    }
    
} catch (PDOException $e) {
    // ❌ ERROR: Capturar y reportar cualquier error de la base de datos
    echo json_encode([
        'success' => false,
        'error' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
?>