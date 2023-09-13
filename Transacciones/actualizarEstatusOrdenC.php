<?php
 
 $id = $_POST['id'];
 include("conexion.php");

 //Manejo del error que sea capaz de actualizar un estatus de manera individual.
 
 if (isset($_POST['CmbEstatusAlmacen']) && is_numeric($_POST['CmbEstatusAlmacen'])) {
     $idestatusalmacen = $_POST['CmbEstatusAlmacen'];
     $cmd = "UPDATE ProductosRequisicion SET Id_EstatusAlmacen=? WHERE Id=?";
     $sql = $cn->prepare($cmd);
     $resultado = $sql->execute([$idestatusalmacen, $id]);
     
     if (!$resultado) {
         // Manejo de error en la ejecución de la consulta
         header("Location: ../Pages/Proyectos.php?error=true");
         exit();
     }
 }
 
 if (isset($_POST['CmbEstatusMaquinado']) && is_numeric($_POST['CmbEstatusMaquinado'])) {
     $idestatusmaquinado = $_POST['CmbEstatusMaquinado'];
     $cmd = "UPDATE ProductosRequisicion SET Id_Estatus_Maquinado=? WHERE Id=?";
     $sql = $cn->prepare($cmd);
     $resultado = $sql->execute([$idestatusmaquinado, $id]);
 
     if (!$resultado) {
         // Manejo de error en la ejecución de la consulta
         header("Location: ../Pages/Proyectos.php?error=true");
         exit();
     }
 }
 
 header("Location: ../Pages/ResultadosOrdenCompra.php?success=true");
       
?>
