<?php
require_once '../conexion.php';
$conexion = conectar();

// Lógica para la barra de búsqueda
$busqueda = $_POST['busqueda'] ?? '';
if (!empty($busqueda)) {
	$sql = "SELECT * FROM socios WHERE nombre LIKE ? OR apellido LIKE ? ORDER BY idsocio DESC";
	$stmt = mysqli_prepare($conexion, $sql);
	$like_busqueda = "%{$busqueda}%";
	mysqli_stmt_bind_param($stmt, "ss", $like_busqueda, $like_busqueda);
	mysqli_stmt_execute($stmt);
	$resultado = mysqli_stmt_get_result($stmt);
} else {
	$sql = "SELECT * FROM socios ORDER BY idsocio DESC";
	$resultado = mysqli_query($conexion, $sql);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Gestión de Socios</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
	<style>
		body {
			background-color: #f8f9fa;
		}

		.card-header {
			background-color: #343a40;
			color: white;
		}

		.profile-pic {
			width: 50px;
			height: 50px;
			border-radius: 50%;
			object-fit: cover;
		}

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

		.img-preview {
			max-width: 200px;
			max-height: 200px;
			object-fit: cover;
		}
	</style>
</head>

<body class="d-flex">
	<!-- Sidebar -->
	<?php include $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/includes/sidebar.php'; ?>

	<!-- Main Content -->
	<div class="flex-grow-1 p-4" style="min-width:0;">
		<div class="container-fluid">
			<div class="card shadow-sm">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h2 class="mb-0"><i class="fas fa-users me-2"></i>Gestión de Socios</h2>
				</div>
				<div class="card-body">
					<!-- Feedback Alerts -->
					<?php if (isset($_GET['success'])): ?>
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							Operación realizada con éxito.
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					<?php endif; ?>
					<?php if (isset($_GET['error'])): ?>
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<strong>Error:</strong> No se pudo realizar la operación.
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					<?php endif; ?>

					<!-- Controls -->
					<div class="row mb-4 g-3 align-items-center">
						<div class="col-md-8">
							<form action="socios.php" method="POST" class="d-flex">
								<div class="input-group">
									<input type="text" class="form-control" name="busqueda"
										placeholder="Buscar por nombre o apellido..."
										value="<?php echo htmlspecialchars($busqueda); ?>">
									<button class="btn btn-primary" type="submit"><i class="fas fa-search"></i>
										Buscar</button>
								</div>
							</form>
						</div>
						<div class="col-md-4 text-end">
							<button type="button" class="btn btn-danger" data-bs-toggle="modal"
								data-bs-target="#modalNuevoSocio">
								<i class="fas fa-user-plus me-1"></i>Nuevo Socio
							</button>
						</div>
					</div>

					<!-- Members Table -->
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
								<?php while ($row = mysqli_fetch_assoc($resultado)): ?>
									<tr>
										<td>
											<?php if (!empty($row['foto'])): ?>
												<img src="<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $row['foto']); ?>"
													class="profile-pic" alt="Foto de <?php echo $row['nombre']; ?>">
											<?php else: ?>
												<div
													class="profile-pic bg-secondary d-flex align-items-center justify-content-center">
													<i class="bi bi-person text-white"></i>
												</div>
											<?php endif; ?>
										</td>
										<td><?php echo htmlspecialchars($row['nombre']); ?></td>
										<td><?php echo htmlspecialchars($row['apellido']); ?></td>
										<td><?php echo htmlspecialchars($row['dni']); ?></td>
										<td><?php echo htmlspecialchars($row['telef']); ?></td>
										<td><?php echo htmlspecialchars($row['email']); ?></td>
										<td class="text-center">
											<!-- Opciones: editar/eliminar (puedes agregar los enlaces o botones aquí) -->
											<button type="button" class="btn btn-sm btn-warning me-1 btn-editar"
												data-bs-toggle="modal" data-bs-target="#modalEditarSocio"
												data-id="<?php echo $row['idsocio']; ?>"
												data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>"
												data-apellido="<?php echo htmlspecialchars($row['apellido']); ?>"
												data-dni="<?php echo htmlspecialchars($row['dni']); ?>"
												data-telef="<?php echo htmlspecialchars($row['telef']); ?>"
												data-email="<?php echo htmlspecialchars($row['email']); ?>"
												data-direc="<?php echo htmlspecialchars($row['direc'] ?? ''); ?>"
												data-fechalta="<?php echo htmlspecialchars($row['fechalta']); ?>"
												data-foto="<?php echo !empty($row['foto']) ? str_replace($_SERVER['DOCUMENT_ROOT'], '', $row['foto']) : ''; ?>">
												<i class="fas fa-edit"></i>
											</button>

											<button class="btn btn-sm btn-danger"
												onclick="prepararEliminacion(<?php echo $row['idsocio']; ?>)"
												data-bs-toggle="modal" data-bs-target="#confirmarEliminarModal"><i
													class="fas fa-trash"></i></button>
										</td>
									</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- MODALS -->

	<!-- Delete Confirmation Modal -->
	<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel"><i
							class="fas fa-exclamation-triangle text-danger me-2"></i>Confirmar Eliminación</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					¿Estás seguro de que deseas eliminar a este socio? Esta acción no se puede deshacer.
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
					<a href="#" id="btnConfirmarEliminar" class="btn btn-danger">Sí, Eliminar</a>
				</div>
			</div>
		</div>
	</div>

	<!--Nuevo Socio Modal -->
	<div class="modal fade" id="modalNuevoSocio" tabindex="-1" aria-labelledby="modalNuevoSocioLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalNuevoSocioLabel">Formulario de Nuevo Socio</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<!-- El action apunta al script que procesa el formulario. Es importante el enctype para la subida de archivos -->
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
									<!-- ▼▼▼ ESTA ES LA LÍNEA MODIFICADA ▼▼▼ -->
									<input type="file" name="fileToUpload" id="fileToUpload" class="d-none"
										onchange="previewImage(this, 'preview', 'imagePreview'); updateFileName(this, 'fileName')">
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
									<div class="col-md-6 mb-3"><label class="form-label">Nombre</label><input
											type="text" class="form-control" name="nombre" required></div>
									<div class="col-md-6 mb-3"><label class="form-label">Apellido</label><input
											type="text" class="form-control" name="apellido" required></div>
									<div class="col-md-6 mb-3"><label class="form-label">DNI</label><input type="text"
											class="form-control" name="dni" required></div>
									<div class="col-md-6 mb-3"><label class="form-label">Teléfono</label><input
											type="tel" class="form-control" name="telef" required></div>
									<div class="col-md-6 mb-3"><label class="form-label">Email</label><input
											type="email" class="form-control" name="email" required></div>
									<div class="col-md-6 mb-3"><label class="form-label">Fecha de alta</label><input
											type="date" class="form-control" name="fechalta" required></div>
									<div class="col-12 mb-3"><label class="form-label">Dirección</label><input
											type="text" class="form-control" name="direc" required></div>
								</div>
							</div>
						</div>

						<!-- Botones dentro del formulario, pero el footer del modal los controla -->
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
							<button type="submit" class="btn btn-success">GUARDAR SOCIO</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal para Editar Socio -->
	<div class="modal fade" id="modalEditarSocio" tabindex="-1" aria-labelledby="modalEditarSocioLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalEditarSocioLabel">Editar Datos del Socio</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formEditarSocio" action="actualizarsocio.php" method="POST" enctype="multipart/form-data">
						<!-- ID del socio (oculto) -->
						<input type="hidden" id="idsocio_editar" name="idsocio">

						<!-- Sección de imagen de perfil -->
						<div class="card mb-4">
							<div class="card-header bg-warning text-dark">
								<h5 class="mb-0">Foto de perfil</h5>
							</div>
							<div class="card-body">
								<div id="imagePreviewEditar" class="mb-3 text-center">
									<img id="previewEditar" src="#" alt="Vista previa"
										class="img-thumbnail img-preview">
								</div>
								<div class="file-upload">
									<label for="fileToUploadEditar" class="form-label d-block">
										<i class="bi bi-camera fs-1"></i><br>
										<span id="fileNameEditar">Cambiar imagen (opcional)</span>
									</label>
									<input type="file" name="fileToUpload" id="fileToUploadEditar" class="d-none"
										onchange="previewImage(this, 'previewEditar', 'imagePreviewEditar'); updateFileName(this, 'fileNameEditar')">
								</div>
							</div>
						</div>

						<!-- Sección de datos personales -->
						<div class="card mb-4">
							<div class="card-header bg-warning text-dark">
								<h5 class="mb-0">Datos personales</h5>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-6 mb-3"><label class="form-label">Nombre</label><input
											type="text" id="nombre_editar" class="form-control" name="nombre" required>
									</div>
									<div class="col-md-6 mb-3"><label class="form-label">Apellido</label><input
											type="text" id="apellido_editar" class="form-control" name="apellido"
											required></div>
									<div class="col-md-6 mb-3"><label class="form-label">DNI</label><input type="text"
											id="dni_editar" class="form-control" name="dni" required></div>
									<div class="col-md-6 mb-3"><label class="form-label">Teléfono</label><input
											type="tel" id="telef_editar" class="form-control" name="telef" required>
									</div>
									<div class="col-md-6 mb-3"><label class="form-label">Email</label><input
											type="email" id="email_editar" class="form-control" name="email" required>
									</div>
									<div class="col-md-6 mb-3"><label class="form-label">Fecha de alta</label><input
											type="date" id="fechalta_editar" class="form-control" name="fechalta"
											required></div>
									<div class="col-12 mb-3"><label class="form-label">Dirección</label><input
											type="text" id="direc_editar" class="form-control" name="direc" required>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
							<button type="submit" class="btn btn-success">GUARDAR CAMBIOS</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>


	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		// --- Lógica para el modal de eliminación ---
		let idSocioParaEliminar = null;
		function prepararEliminacion(id) {
			idSocioParaEliminar = id;
			const btnConfirmar = document.getElementById('btnConfirmarEliminar');
			btnConfirmar.href = `eliminarsocio.php?idsocio=${idSocioParaEliminar}`;
		}

		// --- Lógica para previsualizar imagen y actualizar nombre de archivo (Funciones reutilizables) ---
		function previewImage(input, previewId, previewContainerId) {
			const preview = document.getElementById(previewId);
			const imagePreview = document.getElementById(previewContainerId);
			if (input.files && input.files[0]) {
				const reader = new FileReader();
				reader.onload = function (e) {
					preview.src = e.target.result;
					imagePreview.style.display = 'block';
				}
				reader.readAsDataURL(input.files[0]);
			} else {
				imagePreview.style.display = 'none';
			}
		}

		function updateFileName(input, fileNameId) {
			const defaultText = (fileNameId === 'fileName') ? 'Seleccionar imagen' : 'Cambiar imagen (opcional)';
			document.getElementById(fileNameId).textContent = input.files[0] ? input.files[0].name : defaultText;
		}

		// --- Lógica para el modal de edición ---
		document.addEventListener('DOMContentLoaded', function () {
			const modalEditarSocio = document.getElementById('modalEditarSocio');
			modalEditarSocio.addEventListener('show.bs.modal', function (event) {
				const button = event.relatedTarget; // Botón que abrió el modal

				// Extraer datos de los atributos data-*
				const id = button.getAttribute('data-id');
				const nombre = button.getAttribute('data-nombre');
				const apellido = button.getAttribute('data-apellido');
				const dni = button.getAttribute('data-dni');
				const telef = button.getAttribute('data-telef');
				const email = button.getAttribute('data-email');
				const direc = button.getAttribute('data-direc');
				const fechalta = button.getAttribute('data-fechalta');
				const foto = button.getAttribute('data-foto');

				// Rellenar los campos del formulario
				const modal = this;
				modal.querySelector('#idsocio_editar').value = id;
				modal.querySelector('#nombre_editar').value = nombre;
				modal.querySelector('#apellido_editar').value = apellido;
				modal.querySelector('#dni_editar').value = dni;
				modal.querySelector('#telef_editar').value = telef;
				modal.querySelector('#email_editar').value = email;
				modal.querySelector('#direc_editar').value = direc;
				modal.querySelector('#fechalta_editar').value = fechalta;

				// Manejar la vista previa de la imagen
				const preview = modal.querySelector('#previewEditar');
				const imagePreviewContainer = modal.querySelector('#imagePreviewEditar');
				if (foto) {
					// Asegúrate de que la ruta de la foto sea correcta para el atributo src
					preview.src = '/spartanproject' + foto;
					imagePreviewContainer.style.display = 'block';
				} else {
					imagePreviewContainer.style.display = 'none';
				}

				// Limpiar el input de archivo y el nombre del archivo
				modal.querySelector('#fileToUploadEditar').value = '';
				modal.querySelector('#fileNameEditar').textContent = 'Cambiar imagen (opcional)';
			});
		});
	</script>

</body>

</html>