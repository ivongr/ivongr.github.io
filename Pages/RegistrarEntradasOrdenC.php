<?php
session_start();
include("../Transacciones/ValidarAutenticacion.php");
include("../Transacciones/ValidarAutorizacionCRUD.php");
include("../Layout/Navbar.php");
include("../Transacciones/conexion.php");

$nombreUsuario = $_SESSION['nombre'];
$id_rol = $_SESSION['Id_Rol'];
// Verificar si se ha proporcionado un ID de productos de requisición en la URL
if (isset($_GET['idProductosRequisicion']) && !empty($_GET['idProductosRequisicion'])) {
    $idProductosRequisicion = $_GET['idProductosRequisicion'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener los valores de los campos del formulario
        $idNumeroEntrega = $_POST['CmbNumeroEntrega'];
        $folioEntrada = $_POST['txtFolioMasEntrada'];
        $folioFactura = $_POST['txtFolioFactura'];
        $cantidadEntrada = $_POST['txtCantidadEntrada'];
        $fechaRecibido = $_POST['txtFechaEntrada'];

        // Preparar la consulta SQL para verificar duplicados
        $sqlVerificarDuplicado = "SELECT COUNT(*) AS existe FROM Entradas WHERE Id_NumeroEntrega = ? AND FolioEntrada = ? AND FolioFactura = ? AND Id_ProductoReq = ?";
        $stmtVerificarDuplicado = $cn->prepare($sqlVerificarDuplicado);
        $stmtVerificarDuplicado->execute([$idNumeroEntrega, $folioEntrada, $folioFactura, $idProductosRequisicion]);
        $resultadoVerificarDuplicado = $stmtVerificarDuplicado->fetch(PDO::FETCH_ASSOC);

        if ($resultadoVerificarDuplicado["existe"] > 0) {
            echo "<script>alert('Ya existe una entrada con los mismos valores para este producto en la requisición. Revisa los siguientes valores que pueden estar duplicándose: Número de entrega de proveedor, Folio de Entrada, Folio de la Factura'); window.history.back();</script>";
            exit;
        }

        // Formatear la fecha
        $fechaFormateada = date('Y-m-d', strtotime($fechaRecibido));

        // Preparar la consulta SQL para la inserción de datos en la tabla 'Entradas'
        $cmd = "INSERT INTO Entradas (Id_NumeroEntrega, Id_ProductoReq, FolioEntrada, FolioFactura, CantidadEntrada, FechaRecibido)
                VALUES (?, ?, ?, ?, ?, ?)";
        $sql = $cn->prepare($cmd);
        $sql->execute([$idNumeroEntrega, $idProductosRequisicion, $folioEntrada, $folioFactura, $cantidadEntrada, $fechaFormateada]);

        try {
            // Obtener el ID de Producto relacionado
            $sqlObtenerIdProducto = "SELECT Id_Producto FROM ProductosRequisicion WHERE Id = ?";
            $stmtObtenerIdProducto = $cn->prepare($sqlObtenerIdProducto);
            $stmtObtenerIdProducto->execute([$idProductosRequisicion]);
            $idProducto = $stmtObtenerIdProducto->fetch(PDO::FETCH_COLUMN);

            // Actualizar el stock en la tabla 'Productos'
            $sqlActualizarStock = "UPDATE Productos SET Stock = Stock + ? WHERE Id = ?";
            $stmtActualizarStock = $cn->prepare($sqlActualizarStock);
            $stmtActualizarStock->execute([$cantidadEntrada, $idProducto]);

            // Redirigir a una página de éxito con el ID de productos de requisición
            header("Location: TablaEntradas.php?idProductosRequisicion=$idProductosRequisicion&success=true");
            exit;
        } catch (PDOException $e) {
            echo "Error en la actualización del stock: " . $e->getMessage();
        }
    }
} else {
    echo "<p>Error: Debes proporcionar el ID de productos de requisición.</p>";
    exit; // Terminar el script
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Más Entradas</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
    <div class="container">

    <h1></h1>
 <div class="container">
<?php
 $nombreUsuario;
 $nombreMayuscula = ucwords($nombreUsuario);
  echo '<h6 style="background-color: #E6E6FA; padding: 10px; width: 9%; color: #000000;"> Usuario: ' . $nombreMayuscula . '</h6>';
?> 
   </div>
        <form action="" method="post">
            <div class="container mt-3">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="CmbNumeroEntrega">Num.Entrega de proveedor</label>
                        <select name="CmbNumeroEntrega" id="CmbNumeroEntrega" class="form-select" required>
                            <option value="">Seleccione un número de Entrega...</option>
                            <?php
                            $sql = "SELECT * FROM NumeroEntregaProveedor ORDER BY NumeroEntrega";
                            $result = $cn->query($sql);
                            $cat = $result->fetchAll(PDO::FETCH_OBJ);
                            foreach ($cat as $c) {
                                echo "<option value='" . $c->Id . "'>" . $c->NumeroEntrega . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="txtFolioMasEntrada">Folio de Entrada</label>
                        <input type="text" id="txtFolioMasEntrada" name="txtFolioMasEntrada" placeholder="Folio Entrada" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="txtFolioFactura">Folio de la Factura</label>
                        <input type="text" id="txtFolioFactura" name="txtFolioFactura" placeholder="Folio Factura" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="txtCantidadEntrada">Cantidad de Entrada</label>
                        <input type="number" id="txtCantidadEntrada" name="txtCantidadEntrada" class="form-control" placeholder="Piezas disponibles" step="0.01" min="0" max="500" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="txtFechaEntrada">Fecha de Entrada</label>
                        <input type="date" id="txtFechaEntrada" name="txtFechaEntrada" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <input type="submit" class="btn btn-success" value="Guardar" />
                        <a href="TablaEntradasOrdenC.php?idProductosRequisicion=<?php echo $idProductosRequisicion; ?>" class="btn btn-dark">
                            Regresar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(function() {
            $("#txtFechaEntrada").datepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'HH:mm:ss'
            });
        });
    </script>
</body>

</html>
