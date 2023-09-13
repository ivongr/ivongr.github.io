<?php
session_start();


$nombreUsuario = $_SESSION['nombre'];
$id_rol = $_SESSION['Id_Rol'];
include('../Transacciones/ValidarAutenticacion.php');
include("../Transacciones/conexion.php");
include("../Layout/Navbar.php");

// Inicializa la variable de orden de compra seleccionada
$ordenCompraSeleccionada = null;

// Procesa el formulario si se envió
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ordenCompraSeleccionada = $_POST['selectOrdenCompra'];

    // Almacena la selección en una variable de sesión
    $_SESSION['ordenCompraSeleccionada'] = $ordenCompraSeleccionada;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Orden de Compra</title>
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
                Productos por orden de compra
            </h5>


            <div class="container">
                <!-- Formulario para seleccionar la orden de compra -->
                <form method="POST" class="col-md-6 mb-6">
                    <div class="d-flex  align-items-center">
                        <label for="selectOrdenCompra" class="mb-0 custom-label" style="white-space: nowrap;">Orden de Compra</label>
                        <select id="selectOrdenCompra" name="selectOrdenCompra" class="form-control" required>
                            <option value="">Selecciona una orden de compra...</option> <!-- Opción vacía -->
                            <?php
                            // Consulta para obtener las órdenes de compra desde la base de datos
                            $ordenesCompraQuery = "SELECT DISTINCT Codigo FROM ProductosRequisicion";
                            $ordenesCompraResultado = $cn->query($ordenesCompraQuery);

                            while ($ordenCompra = $ordenesCompraResultado->fetch(PDO::FETCH_OBJ)) {
                                $selected = ($ordenCompraSeleccionada == $ordenCompra->Codigo) ? 'selected' : '';
                                echo '<option value="' . $ordenCompra->Codigo . '" ' . $selected . '>' . $ordenCompra->Codigo . '</option>';
                            }
                            ?>
                        </select>
                        <button class="btn btn-outline-success btn-md" type="submit">Buscar</button>
                    </div>
                </form>
                <!-- Verifica si la variable $ordenCompraSeleccionada está definida antes de mostrar el título -->
                <?php if (isset($ordenCompraSeleccionada)) : ?>
                    <h6 style="background-color: #B7C4B7; padding: 10px; width: 14%; color: #000000;"> Orden de compra: <?php echo htmlspecialchars($ordenCompraSeleccionada); ?></h6>
                <?php endif; ?>
                <?php
                // Verifica si hay una orden de compra seleccionada almacenada en la sesión
                if (isset($_SESSION['ordenCompraSeleccionada'])) {
                    $ordenCompraSeleccionada = $_SESSION['ordenCompraSeleccionada'];
                }

                // Consulta SQL adaptada para filtrar por orden de compra seleccionada
                $consultaResultados = "SELECT
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
                        LEFT JOIN EstatusAlmacen AS ea ON pr.Id_EstatusAlmacen = ea.Id
                        WHERE Codigo = :ordenCompraSeleccionada";

                $stmt = $cn->prepare($consultaResultados);
                $stmt->bindParam(':ordenCompraSeleccionada', $ordenCompraSeleccionada, PDO::PARAM_STR);
                $stmt->execute();
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($resultados) > 0) {
                    // Muestra los resultados en una tabla
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

                    foreach ($resultados as $productoReq) {
                        echo '<tr class="text-center">';
                        // Agrega aquí las celdas correspondientes a cada columna
                        echo "<td>" . $productoReq['Codigo'] . "</td>";
                        echo "<td>" . $productoReq['NombreProyecto'] . "</td>";
                        echo "<td><textarea>" . $productoReq['NombreProducto'] . "</textarea></td>";
                        echo "<td>" . $productoReq['Cantidad'] .  "</td>";
                        echo "<td>" . $productoReq['PlanoModelo'] . "</td>";
                        echo "<td>" . $productoReq['Marca'] . "</td>";
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
                        echo "<td><a href='ActualizarEstatusOrdenC.php?idp=" . $productoReq['ProductoId'] . "&idproyecto=" . $productoReq['NombreProyectoId'] . "&idRequisiciones=" . $productoReq['RequisicionId'] . "' class='btn' style='background-color:#FBF37F'>Estatus</a></td>";
                        echo "<td><a href='TablaEntradasOrdenC.php?idProductosRequisicion=" . $productoReq['ProductoId'] . "' class='btn' style='background-color:#74baff'>Entradas</a></td>";
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                } else {
                    // Muestra un mensaje si no se encontraron resultados
                    echo '<p class="text-center">No se encontraron productos para la orden de compra seleccionada.</p>';
                }
                ?>
            </div>
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
            $('#selectOrdenCompra').select2({
                placeholder: 'Selecciona una orde de compra...',
                width: '100%',
                search: true, // Habilita la búsqueda
            });
        });
    </script>
</body>

</html>