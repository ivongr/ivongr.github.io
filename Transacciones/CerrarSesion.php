<?php
session_start();
unset($_SESSION['Id']);
unset($_SESSION['nombre']);
unset($_SESSION['User']);
unset($_SESSION['Id_User']);
unset($_SESSION['Id_Rol']);
unset($_SESSION['Rol']);
//session_destroy();
header("location: ../Pages/Login.php");
?>

