<?php
session_start();
$nombreUsuario = $_SESSION['nombre'];
$id_rol = $_SESSION['Id_Rol'];
include('../Transacciones/ValidarAutenticacion.php');
include("../Transacciones/ValidarAutorizacionCRUD.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entradas</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>
    <?php
    include('../Transacciones/conexion.php');
    include("../Layout/Navbar.php");

    // Verificar si se ha proporcionado un ID de productos de requisición en la URL
    if (isset($_GET['idProductosRequisicion']) && !empty($_GET['idProductosRequisicion'])) {
        $idProductosRequisicion = $_GET['idProductosRequisicion'];

        // Consulta SQL para seleccionar las entradas relacionadas con los productos de la requisición específica
        $sql = "SELECT e.Id, n.NumeroEntrega, e.FolioEntrada, e.FolioFactura, e.CantidadEntrada, e.FechaRecibido
        FROM Entradas AS e
        INNER JOIN ProductosRequisicion AS pr ON e.Id_ProductoReq = pr.Id
        INNER JOIN NumeroEntregaProveedor AS n ON e.Id_NumeroEntrega = n.Id
        WHERE pr.Id = :idProductosRequisicion
        ORDER BY n.NumeroEntrega";


        $stmt = $cn->prepare($sql);
        $stmt->bindParam(':idProductosRequisicion', $idProductosRequisicion, PDO::PARAM_INT);
        $stmt->execute();
        $entradas = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
        // Si no se proporciona el ID de productos de requisición, mostrar un mensaje de error
        echo "<p>Error: Debes proporcionar el ID de productos de requisición.</p>";
        exit; // Terminar el script
    }
    ?>

    <div class="container">
        <h2></h2>

        <div class="container">

            <h5></h5>
            <div class="container">
                <?php
                $nombreUsuario;
                $nombreMayuscula = ucwords($nombreUsuario);
                echo '<h6 style="background-color: #E6E6FA; padding: 10px; width: 15%; color: #000000;"> Usuario: ' . $nombreMayuscula . '</h6>';
                ?>
            </div>
            <div class="card">
                <h5 class="card-header" style="background-color: #DFF0D8; color: #3C763D; margin-bottom: 0.5cm;">
                    <img src="../Images/bandejaentradagreen.png" class="img-fluid" width="30" alt="sin imagen">
                    Entradas
                </h5>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="RegistrarEntradas.php?idProductosRequisicion=<?php echo $idProductosRequisicion; ?>" style="background-color: #62BA62;" class="btn text-white me-md-2">
                        <img src="../Images/signomas.png" width="15" alt="20"> Nueva Entrada
                    </a>
                </div>

                <div class="d-flex justify-content-start">
                    <button style="background-color: #bb0a3f;" class="btn text-white me-md-2" id="vaciarTabla" onclick="return validarEliminado();">
                        <img src="../Images/eliminar.png" width="21" alt="25">Eliminar registros
                    </button>
                </div>

                <div class="container">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <h2></h2>
                            <thead style="background-color: #DFF0D8;">
                                <tr class="text-center">
                                    <th>Num.Entrega de proveedor</th>
                                    <th>Folio de Entrada</th>
                                    <th>Folio de la Factura</th>
                                    <th>Q</th>
                                    <th>Fecha de Recibido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($entradas as $entrada) {
                                    echo "<tr>";
                                    echo "<td style='text-align: center;'>" . $entrada->NumeroEntrega . "</td>";
                                    echo "<td style='text-align: center;'>" . $entrada->FolioEntrada . "</td>";
                                    echo "<td style='text-align: center;'>" . $entrada->FolioFactura . "</td>";
                                    echo "<td style='text-align: center;'>" . $entrada->CantidadEntrada . "</td>";
                                    echo "<td style='text-align: center;'>" . $entrada->FechaRecibido . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <h6></h6>
        <div class="row">
            <div class="col-12 text-center">
                <a href="ResultadosEstaciones.php" class="btn btn-dark">
                    Volver
                </a>
            </div>
        </div>


        <!--MODAL PARA CONFIRMAR LA ELIMINACIÓN DE LOS REGISTRO DE LA TABLA ENTRADAS-->

        <!-- Bootstrap JS y jQuery (asegúrate de utilizar la versión adecuada de jQuery) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.min.js"></script>



        <div class="modal fade" id="confirmarModalEliminar" tabindex="-1" aria-labelledby="confirmarModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmarModalLabel">Confirmación de Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="../Images/alerta.png" width="120" alt="120">
                        <h5>¿Estás seguro de que deseas eliminar todos los registros?</h5>
                        <p class="fw-light">Esta acción es irreversible. No podrás recuperar los datos después.</p>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger btn-md" id="confirmarEliminarBtn">Eliminar registros</button>

                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL PARA MOSTRAR LA ACCIÓN EXITOSA -->

        <div class="modal fade" id="exitoModal" tabindex="-1" aria-labelledby="exitoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="text-center">
                        <img src="../Images/exito.png" width="120" alt="120">
                    </div>
                    <div class="modal-body text-center">
                        <h5 class="modal-title">
                            <div>Los registros de la tabla 'Entradas'</div>
                            <div>se han vaciado exitosamente.</div>
                        </h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-md mx-auto" data-bs-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Función para validar la eliminación y mostrar el modal de confirmación
            function validarEliminado() {
                // Abre el modal de confirmación
                $('#confirmarModalEliminar').modal('show');
            }

            // Agrega el evento al botón "Confirmar" del modal de confirmación
            $('#confirmarEliminarBtn').click(function() {
                $.ajax({
                    type: "POST",
                    url: "../Transacciones/vaciarTablaEntradas.php", // Ruta al script PHP en tu servidor
                    data: {
                        idProductosRequisicion: <?php echo $idProductosRequisicion; ?> // Envía el ID de productos requisición al script PHP
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message); // Muestra una alerta con el mensaje de éxito
                            // Abre el modal de éxito
                            $('#exitoModal').modal('show');
                            // Puedes realizar otras acciones aquí después de vaciar la tabla si es necesario
                            // Por ejemplo, recargar la página para mostrar la tabla actualizada
                            //location.reload();
                            setTimeout(function() {
                                location.reload(); // Esto recargará la página después de que el usuario haga clic en "Aceptar"
                            }, 1200); // Cambia el valor si deseas un retraso diferente

                        } else {
                            console.error("Error al vaciar la tabla: " + response.message);
                        }
                    },
                    error: function(error) {
                        console.error("Error al vaciar la tabla: " + JSON.stringify(error));
                    }
                });

                // Cierra el modal de confirmación
                $('#confirmarModalEliminar').modal('hide');
            });
        </script>

</body>

</html>
<div class="container">
    <footer>
        <p class="footer-text">Copyright &copy; 2007 - 2023 Antal Automation, S. De R.l. De C.V.</p>
        <!--<p>Desarrollado por <a href="#" class="card-link">Ivón García</a></p>-->
    </footer>
</div>