<?php
header('Content-Type: application/json');

require_once '../conexion.php';
require_once 'modelo_ejercicios.php';

$respuesta = ['status' => 'error', 'message' => 'Datos inválidos.'];

try {
    // 1. Validar ID del ejercicio
    if (!isset($_POST['idejercicio']) || !is_numeric($_POST['idejercicio'])) {
        throw new Exception('ID de ejercicio no válido.');
    }
    $idEjercicio = (int)$_POST['idejercicio'];

    // 2. Recoger datos principales
    $datosEjercicio = [
        'nomb_ejer' => $_POST['nomb_ejer'] ?? '',
        'grupo_mus' => $_POST['grupo_mus'] ?? '',
        'nivel_dificultad' => $_POST['nivel_dificultad'] ?? '',
        'descripcion' => $_POST['descripcion'] ?? ''
    ];

    // 3. Recoger IDs de medios a eliminar
    $mediaAEliminar = [];
    if (!empty($_POST['media_a_eliminar']) && is_array($_POST['media_a_eliminar'])) {
        // Asegurarnos de que todos los valores son enteros
        $mediaAEliminar = array_map('intval', $_POST['media_a_eliminar']);
    }

    // 4. Procesar y estructurar los nuevos medios
    $nuevosMedios = [];
    $tiposMedia = $_POST['media_types'] ?? [];
    $linksMedia = $_POST['media_links'] ?? [];
    $archivosMedia = $_FILES['media_files'] ?? [];
    
    $linkIndex = 0;
    $fileIndex = 0;

    foreach ($tiposMedia as $tipo) {
        if ($tipo === 'IMAGEN') {
            if (isset($archivosMedia['name'][$fileIndex]) && $archivosMedia['error'][$fileIndex] === UPLOAD_ERR_OK) {
                $archivo = [
                    'name' => $archivosMedia['name'][$fileIndex],
                    'type' => $archivosMedia['type'][$fileIndex],
                    'tmp_name' => $archivosMedia['tmp_name'][$fileIndex],
                    'error' => $archivosMedia['error'][$fileIndex],
                    'size' => $archivosMedia['size'][$fileIndex]
                ];
                
                $url = gestionarSubidaMediaEjercicio($archivo);
                if (!$url) {
                    throw new Exception('Error al procesar uno de los nuevos archivos de imagen.');
                }
                $nuevosMedios[] = ['tipo' => 'IMAGEN', 'url' => $url];
            }
            $fileIndex++;

        } elseif ($tipo === 'VIDEO_LINK') {
            $url = $linksMedia[$linkIndex] ?? '';
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $nuevosMedios[] = ['tipo' => 'VIDEO_LINK', 'url' => $url];
            }
            $linkIndex++;
        }
    }

    // 5. Llamar al modelo para que haga todo el trabajo pesado
    $conexion = conectar();
    $exito = actualizarEjercicio($conexion, $idEjercicio, $datosEjercicio, $mediaAEliminar, $nuevosMedios);
    
    if ($exito) {
        // Si la actualización fue exitosa, obtenemos los datos actualizados para devolverlos
        $ejercicioActualizado = obtenerDetallesEjercicio($conexion, $idEjercicio);
        $respuesta = [
            'status' => 'success',
            'message' => 'Ejercicio actualizado con éxito.',
            'ejercicio' => $ejercicioActualizado
        ];
    } else {
        throw new Exception('El modelo no pudo actualizar el ejercicio.');
    }

    mysqli_close($conexion);

} catch (Exception $e) {
    $respuesta['message'] = $e->getMessage();
}

// 6. Devolver la respuesta final
echo json_encode($respuesta);
?>