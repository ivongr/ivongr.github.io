<head>
    <link rel="stylesheet" href="../Diseno/estilos.css">
</head>


<nav class="navbar navbar-expand-lg navbar-dark bg-custom">
    <div class="container-fluid">
        <span class="navbar-brand">Inventario</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">

                <?php if (isset($_SESSION['Id_Usuario'])) : ?>
                    <?php if ($_SESSION['Rol'] == 'user') : ?>

                
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="../Pages/Proyectos.php">
                                <img src="../Images/lista.png" width="20" alt="sin imagen"> Proyectos
                            </a>
                        </li>

                        
                        <div class="position-absolute top-50 end-0 translate-middle-y">
                            <li class="nav-item">
                                <a class="nav-link" href="../Transacciones/CerrarSesion.php">
                                    <img src="../Images/cerrarsesion.png" width="20" alt="sin imagen"> Cerrar Sesión
                                </a>
                            </li>
                        </div>


                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="../Pages/Proyectos.php">
                                <img src="../Images/lista.png" width="20" alt="sin imagen"> Proyectos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../Pages/Salidas.php">
                                <img src="../Images/salidas.png" width="20" alt="sin imagen"> Salidas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../Pages/Entregas.php">
                                <img src="../Images/bandejaentrada.png" width="20" alt="sin imagen"> Entregas
                            </a>
                        </li>
                       
                        <div class="position-absolute top-50 end-0 translate-middle">
                            <li class="nav-item">
                                <a class="nav-link" href="../Transacciones/CerrarSesion.php">
                                    <img src="../Images/cerrarsesion.png" width="20" alt="sin imagen"> Cerrar Sesión
                                </a>
                            </li>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../Pages/Login.php">Iniciar Sesión</a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>