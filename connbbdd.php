<?php

$db_server = 'localhost';
$db_name = 'libreriajuegos';
$db_user = 'root';
$db_password = '';

try {
    $conn = new PDO("mysql:host=$db_server;dbname=$db_name" , $db_user , $db_password);    
    $conn -> setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
    //echo "Conexion exitosa";
} catch (PDOException $e) {
    echo "Error con la conexion".$e->getMessage();
}