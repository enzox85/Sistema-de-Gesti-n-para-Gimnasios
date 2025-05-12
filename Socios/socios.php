<?php
include("conexion.php");
$con = conectar();

$sql = "SELECT * FROM socios ORDER BY apellido";
$query = mysqli_query($con, $sql);
?>

<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Socios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .foto-perfil {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #dee2e6;
        }
        .acciones-btn {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include $_SERVER['DOCUMENT_ROOT'].'/spartanproject/includes/sidebar.php'; ?>
        
        <!-- Contenido principal -->
        <div class="flex-grow-1 p-4">
            <h2>Gestión de Socios</h2>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-success" onclick="window.location.href='http://localhost/spartanproject/Socios/nuevosocio.php'">Nuevo Socio</button>
            </div>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>DNI</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th class="acciones-btn">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr>
                        <td>
                            <?php if(!empty($row['foto'])): ?>
                               <img src="<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $row['foto']); ?>" 
                                class="foto-perfil" 
                                alt="Foto de <?php echo $row['nombre']; ?>"
                                data-bs-toggle="modal" 
                                data-bs-target="#fotoModal"
                                onclick="document.getElementById('fotoAmpliada').src=this.src">          
                             <?php else: ?>
                                <div class="foto-perfil bg-secondary d-flex align-items-center justify-content-center">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($row['dni']); ?></td>
                            <td><?php echo htmlspecialchars($row['direc']); ?></td>
                            <td><?php echo htmlspecialchars($row['telef']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="acciones-btn">
                            <button 
                                class="btn btn-warning btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editarModal" 
                                onclick='cargarDatos(<?php echo json_encode($row); ?>)'>
                                Editar
                            </button>
                            <button 
                                class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#confirmarEliminarModal" 
                                onclick="setIdEliminar(<?php echo $row['idsocio']; ?>)">
                                Eliminar
                            </button>
                        </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img src="" id="fotoAmpliada" class="img-fluid w-100" alt="Foto ampliada">
      </div>
    </div>
  </div>
</div>

<!-- Modal para editar socio -->
<div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="editar.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="editarModalLabel">Editar Socio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="idsocio" id="editar_idsocio">
          <div class="mb-3">
            <label for="editar_nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="editar_nombre" required>
          </div>
          <div class="mb-3">
            <label for="editar_apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" id="editar_apellido" required>
          </div>
          <div class="mb-3">
            <label for="editar_dni" class="form-label">DNI</label>
            <input type="text" class="form-control" name="dni" id="editar_dni" required>
          </div>
          <!-- Agrega los demás campos según tu base de datos -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de Confirmación ELIMINAR -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-labelledby="confirmarEliminarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmarEliminarLabel">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas eliminar este usuario? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="btnEliminarConfirmado" href="#" class="btn btn-danger">Eliminar</a>
      </div>
    </div>
  </div>
</div>



<!--  SCRIPTS CRUD -->
<Script>   
function cargarDatos(data) {
    document.getElementById('editar_idsocio').value = data.idsocio;
    document.getElementById('editar_nombre').value = data.nombre;
    document.getElementById('editar_apellido').value = data.apellido;
    document.getElementById('editar_dni').value = data.dni;
    // Agrega aquí los demás campos como direc, telef, email si están en el modal
}

</script>

<script>
function setIdEliminar(id) {
    document.getElementById('btnEliminarConfirmado').href = "eliminarsocio.php?id=" + id;
}
</script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>!-- Bootstrap Icons -->

    <!-- Modal para mostrar la foto ampliada -->



</body>
</html>