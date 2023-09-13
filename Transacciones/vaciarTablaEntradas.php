<?php
// Archivo vaciarTablaSalidas.php

$response = array();

try {
    include "../Transacciones/conexion.php";

    // ID de productos requisición a eliminar (debe recibir este valor desde la solicitud AJAX)
    $idProductosRequisicion = $_POST['idProductosRequisicion'];

    // Consulta SQL para eliminar todos los registros de la tabla "Entradas" con el ID de productos requisición especificado
    $sql = "DELETE FROM Entradas WHERE Id_ProductoReq = :idProductosRequisicion";

    // Prepara y ejecuta la consulta
    $stmt = $cn->prepare($sql);
    $stmt->bindParam(':idProductosRequisicion', $idProductosRequisicion, PDO::PARAM_INT);
    $resultado = $stmt->execute();

    if ($resultado) {
        $response['success'] = true;
        $response['message'] = "Registros de la tabla 'Entradas' vaciados exitosamente.";
    } else {
        $response['success'] = false;
        $response['message'] = "Error al vaciar la tabla 'Entradas': " . print_r($stmt->errorInfo(), true);
    }
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = "Error: " . $e->getMessage();
}

// Devuelve la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
