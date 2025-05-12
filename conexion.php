<?php
function conectar() {
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "spartadb";
 
    $con = mysqli_connect($host, $user, $password, $database);

    if (!$con) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    return $con;
}
?>
