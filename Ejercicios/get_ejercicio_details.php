<?php
header('Content-Type: application/json');
include("../conexion.php");

$response = ['success' => false, 'message' => 'ID de ejercicio no proporcionado.'];

if (isset($_GET['id'])) {
    $con = conectar();
    $id_ejercicio = intval($_GET['id']);

    // Obtener datos principales del ejercicio
    $sql_ejercicio = "SELECT idejercicio, nomb_ejer, grupo_mus, nivel_dificultad, descripcion FROM ejercicios WHERE idejercicio = ?";
    $stmt_ejercicio = $con->prepare($sql_ejercicio);
    $stmt_ejercicio->bind_param("i", $id_ejercicio);
    $stmt_ejercicio->execute();
    $result_ejercicio = $stmt_ejercicio->get_result();

    if ($ejercicio = $result_ejercicio->fetch_assoc()) {
        // Obtener datos de los medios
        $sql_media = "SELECT id_media, tipo_media, url_media FROM ejercicios_media WHERE idejercicio = ? ORDER BY orden ASC";
        $stmt_media = $con->prepare($sql_media);
        $stmt_media->bind_param("i", $id_ejercicio);
        $stmt_media->execute();
        $result_media = $stmt_media->get_result();
        $media = $result_media->fetch_all(MYSQLI_ASSOC);

        $response = [
            'success' => true,
            'ejercicio' => $ejercicio,
            'media' => $media
        ];
        
        $stmt_media->close();
    } else {
        $response['message'] = 'Ejercicio no encontrado.';
    }
    
    $stmt_ejercicio->close();
    $con->close();
}

echo json_encode($response);
