<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Gestión de Ejercicios</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ /libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		body {
			background-color: #f8f9fa;
		}

		.card-header {
			background-color: #343a40;
			color: white;
		}

		.exercise-pic {
			width: 60px;
			height: 60px;
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
									<th>Imagen</th>
									<th>Nombre</th>
									<th>Grupo Muscular</th>
									<th>Dificultad</th>
									<th class="text-center">Opciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($ejercicios as $ejercicio): ?>
									<tr id="ejercicio-row-<?php echo $ejercicio['idejercicio']; ?>">
										<td>
											<?php $imagen_url = $ejercicio['imagen_principal'] ?? '/spartanproject/uploads/ejercicios/default.png'; ?>
											<img src="<?php echo htmlspecialchars($imagen_url); ?>" class="exercise-pic"
												alt="Imagen de <?php echo htmlspecialchars($ejercicio['nomb_ejer']); ?>">
										</td>
										<td><?php echo htmlspecialchars($ejercicio['nomb_ejer']); ?></td>
										<td><?php echo htmlspecialchars($ejercicio['grupo_mus']); ?></td>
										<td><?php echo htmlspecialchars($ejercicio['nivel_dificultad']); ?></td>
										<td class="text-center">
											<button class="btn btn-info btn-sm" title="Ver Media" data-bs-toggle="modal"
												data-bs-target="#modalVerMedia"
												data-idejercicio="<?php echo $ejercicio['idejercicio']; ?>">
												<i class="fas fa-eye"></i>
											</button>
											<button type="button" class="btn btn-warning btn-sm btn-editar" title="Editar"
												data-bs-toggle="modal" data-bs-target="#modalModificarEjercicio"
												data-idejercicio="<?php echo $ejercicio['idejercicio']; ?>">
												<i class="fas fa-edit"></i>
											</button>
											<button type="button" class="btn btn-danger btn-sm" title="Eliminar"
												onclick="confirmarEliminacion(<?php echo $ejercicio['idejercicio']; ?>)">
												<i class="fas fa-trash"></i>
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- MODALS Y TEMPLATES -->
	<div class="modal fade" id="modalAgregarEjercicio" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Constructor de Ejercicios</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<form id="form-ejercicio" enctype="multipart/form-data">
						<div class="row g-3 mb-3">
							<div class="col-md-6"><label class="form-label">Nombre del ejercicio</label><input
									type="text" name="nomb_ejer" class="form-control" required></div>
							<div class="col-md-6"><label class="form-label">Grupo muscular</label><select
									name="grupo_mus" class="form-select" required>
									<option value="">Seleccione...</option>
									<option value="PIERNA">Pierna</option>
									<option value="BRAZO">Brazo</option>
									<option value="PECHO">Pecho</option>
									<option value="ESPALDA">Espalda</option>
									<option value="HOMBRO">Hombro</option>
									<option value="ABDOMEN">Abdomen</option>
								</select></div>
							<div class="col-md-12"><label class="form-label">Nivel de dificultad</label><select
									name="nivel_dificultad" class="form-select" required>
									<option value="">Seleccione...</option>
									<option value="PRINCIPIANTE">Principiante</option>
									<option value="INTERMEDIO">Intermedio</option>
									<option value="AVANZADO">Avanzado</option>
								</select></div>
							<div class="col-md-12"><label class="form-label">Descripción</label><textarea
									name="descripcion" class="form-control" rows="3"></textarea></div>
						</div>
						<hr>
						<h5 class="mb-3">Archivos Multimedia</h5>
						<div id="media-container" class="vstack gap-3"></div>
						<div class="mt-3"><button type="button" class="btn btn-outline-primary" id="btn-add-image"><i
									class="fas fa-image me-1"></i>Añadir Imagen/GIF</button><button type="button"
								class="btn btn-outline-info" id="btn-add-video"><i
									class="fab fa-youtube me-1"></i>Añadir Link de Video</button></div>
					</form>
				</div>
				<div class="modal-footer"><button type="button" class="btn btn-secondary"
						data-bs-dismiss="modal">Cancelar</button><button type="submit" form="form-ejercicio"
						class="btn btn-success">Guardar Ejercicio</button></div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalModificarEjercicio" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header bg-warning">
					<h5 class="modal-title"><i class="fas fa-edit me-2"></i>Modificar Ejercicio</h5><button
						type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div id="loader-modificar" class="text-center p-5">
						<div class="spinner-border text-warning" role="status"><span
								class="visually-hidden">Cargando...</span></div>
					</div>
					<div id="form-container-modificar" class="d-none"></div>
				</div>
				<div class="modal-footer"><button type="button" class="btn btn-secondary"
						data-bs-dismiss="modal">Cancelar</button><button type="submit" form="form-modificar-ejercicio"
						class="btn btn-success">Guardar Cambios</button></div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalVerMedia" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Visor de Medios</h5><button type="button" class="btn-close"
						data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body" id="media-viewer-body"></div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Confirmar Eliminación</h5><button type="button" class="btn-close"
						data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">¿Estás seguro? Esta acción no se puede deshacer.</div>
				<div class="modal-footer"><button type="button" class="btn btn-secondary"
						data-bs-dismiss="modal">Cancelar</button><button type="button" id="btn-confirmar-eliminacion"
						class="btn btn-danger">Sí, Eliminar</button></div>
			</div>
		</div>
	</div>
	<template id="template-media-imagen">
		<div class="p-2 rounded media-row bg-light">
			<div class="d-flex align-items-center">
				<div class="flex-grow-1"><label class="form-label"><i class="fas fa-image text-primary me-2"></i>Subir
						Imagen o GIF</label><input type="file" name="media_files[]" class="form-control"
						accept="image/*,image/gif" required><input type="hidden" name="media_types[]" value="IMAGEN">
				</div><button type="button" class="btn-close ms-3" aria-label="Eliminar"
					onclick="this.closest('.media-row').remove()"></button>
			</div>
		</div>
	</template>
	<template id="template-media-video">
		<div class="p-2 rounded media-row bg-light">
			<div class="d-flex align-items-center">
				<div class="flex-grow-1"><label class="form-label"><i class="fab fa-youtube text-info me-2"></i>Link de
						Video (YouTube)</label><input type="url" name="media_links[]" class="form-control"
						placeholder="https://www.youtube.com/watch?v=..." required><input type="hidden"
						name="media_types[]" value="VIDEO_LINK"></div><button type="button" class="btn-close ms-3"
					aria-label="Eliminar" onclick="this.closest('.media-row').remove()"></button>
			</div>
		</div>
	</template>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {

			// --- LÓGICA GENERAL Y AUXILIARES ---
			function getYouTubeID(url) {
				if (!url) return null;
				const p = /^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:\S+)?$/;
				return (url.match(p)) ? RegExp.$1 : null;
			}

			function escapeHTML(str) {
				if (str === null || typeof str === 'undefined') return '';
				const p = document.createElement('p');
				p.appendChild(document.createTextNode(str));
				return p.innerHTML;
			}

			function actualizarFilaEjercicio(fila, ejercicioData) {
				if (!fila || !ejercicioData || !ejercicioData.ejercicio) return;
				const ejercicio = ejercicioData.ejercicio;
				const imagen_url = ejercicio.imagen_principal ?? '/spartanproject/uploads/ejercicios/default.png';
				fila.innerHTML = `
					<td><img src="${escapeHTML(imagen_url)}" class="exercise-pic" alt="${escapeHTML(ejercicio.nomb_ejer)}"></td>
					<td>${escapeHTML(ejercicio.nomb_ejer)}</td>
					<td>${escapeHTML(ejercicio.grupo_mus)}</td>
					<td>${escapeHTML(ejercicio.nivel_dificultad)}</td>
					<td class="text-center">
						<button class="btn btn-info btn-sm" title="Ver Media" data-bs-toggle="modal" data-bs-target="#modalVerMedia" data-idejercicio="${ejercicio.idejercicio}"><i class="fas fa-eye"></i></button>
						<button type="button" class="btn btn-warning btn-sm btn-editar" title="Editar" data-bs-toggle="modal" data-bs-target="#modalModificarEjercicio" data-idejercicio="${ejercicio.idejercicio}"><i class="fas fa-edit"></i></button>
						<button type="button" class="btn btn-danger btn-sm" title="Eliminar" onclick="confirmarEliminacion(${ejercicio.idejercicio})"><i class="fas fa-trash"></i></button>
					</td>
				`;
			}

			function agregarFilaEjercicio(ejercicioData) {
				const tbody = document.querySelector('.table tbody');
				const nuevaFila = document.createElement('tr');
				nuevaFila.id = `ejercicio-row-${ejercicioData.ejercicio.idejercicio}`;
				actualizarFilaEjercicio(nuevaFila, ejercicioData);
				tbody.prepend(nuevaFila);
			}

			// --- LÓGICA PARA AÑADIR EJERCICIO ---
			const mediaContainer = document.getElementById('media-container');
			document.getElementById('btn-add-image').addEventListener('click', () => {
				mediaContainer.insertAdjacentHTML('beforeend', document.getElementById('template-media-imagen').innerHTML);
			});
			document.getElementById('btn-add-video').addEventListener('click', () => {
				mediaContainer.insertAdjacentHTML('beforeend', document.getElementById('template-media-video').innerHTML);
			});

			document.getElementById('form-ejercicio').addEventListener('submit', function (e) {
				e.preventDefault();
				const form = this;
				const formData = new FormData(form);
				const modal = document.getElementById('modalAgregarEjercicio');
				const submitButton = modal.querySelector('button[type="submit"]');
				const originalButtonText = submitButton.innerHTML;
				submitButton.disabled = true;
				submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Guardando...`;

				fetch('guardarejercicio.php', { method: 'POST', body: formData })
					.then(response => response.json())
					.then(data => {
						if (data.status === 'success') {
							const modal = bootstrap.Modal.getInstance(document.getElementById
								('modalAgregarEjercicio'));
							modal.hide();
							form.reset();
							document.getElementById('media-container').innerHTML = '';
							agregarFilaEjercicio(data.ejercicio);
						} else {
							alert('Error al guardar: ' + (data.message || 'Error desconocido'));
						}
					})
					.catch(error => console.error('Error:', error))
					.finally(() => {
						submitButton.disabled = false;
						submitButton.innerHTML = originalButtonText;
					});
			});

			// --- LÓGICA PARA VER MEDIA ---
			const modalVerMedia = document.getElementById('modalVerMedia');
			modalVerMedia.addEventListener('show.bs.modal', function (event) {
				const button = event.relatedTarget;
				const idejercicio = button.getAttribute('data-idejercicio');
				const modalBody = document.getElementById('media-viewer-body');
				modalBody.innerHTML = '<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

				fetch(`get_ejercicio_media.php?idejercicio=${idejercicio}`)
					.then(response => response.json())
					.then(data => {
						if (data.success && data.media.length > 0) {
							let carouselHTML = '<div id="carouselMedia' + idejercicio + '" class="carousel slide"><div class="carousel-inner">';
							data.media.forEach((item, index) => {
								const activeClass = index === 0 ? 'active' : '';
								carouselHTML += `<div class="carousel-item ${activeClass}">`;
								if (item.tipo_media === 'IMAGEN') {
									carouselHTML += `<img src="${item.url_media}" class="d-block w-100" alt="Media">`;
								} else if (item.tipo_media === 'VIDEO_LINK') {
									const videoId = getYouTubeID(item.url_media);
									if (videoId) {
										carouselHTML += `<iframe src="https://www.youtube.com/embed/${videoId}?enablejsapi=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
									}
								}
								carouselHTML += `</div>`;
							});
							carouselHTML += '</div>';
							if (data.media.length > 1) {
								carouselHTML += `<button class="carousel-control-prev" type="button" data-bs-target="#carouselMedia${idejercicio}" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button><button class="carousel-control-next" type="button" data-bs-target="#carouselMedia${idejercicio}" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>`;
							}
							carouselHTML += '</div>';
							modalBody.innerHTML = carouselHTML;
						} else {
							modalBody.innerHTML = '<p class="text-center">No hay medios disponibles.</p>';
						}
					})
					.catch(error => console.error('Error:', error));
			});
			modalVerMedia.addEventListener('hide.bs.modal', function (event) {
				const iframe = this.querySelector('iframe');
				if (iframe) iframe.src = '';
			});

			// --- LÓGICA PARA MODIFICAR EJERCICIO ---
			const modalModificarEjercicio = document.getElementById('modalModificarEjercicio');
			modalModificarEjercicio.addEventListener('show.bs.modal', function (event) {
				const loader = document.getElementById('loader-modificar');
				const formContainer = document.getElementById('form-container-modificar');
				loader.style.display = 'block';
				formContainer.innerHTML = '';
				formContainer.classList.add('d-none');

				const idejercicio = event.relatedTarget.getAttribute('data-idejercicio');

				fetch(`get_ejercicio_details.php?id=${idejercicio}`)
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							formContainer.innerHTML = data.html_form;
							loader.style.display = 'none';
							formContainer.classList.remove('d-none');

							formContainer.querySelector('.btn-add-image-modificar').addEventListener('click', () => {
								formContainer.querySelector('#new-media-container-modificar').insertAdjacentHTML('beforeend', document.getElementById('template-media-imagen').innerHTML);
							});
							formContainer.querySelector('.btn-add-video-modificar').addEventListener('click', () => {
								formContainer.querySelector('#new-media-container-modificar').insertAdjacentHTML('beforeend', document.getElementById('template-media-video').innerHTML);
							});

							document.getElementById('form-modificar-ejercicio').addEventListener('submit', function (e) {
								e.preventDefault();
								const formData = new FormData(this);
								const submitButton = modalModificarEjercicio.querySelector('button[type="submit"]');
								const originalButtonText = submitButton.innerHTML;
								submitButton.disabled = true;
								submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Guardando...`;

								fetch('actualizarejercicio.php', { method: 'POST', body: formData })
									.then(res => res.json())
									.then(updateData => {
										if (updateData.status === 'success') {
											bootstrap.Modal.getInstance(modalModificarEjercicio).hide();
											const fila = document.getElementById(`ejercicio-row-${updateData.ejercicio.ejercicio.idejercicio}`);
											actualizarFilaEjercicio(fila, updateData.ejercicio);
										} else {
											alert('Error al actualizar: ' + updateData.message);
										}
									})
									.catch(err => console.error('Error:', err))
									.finally(() => {
										submitButton.disabled = false;
										submitButton.innerHTML = originalButtonText;
									});
							});
						}
					});
			});

			// --- LÓGICA PARA ELIMINAR EJERCICIO ---
			const modalConfirmar = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
			const btnConfirmarEliminacion = document.getElementById('btn-confirmar-eliminacion');
			let idParaEliminar = null;

			window.confirmarEliminacion = function (id) {
				idParaEliminar = id;
				modalConfirmar.show();
			}

			btnConfirmarEliminacion.addEventListener('click', function () {
				if (!idParaEliminar) return;
				fetch(`eliminarejercicio.php?id=${idParaEliminar}`)
					.then(response => response.json())
					.then(data => {
						if (data.status === 'success') {
							document.getElementById(`ejercicio-row-${idParaEliminar}`)?.remove();
						} else {
							alert('Error: ' + data.message);
						}
						modalConfirmar.hide();
					})
					.catch(error => console.error('Error:', error));
			});
		});
	</script>
</body>

</html>