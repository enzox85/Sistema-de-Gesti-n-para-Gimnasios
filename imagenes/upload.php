<?php
// Configuración básica
$target_dir = "uploads/";
$uploadOk = 1;
$mensaje = "";

// Crear directorio si no existe
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Verificar si se envió el formulario completo (no solo la imagen)
if(isset($_POST["submit"]) && isset($_FILES["fileToUpload"])) {
    $nombre_archivo = basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . uniqid() . '_' . $nombre_archivo; // Agregamos un ID único para evitar duplicados
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si es una imagen real
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check === false) {
        $mensaje = "El archivo no es una imagen válida.";
        $uploadOk = 0;
    }

    // Verificar tamaño del archivo (2MB máximo)
    if ($_FILES["fileToUpload"]["size"] > 2000000) {
        $mensaje = "La imagen es demasiado grande (máximo 2MB).";
        $uploadOk = 0;
    }

    // Permitir solo ciertos formatos
    $formatos_permitidos = ["jpg", "jpeg", "png", "gif"];
    if(!in_array($imageFileType, $formatos_permitidos)) {
        $mensaje = "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
        $uploadOk = 0;
    }

    // Si todo está bien, subir el archivo
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $mensaje = "La imagen ". htmlspecialchars($nombre_archivo). " se subió correctamente.";
            
            // Aquí deberías guardar $target_file en tu base de datos junto con los demás datos del socio
            // Por ejemplo: $foto_perfil = $target_file;
            
        } else {
            $mensaje = "Ocurrió un error al subir la imagen.";
        }
    }
    
    // Redireccionar o mostrar mensaje (depende de tu flujo)
    // header("Location: nuevosocio.php?mensaje=".urlencode($mensaje));
    // exit();
}
?>