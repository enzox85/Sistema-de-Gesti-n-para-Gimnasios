<?php
header('Content-Type: application/json');

require_once '../conexion.php';
require_once 'modelo_ejercicios.php';

$respuesta = ['status' => 'error', 'message' => 'Datos inválidos.'];

try {
    // 1. Validar y recoger datos del ejercicio
    if (!isset($_POST['nomb_ejer'], $_POST['grupo_mus'], $_POST['nivel_dificultad'])) {
        throw new Exception('Faltan datos principales del ejercicio.');
    }
    $datosEjercicio = [
        'nomb_ejer' => $_POST['nomb_ejer'],
        'grupo_mus' => $_POST['grupo_mus'],
        'nivel_dificultad' => $_POST['nivel_dificultad'],
        'descripcion' => $_POST['descripcion'] ?? ''
    ];

    // 2. Procesar y estructurar los datos de los medios
    $mediosParaGuardar = [];
    $tiposMedia = $_POST['media_types'] ?? [];
    $linksMedia = $_POST['media_links'] ?? [];
    $archivosMedia = $_FILES['media_files'] ?? [];
    
    $linkIndex = 0;
    $fileIndex = 0;

    foreach ($tiposMedia as $tipo) {
        if ($tipo === 'IMAGEN') {
            // Estructurar el array del archivo para la función del modelo
            $archivo = [
                'name' => $archivosMedia['name'][$fileIndex],
                'type' => $archivosMedia['type'][$fileIndex],
                'tmp_name' => $archivosMedia['tmp_name'][$fileIndex],
                'error' => $archivosMedia['error'][$fileIndex],
                'size' => $archivosMedia['size'][$fileIndex]
            ];
            
            // La función del modelo se encargará de la subida
            $url = gestionarSubidaMediaEjercicio($archivo);
            if (!$url) {
                throw new Exception('Error al procesar uno de los archivos de imagen.');
            }
            $mediosParaGuardar[] = ['tipo' => 'IMAGEN', 'url' => $url];
            $fileIndex++;

        } elseif ($tipo === 'VIDEO_LINK') {
            $url = $linksMedia[$linkIndex] ?? '';
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $mediosParaGuardar[] = ['tipo' => 'VIDEO_LINK', 'url' => $url];
            }
            $linkIndex++;
        }
    }

    // 3. Llamar al modelo para crear el ejercicio
    $conexion = conectar();
    $idNuevoEjercicio = crearEjercicio($conexion, $datosEjercicio, $mediosParaGuardar);
    
    // Si la línea anterior no lanzó una excepción, la creación fue exitosa.
    // Obtenemos todos los datos para devolverlos al frontend.
    $nuevoEjercicioCompleto = obtenerDetallesEjercicio($conexion, $idNuevoEjercicio);
    $respuesta = [
        'status' => 'success',
        'message' => 'Ejercicio creado con éxito.',
        'ejercicio' => $nuevoEjercicioCompleto
    ];

    mysqli_close($conexion);

} catch (Exception $e) {
    // Si algo falló (en este script o en el modelo), lo capturamos aquí.
    // El mensaje de la excepción será el que se envíe al frontend.
    $respuesta['message'] = $e->getMessage();
    if (isset($conexion) && $conexion) {
        mysqli_close($conexion);
    }
}

// 4. Devolver respuesta JSON
echo json_encode($respuesta);
?>