<?php
    if(!isset($_SESSION['Id_Usuario'])){
        header("location: ../Pages/Login.php?Error=401");
    }
?>