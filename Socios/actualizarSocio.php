<?php
require_once '../conexion.php';

function actualizarSocio() {
    $conexion = conectar();

    // Validar que los datos POST existen
    $campos_requeridos = ['idsocio', 'nombre', 'apellido', 'dni', 'telef', 'email', 'fechalta', 'direc'];
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo])) {
            header('Location: socios.php?error=faltan_datos');
            exit;
        }
    }

    $idsocio = $_POST['idsocio'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $telef = $_POST['telef'];
    $email = $_POST['email'];
    $fechalta = $_POST['fechalta'];
    $direc = $_POST['direc'];
    $foto_actual_path = '';

    // --- Lógica para manejar la subida de la nueva foto ---
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
        // Primero, obtener la ruta de la foto antigua para borrarla si se sube una nueva
        $sql_foto_antigua = "SELECT foto FROM socios WHERE idsocio = ?";
        $stmt_foto = mysqli_prepare($conexion, $sql_foto_antigua);
        mysqli_stmt_bind_param($stmt_foto, "i", $idsocio);
        mysqli_stmt_execute($stmt_foto);
        $resultado_foto = mysqli_stmt_get_result($stmt_foto);
        if ($fila = mysqli_fetch_assoc($resultado_foto)) {
            $foto_antigua = $fila['foto'];
            if (!empty($foto_antigua) && file_exists($foto_antigua)) {
                unlink($foto_antigua); // Borrar el archivo de imagen antiguo
            }
        }
        mysqli_stmt_close($stmt_foto);

        // Ahora, procesar la nueva imagen
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/spartanproject/uploads/socios/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION);
        $new_file_name = "socio_" . $idsocio . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_file_name;

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $foto_actual_path = $target_file;
        } else {
            header('Location: socios.php?error=subida_fallida');
            exit;
        }
    }

    // --- Preparar y ejecutar la consulta SQL ---
    if (!empty($foto_actual_path)) {
        // Si se subió una nueva foto, actualizar la ruta en la BD
        $sql = "UPDATE socios SET nombre=?, apellido=?, dni=?, telef=?, email=?, fechalta=?, direc=?, foto=? WHERE idsocio=?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssi", $nombre, $apellido, $dni, $telef, $email, $fechalta, $direc, $foto_actual_path, $idsocio);
    } else {
        // Si no se subió foto, actualizar solo los demás datos
        $sql = "UPDATE socios SET nombre=?, apellido=?, dni=?, telef=?, email=?, fechalta=?, direc=? WHERE idsocio=?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssi", $nombre, $apellido, $dni, $telef, $email, $fechalta, $direc, $idsocio);
    }

    if (mysqli_stmt_execute($stmt)) {
        header('Location: socios.php?success=actualizacion_exitosa');
    } else {
        header('Location: socios.php?error=actualizacion_fallida');
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
}

actualizarSocio();
?>
