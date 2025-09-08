<?php

require_once '../conexion.php';
require_once 'modelo_ejercicios.php';
$con = conectar();
$ejercicios = obtenerEjercicios($con);
include 'ejercicios_view.php';

?>