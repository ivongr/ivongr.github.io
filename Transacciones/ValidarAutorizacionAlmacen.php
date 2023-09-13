<?php

if ($_SESSION['Rol'] != "almacen") {
    header("location: ../Pages/Login.php?Error=403");
}
