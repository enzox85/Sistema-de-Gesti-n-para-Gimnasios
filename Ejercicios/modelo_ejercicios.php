<?php
/**
 * /Ejercicios/modelo_ejercicios.php
 * 
 * Este archivo contiene las funciones del "Modelo" para la gestión de ejercicios.
 * Se encarga de todas las interacciones con la base de datos relacionadas con los ejercicios y sus medios.
 */

/**
 * Obtiene todos los ejercicios con su imagen principal para el listado.
 *
 * @param mysqli $conexion
 * @return array
 */
function obtenerEjercicios(mysqli $conexion): array
{
    // La subconsulta busca la primera URL de tipo IMAGEN para cada ejercicio.
    $sql = "SELECT e.*, 
                   (SELECT url_media 
                    FROM ejercicios_media 
                    WHERE idejercicio = e.idejercicio AND tipo_media = 'IMAGEN' 
                    ORDER BY orden ASC 
                    LIMIT 1) as imagen_principal 
            FROM ejercicios e 
            ORDER BY e.nomb_ejer ASC";
    
    $resultado = mysqli_query($conexion, $sql);
    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}

/**
 * Obtiene los detalles completos de un solo ejercicio, incluyendo todos sus medios.
 *
 * @param mysqli $conexion
 * @param integer $idEjercicio
 * @return array|null
 */
function obtenerDetallesEjercicio(mysqli $conexion, int $idEjercicio): ?array
{
    $detalles = ['ejercicio' => null, 'media' => []];

    // Obtener datos del ejercicio
    $sqlEjercicio = "SELECT * FROM ejercicios WHERE idejercicio = ?";
    $stmt = mysqli_prepare($conexion, $sqlEjercicio);
    mysqli_stmt_bind_param($stmt, "i", $idEjercicio);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $detalles['ejercicio'] = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    if (!$detalles['ejercicio']) {
        return null; // Si no se encuentra el ejercicio, devolvemos null.
    }

    // Obtener medios asociados
    $sqlMedia = "SELECT * FROM ejercicios_media WHERE idejercicio = ? ORDER BY orden ASC";
    $stmtMedia = mysqli_prepare($conexion, $sqlMedia);
    mysqli_stmt_bind_param($stmtMedia, "i", $idEjercicio);
    mysqli_stmt_execute($stmtMedia);
    $resultadoMedia = mysqli_stmt_get_result($stmtMedia);
    $detalles['media'] = mysqli_fetch_all($resultadoMedia, MYSQLI_ASSOC);
    mysqli_stmt_close($stmtMedia);

    // Añadido: Buscar la imagen principal desde los medios ya obtenidos
    $detalles['ejercicio']['imagen_principal'] = null; // Iniciar como null
    foreach ($detalles['media'] as $media_item) {
        if ($media_item['tipo_media'] === 'IMAGEN') {
            $detalles['ejercicio']['imagen_principal'] = $media_item['url_media'];
            break; // Encontramos la primera imagen, es suficiente.
        }
    }

    return $detalles;
}

/**
 * Elimina un ejercicio, sus registros de medios y los archivos físicos asociados.
 *
 * @param mysqli $conexion
 * @param integer $idEjercicio
 * @return boolean
 */
function eliminarEjercicio(mysqli $conexion, int $idEjercicio): bool
{
    mysqli_begin_transaction($conexion);

    try {
        // 1. Obtener las URLs de los archivos físicos para borrarlos del servidor.
        $sqlSelectMedia = "SELECT url_media FROM ejercicios_media WHERE idejercicio = ? AND tipo_media = 'IMAGEN'";
        $stmtSelect = mysqli_prepare($conexion, $sqlSelectMedia);
        mysqli_stmt_bind_param($stmtSelect, "i", $idEjercicio);
        mysqli_stmt_execute($stmtSelect);
        $resultado = mysqli_stmt_get_result($stmtSelect);
        $archivos_a_borrar = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
        mysqli_stmt_close($stmtSelect);

        // 2. Eliminar los registros de la tabla 'ejercicios_media'.
        $sqlDeleteMedia = "DELETE FROM ejercicios_media WHERE idejercicio = ?";
        $stmtDeleteMedia = mysqli_prepare($conexion, $sqlDeleteMedia);
        mysqli_stmt_bind_param($stmtDeleteMedia, "i", $idEjercicio);
        mysqli_stmt_execute($stmtDeleteMedia);
        mysqli_stmt_close($stmtDeleteMedia);

        // 3. Eliminar el ejercicio principal.
        $sqlDeleteEjercicio = "DELETE FROM ejercicios WHERE idejercicio = ?";
        $stmtDeleteEjercicio = mysqli_prepare($conexion, $sqlDeleteEjercicio);
        mysqli_stmt_bind_param($stmtDeleteEjercicio, "i", $idEjercicio);
        mysqli_stmt_execute($stmtDeleteEjercicio);
        
        // Verificar si la eliminación fue exitosa
        if (mysqli_stmt_affected_rows($stmtDeleteEjercicio) === 0) {
            throw new Exception("No se encontró el ejercicio a eliminar.");
        }
        mysqli_stmt_close($stmtDeleteEjercicio);

        // 4. Si todo en la BD fue bien, borrar los archivos físicos.
        foreach ($archivos_a_borrar as $archivo) {
            // Convertir la URL a una ruta de servidor
            $ruta_servidor = $_SERVER['DOCUMENT_ROOT'] . parse_url($archivo['url_media'], PHP_URL_PATH);
            if (file_exists($ruta_servidor)) {
                unlink($ruta_servidor);
            }
        }

        mysqli_commit($conexion);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($conexion);
        // Opcional: registrar el error en un log: error_log($e->getMessage());
        return false;
    }
}

