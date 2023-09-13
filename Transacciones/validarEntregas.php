<?php
include('../Transacciones/conexion.php');
?>

<?php
// Verificar que la solicitud se haya realizado mediante el método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los datos enviados desde JavaScript (por ejemplo, el ID de la salida)
    $folioSalida = $_POST["FolioSalida"];

    try {
        // Consulta SQL para almacenar la entrega en la tabla de entregas
        $sqlInsertEntrega = "INSERT INTO Entregas (FolioSalida, CantidadSalida, Fecha, Id_Proyecto, Id_Estacion, Id_Producto, Id_Empleado) 
        SELECT FolioSalida, CantidadSalida, Fecha, Id_Proyecto, Id_Estacion, Id_Producto, Id_Empleado
        FROM SalidasProductos
        WHERE FolioSalida = :folioSalida";

        $stmtInsertEntrega = $cn->prepare($sqlInsertEntrega);
        $stmtInsertEntrega->bindParam(":folioSalida", $folioSalida);
        $stmtInsertEntrega->execute();

        // Actualizar el stock en la tabla Productos
        $sqlActualizarStock = "UPDATE Productos 
        SET Stock = Stock + (SELECT CantidadSalida FROM SalidasProductos WHERE FolioSalida = :folioSalida)
        WHERE Id = (SELECT Id_Producto FROM SalidasProductos WHERE FolioSalida = :folioSalida)";

        $stmtActualizarStock = $cn->prepare($sqlActualizarStock);
        $stmtActualizarStock->bindParam(":folioSalida", $folioSalida);
        $stmtActualizarStock->execute();

        // Eliminar la entrada de la tabla SalidasProductos
        $sqlEliminarSalida = "DELETE FROM SalidasProductos WHERE FolioSalida = :folioSalida";
        $stmtEliminarSalida = $cn->prepare($sqlEliminarSalida);
        $stmtEliminarSalida->bindParam(":folioSalida", $folioSalida);
        $stmtEliminarSalida->execute();

        // Si llegamos aquí, la entrega se ha almacenado correctamente en la base de datos.
        echo "Entrega validada correctamente";
    } catch (PDOException $e) {
        // Si hay un error en la base de datos, capturamos la excepción y enviamos un mensaje de error.
        echo "Error al validar la entrega: " . $e->getMessage();
    }
} else {
    // Si la solicitud no es mediante el método POST, enviar un mensaje de error.
    echo "Método no permitido";
}
?>
