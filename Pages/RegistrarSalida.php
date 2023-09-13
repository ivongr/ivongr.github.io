<?php
session_start();
$nombreUsuario = $_SESSION['nombre'];
$id_rol = $_SESSION['Id_Rol'];
include("../Transacciones/ValidarAutenticacion.php");
include("../Transacciones/ValidarAutorizacionCRUD.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Salidas</title>
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
    include("../Layout/Navbar.php");
    include("../Transacciones/conexion.php");

    ?>

    <div class="container">
    <h1></h1>
 <div class="container">
<?php
 $nombreUsuario;
 $nombreMayuscula = ucwords($nombreUsuario);
  echo '<h6 style="background-color: #E6E6FA; padding: 10px; width: 15%; color: #000000;"> Usuario: ' . $nombreMayuscula . '</h6>';
?> 
</div>
        <form action="../Transacciones/registrarSalidas.php" method="post">
            <div class="container mt-3">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="txtFolioSalida">Folio de salida</label>
                        <input type="text" id="txtFolioSalida" name="txtFolioSalida" placeholder="Folio salida" class="form-control" required>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label for="CmbProyecto">Proyecto</label>
                        <select name="CmbProyecto" id="CmbProyecto" class="form-select" required>
                            <option value="">Selecciona un proyecto...</option> <!-- Opción vacía -->
                            <?php
                            $sql = "SELECT * FROM Proyectos ORDER BY NombreProyecto;";
                            $result = $cn->query($sql);
                            $cat = $result->fetchAll(PDO::FETCH_OBJ);
                            foreach ($cat as $c) {
                                echo "<option value='" . $c->Id . "'>" . $c->NombreProyecto . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="CmbEstacion">Estación</label>
                        <select name="CmbEstacion" id="CmbEstacion" class="form-select" required>
                            <option value="">Selecciona una estación...</option> <!-- Opción vacía -->
                            <?php
                            $sql = "SELECT * FROM Estaciones ORDER BY NombreEstacion";
                            $result = $cn->query($sql);
                            $cat = $result->fetchAll(PDO::FETCH_OBJ);
                            foreach ($cat as $c) {
                                echo "<option value='" . $c->Id . "'>" . $c->NombreEstacion . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="CmbCodigoProducto">Código del producto</label>
                        <select name="CmbCodigoProducto" id="CmbCodigoProducto" class="form-select" required>
                            <option value="">Seleccione un código...</option> <!-- Opción vacía -->
                            <?php
                            $sql = "SELECT * FROM Productos ORDER BY PlanoModelo";
                            $result = $cn->query($sql);
                            $cat = $result->fetchAll(PDO::FETCH_OBJ);
                            foreach ($cat as $c) {
                                echo "<option value='" . $c->Id . "'>" . $c->PlanoModelo . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>


                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label for="txtFecha">Fecha</label>
                        <input type="date" id="txtFecha" name="txtFecha" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="txtStock">Cantidad/Stock</label>
                        <input type="number" id="txtStock" name="txtStock" class="form-control" id="cantidadInput" placeholder="Piezas de salidas" step="0.01" min="0" max="500" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="CmbPersonal">Trabajador</label>
                        <select name="CmbPersonal" id="CmbPersonal" class="form-select" required>
                            <option value="">Selecciona un trabajador...</option> <!-- Opción vacía -->
                            <!-- Consulta para concatenar los apellidos-->
                            <?php
                            $sql = "SELECT  Id, CONCAT(Nombre, ' ', ApePat, ' ', ApeMat) AS NombreCompleto FROM Empleados ORDER BY NombreCompleto";
                            $result = $cn->query($sql);
                            $cat = $result->fetchAll(PDO::FETCH_OBJ);
                            foreach ($cat as $c) {
                                echo "<option value='" . $c->Id . "'>" . $c->NombreCompleto . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <input type="submit" class="btn btn-success" value="Guardar" onsubmit="return validarStock();" />
                    <a href="Salidas.php" class="btn btn-dark">
                        Volver
                    </a>
                </div>
            </div>

        </form>

    </div>


    <!--para cambiar el formato de la fecha -->
    <script>
        $(function() {
            $("#txtFecha").datepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'HH:mm:ss' // Agrega el formato de hora, minutos y segundos
            });
        });
    </script>

    <!-- Agrega el script para habilitar la búsqueda en los ComboBox -->
    <script>
        $(document).ready(function() {
            // Inicializa Select2 en los ComboBox y habilita la búsqueda
            $('.form-select').select2({
                placeholder: "Selecciona una opción...",
                allowClear: true
            });
        });
    </script>

    

  
</body>


</html>