/**
 * Gestiona la subida de un archivo multimedia para un ejercicio.
 *
 * @param array $archivo El array de archivo de $_FILES.
 * @return string|null La ruta web relativa si tiene éxito, o null si falla.
 */
function gestionarSubidaMediaEjercicio(array $archivo): ?string
{
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/uploads/ejercicios/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombre_unico = 'ejer_' . uniqid() . '.' . $extension;
    $ruta_completa = $upload_dir . $nombre_unico;

    if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
        // Devolvemos la ruta web, no la del servidor
        return '/spartanproject/uploads/ejercicios/' . $nombre_unico;
    }

    return null;
}

/**
 * Crea un nuevo ejercicio y todos sus medios asociados dentro de una transacción.
 *
 * @param mysqli $conexion
 * @param array $datosEjercicio Datos del formulario del ejercicio.
 * @param array $medios Un array estructurado con los medios a añadir.
 * @return int|null El ID del nuevo ejercicio si tiene éxito, null si falla.
 */
function crearEjercicio(mysqli $conexion, array $datosEjercicio, array $medios): int
{
    mysqli_begin_transaction($conexion);

    try {
        // 1. Insertar el ejercicio principal
        $sql = "INSERT INTO ejercicios (nomb_ejer, grupo_mus, nivel_dificultad, descripcion) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $datosEjercicio['nomb_ejer'],
            $datosEjercicio['grupo_mus'],
            $datosEjercicio['nivel_dificultad'],
            $datosEjercicio['descripcion']
        );

        if (!mysqli_stmt_execute($stmt)) {
            // Usamos mysqli_error para obtener un mensaje más específico de la BD
            throw new Exception("Error al insertar el ejercicio principal: " . mysqli_error($conexion));
        }

        $idEjercicio = mysqli_insert_id($conexion);
        mysqli_stmt_close($stmt);

        // 2. Insertar los medios asociados
        if (!empty($medios)) {
            $sqlMedia = "INSERT INTO ejercicios_media (idejercicio, tipo_media, url_media, orden) VALUES (?, ?, ?, ?)";
            $stmtMedia = mysqli_prepare($conexion, $sqlMedia);
            
            foreach ($medios as $index => $media) {
                $url = $media['url'];
                $tipo = $media['tipo'];
                $orden = $index + 1;

                mysqli_stmt_bind_param($stmtMedia, "issi", $idEjercicio, $tipo, $url, $orden);
                if (!mysqli_stmt_execute($stmtMedia)) {
                    // Usamos mysqli_error para obtener un mensaje más específico de la BD
                    throw new Exception("Error al insertar el medio (" . $url . "): " . mysqli_error($conexion));
                }
            }
            mysqli_stmt_close($stmtMedia);
        }

        // 3. Si todo fue bien, confirmar
        mysqli_commit($conexion);
        return $idEjercicio;

    } catch (Exception $e) {
        mysqli_rollback($conexion);
        // Re-lanzamos la excepción para que el controlador la capture y muestre el error específico.
        throw $e;
    }
}

/**
 * Actualiza un ejercicio existente, sus medios y añade nuevos.
 *
 * @param mysqli $conexion
 * @param integer $idEjercicio El ID del ejercicio a actualizar.
 * @param array $datosEjercicio Los nuevos datos principales del ejercicio.
 * @param array $mediaAEliminar Un array de IDs de medios a eliminar.
 * @param array $nuevosMedios Un array de nuevos medios a añadir.
 * @return boolean True si la actualización fue exitosa, false si no.
 */
