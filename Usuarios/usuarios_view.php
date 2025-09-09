<?php
// Recibe $usuarios desde el controlador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/includes/sidebar.php'; ?>
    <main class="flex-grow-1 p-4">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><i class="fas fa-user-shield me-2"></i>Gestión de Usuarios</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-4 g-3 align-items-center">
                        <div class="col-md-8">
                            <h4>Registrar nuevo usuario</h4>
                            <form action="usuarios_controller.php?accion=crear" method="POST" class="mb-4">
                                <div class="mb-3">
                                    <label for="id_persona" class="form-label">Persona</label>
                                    <select name="id_persona" id="id_persona" class="form-control" required>
                                        <option value="">Selecciona una persona...</option>
                                        <?php
                                        if (isset($personas) && is_array($personas)) {
                                            foreach ($personas as $p) {
                                                echo '<option value="' . $p['id_persona'] . '">' . htmlspecialchars($p['apellido']) . ', ' . htmlspecialchars($p['nombre']) . ' (DNI: ' . htmlspecialchars($p['dni']) . ')</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="rol" class="form-label">Rol</label>
                                    <select name="rol" id="rol" class="form-control">
                                        <option value="admin">Admin</option>
                                        <option value="empleado">Empleado</option>
                                        <option value="socio">Socio</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Registrar</button>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Email</th><th>Rol</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                    <tr><td colspan="5" class="text-center">No hay usuarios registrados.</td></tr>
                                <?php else: foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?php echo $usuario['id_usuario']; ?></td>
                                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
