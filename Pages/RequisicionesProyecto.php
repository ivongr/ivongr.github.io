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
    <title>Requisiciones</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../Diseno/estilos.css">
</head>

<body>

    <?php
    include("../Layout/Navbar.php");
    include("../Transacciones/conexion.php");

    // Verificar si se ha proporcionado un ID de proyecto en la URL
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $idProyecto = $_GET['id'];

        // Obtener información del proyecto
        $consultaProyecto = "SELECT * FROM Proyectos WHERE Id = :idProyecto";
        $stmtProyecto = $cn->prepare($consultaProyecto);
        $stmtProyecto->bindParam(':idProyecto', $idProyecto, PDO::PARAM_INT);
        $stmtProyecto->execute();
        $proyecto = $stmtProyecto->fetch(PDO::FETCH_ASSOC);

        // Obtener requisiciones para el proyecto
        $consultaRequisiciones = "SELECT * FROM Requisiciones WHERE Id_Proyecto = :idProyecto";
        $stmtRequisiciones = $cn->prepare($consultaRequisiciones);
        $stmtRequisiciones->bindParam(':idProyecto', $idProyecto, PDO::PARAM_INT);
        $stmtRequisiciones->execute();
        $requisiciones = $stmtRequisiciones->fetchAll(PDO::FETCH_ASSOC);

        // Mostrar nombre del proyecto y requisiciones
        echo '<h6> ' . $proyecto['NombreProyecto'] . '</h6>';
        if (count($requisiciones) > 0) {
            echo '<ul>';
        } else {
            echo '<div class="container">';
            echo ' <div class="alert alert-warning alert-dismissible fade show" role="alert">';
            echo '<img src="../Images/información.png" class="img-fluid" alt="sin imagen">
             <strong>¡Información!</strong> No hay requisiciones registradas para este proyecto.
          </div>';
            echo '</div>';
     
        }
    }



    // Antes de generar los enlaces de paginación, calcula el total de filas en la base de datos
    $totalFilasQuery = "SELECT COUNT(*) FROM Requisiciones";
    $totalFilasResultado = $cn->query($totalFilasQuery);
    $totalFilas = $totalFilasResultado->fetchColumn();

    // Define la cantidad de elementos por página
    $elementosPorPagina = 18;

    // Obtén el número de página actual desde la URL
    $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

    // Calcula el valor OFFSET
    $offset = ($paginaActual - 1) * $elementosPorPagina;

    // Consultar las entregas validadas desde la base de datos con LIMIT y OFFSET
    $sql = "select r.NombreReq 
	        from Requisiciones as r
            LIMIT $elementosPorPagina OFFSET $offset;";

    $resultado = $cn->query($sql);
    $requisiciones = $resultado->fetchAll(PDO::FETCH_OBJ);
    ?>

    <h2></h2>

    <div class="container">

    <h1></h1>
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
                Requisición
            </h5>

            <div class="row">
                <div class="col-md-12">
                    <div class="card-body">
                        <div class="row" id="filaRequisiciones">
                            <?php
                            // Cambia la consulta SQL para obtener las requisiciones asociadas al proyecto específico
                            $consultaRequisiciones = "SELECT Id,NombreReq FROM Requisiciones WHERE Id_Proyecto = :idProyecto ORDER BY NombreReq";
                            $stmtRequisiciones = $cn->prepare($consultaRequisiciones);
                            $stmtRequisiciones->bindParam(':idProyecto', $idProyecto, PDO::PARAM_INT);
                            $stmtRequisiciones->execute();
                            $requisiciones = $stmtRequisiciones->fetchAll(PDO::FETCH_OBJ);

                            $porPagina = 18;
                            $numPaginas = ceil(count($requisiciones) / $porPagina);

                            if (!isset($_GET['pagina'])) {
                                $paginaActual = 1;
                            } else {
                                $paginaActual = $_GET['pagina'];
                            }

                            $inicio = ($paginaActual - 1) * $porPagina;
                            for ($i = $inicio; $i < min($inicio + $porPagina, count($requisiciones)); $i++) {
                                if ($i % 6 === 0) {
                                    echo '</div><div class="row">';
                                }
                                echo '<div class="col-md-2 col-sm-6 mb-4">';
                                echo '  <div class="card h-100" style="width: 100%;">'; // Agrega la clase h-100
                                echo '    <div class="card-body d-flex flex-column align-items-center">';
                                echo '      <h5 class="card-title text-center">' . $requisiciones[$i]->NombreReq . '</h5>';
                                echo '    <a href="ProductosRequisiciones.php?id=' . $requisiciones[$i]->Id .
                                    '" class="btn btn-outline-primary btn-sm mx-auto mb-2">Productos Requisiciones</a>';
                                echo '    </div>';
                                echo '  </div>';
                                echo '</div>';
                            }

                            // Cierra la última fila si no está cerrada
                            if (count($requisiciones) % 6 !== 0) {
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <h1></h1>
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

    <h2></h2>
    <div class="row">
        <div class="col-12 text-center">
            <a href="Proyectos.php" class="btn btn-dark">
                Volver
            </a>
        </div>
    </div>

    <script src="../js/bootstrap.min.js"></script>

</body>

</html>