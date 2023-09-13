<?php
session_start();
$user = $_POST['txtUsuario'];
$pwd = $_POST['txtPassword'];

include "../Transacciones/conexion.php";
$sentencia = $cn->prepare("SELECT u.Id, u.NombreUsuario, u.Pwd, r.Id,Rol
 FROM Usuarios as u
INNER JOIN Roles as r ON u.Id_Rol = r.Id where NombreUsuario=? and Pwd=?");
$sentencia->execute([$user, $pwd]);
$login = $sentencia->fetch(PDO::FETCH_OBJ); 
if($login){
    $_SESSION['Id_Usuario'] = $login->Id;
    $_SESSION['nombre'] = $login->NombreUsuario;
    $_SESSION['Id_User'] = $login->Id;
    $_SESSION['Id_Rol'] = $login->Id;
    $_SESSION['Rol'] = $login->Rol;
    header("location: ../Pages/Proyectos.php");
}
else{
    header("location: ../Pages/Login.php?Error=400");
}
?>