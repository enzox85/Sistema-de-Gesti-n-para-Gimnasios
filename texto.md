<?php
require_once '../conexion.php';
$conexion = conectar();

// Lógica del buscador
$busqueda = $_POST['busqueda'] ?? '';
$sql = "SELECT * FROM socios";
if (!empty($busqueda)) {
    $sql .= " WHERE nombre LIKE ? OR apellido LIKE ? OR dni LIKE ?";
}
$sql .= " ORDER BY idsocio DESC";

$stmt = mysqli_prepare($conexion, $sql);

if (!empty($busqueda)) {
    $like_busqueda = "%{$busqueda}%";
    mysqli_stmt_bind_param($stmt, "sss", $like_busqueda, $like_busqueda, $like_busqueda);
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Socios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            overflow-x: hidden; /* Previene el scroll horizontal no deseado */
        }
        
        #wrapper {
            display: flex;
            transition: all 0.3s ease;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: 280px; /* Ancho fijo del sidebar */
            margin-left: 0;
            transition: margin 0.3s ease;
        }

        #page-content-wrapper {
            flex-grow: 1;
            width: calc(100% - 280px);
            transition: width 0.3s ease;
        }

        /* ESTADO OCULTO (TOGGLED) */
        #wrapper.toggled #sidebar-wrapper {
            margin-left: -280px; /* Oculta el sidebar moviéndolo a la izquierda */
        }

        #wrapper.toggled #page-content-wrapper {
            width: 100%; /* El contenido principal ocupa todo el ancho */
        }

        /* Estilos para pantallas pequeñas */
        @media (max-width: 768px) {
            #sidebar-wrapper {
                margin-left: -280px; /* Oculto por defecto en móvil */
            }
            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0; /* Se muestra al hacer toggle */
            }
            #page-content-wrapper {
                width: 100%;
            }
        }

        .profile-pic { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body>

<div id="wrapper">

    <!-- Sidebar Wrapper -->
    <div id="sidebar-wrapper">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/includes/sidebar.php'; ?>
    </div>

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper">
        <div class="container-fluid p-3 p-md-4">
            
            <!-- Botón para mostrar/ocultar el menú -->
            <button class="btn btn-primary mb-3" id="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h2 class="mb-0"><i class="fas fa-users me-2"></i>Gestión de Socios</h2>
                </div>
                <div class="card-body">
                    <!-- Controles y tabla (tu código que ya te gusta) -->
                    <div class="row mb-4 g-3">
                        <div class="col-md-8">
                            <form action="socios.php" method="POST" class="d-flex">
                                <input type="text" class="form-control me-2" name="busqueda" placeholder="Buscar..." value="<?php echo htmlspecialchars($busqueda); ?>">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button type="button" class="btn btn-danger w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#modalNuevoSocio">
                                <i class="fas fa-user-plus me-1"></i>Nuevo Socio
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>DNI</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($resultado && $resultado->num_rows > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $foto_nombre = $row['foto'] ?? null;
                                            $foto_path = '../uploads/fotos_perfil/' . $foto_nombre;
                                            if (empty($foto_nombre) || !file_exists($foto_path)) {
                                                $foto_path = '../uploads/fotos_perfil/default.png';
                                            }
                                            ?>
                                            <img src="<?php echo $foto_path; ?>?v=<?php echo time(); ?>" alt="Foto de perfil" class="profile-pic">
                                        </td>
                                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($row['dni']); ?></td>
                                        <td><?php echo htmlspecialchars($row['telef']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-sm" title="Ver Ficha"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No se encontraron socios.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tu Modal de Nuevo Socio (sin cambios) -->
<div class="modal fade" id="modalNuevoSocio" tabindex="-1">
    <!-- ... tu código del modal aquí ... -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // El script que hace la magia
    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("wrapper").classList.toggle("toggled");
    });
</script>
</body>
</html>
