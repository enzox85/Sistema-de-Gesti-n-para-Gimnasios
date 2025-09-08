<?php
// ===== INICIO DE BLOQUE DE MANEJO DE ERRORES =====
// Ocultamos los errores de PHP para que no corrompan el JSON.
ini_set('display_errors', 0); 
// Creamos un manejador de errores personalizado para capturar warnings y notices.
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
// ===== FIN DE BLOQUE DE MANEJO DE ERRORES =====

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/Planes/modelo_planes.php';

$respuesta = ['status' => 'error', 'message' => 'Acción no válida.'];

try {
    $accion = $_GET['accion'] ?? null;

    if ($accion == 'asignar') {
        if (isset($_POST['idsocio'], $_POST['fecha_inicio'])) {
            $conexion = conectar();
            
            if (asignarNuevoPlan($conexion, $_POST)) {
                $respuesta = ['status' => 'success', 'message' => 'Plan asignado con éxito.'];
            } else {
                // Si asignarNuevoPlan devuelve false, puede ser por un error de ejecución de la consulta
                $error_db = mysqli_error($conexion);
                $respuesta['message'] = $error_db ? 'Error en la base de datos: ' . $error_db : 'Error desconocido al asignar el plan.';
            }
            mysqli_close($conexion);
        } else {
            $respuesta['message'] = 'Faltan datos para asignar el plan.';
        }
    }
} catch (Exception $e) {
    // Si ocurre cualquier error o warning en el código, lo capturamos aquí.
    http_response_code(500); // Es buena práctica enviar un código de error de servidor.
    $respuesta['message'] = 'Error interno del servidor: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine();
} finally {
    // Restauramos el manejador de errores original.
    restore_error_handler();
}

echo json_encode($respuesta);
?>