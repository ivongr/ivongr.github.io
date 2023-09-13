<?php
session_start();

$nombreUsuario = $_SESSION['nombre'];
$id_rol = $_SESSION['Id_Rol'];
include('../Transacciones/ValidarAutenticacion.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de entregas</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>

    <?php
    include('../Transacciones/conexion.php');
    include("../Layout/Navbar.php");


    // Obtén la fecha de la URL
    $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';

    // Convierte la fecha en un formato compatible con la base de datos si es necesario

    // Consulta la base de datos utilizando la fecha
    $sql = "SELECT s.FolioSalida,s.CantidadSalida,s.Fecha,p.NombreProyecto,e.NombreEstacion,
    pr.Descripcion,pr.PlanoModelo,uni.TipoUnidad,em.Nombre,
    CONCAT(em.Nombre, ' ', em.ApePat, ' ', em.ApeMat) AS NombreCompleto, 
    ar.Id  AS IdAreaEmpleado, ar.Area AS Area
    FROM SalidasProductos as s
    INNER JOIN Proyectos as p ON s.Id_Proyecto = p.Id
    INNER JOIN Estaciones as e ON s.Id_Estacion = e.Id
    INNER JOIN Productos as pr ON s.Id_Producto = pr.Id
    INNER JOIN Unidades AS uni ON pr.Id_Unidad = uni.Id
    INNER JOIN Empleados as em ON s.Id_Empleado = em.Id
    INNER JOIN Areas as ar ON em.Id_Area = ar.Id
             WHERE s.Fecha = :fecha";

    $stmt = $cn->prepare($sql);
    $stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR);
    $stmt->execute();
    $salidas = $stmt->fetchAll(PDO::FETCH_OBJ);

    ?>
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
                <img src="../Images/buscar.png" class="img-fluid" width="30" alt="sin imagen">
                Buscar Entregas
            </h5>


            <div class="container">

                <div class="table-responsive">
                    <table class="table table-bordered">
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
                                <th>¿Se regresó el producto?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($salidas as $salida) {
                                echo "<tr>";
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

                                echo "<td><button style='background-color:#FBF37F' class='btn'
                                 onclick='validarEntrega(\"" . $salida->FolioSalida . "\")'>Validar Entrega</button></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <h2></h2>
    <div class="row">
        <div class="col-12 text-center">
            <a href="Salidas.php" class="btn btn-dark">
                Regresar
            </a>
            <h5></h5>
        </div>
    </div>

    <script src="../js/bootstrap.min.js"></script>
</body>

</html>

<div class="container">
    <footer>
        <p class="footer-text">Copyright &copy; 2007 - 2023 Antal Automation, S. De R.l. De C.V.</p>
        <!--<p>Desarrollado por <a href="#" class="card-link">Ivón García</a></p>-->
    </footer>
</div>