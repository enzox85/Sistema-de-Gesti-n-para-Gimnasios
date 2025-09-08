<?php
// Definimos la respuesta como JSON
header('Content-Type: application/json');

// Incluimos nuestros archivos principales
require_once '../conexion.php';
require_once 'modelo_ejercicios.php';

// Preparamos una respuesta por defecto
$respuesta = ['status' => 'error', 'message' => 'Petición inválida.'];

// Verificamos que se haya enviado un ID numérico
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idEjercicio = (int)$_GET['id'];
    
    $conexion = conectar();

    // Llamamos a la función centralizada en nuestro modelo
    if (eliminarEjercicio($conexion, $idEjercicio)) {
        $respuesta['status'] = 'success';
        $respuesta['message'] = 'Ejercicio eliminado correctamente.';
    } else {
        $respuesta['message'] = 'Error al eliminar el ejercicio. Es posible que ya haya sido eliminado.';
    }

    mysqli_close($conexion);
}

// Imprimimos la respuesta final en formato JSON
echo json_encode($respuesta);
?>