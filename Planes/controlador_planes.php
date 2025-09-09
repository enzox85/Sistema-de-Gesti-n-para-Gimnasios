<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/Planes/modelo_planes.php';

$respuesta = ['status' => 'error', 'message' => 'Acción no válida.'];

$accion = $_GET['accion'] ?? null;

if ($accion == 'asignar') {
    if (isset($_POST['idsocio'], $_POST['fecha_inicio'])) {
        $conexion = conectar();
        
        if (asignarNuevoPlan($conexion, $_POST)) {
            $respuesta = ['status' => 'success', 'message' => 'Plan asignado con éxito.'];
        } else {
            $error_db = mysqli_error($conexion);
            $respuesta['message'] = $error_db ? 'Error en la base de datos: ' . $error_db : 'Error desconocido al asignar el plan.';
        }
        mysqli_close($conexion);
    } else {
        $respuesta['message'] = 'Faltan datos para asignar el plan.';
    }
} else if ($accion == 'eliminar') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['idplan'])) {
        $conexion = conectar();
        if (eliminarPlan($conexion, $data['idplan'])) {
            $respuesta = ['status' => 'success', 'message' => 'Plan eliminado con éxito.'];
        } else {
            $error_db = mysqli_error($conexion);
            $respuesta['message'] = $error_db ? 'Error en la base de datos: ' . $error_db : 'Error desconocido al eliminar el plan.';
        }
        mysqli_close($conexion);
    } else {
        $respuesta['message'] = 'No se proporcionó el ID del plan a eliminar.';
    }
} else if ($accion == 'obtener_detalles') {
    if (isset($_GET['idplan'])) {
        $conexion = conectar();
        $plan = obtenerPlanPorId($conexion, $_GET['idplan']);
        mysqli_close($conexion);

        if ($plan) {
            $respuesta = ['status' => 'success', 'data' => $plan];
        } else {
            $respuesta['message'] = 'Plan no encontrado.';
        }
    } else {
        $respuesta['message'] = 'No se proporcionó el ID del plan.';
    }
} else if ($accion == 'actualizar') {
    if (isset($_POST['idplan'])) {
        $conexion = conectar();
        if (actualizarPlan($conexion, $_POST)) {
            $respuesta = ['status' => 'success', 'message' => 'Plan actualizado con éxito.'];
        } else {
            $error_db = mysqli_error($conexion);
            $respuesta['message'] = $error_db ? 'Error en la base de datos: ' . $error_db : 'Error desconocido al actualizar el plan.';
        }
        mysqli_close($conexion);
    } else {
        $respuesta['message'] = 'Faltan datos para actualizar el plan.';
    }
}

echo json_encode($respuesta);
?>