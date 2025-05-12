<?php
include("../conexion.php"); // ¡IMPORTANTE!
$con = conectar();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['nomb_ejer']) || empty($_POST['descripcion']) || empty($_POST['grupo_mus']) || empty($_POST['nivel_dificultad'])) {
        die("Error: Todos los campos son obligatorios.");
    }

    $nomb_ejer = $_POST['nomb_ejer'];
    $descripcion = $_POST['descripcion'];
    $grupo_mus = $_POST['grupo_mus'];
    $nivel_dificultad = $_POST['nivel_dificultad'];
    $video_ejemplo = $_POST['video_ejemplo']; // este campo puede ser opcional

    // Manejo de imagen
    $carpeta_destino = "uploads/";
    $ruta_absoluta = __DIR__ . '/' . $carpeta_destino;

    if (isset($_FILES["imagen_ejemplo"]) && $_FILES["imagen_ejemplo"]["error"] == 0) {
        if (!file_exists($ruta_absoluta)) {
            mkdir($ruta_absoluta, 0755, true);
        }

        $nombre_archivo = uniqid() . '_' . basename($_FILES["imagen_ejemplo"]["name"]);
        $ruta_destino = $ruta_absoluta . $nombre_archivo;
        $foto_relativa = $carpeta_destino . $nombre_archivo;

        $check = getimagesize($_FILES["imagen_ejemplo"]["tmp_name"]);
        if ($check !== false) {
            if (!move_uploaded_file($_FILES["imagen_ejemplo"]["tmp_name"], $ruta_destino)) {
                die("Error: No se pudo subir la imagen.");
            }
            $ruta_imagen = $foto_relativa;
        } else {
            die("Error: El archivo no es una imagen válida.");
        }
    }

    // Insertar en base de datos
    $sql = "INSERT INTO ejercicios (nomb_ejer, descripcion, grupo_mus, nivel_dificultad, imagen_ejemplo, video_ejemplo) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssss", $nomb_ejer, $descripcion, $grupo_mus, $nivel_dificultad, $ruta_imagen, $video_ejemplo);

    if ($stmt->execute()) {
        $mensaje = "Ejercicio guardado correctamente.";
    } else {
        $mensaje = "Error al guardar en la base de datos: " . $stmt->error;
    }
    
    $stmt->close();
    $con->close();

    header("Location: /spartanproject/Ejercicios/ejerciciosmain.php?mensaje=" . urlencode($mensaje));
    exit();
}
?>
