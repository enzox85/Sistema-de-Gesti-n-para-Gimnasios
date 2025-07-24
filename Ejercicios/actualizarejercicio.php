<?php
header('Content-Type: application/json');
include("../conexion.php");

$con = conectar();
$response = ['success' => false, 'message' => 'Datos incompletos.'];

if (isset($_POST['idejercicio'])) {
    $con->begin_transaction();
    try {
        // 1. Actualizar datos principales del ejercicio
        $sql_update = "UPDATE ejercicios SET nomb_ejer = ?, grupo_mus = ?, nivel_dificultad = ?, descripcion = ? WHERE idejercicio = ?";
        $stmt = $con->prepare($sql_update);
        $stmt->bind_param(
            "ssssi",
            $_POST['nomb_ejer'],
            $_POST['grupo_mus'],
            $_POST['nivel_dificultad'],
            $_POST['descripcion'],
            $_POST['idejercicio']
        );
        $stmt->execute();
        $stmt->close();

        // 2. Eliminar medios marcados
        if (!empty($_POST['eliminar_media'])) {
            // Primero, obtener las URLs de los archivos a eliminar para borrarlos del servidor
            $ids_a_eliminar = implode(',', array_map('intval', $_POST['eliminar_media']));
            $sql_select_urls = "SELECT url_media, tipo_media FROM ejercicios_media WHERE idmedia IN ($ids_a_eliminar)";
            $result_urls = $con->query($sql_select_urls);
            while ($row = $result_urls->fetch_assoc()) {
                if ($row['tipo_media'] == 'IMAGEN' && !empty($row['url_media'])) {
                    $file_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($row['url_media'], PHP_URL_PATH);
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }

            // Ahora, eliminar los registros de la base de datos
            $sql_delete = "DELETE FROM ejercicios_media WHERE idmedia IN ($ids_a_eliminar)";
            $con->query($sql_delete);
        }

        // 3. Insertar nuevos medios (reutilizando lógica de guardarejercicio.php)
        $idejercicio = $_POST['idejercicio'];
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/uploads/ejercicios/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $sql_insert_media = "INSERT INTO ejercicios_media (idejercicio, tipo_media, url_media, orden) VALUES (?, ?, ?, ?)";
        $stmt_media = $con->prepare($sql_insert_media);
        $orden = 1; // Se podría mejorar para continuar el orden, pero para este caso es suficiente

        // Procesar archivos subidos
        if (!empty($_FILES['media_files']['name'][0])) {
            foreach ($_FILES['media_files']['name'] as $key => $name) {
                $tmp_name = $_FILES['media_files']['tmp_name'][$key];
                $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                $new_filename = uniqid('ejer' . $idejercicio . '_', true) . '.' . $file_ext;
                $destination = $upload_dir . $new_filename;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $url_media = '/spartanproject/uploads/ejercicios/' . $new_filename;
                    $tipo_media = 'IMAGEN';
                    $stmt_media->bind_param("issi", $idejercicio, $tipo_media, $url_media, $orden);
                    $stmt_media->execute();
                    $orden++;
                }
            }
        }

        // Procesar links de video
        if (!empty($_POST['media_links'])) {
            foreach ($_POST['media_links'] as $link) {
                if (!empty($link)) {
                    $url_media = $link;
                    $tipo_media = 'VIDEO_LINK';
                    $stmt_media->bind_param("issi", $idejercicio, $tipo_media, $url_media, $orden);
                    $stmt_media->execute();
                    $orden++;
                }
            }
        }
        $stmt_media->close();

        // Si todo fue bien, confirmar transacción
        $con->commit();
        $response = ['success' => true, 'message' => 'Ejercicio actualizado correctamente.'];

    } catch (Exception $e) {
        $con->rollback();
        $response['message'] = 'Error en la transacción: ' . $e->getMessage();
    }
}

$con->close();
echo json_encode($response);
