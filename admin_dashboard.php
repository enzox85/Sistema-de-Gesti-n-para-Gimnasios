<?php
session_start();

// ---Seguridad ---
// Si el usuario no está logueado o no tiene el rol de 'admin', lo redirigimos al login.
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit(); // Es importante terminar el script después de redirigir.
}

// Incluimos el sidebar y la conexión
require 'includes/sidebar.php';
require 'conexion.php';
$conexion = conectar();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/sidebar.css">
</head>
<body>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1>Panel de Administrador</h1>
                    <p class="lead">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>.</p>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Socios</div>
                        <div class="card-body">
                            <h5 class="card-title">Gestionar Socios</h5>
                            <p class="card-text">Ver, agregar, editar y eliminar socios.</p>
                            <a href="/spartanproject/Socios/socios.php" class="btn btn-light">Ir a Socios</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Ejercicios</div>
                        <div class="card-body">
                            <h5 class="card-title">Gestionar Ejercicios</h5>
                            <p class="card-text">Administrar la biblioteca de ejercicios del gimnasio.</p>
                            <a href="/spartanproject/Ejercicios/ejercicios_view.php" class="btn btn-light">Ir a Ejercicios</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">Planes</div>
                        <div class="card-body">
                            <h5 class="card-title">Gestionar Planes</h5>
                            <p class="card-text">Crear y asignar planes de entrenamiento.</p>
                            <a href="/spartanproject/Planes/planes.php" class="btn btn-light">Ir a Planes</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Aquí se podrían agregar más tarjetas o contenido -->

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sidebar.js"></script>
</body>
</html>
