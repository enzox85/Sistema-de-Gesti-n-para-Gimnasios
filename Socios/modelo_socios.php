<?php
// Inserta un nuevo socio (usado por el controlador para AJAX)
function insertarNuevoSocio(mysqli $conexion, array $datos): bool
{
	mysqli_begin_transaction($conexion);
	try {
		$sql_persona = "INSERT INTO personas (nombre, apellido, dni, email, telefono, direccion, fecha_nacimiento) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$stmt_persona = mysqli_prepare($conexion, $sql_persona);
		if ($stmt_persona === false) {
			throw new Exception('Error al preparar la consulta de persona: ' . mysqli_error($conexion));
		}
		mysqli_stmt_bind_param($stmt_persona, "sssssss", $datos['nombre'], $datos['apellido'], $datos['dni'], $datos['email'], $datos['telefono'], $datos['direccion'], $datos['fecha_nacimiento']);
		mysqli_stmt_execute($stmt_persona);
		$id_persona = mysqli_insert_id($conexion);
		mysqli_stmt_close($stmt_persona);
		if ($id_persona == 0) {
			throw new Exception("La inserción en 'personas' no generó un ID.");
		}
		$sql_socio = "INSERT INTO socios (id_persona, fechalta, probfis, foto) VALUES (?, ?, ?, ?)";
		$stmt_socio = mysqli_prepare($conexion, $sql_socio);
		if ($stmt_socio === false) {
			throw new Exception('Error al preparar la consulta de socio: ' . mysqli_error($conexion));
		}
		mysqli_stmt_bind_param($stmt_socio, "isss", $id_persona, $datos['fechalta'], $datos['probfis'], $datos['foto']);
		mysqli_stmt_execute($stmt_socio);
		mysqli_stmt_close($stmt_socio);
		mysqli_commit($conexion);
		return true;
	} catch (Exception $e) {
		mysqli_rollback($conexion);
		error_log('Error en insertarNuevoSocio: ' . $e->getMessage());
		return false;
	}
}

function obtenerSocios(mysqli $conexion, string $busqueda = ''): array
{
	$socios = [];
	$sql = "SELECT p.id_persona, p.nombre, p.apellido, p.dni, p.email, p.telefono, p.direccion, p.fecha_nacimiento, s.idsocio, s.fechalta, s.probfis, s.foto FROM personas p JOIN socios s ON p.id_persona = s.id_persona";
	if (!empty($busqueda)) {
		$sql .= " WHERE p.nombre LIKE ? OR p.apellido LIKE ? OR p.dni LIKE ?";
		$stmt = mysqli_prepare($conexion, $sql);
		$termino_busqueda = "%{$busqueda}%";
		mysqli_stmt_bind_param($stmt, "sss", $termino_busqueda, $termino_busqueda, $termino_busqueda);
	} else {
		$sql .= " ORDER BY p.apellido, p.nombre";
		$stmt = mysqli_prepare($conexion, $sql);
	}
	if ($stmt) {
		mysqli_stmt_execute($stmt);
		$resultado = mysqli_stmt_get_result($stmt);
		if ($resultado) {
			$socios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
		}
		mysqli_stmt_close($stmt);
	}
	return $socios;
}

function crearSocio(mysqli $conexion, array $datos, ?array $archivo): bool
{
	$rutaFotoBD = null;
	if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
		$directorioDestino = __DIR__ . '/uploads/';
		if (!is_dir($directorioDestino)) {
			mkdir($directorioDestino, 0777, true);
		}
		$nombreArchivo = uniqid() . '_' . basename($archivo['name']);
		$rutaCompletaDestino = $directorioDestino . $nombreArchivo;
		if (move_uploaded_file($archivo['tmp_name'], $rutaCompletaDestino)) {
			$rutaFotoBD = $nombreArchivo;
		}
	}
	mysqli_begin_transaction($conexion);
	try {
		$sql_persona = "INSERT INTO personas (nombre, apellido, dni, email, telefono, direccion, fecha_nacimiento) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$stmt_persona = mysqli_prepare($conexion, $sql_persona);
		if ($stmt_persona === false) {
			throw new Exception('Error al preparar la consulta de persona: ' . mysqli_error($conexion));
		}
		mysqli_stmt_bind_param($stmt_persona, "sssssss", $datos['nombre'], $datos['apellido'], $datos['dni'], $datos['email'], $datos['telefono'], $datos['direccion'], $datos['fecha_nacimiento']);
		mysqli_stmt_execute($stmt_persona);
		$id_persona = mysqli_insert_id($conexion);
		mysqli_stmt_close($stmt_persona);
		if ($id_persona == 0) {
			throw new Exception("La inserción en 'personas' no generó un ID.");
		}
		$sql_socio = "INSERT INTO socios (id_persona, fechalta, probfis, foto) VALUES (?, ?, ?, ?)";
		$stmt_socio = mysqli_prepare($conexion, $sql_socio);
		if ($stmt_socio === false) {
			throw new Exception('Error al preparar la consulta de socio: ' . mysqli_error($conexion));
		}
		mysqli_stmt_bind_param($stmt_socio, "isss", $id_persona, $datos['fechalta'], $datos['probfis'], $rutaFotoBD);
		mysqli_stmt_execute($stmt_socio);
		mysqli_stmt_close($stmt_socio);
		mysqli_commit($conexion);
		return true;
	} catch (Exception $e) {
		mysqli_rollback($conexion);
		error_log('Error en crearSocio: ' . $e->getMessage());
		return false;
	}
}

