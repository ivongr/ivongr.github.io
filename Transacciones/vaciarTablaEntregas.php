<?php
// Archivo vaciarEntregas.php

try {
    include "../Transacciones/conexion.php";

    // Consulta SQL para eliminar todos los registros de la tabla "Entregas"
    $sql = "DELETE FROM Entregas";

    // Ejecuta la consulta
    $resultado = $cn->exec($sql);

    if ($resultado !== false) {
        $response['success'] = true;
        $response['message'] = "Registros de la tabla 'EntregasProductos' vaciados exitosamente.";
       // echo "Registros de la tabla 'Entregas' vaciados exitosamente.";
    } else {
        $response['success'] = false;
        $response['message'] = "Error al vaciar la tabla 'EntregasProductos': " . print_r($cn->errorInfo(), true);
       // echo "Error al vaciar la tabla 'Entregas': " . print_r($cn->errorInfo(), true);
    }
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = "Error: " . $e->getMessage();
    //echo "Error: " . $e->getMessage();
}

// Devuelve la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>