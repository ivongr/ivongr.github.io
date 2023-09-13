<?php
session_start();
include('../Transacciones/ValidarAutenticacion.php');
include('../Transacciones/conexion.php');
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $foliosalida = $_POST['txtFolioSalida'];
$idproyecto = $_POST['CmbProyecto'];
$idestacion = $_POST['CmbEstacion'];
$idplanoModelo = $_POST['CmbCodigoProducto'];
$fecha = $_POST['txtFecha'];
$stock = $_POST['txtStock'];
$idpersonal = $_POST['CmbPersonal'];

   

    // Verificar si ya existe un registro con el mismo folio de salida
    $sqlVerificar = "SELECT COUNT(*) AS existe FROM SalidasProductos WHERE FolioSalida = ?";
    $stmtVerificar = $cn->prepare($sqlVerificar);
    $stmtVerificar->execute([$foliosalida]);
    $resultadoVerificar = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

    if ($resultadoVerificar["existe"] > 0) {
        // Mostrar una alerta al usuario
        echo "<script>alert('El folio de salida ya existe'); window.history.back();</script>";
        exit;
    }

    // Obtener el stock disponible para el producto seleccionado
    $sqlStock = "SELECT Stock FROM Productos WHERE Id = ?";
    $stmtStock = $cn->prepare($sqlStock);
    $stmtStock->execute([$idplanoModelo]);
    $stockDisponible = $stmtStock->fetchColumn();

    if ($stock > $stockDisponible) {
        // Mostrar una alerta al usuario
        echo "<script>alert('La cantidad ingresada supera el stock disponible'); window.history.back();</script>";
        exit;
    }

    // Convertir la fecha al formato correcto (YYYY-MM-DD HH:MM:SS)
    $fechaFormateada = date('Y-m-d H:i:s', strtotime($fecha));

    // Iniciar una transacción para asegurar la consistencia de la base de datos
    $cn->beginTransaction();

    try {
        // Realizar la inserción en la tabla SalidasProductos
        $cmd = "INSERT INTO SalidasProductos (FolioSalida, CantidadSalida, Fecha, Id_Proyecto, Id_Estacion, Id_Producto, Id_Empleado) " .
            "VALUES (?, ?, ?, ?, ?, ?, ?)";
        $sql = $cn->prepare($cmd);
        $sql->execute([$foliosalida, $stock, $fechaFormateada, $idproyecto, $idestacion, $idplanoModelo, $idpersonal]);

        // Actualizar el stock en la tabla Productos
        $nuevoStock = $stockDisponible - $stock;
        $cmdActualizarStock = "UPDATE Productos SET Stock = ? WHERE Id = ?";
        $sqlActualizarStock = $cn->prepare($cmdActualizarStock);
        $sqlActualizarStock->execute([$nuevoStock, $idplanoModelo]);

        // Confirmar la transacción
        $cn->commit();

        // Redirigir a la página de salidas después de la inserción exitosa
        header("Location: ../Pages/Salidas.php?success=true");
  
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $cn->rollback();

        // Mostrar mensaje de error y regresar
        echo "<script>alert('Error al registrar la salida'); window.history.back();</script>";
        exit;
    }
}

?>