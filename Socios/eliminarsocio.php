<?php

// 1. RUTA CORREGIDA: Subimos un nivel para encontrar la conexión.
include("../conexion.php");
$con = conectar();

// 2. VALIDACIÓN: Nos aseguramos de que el ID sea un número válido.
if (!isset($_GET['idsocio']) || !is_numeric($_GET['idsocio'])) {
    header("Location: socios.php?error=invalid_id");
    exit;
}

$id_socio = $_GET['idsocio'];

// 3. SENTENCIAS PREPARADAS: Máxima seguridad contra inyección SQL.

// Eliminar primero los registros relacionados en 'cuotas'.
$stmtCuotas = mysqli_prepare($con, "DELETE FROM cuotas WHERE idsocio = ?");
mysqli_stmt_bind_param($stmtCuotas, "i", $id_socio);
mysqli_stmt_execute($stmtCuotas);
mysqli_stmt_close($stmtCuotas);

// Ahora eliminar el socio.
// NOTA: Si tienes claves foráneas con ON DELETE CASCADE (como en socios_rutinas_asignadas),
// los registros en esas tablas se borrarán automáticamente, lo cual es ideal.
$stmtSocio = mysqli_prepare($con, "DELETE FROM socios WHERE idsocio = ?");
mysqli_stmt_bind_param($stmtSocio, "i", $id_socio);
$success = mysqli_stmt_execute($stmtSocio);
mysqli_stmt_close($stmtSocio);

// 4. REDIRECCIÓN: Informamos si la operación fue exitosa o no.
if ($success) {
    header("Location: socios.php?success=deleted");
    exit;
} else {
    header("Location: socios.php?error=delete_failed");
    exit;
}

?>
