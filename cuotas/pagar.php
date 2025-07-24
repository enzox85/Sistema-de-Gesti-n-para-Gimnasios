<?php
include("../conexion.php");
$con = conectar();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idcuota = $_POST['idcuota'];
    $monto = $_POST['monto'];
    
    // Validación básica
    if(!is_numeric($idcuota) || !is_numeric($monto)) {
        header("Location: cuotas.php?error=Datos inválidos");
        exit();
    }
    
    // FORMA SEGURA (versión procedural compatible con tu conexión)
    $stmt = mysqli_prepare($con, "UPDATE cuotas 
                                 SET estado = 'PAGADA', 
                                     fecha_pago = CURDATE(), 
                                     monto = ? 
                                 WHERE idcuota = ?");
    
    mysqli_stmt_bind_param($stmt, "di", $monto, $idcuota);
    
    if(mysqli_stmt_execute($stmt)) {
        header("Location: cuotas.php?success=Pago registrado correctamente");
    } else {
        header("Location: cuotas.php?error=Error al registrar pago: " . mysqli_error($con));
    }
    
    mysqli_stmt_close($stmt);
    exit();
}

// Redirección por defecto si no es POST
header("Location: cuotas.php");
?>