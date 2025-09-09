<?php

require_once '../conexion.php';
require_once 'modelo_socios.php';

$conexion = conectar();

$accion = $_GET['accion'] ?? null;


if ($accion === 'eliminar') {
    $id_persona = filter_input(INPUT_GET, 'id_persona', FILTER_VALIDATE_INT);
    if ($id_persona) {
        eliminarSocio($conexion, $id_persona);
    }
    mysqli_close($conexion);
    header('Location: socios_controller.php');
    exit;
}

if ($accion === 'crear') {
    // Recibir datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $fechalta = $_POST['fechalta'] ?? date('Y-m-d');
    $probfis = $_POST['probfis'] ?? '';
    $foto = '';

    // Procesar la foto si se envió
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombreFoto = uniqid() . '_' . basename($_FILES['foto']['name']);
        $rutaDestino = __DIR__ . '/uploads/' . $nombreFoto;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
            $foto = $nombreFoto;
        }
    }

    // Validar datos mínimos
    if ($nombre && $apellido && $dni) {
        // Llamar al modelo para insertar
        $resultado = insertarNuevoSocio($conexion, [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'dni' => $dni,
            'fecha_nacimiento' => $fecha_nacimiento,
            'telefono' => $telefono,
            'email' => $email,
            'direccion' => $direccion,
            'fechalta' => $fechalta,
            'probfis' => $probfis,
            'foto' => $foto
        ]);
        if ($resultado) {
            echo json_encode(['status' => 'success', 'message' => 'Socio creado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al crear el socio.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios.']);
    }
    mysqli_close($conexion);
    exit;
}

// Procesar búsqueda y listado (por defecto)
$busqueda = $_POST['busqueda'] ?? '';
$socios = obtenerSocios($conexion, $busqueda);
mysqli_close($conexion);

// Cargar la vista y pasarle los datos
include 'socios_view.php';
?>