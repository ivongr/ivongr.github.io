<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
  
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>
    <?php
    include('../Transacciones/conexion.php');
    //include("../Layout/Navbar.php");
    ?>
    <form action="../Transacciones/ValidarSesion.php" method="post">

        <div class="row mt-2 ">
            <div class="col-xs-12 offset-sm-2 col-sm-8 offset-md-4 col-md-4 offset-lg-4 col-lg-4">

                <div class="card">
                    <h5 class="card-header bg-success text-white text-center">Iniciar Sesión</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <img src="https://antalmexico.com/Logo_antal.jpg" class="img-fluid" width="200" alt="sin imagen">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <label>Usuario</label>
                                <input type="text" name='txtUsuario' placeholder="Ingrese su usuario" class="form-control form-control-lg" />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="txtPassword" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" name="txtPassword" id="txtPassword" placeholder="Ingrese su contraseña" class="form-control form-control-lg" />
                                    <button type="button" id="showHidePassword" class="btn btn-secondary">
                                    <img src="../Images/motrarcontrasena.png"  width="21" alt="25" >
                                        <i id="eyeIcon" class="bi bi-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
            
            <div class="d-grid gap-2 mt-3 mx-auto">
                <button type="submit" class="btn btn-success btn-lg btn-block ">
                    Iniciar Sesión
                </button>
            </div>
            <h1></h1>
        </div>
        </div>


        </div>

    </form>

    <?php
    if (isset($_GET['Error'])) {
        echo "<h3 class='text-center text-danger'>";
        if ($_GET['Error'] == 400) {
            echo "Usuario y/o password incorrecto";
        } else if ($_GET['Error'] == 401) {
            echo "Para acceder al recurso debes iniciar sesión";
        } else if ($_GET['Error'] == 403) {
            echo "No tienes permisos para acceder a este módulo";
        }
        echo "</h3>";
    }
    ?>
    <script src="../js/bootstrap.min.js"></script>
</body>

</html>
<!--PARA OCULTAR LA CONTRASEÑA-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordField = document.getElementById("txtPassword");
        const showHideButton = document.getElementById("showHidePassword");
        const eyeIcon = document.getElementById("eyeIcon");

        showHideButton.addEventListener("click", function () {
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove("bi-eye-slash");
                eyeIcon.classList.add("bi-eye");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("bi-eye");
                eyeIcon.classList.add("bi-eye-slash");
            }
        });
    });
</script>
</body>
</html>
<div class="container">
    <footer>
        <!--<p class="footer-text">Copyright &copy; 2007 - 2023 Antal Automation, S. De R.l. De C.V.</p>
         <p>Desarrollado por <a href="#" class="card-link">Ivón García</a></p>-->
    </footer>
</div>