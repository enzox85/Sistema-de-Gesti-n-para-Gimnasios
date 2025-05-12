<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Nuevo Socio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .file-upload {
      border: 2px dashed #dee2e6;
      border-radius: 5px;
      padding: 20px;
      text-align: center;
      margin-bottom: 20px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .file-upload:hover {
      background-color: #f8f9fa;
    }
    .sidebar {
      width: 250px;
      min-height: 100vh;
    }
    .img-preview {
      max-width: 200px;
      max-height: 200px;
      object-fit: cover;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <?php include $_SERVER['DOCUMENT_ROOT'].'/spartanproject/includes/sidebar.php'; ?>

    <!-- Contenido principal -->
    <div class="flex-grow-1 p-4">
      <h2 class="mb-4">Nuevo Socio</h2>
      
      <?php if(!empty($mensaje)): ?>
        <div class="alert alert-info mb-4"><?php echo $mensaje; ?></div>
      <?php endif; ?>

      <form action="insertarnuevosoc.php" method="POST" enctype="multipart/form-data">
        <!-- Sección de imagen de perfil -->
        <div class="card mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Foto de perfil</h5>
          </div>
          <div class="card-body">
            <div id="imagePreview" class="mb-3 text-center" style="display: none;">
              <img id="preview" src="#" alt="Vista previa" class="img-thumbnail img-preview">
            </div>

            <div class="file-upload">
              <label for="fileToUpload" class="form-label d-block">
                <i class="bi bi-camera fs-1"></i><br>
                <span id="fileName">Seleccionar imagen</span>
              </label>
              <input type="file" name="fileToUpload" id="fileToUpload" class="d-none" 
                     onchange="previewImage(this); updateFileName(this)">
            </div>
          </div>
        </div>

        <!-- Sección de datos personales -->
        <div class="card mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Datos personales</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Apellido</label>
                <input type="text" class="form-control" name="apellido" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">DNI</label>
                <input type="text" class="form-control" name="dni" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Teléfono</label>
                <input type="tel" class="form-control" name="telef" required>
              </div>
              
              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Fecha de alta</label>
                <input type="date" class="form-control" name="fechalta" required>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" class="form-control" name="direc" required>
              </div>
            </div>
          </div>
        </div>

        <!-- Sección de salud -->
        <div class="card mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Información de salud</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="form-check">
                <input type="checkbox" id="problemaFisico" class="form-check-input" onchange="toggleProblemaFisico()">
                <label class="form-check-label" for="problemaFisico">¿Tiene alguna enfermedad o problema físico?</label>
              </div>
              <input type="text" id="detalleProblema" class="form-control mt-2" name="probfis" placeholder="Especifique el problema" disabled>
            </div>
          </div>
        </div>

        <!-- Sección de plan de entrenamiento -->
        <div class="card mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Plan de entrenamiento</h5>
          </div>
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="form-check me-3">
                <input type="checkbox" id="plan" class="form-check-input" onchange="togglePlan()" name="necesita_plan">
                <label class="form-check-label" for="plan">¿Desea un plan de entrenamiento?</label>
              </div>
              <button type="button" id="entrenar" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#myModal" disabled>Configurar plan</button>
            </div>
          </div>
        </div>

        

  <!-- Modal Plan de Entrenamiento -->
      <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalLabel">Formulario para Plan de Entrenamiento</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>  
            <div class="modal-body">
      <div class="mb-3">
        <label class="form-label">¿Qué tipo de plan desea?</label>
       <!-- En el modal, asegúrate que los radio buttons tengan name="tipo_plan" -->
      <div class="form-check">
        <input class="form-check-input" type="radio" id="masmuscular" name="tipo_plan" value="MASMUSCULAR" >
        <label class="form-check-label" for="masmuscular">Aumentar masa muscular</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="bajarpeso" name="tipo_plan" value="BAJARPESO">
        <label class="form-check-label" for="bajarpeso">Bajar de peso</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="otro" name="tipo_plan" value="OTRO">
        <label class="form-check-label" for="otro">Otro plan</label>
      </div>
        <input type="text" class="form-control mt-2" id="campo_otro_plan" name="otro_plan" placeholder="Especifique" style="display: none;">
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Peso actual (kg)</label>
          <input type="number" step="0.01" class="form-control" name="peso_actual" >
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Altura (cm)</label>
          <input type="number" class="form-control" name="altura" >
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Disponibilidad horaria</label>
        <input type="text" class="form-control" name="disponibilidad" >
      </div>
    </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="text-end">
  <button type="submit" class="btn btn-success btn-lg px-4">GUARDAR SOCIO</button>
</div>
</form>
</div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Vista previa de imagen
    function previewImage(input) {
      const preview = document.getElementById('preview');
      const imagePreview = document.getElementById('imagePreview');
      
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          preview.src = e.target.result;
          imagePreview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
      } else {
        imagePreview.style.display = 'none';
      }
    }
    
    // Actualizar nombre de archivo
    function updateFileName(input) {
      document.getElementById('fileName').textContent = input.files[0] ? input.files[0].name : 'Seleccionar imagen';
    }
    
  // Mostrar/ocultar campo "Especifique" cuando se selecciona "Otro plan"
document.getElementById('otro').addEventListener('change', function() {
  const campoOtro = document.getElementById('campo_otro_plan');
  campoOtro.style.display = this.checked ? 'block' : 'none';
  
  // Limpiar campo si se deselecciona
  if(!this.checked) campoOtro.value = '';
});

// También para los otros radios (opcional)
document.querySelectorAll('input[name="tipo_plan"]').forEach(radio => {
  radio.addEventListener('change', function() {
    if(this.value !== 'OTRO') {
      document.getElementById('campo_otro_plan').style.display = 'none';
    }
  });
});
    
    // Toggle campo problema físico
    function toggleProblemaFisico() {
      const checkbox = document.getElementById("problemaFisico");
      const inputProblema = document.getElementById("detalleProblema");
      inputProblema.disabled = !checkbox.checked;
      if (!checkbox.checked) inputProblema.value = "";
    }
    
    // Toggle botón de plan
    function togglePlan() {
      const checkbox = document.getElementById("plan");
      const boton = document.getElementById("entrenar");
      boton.disabled = !checkbox.checked;
    }
  </script>
</body>
</html>