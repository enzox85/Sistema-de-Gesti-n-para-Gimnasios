<?php
require_once '../conexion.php';
$conexion = conectar();
header('Content-Type: application/json');

// Validamos que se haya recibido un idejercicio y que sea un número
if (!isset($_GET['idejercicio']) || !is_numeric($_GET['idejercicio'])) {
	echo json_encode(['success' => false, 'message' => 'ID de ejercicio no válido.']);
	exit;
}

$idejercicio = (int) $_GET['idejercicio'];

// Usamos sentencias preparadas para seguridad
$stmt = mysqli_prepare($conexion, "SELECT tipo_media, url_media FROM ejercicios_media WHERE idejercicio = ? ORDER BY orden ASC");
mysqli_stmt_bind_param($stmt, "i", $idejercicio);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$media = [];
if ($resultado) {
	while ($row = mysqli_fetch_assoc($resultado)) {
		$media[] = $row;
	}
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);

echo json_encode(['success' => true, 'media' => $media]);
?>