<?php
// Establecer la cabecera para devolver una respuesta en formato JSON.
header('Content-Type: application/json');

// Incluir el archivo de conexión a la base de datos.
require_once '../conexion.php';

/**
 * Función para enviar una respuesta JSON estandarizada y terminar la ejecución.
 * @param bool $success - Indica si la operación fue exitosa.
 * @param string $message - Un mensaje descriptivo.
 */
function enviar_respuesta($success, $message = '')
{
	echo json_encode(['success' => $success, 'message' => $message]);
	exit;
}

// Verificar que la solicitud se haya hecho mediante el método POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	enviar_respuesta(false, 'Acceso denegado. Se esperaba una solicitud POST.');
}

// Verificar que se haya recibido el ID de la rutina.
if (!isset($_POST['idrutina']) || empty(trim($_POST['idrutina']))) {
	enviar_respuesta(false, 'No se ha proporcionado el ID de la rutina a eliminar.');
}

$idrutina = $_POST['idrutina'];

// Conectar a la base de datos.
$conexion = conectar();
if (!$conexion) {
	// Si la conexión falla, se notifica el error.
	enviar_respuesta(false, 'Error crítico al conectar con la base de datos.');
}

// Iniciar una transacción para garantizar la integridad de los datos.
// Si alguna de las eliminaciones falla, se revertirán todas.
mysqli_begin_transaction($conexion);

try {
	// Paso 1: Obtener los IDs de los días asociados a la rutina.
	// Esto es necesario para poder eliminar primero los ejercicios de esos días.
	$sql_get_dias = "SELECT iddia FROM rutinas_dias WHERE idrutina = ?";
	$stmt_get_dias = mysqli_prepare($conexion, $sql_get_dias);
	mysqli_stmt_bind_param($stmt_get_dias, 'i', $idrutina);
	mysqli_stmt_execute($stmt_get_dias);
	$resultado_dias = mysqli_stmt_get_result($stmt_get_dias);

	$ids_dias = [];
	while ($fila = mysqli_fetch_assoc($resultado_dias)) {
		$ids_dias[] = $fila['iddia'];
	}
	mysqli_stmt_close($stmt_get_dias);

	// Paso 2: Si existen días, eliminar todos los ejercicios asociados a ellos.
	if (!empty($ids_dias)) {
		// Se crea una cadena de marcadores de posición (?,?,?) para la consulta IN.
		$placeholders = implode(',', array_fill(0, count($ids_dias), '?'));
		$sql_delete_ejercicios = "DELETE FROM rutinas_ejercicios WHERE iddia IN ($placeholders)";
		$stmt_delete_ejercicios = mysqli_prepare($conexion, $sql_delete_ejercicios);

		// Se vinculan los IDs de los días a la consulta.
		$tipos = str_repeat('i', count($ids_dias));
		mysqli_stmt_bind_param($stmt_delete_ejercicios, $tipos, ...$ids_dias);

		if (!mysqli_stmt_execute($stmt_delete_ejercicios)) {
			throw new Exception('Error al eliminar los ejercicios asociados a la rutina.');
		}
		mysqli_stmt_close($stmt_delete_ejercicios);
	}

	// Paso 3: Eliminar los registros de los días de la tabla 'dias_rutina'.
	$sql_delete_dias = "DELETE FROM rutinas_dias WHERE idrutina = ?";
	$stmt_delete_dias = mysqli_prepare($conexion, $sql_delete_dias);
	mysqli_stmt_bind_param($stmt_delete_dias, 'i', $idrutina);
	if (!mysqli_stmt_execute($stmt_delete_dias)) {
		throw new Exception('Error al eliminar los días de la rutina.');
	}
	mysqli_stmt_close($stmt_delete_dias);

	// Paso 4: Finalmente, eliminar la rutina principal de la tabla 'rutinas'.
	$sql_delete_rutina = "DELETE FROM rutinas WHERE idrutina = ?";
	$stmt_delete_rutina = mysqli_prepare($conexion, $sql_delete_rutina);
	mysqli_stmt_bind_param($stmt_delete_rutina, 'i', $idrutina);
	if (!mysqli_stmt_execute($stmt_delete_rutina)) {
		throw new Exception('Error al eliminar la rutina principal.');
	}
	mysqli_stmt_close($stmt_delete_rutina);

	// Si todas las consultas se ejecutaron sin errores, se confirman los cambios.
	mysqli_commit($conexion);
	enviar_respuesta(true, 'La rutina y todos sus datos asociados han sido eliminados correctamente.');

} catch (Exception $e) {
	// Si ocurre cualquier error, se revierten todos los cambios realizados.
	mysqli_rollback($conexion);
	enviar_respuesta(false, $e->getMessage());
} finally {
	// Se cierra la conexión a la base de datos en cualquier caso.
	mysqli_close($conexion);
}
?>