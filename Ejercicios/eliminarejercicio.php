<?php
include("../conexion.php");
$con = conectar();

// 1. Validar que se recibió un ID numérico desde la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idejercicio = (int)$_GET['id'];

    // 2. Iniciar una transacción para garantizar que todas las eliminaciones se completen con éxito
    mysqli_begin_transaction($con);

    try {
        // 3. ANTES de borrar el ejercicio, debemos borrar sus dependencias.
        // Primero, eliminamos los registros asociados en la tabla 'ejercicios_media'.
        // Esto es crucial para evitar errores de clave foránea (foreign key).
        $sql_delete_media = "DELETE FROM ejercicios_media WHERE idejercicio = ?";
        $stmt_delete_media = mysqli_prepare($con, $sql_delete_media);
        mysqli_stmt_bind_param($stmt_delete_media, "i", $idejercicio);
        mysqli_stmt_execute($stmt_delete_media);
        mysqli_stmt_close($stmt_delete_media);

        // 4. Ahora sí, eliminamos el ejercicio principal de la tabla 'ejercicios'.
        $sql_delete_ejercicio = "DELETE FROM ejercicios WHERE idejercicio = ?";
        $stmt_delete_ejercicio = mysqli_prepare($con, $sql_delete_ejercicio);
        mysqli_stmt_bind_param($stmt_delete_ejercicio, "i", $idejercicio);
        mysqli_stmt_execute($stmt_delete_ejercicio);
        mysqli_stmt_close($stmt_delete_ejercicio);

        // 5. Si ambas consultas se ejecutaron sin errores, confirmamos los cambios en la base de datos.
        mysqli_commit($con);

    } catch (mysqli_sql_exception $exception) {
        // 6. Si ocurrió algún error en el proceso, revertimos todos los cambios.
        mysqli_rollback($con);
        // En una aplicación real, aquí podrías registrar el error o mostrar un mensaje más amigable.
        die("Error al intentar eliminar el ejercicio: " . $exception->getMessage());
    }

    // 7. Una vez completada la operación, redirigimos al usuario de vuelta a la lista de ejercicios.
    header("Location: ejerciciosmain.php");
    exit(); // Es una buena práctica usar exit() después de una redirección para detener la ejecución del script.

} else {
    // Si no se proporcionó un ID válido en la URL, simplemente redirigimos al inicio.
    header("Location: ejerciciosmain.php");
    exit();
}
?>
