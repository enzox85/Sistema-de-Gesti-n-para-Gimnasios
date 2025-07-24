<?php
include("../conexion.php");
$con = conectar();

// Consulta para obtener el primer medio de tipo IMAGEN para cada ejercicio
$sql = "SELECT e.*, (SELECT url_media FROM ejercicios_media WHERE idejercicio = e.idejercicio AND tipo_media = 'IMAGEN' ORDER BY orden ASC LIMIT 1) as imagen_principal FROM ejercicios e ORDER BY e.nomb_ejer ASC";
$query = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Gestión de Ejercicios</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		body {
			background-color: #f8f9fa;
		}

		.card-header {
			background-color: #343a40;
			color: white;
		}

		.exercise-pic {
			width: 50px;
			height: 50px;
			border-radius: 10%;
			object-fit: cover;
			cursor: pointer;
		}

		.media-row {
			border-left: 3px solid #0d6efd;
			padding-left: 10px;
		}

		.carousel-item img,
		.carousel-item iframe {
			max-height: 450px;
			object-fit: contain;
			margin: auto;
			width: 100%;
		}

		.modal-lg {
			max-width: 800px;
		}

		.carousel-item iframe {
			aspect-ratio: 16 / 9;
			width: 100%;
			height: auto;
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
					<h2 class="mb-0"><i class="fas fa-running me-2"></i>Biblioteca de Ejercicios</h2>
				</div>
				<div class="card-body">
					<div class="d-flex justify-content-end mb-4">
						<button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalAgregarEjercicio">
							<i class="fas fa-plus me-1"></i>Añadir Ejercicio
						</button>
					</div>
					<div class="table-responsive">
						<table class="table table-striped table-hover align-middle">
							<thead class="table-dark">
								<tr>
									<th>Imagen Principal</th>
									<th>Nombre</th>
									<th>Grupo Muscular</th>
									<th>Nivel de Dificultad</th>
									<th class="text-center">Opciones</th>
								</tr>
							</thead>
							<tbody>
								<?php while ($row = $query->fetch_assoc()): ?>
									<tr>
										<td>
											<?php
											$imagen_url = $row['imagen_principal'] ?? '/spartanproject/uploads/ejercicios/default.png';
											?>
											<img src="<?php echo htmlspecialchars($imagen_url); ?>" class="exercise-pic"
												alt="Imagen de <?php echo htmlspecialchars($row['nomb_ejer']); ?>">
										</td>
										<td><?php echo htmlspecialchars($row['nomb_ejer']); ?></td>
										<td><?php echo htmlspecialchars($row['grupo_mus']); ?></td>
										<td><?php echo htmlspecialchars($row['nivel_dificultad']); ?></td>
										<td class="text-center">
											<button class="btn btn-info btn-sm" title="Ver Media" data-bs-toggle="modal"
												data-bs-target="#modalVerMedia"
												data-idejercicio="<?php echo $row['idejercicio']; ?>">
												<i class="fas fa-eye"></i>
											</button>
											<button type="button" class="btn btn-warning btn-sm btn-editar" title="Editar"
												data-bs-toggle="modal" data-bs-target="#modalModificarEjercicio"
												data-idejercicio="<?php echo $row['idejercicio']; ?>">
												<i class="fas fa-edit"></i>
											</button>

											<a href="eliminarejercicio.php?id=<?php echo $row['idejercicio']; ?>"
												class="btn btn-danger btn-sm" title="Eliminar"
												onclick="return confirm('¿Estás seguro de que quieres eliminar este ejercicio?');">
												<i class="fas fa-trash"></i>
											</a>
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

	<!-- Add/Edit Exercise Modal (sin cambios) -->
	<div class="modal fade" id="modalAgregarEjercicio" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Constructor de Ejercicios</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<form id="form-ejercicio" enctype="multipart/form-data">
						<!-- Main exercise info -->
						<div class="row g-3 mb-3">
							<div class="col-md-6">
								<label class="form-label">Nombre del ejercicio</label>
								<input type="text" name="nomb_ejer" class="form-control" required>
							</div>
							<div class="col-md-6">
								<label class="form-label">Grupo muscular</label>
								<select name="grupo_mus" class="form-select" required>
									<option value="">Seleccione...</option>
									<option value="PIERNA">Pierna</option>
									<option value="BRAZO">Brazo</option>
									<option value="PECHO">Pecho</option>
									<option value="ESPALDA">Espalda</option>
									<option value="HOMBRO">Hombro</option>
									<option value="ABDOMEN">Abdomen</option>
								</select>
							</div>
							<div class="col-md-12">
								<label class="form-label">Nivel de dificultad</label>
								<select name="nivel_dificultad" class="form-select" required>
									<option value="">Seleccione...</option>
									<option value="PRINCIPIANTE">Principiante</option>
									<option value="INTERMEDIO">Intermedio</option>
									<option value="AVANZADO">Avanzado</option>
								</select>
							</div>
							<div class="col-md-12">
								<label class="form-label">Descripción</label>
								<textarea name="descripcion" class="form-control" rows="3"></textarea>
							</div>
						</div>
						<hr>
						<!-- Dynamic media container -->
						<h5 class="mb-3">Archivos Multimedia</h5>
						<div id="media-container" class="vstack gap-3">
							<!-- Media rows will be added here -->
						</div>
						<div class="mt-3">
							<button type="button" class="btn btn-outline-primary" id="btn-add-image">
								<i class="fas fa-image me-1"></i>Añadir Imagen/GIF
							</button>
							<button type="button" class="btn btn-outline-info" id="btn-add-video">
								<i class="fab fa-youtube me-1"></i>Añadir Link de Video
							</button>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button type="submit" form="form-ejercicio" class="btn btn-success">Guardar Ejercicio</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Media Viewer Modal -->
	<div class="modal fade" id="modalVerMedia" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Visor de Medios</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body" id="media-viewer-body">
					<!-- El carrusel se generará aquí con JavaScript -->
				</div>
			</div>
		</div>
	</div>

	<!-- TEMPLATES (hidden) -->
	<template id="template-media-imagen">
		<div class="p-2 rounded media-row bg-light">
			<div class="d-flex align-items-center">
				<div class="flex-grow-1">
					<label class="form-label"><i class="fas fa-image text-primary me-2"></i>Subir Imagen o GIF</label>
					<input type="file" name="media_files[]" class="form-control" accept="image/*,image/gif" required>
					<input type="hidden" name="media_types[]" value="IMAGEN">
				</div>
				<button type="button" class="btn-close ms-3" aria-label="Eliminar"
					onclick="this.closest('.media-row').remove()"></button>
			</div>
		</div>
	</template>

	<template id="template-media-video">
		<div class="p-2 rounded media-row bg-light">
			<div class="d-flex align-items-center">
				<div class="flex-grow-1">
					<label class="form-label"><i class="fab fa-youtube text-info me-2"></i>Link de Video
						(YouTube)</label>
					<input type="url" name="media_links[]" class="form-control"
						placeholder="https://www.youtube.com/watch?v=..." required>
					<input type="hidden" name="media_types[]" value="VIDEO_LINK">
				</div>
				<button type="button" class="btn-close ms-3" aria-label="Eliminar"
					onclick="this.closest('.media-row').remove()"></button>
			</div>
		</div>
	</template>

	<!-- ================================================== -->
	<!-- ========= MODAL MODIFICAR EJERCICIO ========== -->
	<!-- ================================================== -->
	<div class="modal fade" id="modalModificarEjercicio" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header bg-warning">
					<h5 class="modal-title"><i class="fas fa-edit me-2"></i>Modificar Ejercicio</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<!-- El contenido del formulario se cargará aquí con JavaScript -->
					<div id="loader-modificar" class="text-center p-5">
						<div class="spinner-border text-warning" role="status">
							<span class="visually-hidden">Cargando...</span>
						</div>
					</div>
					<div id="form-container-modificar" class="d-none">
						<!-- El formulario se inyectará aquí -->
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button type="submit" form="form-modificar-ejercicio" class="btn btn-success">Guardar
						Cambios</button>
				</div>
			</div>
		</div>
	</div>


	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			// --- Lógica para el constructor de ejercicios ---
			const mediaContainer = document.getElementById('media-container');
			document.getElementById('btn-add-image').addEventListener('click', () => {
				const template = document.getElementById('template-media-imagen');
				mediaContainer.appendChild(template.content.cloneNode(true));
			});
			document.getElementById('btn-add-video').addEventListener('click', () => {
				const template = document.getElementById('template-media-video');
				mediaContainer.appendChild(template.content.cloneNode(true));
			});
			document.getElementById('form-ejercicio').addEventListener('submit', function (e) {
				e.preventDefault();
				const formData = new FormData(this);
				fetch('guardarejercicio.php', {
					method: 'POST',
					body: formData
				})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							location.reload();
						} else {
							alert('Error al guardar el ejercicio: ' + data.message);
						}
					})
					.catch(error => {
						console.error('Error:', error);
						alert('Hubo un error de conexión al guardar el ejercicio.');
					});
			});

			// --- Lógica para el visor de medios ---
			const modalVerMedia = document.getElementById('modalVerMedia');

			function getYouTubeID(url) {
				const p = /^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:\S+)?$/;
				return (url.match(p)) ? RegExp.$1 : null;
			}

			// EVENTO PARA CUANDO EL MODAL SE MUESTRA (carga el contenido)
			modalVerMedia.addEventListener('show.bs.modal', function (event) {
				const button = event.relatedTarget;
				const idejercicio = button.getAttribute('data-idejercicio');
				const modalBody = document.getElementById('media-viewer-body');
				modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

				fetch(`get_ejercicio_media.php?idejercicio=${idejercicio}`)
					.then(response => response.json())
					.then(data => {
						if (data.success && data.media.length > 0) {
							let carouselHTML = `
					<div id="carouselMedia" class="carousel slide" data-bs-ride="carousel">
						<div class="carousel-inner">`;

							data.media.forEach((item, index) => {
								const activeClass = index === 0 ? 'active' : '';
								carouselHTML += `<div class="carousel-item ${activeClass}">`;
								if (item.tipo_media === 'IMAGEN') {
									carouselHTML += `<img src="${item.url_media}" class="d-block w-100" alt="Media de ejercicio">`;
								} else if (item.tipo_media === 'VIDEO_LINK') {
									const videoId = getYouTubeID(item.url_media);
									if (videoId) {
										const embedUrl = `https://www.youtube.com/embed/${videoId}`;
										// Añadimos ?enablejsapi=1 para poder controlarlo si fuera necesario en el futuro
										carouselHTML += `<iframe src="${embedUrl}?enablejsapi=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
									} else {
										carouselHTML += `<div class="d-flex justify-content-center align-items-center bg-light" style="height: 450px;"><div class="text-center"><p>No se pudo procesar el link del video.</p><a href="${item.url_media}" target="_blank" class="btn btn-primary">Ver video</a></div></div>`;
									}
								}
								carouselHTML += `</div>`;
							});

							carouselHTML += `</div>`;

							if (data.media.length > 1) {
								carouselHTML += `
						<button class="carousel-control-prev" type="button" data-bs-target="#carouselMedia" data-bs-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Anterior</span>
						</button>
						<button class="carousel-control-next" type="button" data-bs-target="#carouselMedia" data-bs-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Siguiente</span>
						</button>`;
							}
							carouselHTML += `</div>`;
							modalBody.innerHTML = carouselHTML;
						} else {
							modalBody.innerHTML = '<p class="text-center">No hay medios disponibles para este ejercicio.</p>';
						}
					})
					.catch(error => {
						console.error('Error:', error);
						modalBody.innerHTML = '<p class="text-center text-danger">No se pudieron cargar los medios.</p>';
					});
			});

			// ==================================================================
			// ¡NUEVO! EVENTO PARA CUANDO EL MODAL SE OCULTA (detiene el video)
			// ==================================================================
			modalVerMedia.addEventListener('hide.bs.modal', function (event) {
				const modalBody = document.getElementById('media-viewer-body');
				const iframe = modalBody.querySelector('iframe');
				if (iframe) {
					iframe.src = ''; // Detiene el video al vaciar la URL
				}
			});

			// --- Lógica para el MODAL DE MODIFICACIÓN ---
			const modalModificarEjercicio = document.getElementById('modalModificarEjercicio');
			const loaderModificar = document.getElementById('loader-modificar');
			const formContainerModificar = document.getElementById('form-container-modificar');

			modalModificarEjercicio.addEventListener('show.bs.modal', function (event) {
				// Mostrar loader y ocultar form
				loaderModificar.style.display = 'block';
				formContainerModificar.innerHTML = ''; // Limpiar contenido anterior
				formContainerModificar.classList.add('d-none');

				const button = event.relatedTarget;
				const idejercicio = button.getAttribute('data-idejercicio');

				// Fetch para obtener los datos del ejercicio
				fetch(`get_ejercicio_details.php?id=${idejercicio}`)
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							// Construir el HTML del formulario con los datos recibidos
							const ejercicio = data.ejercicio;
							const media = data.media;

							// Aquí puedes crear el HTML del formulario como un string largo
							// Es más fácil de mantener que crear elemento por elemento
							let formHTML = `
				<form id="form-modificar-ejercicio" enctype="multipart/form-data">
					<input type="hidden" name="idejercicio" value="${ejercicio.idejercicio}">
					
					<div class="row g-3 mb-3">
						<div class="col-md-6">
							<label class="form-label">Nombre</label>
							<input type="text" name="nomb_ejer" class="form-control" value="${ejercicio.nomb_ejer}" required>
						</div>
						<div class="col-md-6">
							<label class="form-label">Grupo muscular</label>
							<select name="grupo_mus" class="form-select" required>
								${["PIERNA", "BRAZO", "PECHO", "ESPALDA", "HOMBRO", "ABDOMEN"].map(g => `<option value="${g}" ${ejercicio.grupo_mus == g ? 'selected' : ''}>${g.charAt(0) + g.slice(1).toLowerCase()}</option>`).join('')}
							</select>
						</div>
						<div class="col-md-12">
							<label class="form-label">Nivel de dificultad</label>
							<select name="nivel_dificultad" class="form-select" required>
								${["PRINCIPIANTE", "INTERMEDIO", "AVANZADO"].map(n => `<option value="${n}" ${ejercicio.nivel_dificultad == n ? 'selected' : ''}>${n.charAt(0) + n.slice(1).toLowerCase()}</option>`).join('')}
							</select>
						</div>
						<div class="col-md-12">
							<label class="form-label">Descripción</label>
							<textarea name="descripcion" class="form-control" rows="3">${ejercicio.descripcion}</textarea>
						</div>
					</div>
					<hr>
					<h5 class="mb-3">Gestionar Multimedia</h5>
					<div id="media-container-modificar" class="vstack gap-3">
						${media.map(m => `
						<div class="p-2 rounded media-row bg-light">
							<div class="d-flex align-items-center">
								<div class="flex-grow-1">
									<i class="fas ${m.tipo_media == 'IMAGEN' ? 'fa-image text-primary' : 'fa-video text-danger'} me-2"></i>
									<a href="${m.url_media}" target="_blank">Ver medio actual</a>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="checkbox" name="eliminar_media[]" value="${m.idmedia}">
									<label class="form-check-label text-danger">Eliminar</label>
								</div>
							</div>
						</div>
						`).join('')}
					</div>
					<hr>
					<h5 class="mb-3">Añadir Nuevos Medios</h5>
					<div id="new-media-container-modificar" class="vstack gap-3"></div>
					<div class="mt-3">
						<button type="button" class="btn btn-outline-primary btn-add-image-modificar"><i class="fas fa-image me-1"></i>Añadir Imagen</button>
						<button type="button" class="btn btn-outline-info btn-add-video-modificar"><i class="fab fa-youtube me-1"></i>Añadir Video</button>
					</div>
				</form>
				`;

							// Ocultar loader y mostrar form
							loaderModificar.style.display = 'none';
							formContainerModificar.innerHTML = formHTML;
							formContainerModificar.classList.remove('d-none');

							// Re-asignar eventos para los nuevos botones dentro del modal
							document.querySelector('.btn-add-image-modificar').addEventListener('click', () => {
								document.getElementById('new-media-container-modificar').insertAdjacentHTML('beforeend', document.getElementById('template-media-imagen').innerHTML);
							});
							document.querySelector('.btn-add-video-modificar').addEventListener('click', () => {
								document.getElementById('new-media-container-modificar').insertAdjacentHTML('beforeend', document.getElementById('template-media-video').innerHTML);
							});

							// Evento de submit para el formulario de modificación
							document.getElementById('form-modificar-ejercicio').addEventListener('submit', function (e) {
								e.preventDefault();
								const formData = new FormData(this);

								fetch('actualizarejercicio.php', {
									method: 'POST',
									body: formData
								})
									.then(response => response.json())
									.then(data => {
										if (data.success) {
											location.reload(); // Recarga la página para ver los cambios
										} else {
											alert('Error al actualizar: ' + data.message);
										}
									})
									.catch(error => {
										console.error('Error:', error);
										alert('Hubo un error de conexión al actualizar.');
									});
							});

						} else {
							loaderModificar.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
						}
					})
					.catch(error => {
						console.error('Error fetching details:', error);
						loaderModificar.innerHTML = `<div class="alert alert-danger">No se pudieron cargar los datos del ejercicio.</div>`;
					});
			});

		});
	</script>
</body>

</html>