<?php
// Iniciar la sesión al principio de todo. Es crucial para que funcione el login.
session_start();

// Si el usuario ya está logueado, redirigir a la página principal para evitar que vea el login de nuevo.
if (isset($_SESSION['id_usuario'])) {
    // Más adelante, aquí podemos redirigir según el rol.
    header('Location: index.php');
    exit();
}


$error_message = '';

// Comprobar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    require 'conexion.php';
    $conexion = conectar();
    $email = $_POST['email'];
    $password = $_POST['password'];

    // --- Buena práctica: Usar sentencias preparadas para evitar inyección SQL ---
    // MODIFICADO: Ahora unimos con `personas` para obtener el nombre y apellido.
    $sql = "SELECT u.id_usuario, u.email, u.password, u.rol, p.nombre, p.apellido
            FROM usuarios u
            JOIN personas p ON u.id_persona = p.id_persona
            WHERE u.email = ?";
    
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);

    // --- Buena práctica: Usar password_verify para comparar la contraseña ---
    if ($usuario && password_verify($password, $usuario['password'])) {
        // ¡Login exitoso!

        // Guardar datos esenciales del usuario en la sesión
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['rol'] = $usuario['rol'];
        
        // Guardar el nombre completo si existe (para socios)
        $nombre_completo = trim($usuario['nombre'] . ' ' . $usuario['apellido']);
        $_SESSION['nombre'] = $nombre_completo ?: $usuario['email']; // Si no hay nombre, usar email

        // Redirigir según el rol
        if ($usuario['rol'] == 'admin') {
            header('Location: /spartanproject/admin_dashboard.php');
        } else {
            header('Location: /spartanproject/socio_portal.php');
        }
        exit();

    } else {
        // Error de autenticación
        $error_message = "El email o la contraseña son incorrectos.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Spartan Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Iniciar Sesión</h2>
                
                <?php if(!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
