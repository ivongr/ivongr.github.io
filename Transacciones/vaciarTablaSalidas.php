<?php
// Archivo vaciarTablaSalidas.php

$response = array();

try {
    include "../Transacciones/conexion.php";

    // Consulta SQL para eliminar todos los registros de la tabla "SalidasProductos"
    $sql = "DELETE FROM SalidasProductos";

    // Ejecuta la consulta
    $resultado = $cn->exec($sql);

    if ($resultado !== false) {
        $response['success'] = true;
        $response['message'] = "Registros de la tabla 'SalidasProductos' vaciados exitosamente.";
    } else {
        $response['success'] = false;
        $response['message'] = "Error al vaciar la tabla 'SalidasProductos': " . print_r($cn->errorInfo(), true);
    }
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = "Error: " . $e->getMessage();
}

// Devuelve la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
