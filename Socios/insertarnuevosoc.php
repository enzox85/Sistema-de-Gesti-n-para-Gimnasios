<?php
header('Content-Type: application/json');

require_once '../conexion.php';
require_once 'modelo_socios.php';

$respuesta = ['status' => 'error', 'message' => 'Datos inválidos o incompletos.'];

// Validamos que los datos esenciales del socio existen
if (isset($_POST['nombre'], $_POST['apellido'], $_POST['dni'])) {
    
    $conexion = conectar();

    // Recolectamos todos los datos del POST en un solo array
    $datosSocio = [
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'dni' => $_POST['dni'],
        'direccion' => $_POST['direccion'] ?? null,
        'telefono' => $_POST['telefono'] ?? null,
        'email' => $_POST['email'] ?? null,
        'fechalta' => $_POST['fechalta'] ?? date('Y-m-d'),
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        'probfis' => $_POST['probfis'] ?? null
    ];

    // El archivo de la foto se maneja por separado
    $archivoFoto = isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK ? $_FILES['foto'] : null;

    // Llamamos a la función del modelo
    if (crearSocio($conexion, $datosSocio, $archivoFoto)) {
        $respuesta = [
            'status' => 'success',
            'message' => 'Socio creado con éxito.'
        ];
    } else {
        // El error específico ya se guarda en el log del servidor gracias a error_log()
        $respuesta['message'] = 'Error en el servidor al crear el socio. Revise el log para más detalles.';
    }

    mysqli_close($conexion);
}

echo json_encode($respuesta);
?>