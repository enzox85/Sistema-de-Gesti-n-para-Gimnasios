

<?php
require_once '../conexion.php';
require_once 'modelo_socios.php';

$respuesta = ['status' => 'error', 'message' => 'Datos inválidos o incompletos.'];

// Validamos que el ID del socio a actualizar exista
$id_persona = filter_input(INPUT_POST, 'id_persona', FILTER_VALIDATE_INT);

if ($id_persona && isset($_POST['nombre'], $_POST['apellido'], $_POST['dni'])) {
    
    $conexion = conectar();

    $datosSocio = [
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'dni' => $_POST['dni'],
        'direccion' => $_POST['direccion'] ?? null,
        'telefono' => $_POST['telefono'] ?? null,
        'email' => $_POST['email'] ?? null,
        'fechalta' => $_POST['fechalta'] ?? date('Y-m-d'),
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        'probfis' => $_POST['probfis'] ?? null,
        'foto_actual' => $_POST['foto_actual'] ?? null // Pasamos la foto actual para poder borrarla si se cambia
    ];

    $archivoFoto = isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK ? $_FILES['foto'] : null;

    if (actualizarSocio($conexion, $id_persona, $datosSocio, $archivoFoto)) {
        $respuesta = ['status' => 'success', 'message' => 'Socio actualizado con éxito.'];
    } else {
        $respuesta['message'] = 'Error en el servidor al actualizar el socio. Revise el log para más detalles.';
    }

    mysqli_close($conexion);
}

echo json_encode($respuesta);
?>