<?php

include("conexion.php");
$con = conectar();

$id = $_GET['id'];

// Eliminar primero los registros relacionados en asistencias
$sqlCuotas = "DELETE FROM cuotas WHERE idsocio='$id'";
$queryCuotas = mysqli_query($con, $sqlCuotas);

// Ahora eliminar el alumno
$sqleliminarsocio = "DELETE FROM socios WHERE idsocio='$id'";
$queryeliminarsocios = mysqli_query($con, $sqleliminarsocio);

if ($queryeliminarsocios) {
    Header("Location: socios.php");
}
?>
