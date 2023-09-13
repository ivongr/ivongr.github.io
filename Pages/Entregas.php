<?php
session_start();
include('../Transacciones/ValidarAutenticacion.php');
include("../Transacciones/ValidarAutorizacionCRUD.php");

$nombreUsuario = $_SESSION['nombre'];
$id_rol = $_SESSION['Id_Rol'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos entregados</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
      
     <!-- Agrega las hojas de estilo de Select2 NO -->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<!-- Asegúrate de incluir jQuery, ya que Select2 lo necesita -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Agrega el script de Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<!-- Agrega esta línea después de la referencia a jQuery -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

</head>

<body>

    <?php
    include('../Transacciones/conexion.php');
    include("../Layout/Navbar.php");

    // Antes de generar los enlaces de paginación, calcula el total de filas en la base de datos
    $totalFilasQuery = "SELECT COUNT(*) FROM Entregas";
    $totalFilasResultado = $cn->query($totalFilasQuery);
    $totalFilas = $totalFilasResultado->fetchColumn();

    // Define la cantidad de elementos por página
    $elementosPorPagina = 10;

    // Obtén el número de página actual desde la URL
    $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

    // Calcula el valor OFFSET
    $offset = ($paginaActual - 1) * $elementosPorPagina;

    // Consultar las entregas validadas desde la base de datos con LIMIT y OFFSET
    $sql = "SELECT en.FolioSalida,en.CantidadSalida,en.Fecha,p.NombreProyecto,e.NombreEstacion,
    pr.Descripcion,pr.PlanoModelo,uni.TipoUnidad,em.Nombre,ar.Area,
    CONCAT(em.Nombre, ' ', em.ApePat, ' ', em.ApeMat) AS NombreCompleto
    FROM Entregas as en
    INNER JOIN Proyectos as p ON en.Id_Proyecto = p.Id
    INNER JOIN Estaciones as e ON en.Id_Estacion = e.Id
    INNER JOIN Productos as pr ON en.Id_Producto = pr.Id
    INNER JOIN Unidades AS uni ON pr.Id_Unidad = uni.Id
    INNER JOIN Empleados as em ON en.Id_Empleado = em.Id
    INNER JOIN Areas as ar ON em.Id_Area = ar.Id
    ORDER BY en.FolioSalida 
    LIMIT $elementosPorPagina OFFSET $offset;";

    $resultado = $cn->query($sql);
    $salidas = $resultado->fetchAll(PDO::FETCH_OBJ);
    ?>


    <div class="container">
        <h2></h2>

        <?php
 $nombreUsuario;
 $nombreMayuscula = ucwords($nombreUsuario);
  echo '<h6 style="background-color: #E6E6FA; padding: 10px; width: 15%; color: #000000;"> Usuario: ' . $nombreMayuscula . '</h6>';
?>
        <div class="card">
            <h5 class="card-header" style="background-color: #DFF0D8; color: #3C763D; margin-bottom: 0.5cm;">
                <img src="../Images/entregado.png" class="img-fluid" width="30" alt="sin imagen">
                Productos entregados
            </h5>

            <div class="container">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <form action="ResultadosSalidaCodigo.php" method="post">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="CmbCodigoProducto" class="mb-0 custom-label">Código</label>
                                <select name="CmbCodigoProducto" id="CmbCodigoProducto" class="form-control" required>
                                    <option value="">Selecciona código...</option> <!-- Opción vacía -->
                                    <?php
                                    $sql = "SELECT * FROM Productos ORDER BY PlanoModelo";
                                    $result = $cn->query($sql);
                                    $cat = $result->fetchAll(PDO::FETCH_OBJ);
                                    foreach ($cat as $c) {
                                        echo "<option value='" . $c->PlanoModelo . "'>" . $c->PlanoModelo . "</option>";
                                    }
                                    ?>
                                </select>

                                <button class="btn btn-outline-success btn-md" type="submit" id="btnBuscar" onclick="return buscar();">
                                    Buscar
                                </button>
                            </div>
                        </form>
                    </div>



                    <div class="col-md-6 mb-3">
                        <form action="ResultadosSalida.php" method="get"> <!-- Cambia "post" a "get" para pasar la fecha en la URL -->
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="txtFechaSalida" class="mb-0 custom-label">Fecha</label>
                                <input type="date" id="txtFechaSalida" name="fecha" placeholder="Fecha de Salida" class="form-control" required>
                                <button class="btn btn-outline-success btn-md" type="submit" id="btnBuscar" onclick="return buscarPorFecha();">
                                    Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button style="background-color: #bb0a3f;" class="btn text-white me-md-2" id="vaciarTabla" onclick="return validarEliminado();">
                            <img src="../Images/eliminar.png" width="21" alt="25">Eliminar registros
                        </button>
                    </div>
                </div>
            </div>



            <div class="container">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <h2></h2>
                        <thead style="background-color: #DFF0D8;">
                            <tr class="text-center">

                                <th>Folio de Salida</th>
                                <th>Cantidad</th>
                                <th>Fecha de Salida</th>
                                <th>Proyecto</th>
                                <th>Estación</th>
                                <th>Descripción</th>
                                <th>Código de Producto</th>
                                <th>Unidades</th>
                                <th>Empleado</th>
                                <th>Área</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($salidas as $salida) {
                                echo "<tr class=text-center>";

                                echo "<td>" . $salida->FolioSalida . "</td>";
                                echo "<td>" . $salida->CantidadSalida . "</td>";
                                echo "<td>" . $salida->Fecha . "</td>";
                                echo "<td>" . $salida->NombreProyecto . "</td>";
                                echo "<td>" . $salida->NombreEstacion . "</td>";
                                echo "<td>" . $salida->Descripcion . "</td>";
                                echo "<td>" . $salida->PlanoModelo . "</td>";
                                echo "<td>" . $salida->TipoUnidad . "</td>";
                                echo "<td>" . $salida->NombreCompleto . "</td>";
                                echo "<td>" . $salida->Area . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <h5></h5>

            <div class="container">
                <nav aria-label="...">
                    <ul class="pagination justify-content-end">
                        <?php for ($i = 1; $i <= ceil($totalFilas / $elementosPorPagina); $i++) { ?>
                            <li class="page-item <?php if ($i == $paginaActual) echo 'active'; ?>">
                                <a class="page-link" href="?pagina=<?php echo $i; ?>" style="<?php if ($i == $paginaActual)
                                                                                                    echo 'background-color: #3C763D; color: white;';
                                                                                                else echo 'color: #3C763D;'; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php } ?>

                    </ul>
                </nav>
            </div>
        </div>

    </div>
    <h1></h1>

     <!-- Agrega el script para habilitar la búsqueda en los ComboBox -->
     <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Aplicar Select2 al campo de selección
                $('#CmbCodigoProducto').select2({
                    placeholder: 'Seleccione un código del producto...',
                    width: '100%',
                    search: true, // Habilita la búsqueda
                });
            });
        </script>
    <!--MODAL PARA CONFIRMAR LA ELIMINACIÓN DE LOS REGISTRO DE LA TABLA ENTREGAS-->

    <!-- Bootstrap JS y jQuery (asegúrate de utilizar la versión adecuada de jQuery) -->
   <!--- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
   <!--- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.min.js"></script>-->


    <div class="modal fade" id="confirmarModal" tabindex="-1" aria-labelledby="confirmarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center mt-4">
                    <img src="../Images/alerta.png" width="120" alt="120">
                </div>
                <div class="modal-body text-center">
                    <h5 class="mb-3">¿Estás seguro de que deseas eliminar todos los registros?</h5>
                    <p class="text-muted">Esta acción es irreversible. No podrás recuperar los datos después.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-md mr-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger btn-md" id="confirmarBtn">Eliminar registros</button>
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
                        <div>Los registros de la tabla 'Entregas'</div>
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
        //VALIDAR LA ENTRADA Y QUE SE MUESTRE UN MODAL
        function validarEliminado() {
            // Abre el modal de confirmación
            $('#confirmarModal').modal('show');
        }

        // Agrega el evento al botón "Confirmar" del modal
        $('#confirmarBtn').click(function() {
            $.ajax({
                type: "POST",
                url: "../Transacciones/vaciarTablaEntregas.php", // Ruta al script PHP en tu servidor
                success: function(response) {
                    //alert(response); // Muestra una alerta con la respuesta del servidor
                    // Puedes realizar otras acciones aquí después de vaciar la tabla si es necesario
                    // Por ejemplo, recargar la página para mostrar la tabla actualizada
                    //location.reload();
                    if (response.success) {
                        alert(response.message); // Muestra una alerta con el mensaje de éxito
                        // Abre el modal de éxito
                        //console.log("Antes de abrir el modal de éxito");
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
            $('#confirmarModal').modal('hide');
        });
    </script>


    <script>
        function validarCampo() {
            let codigoInput = document.getElementById("CmbCodigoProducto");
            let codigoValue = codigoInput.value.trim();

            if (codigoValue === "") {
                alert("Por favor, ingresa un código válido.");
                return false; // Evita que el formulario se envíe si la validación falla.
            }

            // Si la validación pasa, el formulario se enviará.
            return true;
        }

        function buscar() {
            if (validarCampo()) {
                // Obtén el valor del campo de búsqueda
                let codigoValue = document.getElementById("CmbCodigoProducto").value;

                // Redirige a la página de resultados de búsqueda
                window.location.href = "ResultadosSalidaCodigo.php?codigo=" + codigoValue;

                return false; // Evita que el formulario se envíe ya que redirigimos manualmente.
            } else {
                return false; // Evita que el formulario se envíe si la validación falla.
            }
        }
    </script>

    <script>
        function validarCampoFecha() {
            let fechaInput = document.getElementById("txtFechaSalida");
            let fechaValue = fechaInput.value;

            if (fechaValue === "") {
                alert("Por favor, selecciona una fecha válida.");
                return false;
            }

            // La validación de la fecha podría ser más específica aquí si es necesario
            return true;
        }

        function buscarPorFecha() {
            if (validarCampoFecha()) {
                // Obtén el valor del campo de fecha
                let fechaValue = document.getElementById("txtFechaSalida").value;

                // Redirige a la página de resultados de búsqueda por fecha
                window.location.href = "ResultadosSalidaFecha.php?fecha=" + fechaValue;

                return false;
            } else {
                return false;
            }
        }
    </script>

    <script src="../js/bootstrap.min.js"></script>

</body>

</html>

<div class="container">
    <footer>
        <p class="footer-text">Copyright &copy; 2007 - 2023 Antal Automation, S. De R.l. De C.V.</p>
        <!--<p>Desarrollado por <a href="#" class="card-link">Ivón García</a></p>-->
    </footer>
</div>