<?php
// 1. CONFIGURACIÓN INICIAL
require_once '../conexion.php';
$conexion = conectar();

// La respuesta será siempre en formato JSON para que el frontend la entienda.
header('Content-Type: application/json');

// 2. PREPARAR DIRECTORIO DE SUBIDA
// Define la carpeta donde se guardarán las imágenes.
// La ruta es relativa al script actual (sube un nivel desde /Ejercicios a la raíz).
$upload_dir = '../uploads/ejercicios/';

// Crea el directorio si no existe. El @ suprime warnings si el directorio ya existe.
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0777, true);
}

// 3. INICIAR TRANSACCIÓN
// Desactivamos el autocommit para tener control total sobre la operación.
mysqli_autocommit($conexion, false);

try {
    // 4. INSERTAR DATOS PRINCIPALES DEL EJERCICIO
    // Preparamos la inserción en la tabla 'ejercicios'.
    $stmt_ejercicio = mysqli_prepare($conexion, "INSERT INTO ejercicios (nomb_ejer, grupo_mus, nivel_dificultad, descripcion) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_ejercicio, "ssss", $_POST['nomb_ejer'], $_POST['grupo_mus'], $_POST['nivel_dificultad'], $_POST['descripcion']);
    
    if (!mysqli_stmt_execute($stmt_ejercicio)) {
        throw new Exception("Error al guardar el ejercicio principal: " . mysqli_stmt_error($stmt_ejercicio));
    }
    
    // Obtenemos el ID del ejercicio que acabamos de crear. Lo necesitaremos para los medios.
    $idejercicio_creado = mysqli_insert_id($conexion);
    mysqli_stmt_close($stmt_ejercicio);

    // 5. PROCESAR Y GUARDAR LOS ARCHIVOS MULTIMEDIA
    $orden = 1;
    $media_types = $_POST['media_types'] ?? [];
    $image_counter = 0; // Contador para el array de archivos $_FILES
    $link_counter = 0;  // Contador para el array de links $_POST

    foreach ($media_types as $type) {
        $url_media = '';
        
        if ($type === 'IMAGEN') {
            // Verificamos que el archivo se haya subido correctamente
            if (isset($_FILES['media_files']['name'][$image_counter]) && $_FILES['media_files']['error'][$image_counter] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['media_files']['tmp_name'][$image_counter];
                $original_name = basename($_FILES['media_files']['name'][$image_counter]);
                $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
                
                // Creamos un nombre de archivo único para evitar sobreescrituras
                $unique_filename = 'ejer_' . $idejercicio_creado . '_' . uniqid() . '.' . $file_extension;
                $target_path = $upload_dir . $unique_filename;

                if (!move_uploaded_file($tmp_name, $target_path)) {
                    throw new Exception('Error al mover el archivo de imagen subido.');
                }
                // Guardamos la ruta relativa desde la raíz del proyecto para usarla en <img src="...">
                $url_media = '/spartanproject/uploads/ejercicios/' . $unique_filename;
                $image_counter++;
            } else {
                 throw new Exception('Error en la subida de la imagen. Código: ' . ($_FILES['media_files']['error'][$image_counter] ?? 'N/A'));
            }

        } elseif ($type === 'VIDEO_LINK') {
            // Verificamos que el link sea una URL válida
            if (isset($_POST['media_links'][$link_counter]) && filter_var($_POST['media_links'][$link_counter], FILTER_VALIDATE_URL)) {
                $url_media = $_POST['media_links'][$link_counter];
                $link_counter++;
            } else {
                throw new Exception('El link de video proporcionado no es una URL válida.');
            }
        }

        // Si tenemos una URL válida (de imagen o video), la insertamos en la base de datos
        if (!empty($url_media)) {
            $stmt_media = mysqli_prepare($conexion, "INSERT INTO ejercicios_media (idejercicio, tipo_media, url_media, orden) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_media, "issi", $idejercicio_creado, $type, $url_media, $orden);
            
            if (!mysqli_stmt_execute($stmt_media)) {
                // Si la inserción falla, borramos el archivo que acabamos de subir para no dejar basura.
                if ($type === 'IMAGEN' && file_exists($target_path)) {
                    unlink($target_path);
                }
                throw new Exception("Error al guardar el medio en la base de datos: " . mysqli_stmt_error($stmt_media));
            }
            mysqli_stmt_close($stmt_media);
            $orden++;
        }
    }

    // 6. CONFIRMAR TRANSACCIÓN
    // Si todo ha ido bien, confirmamos todos los cambios en la base de datos.
    mysqli_commit($conexion);
    echo json_encode(['success' => true, 'message' => '¡Ejercicio guardado con éxito!']);

} catch (Exception $e) {
    // 7. REVERTIR TRANSACCIÓN EN CASO DE ERROR
    // Si algo falló, revertimos todos los cambios para no dejar datos corruptos.
    mysqli_rollback($conexion);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// 8. CERRAR CONEXIÓN
mysqli_close($conexion);
?>
