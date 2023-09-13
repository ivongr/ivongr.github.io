<?php
session_start();

$nombreUsuario = $_SESSION['nombre'];
$id_rol = $_SESSION['Id_Rol'];
include('../Transacciones/ValidarAutenticacion.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../Diseno/estilos.css">
</head>

<body>

    <?php
    include("../Layout/Navbar.php");
    include("../Transacciones/conexion.php");
    ?>

    <h6></h6>
    <div class="container">
    <div class="container">
<?php
 $nombreUsuario;
 $nombreMayuscula = ucwords($nombreUsuario);
  echo '<h6 style="background-color: #E6E6FA; padding: 10px; width: 15%; color: #000000;"> Usuario: ' . $nombreMayuscula . '</h6>';
?> 
</div>
        <div class="card">
            <h5 class="card-header" style="background-color: #DFF0D8; color: #3C763D; margin-bottom: 0.5cm;">
                <img src="../Images/resultadoproyecto.png" class="img-fluid" width="30" alt="sin imagen">
                Resultados de Búsqueda
            </h5>

            <div class="row">
                <div class="col-md-12">
                    <div class="card-body">
                        <div class="row" id="filaProyectos">
                            <?php
                            if (isset($_POST['txtProyecto'])) {
                                $filtro = $_POST['txtProyecto'];
                                $sql = "SELECT * FROM Proyectos WHERE NombreProyecto LIKE '%$filtro%'";
                                $resultado = $cn->query($sql);
                                $proyectos = $resultado->fetchAll(PDO::FETCH_OBJ);

                                foreach ($proyectos as $proyecto) {
                                    echo '<div class="col-md-2 col-sm-6 mb-4">';
                                    echo '  <div class="card h-100" style="width: 100%;">'; // Agrega la clase h-100
                                    echo '    <div class="card-body d-flex flex-column align-items-center">';
                                    echo '      <h5 class="card-title text-center">' . $proyecto->NombreProyecto . '</h5>';
                                    echo '      <a href="RequisicionesProyectos.php" class="btn btn-outline-primary btn-sm mx-auto mb-2">Productos requisiciones</a>';
                                    echo '    </div>';
                                    echo '  </div>';
                                    echo '</div>';
                                }
                            }

                            ?>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <h6></h6>
    <div class="row">
        <div class="col-12 text-center">
            <a href="Proyectos.php" class="btn btn-dark">
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