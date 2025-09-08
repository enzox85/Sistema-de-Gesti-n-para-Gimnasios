<?php
/*
 * Obtiene todos los planes de entrenamiento y los datos del socio asociado.
 */
function obtenerPlanesAsignados($conexion)
{
	// ===== CONSULTA CORREGIDA =====
	// Añadimos el JOIN con la tabla 'personas' (alias 'p') para obtener nombre y apellido.
	// También traemos la foto del socio, que seguramente necesitaremos en la vista.
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
 * Asigna un nuevo plan a un socio de forma segura usando consultas preparadas.
 */
function asignarNuevoPlan($conexion, array $datosPlan): bool
{
	// ===== FUNCIÓN REFACTORIZADA Y SEGURA =====
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

	// Preparamos los datos con valores por defecto si no existen
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

	// 'issssssssssis' corresponde a los tipos de datos de cada variable:
	// i: integer, s: string, d: double
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
?>