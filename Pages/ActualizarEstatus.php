<?php
session_start();
include('../Transacciones/conexion.php');
include("../Transacciones/ValidarAutenticacion.php");
include("../Transacciones/ValidarAutorizacionCRUD.php");


$nombreUsuario = $_SESSION['nombre'];
$id_rol = $_SESSION['Id_Rol'];

$idp = $_GET['idp'];
$cmd = "SELECT * FROM ProductosRequisicion WHERE Id=" . $idp;
$resultado = $cn->query($cmd);
$producto = $resultado->fetch(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Estatus</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>

    <?php
    include("../Layout/Navbar.php");

    ?>
    <h1></h1>
    <div class="container">
        <?php
        $nombreUsuario;
        $nombreMayuscula = ucwords($nombreUsuario);
        echo '<h6 style="background-color: #E6E6FA; padding: 10px; width: 15%; color: #000000;"> Usuario: ' . $nombreMayuscula . '</h6>';
        ?>
    </div>
    <form action="../Transacciones/actualizarEstatus.php" method="post">
        <div class="container mt-3">

            <input type="hidden" name="id" value="<?php echo $producto->Id; ?>" />

            <?php
            //$id = $_GET['id'];
            $idp = $_GET['idp']; // Obtener el valor de 'idp' desde la URL
            $idproyecto = $_GET['idproyecto']; // Obtener el valor de 'idproyecto' desde la URL
            $idRequisiciones = $_GET['idRequisiciones'];


            /* $sql = "SELECT pr.Id,pr.Id_Requisicion,
            pro.Id AS ProductoId, pro.Descripcion AS NombreProducto,
            req.Id AS RequisicionId, req.NombreReq,
            proy.Id AS NombreProyectoId,proy.NombreProyecto AS NombreProyecto
            FROM ProductosRequisicion AS pr
            INNER JOIN Productos AS pro ON pr.Id_Producto = pro.Id
            INNER JOIN Requisiciones AS req ON pr.Id_Requisicion = req.Id
            INNER JOIN Proyectos as proy on req.Id_Proyecto = proy.Id";
            
            $stmt = $cn->query($sql);
            $requisiciones = $stmt->fetchAll(PDO::FETCH_ASSOC);*/


            /* echo "ID del producto: " . $idp . "<br>";
            echo "ID del proyecto: " . $idproyecto . "<br>";
            echo "ID de la requisición: " . $idRequisiciones . "<br>";*/ ?>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="CmbEstatusAlmacen">Estatus/Almacén</label>
                    <select name="CmbEstatusAlmacen" id="CmbEstatusAlmacen" class="form-select">
                        <option value="">Selecciona un status...</option> <!-- Opción vacía -->
                        <?php
                        $sql = "SELECT * FROM EstatusAlmacen ORDER BY Estatus_Almacen";
                        $result = $cn->query($sql);
                        $cat = $result->fetchAll(PDO::FETCH_OBJ);
                        foreach ($cat as $c) {

                            echo "<option value='" . $c->Id . "'>" . $c->Estatus_Almacen . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="CmbEstatusMaquinado">Estatus Maquinados</label>
                    <select name="CmbEstatusMaquinado" id="CmbEstatusMaquinado" class="form-select">
                        <option value="">Selecciona un estatus...</option> <!-- Opción vacía -->
                        <?php
                        $sql = "SELECT * FROM EstatusMaquinados ORDER BY Estatus_Maquinado";
                        $result = $cn->query($sql);
                        $cat = $result->fetchAll(PDO::FETCH_OBJ);
                        foreach ($cat as $c) {

                            echo "<option value='" . $c->Id . "'>" . $c->Estatus_Maquinado . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-12 text-center">
            <input type="submit" class="btn btn-success" value="Guardar" />

            <a href="ResultadosEstaciones.php?id=" class="btn btn-dark">Volver </a>


        </div>


    </form>


    <h6></h6>

</body>

</html>