function actualizarEjercicio(mysqli $conexion, int $idEjercicio, array $datosEjercicio, array $mediaAEliminar, array $nuevosMedios): bool
{
    mysqli_begin_transaction($conexion);

    try {
        // 1. Actualizar los datos principales del ejercicio
        $sql = "UPDATE ejercicios SET nomb_ejer = ?, grupo_mus = ?, nivel_dificultad = ?, descripcion = ? WHERE idejercicio = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        // Check if prepare was successful
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta de actualización del ejercicio principal: " . mysqli_error($conexion));
        }
        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $datosEjercicio['nomb_ejer'],
            $datosEjercicio['grupo_mus'],
            $datosEjercicio['nivel_dificultad'],
            $datosEjercicio['descripcion'],
            $idEjercicio
        );
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al actualizar el ejercicio principal: " . mysqli_error($conexion));
        }
        mysqli_stmt_close($stmt);

        // 2. Eliminar los medios marcados
        if (!empty($mediaAEliminar)) {
            // Primero, obtener las rutas de los archivos para borrarlos del servidor
            $placeholders = implode(',', array_fill(0, count($mediaAEliminar), '?'));
            $sqlSelect = "SELECT url_media FROM ejercicios_media WHERE id_media IN ($placeholders) AND tipo_media = 'IMAGEN'";
            $stmtSelect = mysqli_prepare($conexion, $sqlSelect);
            // Check if prepare was successful before binding and executing
            if (!$stmtSelect) {
                throw new Exception("Error al preparar la consulta de selección de medios a borrar: " . mysqli_error($conexion));
            }
            mysqli_stmt_bind_param($stmtSelect, str_repeat('i', count($mediaAEliminar)), ...$mediaAEliminar);
            if (!mysqli_stmt_execute($stmtSelect)) {
                throw new Exception("Error al seleccionar medios a borrar: " . mysqli_error($conexion));
            }
            $resultado = mysqli_stmt_get_result($stmtSelect);
            $archivos_a_borrar = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
            mysqli_stmt_close($stmtSelect);

            // Borrar los archivos físicos
            foreach ($archivos_a_borrar as $archivo) {
                $ruta_servidor = $_SERVER['DOCUMENT_ROOT'] . parse_url($archivo['url_media'], PHP_URL_PATH);
                if (file_exists($ruta_servidor)) {
                    unlink($ruta_servidor);
                }
            }

            // Ahora, borrar los registros de la base de datos
            $sqlDelete = "DELETE FROM ejercicios_media WHERE id_media IN ($placeholders)";
            $stmtDelete = mysqli_prepare($conexion, $sqlDelete);
            // Check if prepare was successful before binding and executing
            if (!$stmtDelete) {
                throw new Exception("Error al preparar la consulta de eliminación de medios: " . mysqli_error($conexion));
            }
            mysqli_stmt_bind_param($stmtDelete, str_repeat('i', count($mediaAEliminar)), ...$mediaAEliminar);
            if (!mysqli_stmt_execute($stmtDelete)) {
                throw new Exception("Error al eliminar medios de la BD: " . mysqli_error($conexion));
            }
            mysqli_stmt_close($stmtDelete);
        }

        // 3. Añadir los nuevos medios
        if (!empty($nuevosMedios)) {
            $sqlMedia = "INSERT INTO ejercicios_media (idejercicio, tipo_media, url_media, orden) VALUES (?, ?, ?, ?)";
            $stmtMedia = mysqli_prepare($conexion, $sqlMedia);
            // Check if prepare was successful before binding and executing
            if (!$stmtMedia) {
                throw new Exception("Error al preparar la consulta de inserción de nuevos medios: " . mysqli_error($conexion));
            }
            
            $sqlOrden = "SELECT MAX(orden) as max_orden FROM ejercicios_media WHERE idejercicio = ?";
            $stmtOrden = mysqli_prepare($conexion, $sqlOrden);
            // Check if prepare was successful before binding and executing
            if (!$stmtOrden) {
                throw new Exception("Error al preparar la consulta de orden de medios: " . mysqli_error($conexion));
            }
            mysqli_stmt_bind_param($stmtOrden, "i", $idEjercicio);
            mysqli_stmt_execute($stmtOrden);
            $resOrden = mysqli_stmt_get_result($stmtOrden);
            $ordenActual = mysqli_fetch_assoc($resOrden)['max_orden'] ?? 0;
            mysqli_stmt_close($stmtOrden);

            foreach ($nuevosMedios as $media) {
                $ordenActual++;
                mysqli_stmt_bind_param($stmtMedia, "issi", $idEjercicio, $media['tipo'], $media['url'], $ordenActual);
                if (!mysqli_stmt_execute($stmtMedia)) {
                    throw new Exception("Error al insertar nuevo medio (" . $media['url'] . "): " . mysqli_error($conexion));
                }
            }
            mysqli_stmt_close($stmtMedia);
        }

        mysqli_commit($conexion);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($conexion);
        // Re-lanzamos la excepción para que el controlador la capture y muestre el error específico.
        throw $e;
    }
}
function getYouTubeID($url): ?string
{
    preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:\S+)?$/', $url, $matches);
    return $matches[1] ?? null;
}

?>