/**
 * NUEVA FUNCIÓN: Actualiza un socio existente.
 */
function actualizarSocio(mysqli $conexion, int $id_persona, array $datos, ?array $archivo): bool
{
	mysqli_begin_transaction($conexion);
	try {
		// 1. Actualizar tabla 'personas'
		$sql_persona = "UPDATE personas SET nombre = ?, apellido = ?, dni = ?, email = ?, telefono = ?, direccion = ?, fecha_nacimiento = ? WHERE id_persona = ?";
		$stmt_persona = mysqli_prepare($conexion, $sql_persona);
		if ($stmt_persona === false) {
			throw new Exception('Error al preparar la actualización de persona: ' . mysqli_error($conexion));
		}
		mysqli_stmt_bind_param($stmt_persona, "sssssssi", $datos['nombre'], $datos['apellido'], $datos['dni'], $datos['email'], $datos['telefono'], $datos['direccion'], $datos['fecha_nacimiento'], $id_persona);
		mysqli_stmt_execute($stmt_persona);
		mysqli_stmt_close($stmt_persona);

		// 2. Lógica de la foto (si se sube una nueva)
		$rutaFotoBD = $datos['foto_actual']; // Mantenemos la foto actual por defecto
		if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
			$directorioDestino = __DIR__ . '/uploads/';
			if (!is_dir($directorioDestino)) {
				mkdir($directorioDestino, 0777, true);
			}
			$nombreArchivo = uniqid() . '_' . basename($archivo['name']);
			$rutaCompletaDestino = $directorioDestino . $nombreArchivo;
			if (move_uploaded_file($archivo['tmp_name'], $rutaCompletaDestino)) {
				$rutaFotoBD = $nombreArchivo; // Nueva foto
				// Opcional: Borrar la foto antigua si existe
				if (!empty($datos['foto_actual']) && file_exists($directorioDestino . $datos['foto_actual'])) {
					unlink($directorioDestino . $datos['foto_actual']);
				}
			}
		}

		// 3. Actualizar tabla 'socios'
		$sql_socio = "UPDATE socios SET fechalta = ?, probfis = ?, foto = ? WHERE id_persona = ?";
		$stmt_socio = mysqli_prepare($conexion, $sql_socio);
		if ($stmt_socio === false) {
			throw new Exception('Error al preparar la actualización de socio: ' . mysqli_error($conexion));
		}
		mysqli_stmt_bind_param($stmt_socio, "sssi", $datos['fechalta'], $datos['probfis'], $rutaFotoBD, $id_persona);
		mysqli_stmt_execute($stmt_socio);
		mysqli_stmt_close($stmt_socio);

		mysqli_commit($conexion);
		return true;
	} catch (Exception $e) {
		mysqli_rollback($conexion);
		error_log('Error en actualizarSocio: ' . $e->getMessage());
		return false;
	}
}

function eliminarSocio(mysqli $conexion, int $id_persona): bool
{
    mysqli_begin_transaction($conexion);

    try {
        // 1. Obtener el idsocio a partir de id_persona
        $idsocio = null;
        $sql_get_socio = "SELECT idsocio FROM socios WHERE id_persona = ?";
        $stmt_get_socio = mysqli_prepare($conexion, $sql_get_socio);
        mysqli_stmt_bind_param($stmt_get_socio, "i", $id_persona);
        mysqli_stmt_execute($stmt_get_socio);
        $resultado = mysqli_stmt_get_result($stmt_get_socio);
        if ($fila = mysqli_fetch_assoc($resultado)) {
            $idsocio = $fila['idsocio'];
        }
        mysqli_stmt_close($stmt_get_socio);

        // Si existe un socio, eliminar sus cuotas
        if ($idsocio) {
            // 2. Eliminar las cuotas asociadas
            $sql_delete_cuotas = "DELETE FROM cuotas WHERE idsocio = ?";
            $stmt_delete_cuotas = mysqli_prepare($conexion, $sql_delete_cuotas);
            mysqli_stmt_bind_param($stmt_delete_cuotas, "i", $idsocio);
            mysqli_stmt_execute($stmt_delete_cuotas);
            mysqli_stmt_close($stmt_delete_cuotas);
        }

        // 3. Eliminar el registro de la tabla 'socios'
        $sql_delete_socio = "DELETE FROM socios WHERE id_persona = ?";
        $stmt_delete_socio = mysqli_prepare($conexion, $sql_delete_socio);
        mysqli_stmt_bind_param($stmt_delete_socio, "i", $id_persona);
        mysqli_stmt_execute($stmt_delete_socio);
        mysqli_stmt_close($stmt_delete_socio);

        // 4. Eliminar el registro de la tabla 'personas'
        $sql_delete_persona = "DELETE FROM personas WHERE id_persona = ?";
        $stmt_delete_persona = mysqli_prepare($conexion, $sql_delete_persona);
        mysqli_stmt_bind_param($stmt_delete_persona, "i", $id_persona);
        mysqli_stmt_execute($stmt_delete_persona);
        mysqli_stmt_close($stmt_delete_persona);

        // Si todo fue bien, confirmar los cambios
        mysqli_commit($conexion);
        return true;

    } catch (Exception $e) {
        // Si algo falló, revertir los cambios
        mysqli_rollback($conexion);
        error_log('Error en eliminarSocio: ' . $e->getMessage());
        return false;
    }
}
?>