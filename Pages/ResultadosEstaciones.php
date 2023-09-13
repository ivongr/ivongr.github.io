<?php
session_start();

$nombreUsuario = $_SESSION['nombre'];
$id_rol = $_SESSION['Id_Rol'];
include('../Transacciones/ValidarAutenticacion.php');

// Inicializa la variable de estación seleccionada
$estacionSeleccionada = null;

// Procesa el formulario si se envió
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $estacionSeleccionada = $_POST['selectEstacion'];

    // Almacena la estación seleccionada en una variable de sesión
    $_SESSION['estacionSeleccionada'] = $estacionSeleccionada;
}

include("../Transacciones/conexion.php");
include("../Layout/Navbar.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos por Estación</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../Diseno/estilos.css">

    <!-- Agrega las hojas de estilo de Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Asegúrate de incluir jQuery, ya que Select2 lo necesita -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Agrega el script de Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>

<body>
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
                Productos por Estación
            </h5>

            <div class="container">
                <!-- Formulario para seleccionar la estación -->
                <form method="POST" class="col-md-6 mb-6">
                    <div class="d-flex  align-items-center">
                        <label for="selectEstacion" class="mb-0 custom-label">Estación</label>
                        <select id="selectEstacion" name="selectEstacion" class="form-control" required>
                            <option value="">Selecciona una estación...</option> <!-- Opción vacía -->
                            <?php
                            // Consulta para obtener las estaciones desde la base de datos
                            $estacionesQuery = "SELECT Id, NombreEstacion FROM Estaciones";
                            $estacionesResultado = $cn->query($estacionesQuery);

                            while ($estacion = $estacionesResultado->fetch(PDO::FETCH_OBJ)) {
                                $selected = ($estacionSeleccionada == $estacion->Id) ? 'selected' : '';
                                echo '<option value="' . $estacion->Id . '" ' . $selected . '>' . $estacion->NombreEstacion . '</option>';
                            }
                            ?>
                        </select>
                        <button class="btn btn-outline-success btn-md" type="submit">Buscar</button>
                    </div>
                </form>

                <?php
                // Verifica si hay una estación seleccionada almacenada en la sesión
                if (isset($_SESSION['estacionSeleccionada'])) {
                    $estacionSeleccionada = $_SESSION['estacionSeleccionada'];
                }

              // Consulta SQL adaptada para filtrar por estación seleccionada
$sql = "SELECT
e.Id AS EstacionId,
e.NombreEstacion AS NombreEstacion,
pr.Id AS ProductoId,
pr.Numero,
pr.Cantidad,
pr.FechaEstimada,
pr.Precio,
pr.Total,
pr.Comentarios,
pr.Codigo,
pr.Fecha,
pro.Id AS Id_Producto,
pro.Descripcion AS NombreProducto,
pro.PlanoModelo,
marc.Marca,
uni.TipoUnidad,
mat.Material,
req.Id AS RequisicionId,
req.NombreReq,
proy.Id AS NombreProyectoId,
proy.NombreProyecto AS NombreProyecto,
em.Id AS EstatusMaquinadoId,
em.Estatus_Maquinado AS EstatusMaquinado,
ea.Id AS EstatusAlmacenId,
ea.Estatus_Almacen AS EstatusAlmacen
FROM ProductosRequisicion AS pr
INNER JOIN Productos AS pro ON pr.Id_Producto = pro.Id
INNER JOIN Marcas AS marc ON pro.Id_Marca = marc.Id
INNER JOIN Unidades AS uni ON pro.Id_Unidad = uni.Id
INNER JOIN Materiales as mat ON pro.Id_Material = mat.Id
INNER JOIN Estaciones AS e ON pr.Id_Estacion = e.Id
INNER JOIN Requisiciones AS req ON pr.Id_Requisicion = req.Id
INNER JOIN Proyectos as proy on req.Id_Proyecto = proy.Id
LEFT JOIN EstatusMaquinados AS em ON pr.Id_Estatus_Maquinado = em.Id
LEFT JOIN EstatusAlmacen AS ea ON pr.Id_EstatusAlmacen = ea.Id";

// Si se ha seleccionado una estación, agrega una cláusula WHERE para filtrar por esa estación
if ($estacionSeleccionada !== null) {
$sql .= " WHERE e.Id = :estacionId";
}

$sql .= " ORDER BY e.NombreEstacion";

$stmt = $cn->prepare($sql);

// Si se ha seleccionado una estación, asigna el valor correspondiente
if ($estacionSeleccionada !== null) {
$stmt->bindParam(':estacionId', $estacionSeleccionada, PDO::PARAM_INT);
} 

$stmt->execute();
$productosPorEstacion = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener una lista única de estaciones
$estacionesUnicas = array_unique(array_column($productosPorEstacion, 'EstacionId'));

// Verifica si hay alguna estación registrada
if (empty($estacionesUnicas)) {
// No hay estaciones registradas, muestra el mensaje de advertencia
echo '<div class="container">';
echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
echo '<img src="../Images/información.png" class="img-fluid" alt="sin imagen">';
echo '<strong>¡Información!</strong> No hay productos registrados para esta estación.';
echo '</div>';
echo '</div>';
} else {


                // Iterar sobre las estaciones únicas y generar una tabla para cada una
                foreach ($estacionesUnicas as $estacionId) {
                    echo '<h6 style="background-color: #B7C4B7; padding: 10px; width: 14%; color: #000000;"> Estación: ' . $productosPorEstacion[0]['NombreEstacion'] . '</h6>';
                    echo '<div class="container">';
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered">';
                    echo '<thead style="background-color: #DFF0D8;">';
                    echo '<tr class="text-center">';
                    echo '<th>Orden de compra</th>';
                    echo '<th>Proyecto</th>';
                    echo '<th>Descripción</th>';
                    echo '<th>Q</th>';
                    echo '<th>Código del producto/Plano modelo</th>';
                    echo '<th>Marca</th>';
                    echo '<th>Unidad</th>';
                    echo '<th>Estación</th>';
                    echo '<th>Req</th>';
                    echo '<th>Tipo de material</th>';
                    echo '<th>Comentarios</th>';
                    echo '<th>Estatus de almacén</th>';
                    echo '<th>Estatus de maquinados</th>';
                    echo '<th>Actualizar Estatus</th>';
                    echo '<th>Ver Entradas</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    foreach ($productosPorEstacion as $productoReq) {
                        if ($productoReq['EstacionId'] == $estacionId) {
                            echo '<tr class="text-center">';
                            // Agrega aquí las celdas correspondientes a cada columna
                            echo "<td>" . $productoReq['Codigo'] . "</td>";
                            echo "<td>" . $productoReq['NombreProyecto'] . "</td>";
                            echo "<td><textarea>" . $productoReq['NombreProducto'] . "</textarea></td>";
                            echo "<td>" . $productoReq['Cantidad'] .  "</td>";
                            echo "<td>" . $productoReq['PlanoModelo'] . "</td>";
                            echo "<td>" .  $productoReq['Marca'] . "</td>";
                            echo "<td>" . $productoReq['TipoUnidad'] . "</td>";
                            echo "<td>" . $productoReq['NombreEstacion'] . "</td>";
                            echo "<td>" . $productoReq['NombreReq'] . "</td>";
                            echo "<td>" . $productoReq['Material'] . "</td>";
                            echo "<td><textarea>" . $productoReq['Comentarios'] . "</textarea></td>";
                            // Determina el estatus y aplica la clase CSS correspondiente
                            $estatus = $productoReq['EstatusAlmacen'];
                            switch ($estatus) {
                                case 'Completado':
                                    echo "<td class='completado'>$estatus</td>";
                                    break;
                                case 'Pendiente':
                                    echo "<td class='pendiente'>$estatus</td>";
                                    break;
                                case 'Parcial':
                                    echo "<td class='parcial'>$estatus</td>";
                                    break;
                                case 'Sin Registró':
                                    echo "<td class='sin-registro'>$estatus</td>";
                                    break;
                                default:
                                    echo "<td>$estatus</td>"; // Manejo de casos no especificados
                            }

                            $estatusmaqui = $productoReq['EstatusMaquinado'];

                            switch ($estatusmaqui) {
                                case 'Disponible':
                                    echo "<td class='disponible'>$estatusmaqui</td>";
                                    break;
                                case 'Entregado':
                                    echo "<td class='entregado'>$estatusmaqui</td>";
                                    break;
                                case 'Calidad':
                                    echo "<td class='calidad'>$estatusmaqui</td>";
                                    break;
                                case 'Recubrimiento':
                                    echo "<td class='recubrimiento'>$estatusmaqui</td>";
                                    break;
                                case 'Sin Estatus':
                                    echo "<td class='sin-registro'>$estatusmaqui</td>";
                                    break;
                                default:
                                    echo "<td>$estatusmaqui</td>"; // Manejo de casos no especificados
                            }
                            echo "<td><a href='ActualizarEstatus.php?idp=" . $productoReq['ProductoId'] . "&idproyecto=" . $productoReq['NombreProyectoId'] . "&idRequisiciones=" . $productoReq['RequisicionId'] . "' class='btn' style='background-color:#FBF37F'>Estatus</a></td>";
                            echo "<td><a href='TablaEntradas.php?idProductosRequisicion=" . $productoReq['ProductoId'] . "' class='btn' style='background-color:#74baff'>Entradas</a></td>";
                            echo "<tr>";
                        }
                    }

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                    echo '</div>';
                }
            }
                ?>

            </div>
        </div>

        <h2></h2>
        <div class="row">
            <div class="col-12 text-center">
                <a href="Proyectos.php" class="btn btn-dark">
                    Volver
                </a>
            </div>
        </div>
        <!-- Agrega el script para habilitar la búsqueda en los ComboBox -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Aplicar Select2 al campo de selección
                $('#selectEstacion').select2({
                    placeholder: 'Selecciona una estación...',
                    width: '100%',
                    search: true, // Habilita la búsqueda
                });
            });
        </script>
        <script src="../js/bootstrap.min.js"></script>
    </div>

</body>

</html>