<?php
function conectar()
{
	$host = "localhost";
	$user = "root";
	$password = "";
	$database = "spartadb";

	$con = mysqli_connect($host, $user, $password, $database);

	if (!$con) {
		        // BUENA PRÁCTICA: En lugar de "morir", lanza una excepción que puede ser capturada.
        throw new Exception("Falló la conexión a la BD: " . mysqli_connect_error());
	}

	return $con;
}
?>