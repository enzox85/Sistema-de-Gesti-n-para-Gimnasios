<?php
include("conexion.php");
$con = conectar();

$sql = "SELECT * FROM ejercicios ORDER BY nomb_ejer ASC";
$query = mysqli_query($con, $sql);
?>
<!--Mensaje EXITO-->
<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'exito'): ?>
<script>
  alert("¡Éxito! Los datos han sido guardados correctamente.");
</script>
<?php endif; ?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Ejercicios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
 <style>
        .imagen {
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
<body class="p-4">

  <!-- Filtros y búsqueda -->
  <div class="d-flex gap-2 align-items-center mb-3">
    <button class="btn btn-outline-secondary">Filtrar</button>
    <input type="text" class="form-control w-25" placeholder="Buscar ejercicio...">
    <button class="btn btn-danger ms-auto" data-bs-toggle="modal" data-bs-target="#modalAgregarEjercicio">Añadir ejercicio</button>
  </div>

  <!-- Combos de filtro -->
  <div class="row mb-4">
    <div class="col-md-2">
      <select class="form-select">
        <option selected>Tipo de ejercicio</option>
        <option>Fuerza</option>   
        <option selected>Grupo muscular</option>
        <option>Pierna</option>
        <option>Brazo</option>
        <option>Pecho</option>
        <option>Espalda</option>
        <option>Hombro</option>
        <option>Abdomen</option>
      </select>
    </div>
    <div class="col-md-2">  
      <select class="form-select">
        <option selected>Nivel de esfuerzo</option>
        <option>Principiante</option>
        <option>Intermedio</option>
        <option>Avanzado</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select">
        <option selected>Perfil de resistencia</option>
        <option>Alta</option>
        <option>Media</option>
        <option>Baja</option>
      </select>
    </div>
  </div>

  <!-- Tabla de ejercicios -->
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Grupo Muscular</th>
        <th>Nivel de Dificultad</th>
        <th>Opciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $query->fetch_assoc()): ?>
        <tr>
            <td><?php if (!empty($row['imagen_ejemplo'])): ?>
                <img 
                  src="<?php echo htmlspecialchars($row['imagen_ejemplo']); ?>" 
                  class="imagen" 
                  alt="Imagen de <?php echo htmlspecialchars($row['nomb_ejer']); ?>" 
                  data-bs-toggle="modal" 
                  data-bs-target="#modalImagenAmpliada" 
                  onclick="mostrarImagenAmpliada('<?php echo htmlspecialchars($row['imagen_ejemplo']); ?>')"
                  style="cursor: pointer;"
                >
              <?php else: ?>
                <div class="imagen bg-secondary d-flex align-items-center justify-content-center">
                  <i class="bi bi-person text-white"></i>
                </div>
              <?php endif; ?></td>
            <td><?php echo htmlspecialchars($row['nomb_ejer']); ?></td>
            <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($row['grupo_mus']); ?></td>
            <td><?php echo htmlspecialchars($row['nivel_dificultad']); ?></td>
            <td class="acciones-btn">
                <a href="editar.php?id=<?php echo $row['idejercicio']; ?>" class="btn btn-warning btn-sm">Editar</a>
                <a href="eliminarejercicio.php?id=<?php echo $row['idejercicio']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
            </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Modal -->
  <div class="modal fade" id="modalAgregarEjercicio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form class="modal-content" action="guardarejercicio.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Agregar nuevo ejercicio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre del ejercicio</label>
            <input type="text" id="nombre" name="nomb_ejer" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Grupo muscular</label>
            <select name="grupo_mus" class="form-select" required>
              <option value="">Seleccione</option>
              <option value="PIERNA">Pierna</option>
              <option value="BRAZO">Brazo</option>
              <option value="PECHO">Pecho</option>
              <option value="ESPALDA">Espalda</option>
              <option value="HOMBRO">Hombro</option>
              <option value="ABDOMEN">Abdomen</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nivel de dificultad</label>
            <select name="nivel_dificultad" class="form-select" required>
              <option value="">Seleccione</option>
              <option value="PRINCIPIANTE">Principiante</option>
              <option value="INTERMEDIO">Intermedio</option>
              <option value="AVANZADO">Avanzado</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="2" maxlength="150"></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Link de imagen</label>
            <input type="url" id="imagen" name="imagen_ejemplo" class="form-control" placeholder="https://...">
          </div>
          <div class="col-md-6">
            <label class="form-label">Subir imagen (opcional)</label>
            <input type="file" name="imagen_ejemplo" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Link de video</label>
            <input type="url" name="video_ejemplo" class="form-control" placeholder="https://youtube.com/...">
          </div>
          <div class="col-md-6">
            <label class="form-label">Subir video (opcional)</label>
            <input type="file" name="video_upload" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar ejercicio</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  
<!-- Modal para imagen ampliada --> 
<div class="modal fade" id="modalImagenAmpliada" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img id="imagenModal" src="" class="img-fluid w-100 rounded" alt="Imagen ampliada">
      </div>
    </div>
  </div>
</div>

<script>
function mostrarImagenAmpliada(url) {
  document.getElementById('imagenModal').src = url;
}
</script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
