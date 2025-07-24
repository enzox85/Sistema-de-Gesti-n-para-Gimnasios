<?php
// Incluimos la conexión a la base de datos
require_once '../conexion.php';
$conexion = conectar();

// Indicamos que la respuesta será en formato JSON
header('Content-Type: application/json');

// 1. LEER LOS DATOS DE ENTRADA
// Como enviamos JSON desde el frontend, no podemos usar $_POST.
// Leemos el cuerpo de la petición (raw input) y lo decodificamos.
$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

// Verificación básica de que los datos llegaron
if (!$data || !isset($data->nombre) || !isset($data->dias)) {
	echo json_encode(['success' => false, 'message' => 'Error: Datos incompletos.']);
	exit;
}

// 2. INICIAR LA TRANSACCIÓN
// Desactivamos el autocommit para controlar la transacción manualmente.
mysqli_autocommit($conexion, false);

try {
	// 3. INSERTAR LA RUTINA PRINCIPAL
	$stmt_rutina = mysqli_prepare($conexion, "INSERT INTO rutinas (nombre, nivel_dificultad, descripcion) VALUES (?, ?, ?)");
	mysqli_stmt_bind_param($stmt_rutina, "sss", $data->nombre, $data->nivel_dificultad, $data->descripcion);

	if (!mysqli_stmt_execute($stmt_rutina)) {
		throw new Exception("Error al guardar la rutina principal: " . mysqli_stmt_error($stmt_rutina));
	}

	// Obtenemos el ID de la rutina que acabamos de insertar
	$idrutina_creada = mysqli_insert_id($conexion);
	mysqli_stmt_close($stmt_rutina);

	// 4. RECORRER E INSERTAR LOS DÍAS Y SUS EJERCICIOS
	foreach ($data->dias as $dia) {
		// Insertar el día en la tabla 'rutinas_dias'
		$stmt_dia = mysqli_prepare($conexion, "INSERT INTO rutinas_dias (idrutina, dia_semana) VALUES (?, ?)");
		mysqli_stmt_bind_param($stmt_dia, "is", $idrutina_creada, $dia->dia_semana);

		if (!mysqli_stmt_execute($stmt_dia)) {
			throw new Exception("Error al guardar el día: " . mysqli_stmt_error($stmt_dia));
		}

		// Obtenemos el ID del día que acabamos de insertar
		$iddia_creado = mysqli_insert_id($conexion);
		mysqli_stmt_close($stmt_dia);

		$orden = 1; // Para ordenar los ejercicios dentro del día
		foreach ($dia->ejercicios as $ejercicio) {
			// Insertar cada ejercicio en la tabla 'rutinas_ejercicios'
			$stmt_ejercicio = mysqli_prepare($conexion, "INSERT INTO rutinas_ejercicios (iddia, idejercicio, repeticiones, tiempo_descanso_seg, orden) VALUES (?, ?, ?, ?, ?)");
			mysqli_stmt_bind_param($stmt_ejercicio, "iissi", $iddia_creado, $ejercicio->idejercicio, $ejercicio->repeticiones, $ejercicio->descanso, $orden);

			if (!mysqli_stmt_execute($stmt_ejercicio)) {
				throw new Exception("Error al guardar un ejercicio: " . mysqli_stmt_error($stmt_ejercicio));
			}
			mysqli_stmt_close($stmt_ejercicio);
			$orden++;
		}
	}

	// 5. CONFIRMAR LA TRANSACCIÓN
	// Si todo ha ido bien, confirmamos todos los cambios en la base de datos.
	mysqli_commit($conexion);
	echo json_encode(['success' => true, 'message' => '¡Rutina guardada con éxito!']);

} catch (Exception $e) {
	// 6. REVERTIR LA TRANSACCIÓN EN CASO DE ERROR
	// Si algo falló, revertimos todos los cambios para no dejar datos corruptos.
	mysqli_rollback($conexion);
	echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Cerramos la conexión
mysqli_close($conexion);
?>