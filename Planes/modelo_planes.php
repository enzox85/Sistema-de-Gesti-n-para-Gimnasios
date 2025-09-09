<?php

 
function obtenerPlanesAsignados($conexion)
{
	
	$sql = "SELECT pe.*, p.nombre AS nombre_socio, p.apellido AS apellido_socio, s.foto
            FROM planes_entrenamiento pe
            JOIN socios s ON pe.idsocio = s.idsocio
            JOIN personas p ON s.id_persona = p.id_persona
            ORDER BY pe.fecha_inicio DESC";

	$resultado = mysqli_query($conexion, $sql);
	$planes = [];
	if ($resultado) {
		// Usamos fetch_all para un código más limpio
		$planes = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
	}
	return $planes;
}

/**

 */
function asignarNuevoPlan($conexion, array $datosPlan): bool
{
	
	$sql = "INSERT INTO planes_entrenamiento (
                idsocio, tipo_plan, descripcion_plan, peso_actual, altura, 
                nivel_experiencia, preferencias_dieteticas, 
                disponibilidad, fecha_inicio, fecha_fin, activo, observaciones
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

	$stmt = mysqli_prepare($conexion, $sql);
	if ($stmt === false) {
		// Si la preparación falla, registra el error.
		error_log('Error al preparar la consulta de asignar plan: ' . mysqli_error($conexion));
		return false;
	}

	
	$idsocio = $datosPlan['idsocio'] ?? null;
	$tipo_plan = $datosPlan['tipo_plan'] ?? 'General';
	$descripcion_plan = $datosPlan['descripcion_plan'] ?? '';
	$peso_actual = $datosPlan['peso_actual'] ?? null;
	$altura = $datosPlan['altura'] ?? null;
	$nivel_experiencia = $datosPlan['nivel_experiencia'] ?? 'Intermedio';
	$preferencias_dieteticas = $datosPlan['preferencias_dieteticas'] ?? '';
	$disponibilidad = $datosPlan['disponibilidad'] ?? '';
	$fecha_inicio = !empty($datosPlan['fecha_inicio']) ? $datosPlan['fecha_inicio'] : null;
	$fecha_fin = !empty($datosPlan['fecha_fin']) ? $datosPlan['fecha_fin'] : null;

	$activo = $datosPlan['activo'] ?? 1;
	$observaciones = $datosPlan['observaciones'] ?? '';

	
	mysqli_stmt_bind_param(
		$stmt,
		'issdisssssis',
		$idsocio,
		$tipo_plan,
		$descripcion_plan,
		$peso_actual,
		$altura,
		$nivel_experiencia,
		$preferencias_dieteticas,
		$disponibilidad,
		$fecha_inicio,
		$fecha_fin,
		$activo,
		$observaciones
	);

	return mysqli_stmt_execute($stmt);
}

/**
 * Elimina un plan de entrenamiento de la base de datos.
 */
function obtenerPlanPorId($conexion, int $idplan)
{
    $sql = "SELECT * FROM planes_entrenamiento WHERE idplan = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    if ($stmt === false) {
        error_log('Error al preparar la consulta de obtener plan por ID: ' . mysqli_error($conexion));
        return null;
    }
    mysqli_stmt_bind_param($stmt, 'i', $idplan);
    
    if (mysqli_stmt_execute($stmt)) {
        $resultado = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($resultado);
    }
    return null;
}

/**
 * Actualiza un plan de entrenamiento existente.
 */
function actualizarPlan($conexion, array $datosPlan): bool
{
    $sql = "UPDATE planes_entrenamiento SET 
                tipo_plan = ?, 
                descripcion_plan = ?, 
                peso_actual = ?, 
                altura = ?, 
                nivel_experiencia = ?, 
                preferencias_dieteticas = ?, 
                disponibilidad = ?, 
                fecha_inicio = ?, 
                fecha_fin = ?, 
                activo = ?, 
                observaciones = ?
            WHERE idplan = ?";

    $stmt = mysqli_prepare($conexion, $sql);
    if ($stmt === false) {
        error_log('Error al preparar la consulta de actualizar plan: ' . mysqli_error($conexion));
        return false;
    }// Preparamos los datos con valores por defecto si no existen
	$tipo_plan = $datosPlan['tipo_plan'] ?? 'General';
	$descripcion_plan = $datosPlan['descripcion_plan'] ?? '';
	$peso_actual = $datosPlan['peso_actual'] ?? null;
	$altura = $datosPlan['altura'] ?? null;
	$nivel_experiencia = $datosPlan['nivel_experiencia'] ?? 'Intermedio';
	$preferencias_dieteticas = $datosPlan['preferencias_dieteticas'] ?? '';
	$disponibilidad = $datosPlan['disponibilidad'] ?? '';
	$fecha_inicio = !empty($datosPlan['fecha_inicio']) ? $datosPlan['fecha_inicio'] : null;
	$fecha_fin = !empty($datosPlan['fecha_fin']) ? $datosPlan['fecha_fin'] : null;
	$activo = $datosPlan['activo'] ?? 1;
	$observaciones = $datosPlan['observaciones'] ?? '';
    $idplan = $datosPlan['idplan'] ?? null;


    mysqli_stmt_bind_param(
        $stmt,
        'ssdisssssisi',
        $tipo_plan,
        $descripcion_plan,
        $peso_actual,
        $altura,
        $nivel_experiencia,
        $preferencias_dieteticas,
        $disponibilidad,
        $fecha_inicio,
        $fecha_fin,
        $activo,
        $observaciones,
        $idplan
    );

    return mysqli_stmt_execute($stmt);
}

/**
 * Elimina un plan de entrenamiento de la base de datos.
 */
function eliminarPlan($conexion, int $idplan): bool
{
    $sql = "DELETE FROM planes_entrenamiento WHERE idplan = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    if ($stmt === false) {
        error_log('Error al preparar la consulta de eliminar plan: ' . mysqli_error($conexion));
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'i', $idplan);

    return mysqli_stmt_execute($stmt);
}

?>