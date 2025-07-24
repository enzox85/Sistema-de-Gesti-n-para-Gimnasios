<?php
require_once '../conexion.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo json_encode(['error' => 'ID de rutina no válido.']);
    exit;
}

$idrutina = $_GET['id'];
$conexion = conectar();

if (!$conexion) {
    echo json_encode(['error' => 'Error de conexión a la base de datos.']);
    exit;
}

try {
    // 1. Obtener la información principal de la rutina
    $stmt_rutina = mysqli_prepare($conexion, "SELECT * FROM rutinas WHERE idrutina = ?");
    mysqli_stmt_bind_param($stmt_rutina, "i", $idrutina);
    mysqli_stmt_execute($stmt_rutina);
    $result_rutina = mysqli_stmt_get_result($stmt_rutina);
    $rutina = mysqli_fetch_assoc($result_rutina);

    if (!$rutina) {
        throw new Exception('Rutina no encontrada.');
    }

    // 2. Obtener los días y sus ejercicios asociados
    $stmt_dias = mysqli_prepare($conexion, "
        SELECT 
            rd.iddia, 
            rd.dia_semana,
            re.idejercicio_rutina,
            re.repeticiones,
            re.tiempo_descanso_seg AS descanso,
            e.idejercicio,
            e.nomb_ejer
        FROM rutinas_dias rd
        LEFT JOIN rutinas_ejercicios re ON rd.iddia = re.iddia
        LEFT JOIN ejercicios e ON re.idejercicio = e.idejercicio
        WHERE rd.idrutina = ?
        ORDER BY rd.iddia, re.orden
    ");
    mysqli_stmt_bind_param($stmt_dias, "i", $idrutina);
    mysqli_stmt_execute($stmt_dias);
    $result_detalles = mysqli_stmt_get_result($stmt_dias);

    $dias_organizados = [];
    while ($row = mysqli_fetch_assoc($result_detalles)) {
        $id_dia = $row['iddia'];
        if (!isset($dias_organizados[$id_dia])) {
            $dias_organizados[$id_dia] = [
                'iddia' => $id_dia,
                'dia_semana' => $row['dia_semana'],
                'ejercicios' => []
            ];
        }

        if ($row['idejercicio_rutina']) {
            $dias_organizados[$id_dia]['ejercicios'][] = [
                'idejercicio' => $row['idejercicio'],
                'nomb_ejer' => $row['nomb_ejer'],
                'repeticiones' => $row['repeticiones'],
                'descanso' => $row['descanso']
            ];
        }
    }

    $rutina['dias'] = array_values($dias_organizados);

    // 3. Devolver la estructura completa en JSON
    echo json_encode($rutina);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt_rutina)) mysqli_stmt_close($stmt_rutina);
    if (isset($stmt_dias)) mysqli_stmt_close($stmt_dias);
    if ($conexion) mysqli_close($conexion);
}
?>
