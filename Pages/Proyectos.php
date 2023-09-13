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
    <title>Proyectos</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../Diseno/estilos.css">

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

    // Antes de generar los enlaces de paginación, calcula el total de filas en la base de datos
    $totalFilasQuery = "SELECT COUNT(*) FROM Proyectos";
    $totalFilasResultado = $cn->query($totalFilasQuery);
    $totalFilas = $totalFilasResultado->fetchColumn();

    // Define la cantidad de elementos por página
    $elementosPorPagina = 18;

    // Obtén el número de página actual desde la URL
    $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

    // Calcula el valor OFFSET
    $offset = ($paginaActual - 1) * $elementosPorPagina;

    // Consultar las entregas validadas desde la base de datos con LIMIT y OFFSET
    $sql = "select p.NombreProyecto 
	        from Proyectos as p
            LIMIT $elementosPorPagina OFFSET $offset;";

    $resultado = $cn->query($sql);
    $proyectos = $resultado->fetchAll(PDO::FETCH_OBJ);
    ?>
    <h2></h2>



    <div class="container">


        <?php
        $nombreUsuario;
        $nombreMayuscula = ucwords($nombreUsuario);
        echo '<h6 style="background-color: #E6E6FA; padding: 10px; width: 15%; color: #000000;"> Usuario: ' . $nombreMayuscula . '</h6>';
        ?>


        <div class="card">
            <h5 class="card-header" style="background-color: #DFF0D8; color: #3C763D; margin-bottom: 0.5cm;">
                <img src="../Images/buscar.png" class="img-fluid" width="30" alt="sin imagen">
                Proyectos
            </h5>
            <div class="row">


                <form action="ResultadosEstaciones.php" method="post" class="col-md-5 mb-2">
                    <div class="d-flex align-items-center">
                        <label for="selectEstacion" class="mb-0 custom-label">Estación</label>
                        <select id="selectEstacion" name="selectEstacion" class="form-control" required>
                            <option value="">Selecciona una estación...</option> <!-- Opción vacía -->
                            <?php
                            // Consulta para obtener las estaciones desde la base de datos
                            $estacionesQuery = "SELECT Id, NombreEstacion FROM Estaciones";
                            $estacionesResultado = $cn->query($estacionesQuery);

                            while ($estacion = $estacionesResultado->fetch(PDO::FETCH_OBJ)) {
                                echo '<option value="' . $estacion->Id . '">' . $estacion->NombreEstacion . '</option>';
                            }
                            ?>
                        </select>
                        <button class="btn btn-outline-success btn-md" type="submit" id="btnBuscarEstacion">
                            Buscar
                        </button>
                    </div>
                </form>

                <form action="ResultadosOrdenCompra.php" method="post" class="col-md-6 mb-6">
                    <div class="d-flex align-items-center">
                        <label for="selectOrdenCompra" class="mb-0 custom-label" style="white-space: nowrap;">Orden de Compra</label>

                        <select id="selectOrdenCompra" name="selectOrdenCompra" class="form-control">
                            <option value="">Selecciona una orden de compra...</option>
                            <?php
                            // Consulta para obtener las órdenes de compra desde la base de datos
                            $ordenesCompraQuery = "SELECT DISTINCT Codigo FROM ProductosRequisicion";
                            $ordenesCompraResultado = $cn->query($ordenesCompraQuery);

                            while ($ordenCompra = $ordenesCompraResultado->fetch(PDO::FETCH_OBJ)) {
                                echo '<option value="' . $ordenCompra->Codigo . '">' . $ordenCompra->Codigo . '</option>';
                            }
                            ?>
                        </select>
                        <button class="btn btn-outline-success btn-md" type="submit" id="btnBuscarOrdenCompra">
                            Buscar
                        </button>

                    </div>
                </form>



            </div>



            <div class="row">
                <div class="col-md-12">
                    <div class="card-body">
                        <div class="row" id="filaProyectos">

                            <?php
                            $sql = "SELECT Id, NombreProyecto FROM Proyectos ORDER BY NombreProyecto";
                            $resultado = $cn->query($sql);
                            $proyectos = $resultado->fetchAll(PDO::FETCH_OBJ);

                            $porPagina = 18;
                            $numPaginas = ceil(count($proyectos) / $porPagina);

                            if (!isset($_GET['pagina'])) {
                                $paginaActual = 1;
                            } else {
                                $paginaActual = $_GET['pagina'];
                            }

                            $inicio = ($paginaActual - 1) * $porPagina;
                            for ($i = $inicio; $i < min($inicio + $porPagina, count($proyectos)); $i++) {
                                if ($i % 6 === 0) {
                                    echo '</div><div class="row">';
                                }
                                echo '<div class="col-md-2 col-sm-6 mb-4">';
                                echo '  <div class="card h-100" style="width: 100%;">';
                                echo '    <div class="card-body d-flex flex-column align-items-center">';
                                echo '      <h5 class="card-title text-center">' . $proyectos[$i]->NombreProyecto . '</h5>';
                                echo '      <a href="RequisicionesProyecto.php?id=' . $proyectos[$i]->Id .
                                    '" class="btn btn-outline-primary btn-sm mx-auto mb-2">Requisiciones</a>'; // Corregir aquí
                                echo '    </div>';
                                echo '  </div>';
                                echo '</div>';
                            }

                            // Cierra la última fila si no está cerrada
                            if (count($proyectos) % 6 !== 0) {
                                echo '</div>';
                            }
                            ?>

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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Aplicar Select2 al campo de selección
            $('#selectOrdenCompra').select2({
                placeholder: 'Selecciona una orden de compra...',
                width: '100%',
                search: true, // Habilita la búsqueda
            });
        });
    </script>
    <h6></h6>
    <script src="../js/bootstrap.min.js"></script>


</body>

</html>
<div class="container">
    <footer>
        <p class="footer-text">Copyright &copy; 2007 - 2023 Antal Automation, S. De R.l. De C.V.</p>
        <!--<p>Desarrollado por <a href="#" class="card-link">Ivón García</a></p>-->
    </footer>
</div>