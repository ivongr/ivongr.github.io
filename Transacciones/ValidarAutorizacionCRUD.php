<?php

if ($_SESSION['Rol'] != "almacen") {
    if ($_SESSION['Rol'] != "admin") {
    header("location: ../Pages/Login.php?Error=403");
    }
}
