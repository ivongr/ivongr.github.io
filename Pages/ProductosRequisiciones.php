<?php
session_start();
include('../Transacciones/ValidarAutenticacion.php');

$nombreUsuario = $_SESSION['nombre'];
$id_rol = $_SESSION['Id_Rol'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Requisiciones</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../Diseno/estilos.css">
</head>

<body>


    <?php

    include("../Transacciones/conexion.php");
    include("../Layout/Navbar.php");

    ?>

    <div class="container">

        <h5></h5>
        <?php
 $nombreUsuario;
 $nombreMayuscula = ucwords($nombreUsuario);
  echo '<h6 style="background-color: #E6E6FA; padding: 10px; width: 15%; color: #000000;"> Usuario: ' . $nombreMayuscula . '</h6>';
?>
        <div class="card">
            <h5 class="card-header" style="background-color: #DFF0D8; color: #3C763D; margin-bottom: 0.5cm;">
                <img src="../Images/bandejaentradagreen.png" class="img-fluid" width="30" alt="sin imagen">
                Entradas de almacén
            </h5>


            <div class="container">
                <?php
                // Verificar si se ha proporcionado un ID de requisiciones en la URL
                if (isset($_GET['id']) && !empty($_GET['id'])) {
                    $idRequisiciones = $_GET['id'];

                    // Obtener información de la requisición
                    $consultaRequisiciones = "SELECT * FROM Requisiciones WHERE Id = :idRequisiciones";
                    $stmtRequisiciones = $cn->prepare($consultaRequisiciones);
                    $stmtRequisiciones->bindParam(':idRequisiciones', $idRequisiciones, PDO::PARAM_INT);
                    $stmtRequisiciones->execute();
                    $requisiciones = $stmtRequisiciones->fetch(PDO::FETCH_ASSOC);

                    // Obtener productos para la requisición prodcutos
                    $sql = "SELECT pr.Id, pr.Numero, pr.Cantidad, pr.FechaEstimada, pr.Precio, pr.Comentarios, pr.Total,pr.Id_Requisicion,
                    pro.Id AS ProductoId, pro.Descripcion AS NombreProducto, pro.PlanoModelo,marc.Marca,
                    uni.TipoUnidad,mat.Material,
                    e.Id AS EstacionId, e.NombreEstacion AS NombreEstacion,
                    req.Id AS RequisicionId, req.NombreReq,
                    proy.Id AS NombreProyectoId,proy.NombreProyecto AS NombreProyecto,
                    em.Id AS EstatusMaquinadoId, em.Estatus_Maquinado AS EstatusMaquinado,
                    ea.Id AS EstatusAlmacenId, ea.Estatus_Almacen AS EstatusAlmacen
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
                    WHERE pr.Id_Requisicion = :idRequisiciones";

                    $stmt = $cn->prepare($sql);
                    $stmt->bindParam(':idRequisiciones', $idRequisiciones, PDO::PARAM_INT);
                    $stmt->execute();
                    $requisicionesProduc = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    // Mostrar nombre de la requisición y productos asociados

                    if (is_array($requisiciones) && isset($requisiciones['NombreReq'])) {
                        echo '<h6 style="background-color: #B7C4B7; padding: 10px; width: 14%; color: #000000;"> Requisición : '
                            . $requisiciones['NombreReq'] . '</h6>';
                    } else {
                        echo '<h6 style="background-color: #B7C4B7; padding: 10px; width: 14%; color: #000000;"> Requisición no encontrada</h6>';
                    }
                    if (count($requisicionesProduc) > 0) {

                        echo '<div class="container">';
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-bordered">';
                        echo '<thead style="background-color: #DFF0D8;">';
                        echo '<tr class="text-center">';
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
                        $idProyecto = $requisiciones['Id_Proyecto'];
                        foreach ($requisicionesProduc as $productoReq) {
                            echo '<tr class=text-center>';
                            // Agrega aquí las celdas correspondientes a cada columna
                            echo "<td>" . $productoReq['NombreProyecto'] . "</td>";
                            echo "<td><textarea>" . $productoReq['NombreProducto'] . "</textarea></td>";
                            echo "<td>" . $productoReq['Cantidad'] .  "</td>";
                            echo "<td>" . $productoReq['PlanoModelo'] ."</td>";
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


                            echo "<td><a href='ActualizarEstatus.php?idp=" . $productoReq['Id'] . "&idproyecto=" . $idProyecto . "&idRequisiciones=" . $idRequisiciones . "' class='btn' style='background-color:#FBF37F'>Estatus</a></td>";


                            //SE ESTA REPITIENDO LAS SALIDAS
                            echo "<td><a href='TablaEntradas.php?idProductosRequisicion=" . $productoReq['Id'] . "' class='btn' 
                            style='background-color:#74baff'>Entradas</a></td>";

                            echo "<tr>";
                        }

                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                        echo '</div>';
                    } else {
                        echo '<p>No hay productos asociados a esta requisición.</p>';
                    }
                }
                ?>

            </div>
        </div>

        <h2></h2>
        <div class="row">
            <div class="col-12 text-center">

                <a href="RequisicionesProyecto.php?id=<?php echo $requisiciones['Id_Proyecto']; ?>" class="btn btn-dark">Volver </a>

            </div>
        </div>


        <script src="../js/bootstrap.min.js"></script>

</body>

</html>