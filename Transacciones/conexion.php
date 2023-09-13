<?php
    $usuario = "root";
    $password = "12345";
    $nombreBD = "InventarioAntal1";
    try{
        $cn = new PDO("mysql:host=localhost; dbname=".
                  $nombreBD,$usuario,$password,
                  array(PDO::MYSQL_ATTR_INIT_COMMAND => 
                  "Set names utf8"));
    }
    catch(Exception $ex){
        echo "Error: ".$ex->getMessage();
    }