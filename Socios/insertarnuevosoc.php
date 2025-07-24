<?php
include("../conexion.php");
$con = conectar();

// 1. Configuración para subir la imagen
$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/spartanproject/Socios/uploads/";
$uploadOk = 1;
$mensaje = "";
$foto = '';

// Procesar imagen
if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $nombre_archivo = uniqid() . '_' . basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . $nombre_archivo;
    
    // Verificaciones de imagen (tu código actual)
    // ...
    
    if ($uploadOk == 1 && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $foto = $target_file;
    }
}

// 2. Insertar datos del socio
$nombre = mysqli_real_escape_string($con, $_POST['nombre']);
$apellido = mysqli_real_escape_string($con, $_POST['apellido']);
$dni = mysqli_real_escape_string($con, $_POST['dni']);
$direc = mysqli_real_escape_string($con, $_POST['direc']);
$telef = mysqli_real_escape_string($con, $_POST['telef']);
$email = mysqli_real_escape_string($con, $_POST['email']);
$fechalta = mysqli_real_escape_string($con, $_POST['fechalta']);
$probfis = isset($_POST['probfis']) ? mysqli_real_escape_string($con, $_POST['probfis']) : '';

$sql_socio = "INSERT INTO socios (nombre, apellido, dni, direc, telef, email, fechalta, foto, probfis) 
              VALUES ('$nombre', '$apellido', '$dni', '$direc', '$telef', '$email', '$fechalta', '$foto', '$probfis')";

if(mysqli_query($con, $sql_socio)){
    $idsocio = mysqli_insert_id($con);
}  
//   	$tipo_plan = isset($_POST['tipo_plan']) ? mysqli_real_escape_string($con, $_POST['tipo_plan']) : '';
// 	$descripcion_plan = ($tipo_plan == 'OTRO' && !empty($_POST['otro_plan']))
// 	? mysqli_real_escape_string($con, $_POST['otro_plan'])
// 	: $tipo_plan;


    
     
//     $peso_actual = floatval($_POST['peso_actual'] ?? 0);
//     $altura = intval($_POST['altura'] ?? 0);
//     $disponibilidad = mysqli_real_escape_string($con, $_POST['disponibilidad'] ?? '');
    
//     $sql_plan = "INSERT INTO planes_entrenamiento 
//                 (idsocio, tipo_plan, descripcion_plan, peso_actual, altura, 
//                  disponibilidad, fecha_inicio, activo)
//                 VALUES 
//                 ('$idsocio', '$tipo_plan', '$descripcion_plan', '$peso_actual', '$altura', 
//                  '$disponibilidad', CURDATE(), 1)";
    
//     if(mysqli_query($con, $sql_plan)){
//         $idplan = mysqli_insert_id($con);
//     }
	//DATOS TABLA DE ENTRENAMIENTO
   // Manejar el caso en que no se selecciona un plan
if (isset($_POST['tipo_plan'])) {
    $tipo_plan = mysqli_real_escape_string($con, $_POST['tipo_plan']);
    $descripcion_plan = ($tipo_plan == 'OTRO' && !empty($_POST['otro_plan']))
        ? mysqli_real_escape_string($con, $_POST['otro_plan'])
        : $tipo_plan;
} else {
    $tipo_plan = '';
    $descripcion_plan = '';
}

$peso_actual = floatval($_POST['peso_actual'] ?? 0);
$altura = intval($_POST['altura'] ?? 0);
$disponibilidad = mysqli_real_escape_string($con, $_POST['disponibilidad'] ?? '');

// Solo insertar en la tabla planes_entrenamiento si se proporcionó un tipo de plan
if (!empty($tipo_plan) || !empty($descripcion_plan)) {
    $sql_plan = "INSERT INTO planes_entrenamiento (idsocio, tipo_plan, descripcion_plan, peso_actual, altura, disponibilidad, fecha_inicio, activo)
    VALUES ('$idsocio', '$tipo_plan', '$descripcion_plan', '$peso_actual', '$altura', '$disponibilidad', CURDATE(), 1)";

    if(mysqli_query($con, $sql_plan)){
        $idplan = mysqli_insert_id($con);
    }
}
    // 4. Redirección
    echo '
    <script>
        if(confirm("¡Socio guardado correctamente!'.addslashes($mensaje).'\n\n¿Deseas agregar otro socio?")) {
            window.location.href = "/spartanproject/Index.php";
        } else {
            window.location.href = "socios.php";
        }
    </script>';


mysqli_close($con);
?>