<?php

include("conexion.php");
$con = conectar();

$id = $_GET['id'];

// Eliminar primero los registros relacionados en asistencias
$sqleliminar = "DELETE FROM ejercicios WHERE idejercicio='$id'";
$queryejercicio = mysqli_query($con, $sqleliminar);


if ($queryejercicio) {
    Header("Location: ejerciciosmain.php");
}
?>
