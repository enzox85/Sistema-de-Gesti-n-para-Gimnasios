<?php
require_once '../conexion.php';
$conexion = conectar();

// Consulta para la tabla principal de rutinas
$query_rutinas = "SELECT * FROM rutinas ORDER BY idrutina DESC";
$result_rutinas = mysqli_query($conexion, $query_rutinas);

// Consulta para obtener todos los ejercicios disponibles para el modal
$query_ejercicios = "SELECT idejercicio, nomb_ejer FROM ejercicios ORDER BY nomb_ejer ASC";
$result_ejercicios = mysqli_query($conexion, $query_ejercicios);
$ejercicios_disponibles = [];
if ($result_ejercicios) {
	while ($row = mysqli_fetch_assoc($result_ejercicios)) {
		$ejercicios_disponibles[] = $row;
	}
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Gestión de Rutinas</title>
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

		.day-block {
			border: 1px solid #dee2e6;
			border-radius: .375rem;
			padding: 1rem;
			margin-bottom: 1rem;
			background-color: #fff;
		}

		.day-header {
			border-bottom: 1px solid #dee2e6;
			padding-bottom: 0.5rem;
			margin-bottom: 1rem;
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
					<h2 class="mb-0"><i class="fas fa-dumbbell me-2"></i>Gestión de Rutinas</h2>
				</div>
				<div class="card-body">
					<div class="d-flex justify-content-end mb-4">
						<button type="button" class="btn btn-danger" data-bs-toggle="modal"
							data-bs-target="#modalAgregar">
							<i class="fas fa-plus me-1"></i>Añadir Rutina
						</button>
					</div>
					<div class="table-responsive">
						<table class="table table-striped table-hover align-middle">
							<thead class="table-dark">
								<tr>
									<th>Nombre</th>
									<th>Descripción</th>
									<th>Nivel de Dificultad</th>
									<th class="text-center">Opciones</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($result_rutinas && $result_rutinas->num_rows > 0): ?>
									<?php while ($row = $result_rutinas->fetch_assoc()): ?>
										<tr>
											<td><?php echo htmlspecialchars($row['nombre']); ?></td>
											<td><?php echo htmlspecialchars($row['descripcion']); ?></td>
											<td><?php echo htmlspecialchars($row['nivel_dificultad'] ?? 'No especificado'); ?>
											</td>
											<td class="text-center">
												<button class="btn btn-info btn-sm btn-ver-detalles"
													data-idrutina="<?php echo $row['idrutina']; ?>" title="Ver Detalles">
													<i class="fas fa-eye"></i>
												</button>
												<button class="btn btn-warning btn-sm" title="Editar">
													<i class="fas fa-edit"></i>
												</button>
												<button class="btn btn-danger btn-sm" title="Eliminar">
													<i class="fas fa-trash"></i>
												</button>
											</td>
										</tr>
									<?php endwhile; ?>
								<?php else: ?>
									<tr>
										<td colspan="4" class="text-center">No hay rutinas para mostrar.</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Add/Edit Routine Modal -->
	<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalAgregarLabel"><i class="fas fa-plus-circle me-2"></i>Constructor de
						Rutinas</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formCrearRutina">
						<!-- Routine main info -->
						<div class="row mb-3">
							<div class="col-md-6">
								<label for="nombre" class="form-label">Nombre de la Rutina:</label>
								<input type="text" name="nombre" id="nombre" class="form-control" required>
							</div>
							<div class="col-md-6">
								<label for="nivel_dificultad" class="form-label">Nivel de Dificultad:</label>
								<select name="nivel_dificultad" id="nivel_dificultad" class="form-select" required>
									<option value="Principiante">Principiante</option>
									<option value="Intermedio">Intermedio</option>
									<option value="Avanzado">Avanzado</option>
								</select>
							</div>
						</div>
						<div class="mb-3">
							<label for="descripcion" class="form-label">Descripción:</label>
							<textarea name="descripcion" id="descripcion" class="form-control" rows="2"></textarea>
						</div>
						<hr>
						<!-- Dynamic days container -->
						<div id="dias-container"></div>

						<button type="button" class="btn btn-success mt-2" id="btn-agregar-dia">
							<i class="fas fa-calendar-plus me-1"></i>Añadir Día
						</button>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
					<button type="submit" form="formCrearRutina" class="btn btn-primary"><i
							class="fas fa-save me-1"></i>Guardar Rutina</button>
				</div>
			</div>
		</div>
	</div>
	<!-- View Details Modal -->
	<div class="modal fade" id="modalVerDetalles" tabindex="-1" aria-labelledby="modalVerDetallesLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalVerDetallesLabel"><i
							class="fas fa-clipboard-list me-2"></i>Detalles de la Rutina</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div id="detalles-rutina-container">
						<!-- El contenido dinámico de los detalles de la rutina se cargará aquí -->
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<!-- TEMPLATES (hidden) -->
	<template id="template-dia">
		<div class="day-block">
			<div class="day-header d-flex justify-content-between align-items-center">
				<div class="w-50">
					<label class="form-label">Día de la Semana:</label>
					<select name="dia_semana[]" class="form-select" required>
						<option value="LUNES">Lunes</option>
						<option value="MARTES">Martes</option>
						<option value="MIERCOLES">Miércoles</option>
						<option value="JUEVES">Jueves</option>
						<option value="VIERNES">Viernes</option>
						<option value="SABADO">Sábado</option>
						<option value="DOMINGO">Domingo</option>
					</select>
				</div>
				<button type="button" class="btn btn-danger btn-sm btn-eliminar-dia"><i class="fas fa-trash-alt"></i>
					Eliminar Día</button>
			</div>
			<div class="ejercicios-container">
				<!-- Exercise rows will be added here -->
			</div>
			<button type="button" class="btn btn-outline-primary btn-sm mt-2 btn-agregar-ejercicio">
				<i class="fas fa-plus"></i> Agregar Ejercicio
			</button>
		</div>
	</template>

	<template id="template-ejercicio">
		<div class="row g-2 align-items-center mb-2 ejercicio-row">
			<div class="col-md-5">
				<select name="idejercicio[]" class="form-select" required>
					<option value="">Seleccione un ejercicio...</option>
					<?php foreach ($ejercicios_disponibles as $ejercicio): ?>
						<option value="<?php echo $ejercicio['idejercicio']; ?>">
							<?php echo htmlspecialchars($ejercicio['nomb_ejer']); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-md-3">
				<input type="text" name="repeticiones[]" class="form-control" placeholder="Repeticiones (ej: 4x12)"
					required>
			</div>
			<div class="col-md-3">
				<div class="input-group">
					<input type="number" name="descanso[]" class="form-control" placeholder="Descanso" required>
					<span class="input-group-text">seg.</span>
				</div>
			</div>
			<div class="col-md-1">
				<button type="button" class="btn btn-danger btn-sm btn-eliminar-ejercicio"><i
						class="fas fa-times"></i></button>
			</div>
		</div>
	</template>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {

			// --- INICIO: CÓDIGO PARA VER DETALLES DE RUTINA ---
			const modalVerDetallesElement = document.getElementById('modalVerDetalles');
			if (modalVerDetallesElement) {
				const modalVerDetalles = new bootstrap.Modal(modalVerDetallesElement);
				const detallesContainer = document.getElementById('detalles-rutina-container');
				const tablaContainer = document.querySelector('.table-responsive');

				if (tablaContainer && detallesContainer) {
					tablaContainer.addEventListener('click', function (e) {
						const verBtn = e.target.closest('.btn-ver-detalles');
						if (verBtn) {
							const idRutina = verBtn.dataset.idrutina;

							detallesContainer.innerHTML = '<p class="text-center">Cargando detalles de la rutina...</p>';
							modalVerDetalles.show();

							fetch(`get_rutina_detalles.php?id=${idRutina}`)
								.then(response => {
									if (!response.ok) throw new Error('La respuesta de la red no fue correcta');
									return response.json();
								})
								.then(data => {
									if (data.error) throw new Error(data.error);

									let html = `
								<h3>${data.nombre}</h3>
								<p><strong>Nivel:</strong> ${data.nivel_dificultad}</p>
								<p><strong>Descripción:</strong> ${data.descripcion || 'No se proporcionó descripción.'}</p>
								<hr>
							`;

									if (data.dias && data.dias.length > 0) {
										data.dias.forEach(dia => {
											html += `
									<div class="day-block">
										<div class="day-header">
											<h4>Día: ${dia.dia_semana}</h4>
										</div>
										<table class="table table-sm table-bordered">
											<thead class="table-light">
												<tr>
													<th>Ejercicio</th>
													<th>Repeticiones</th>
													<th>Descanso (seg)</th>
												</tr>
											</thead>
											<tbody>
									`;
											if (dia.ejercicios && dia.ejercicios.length > 0) {
												dia.ejercicios.forEach(ejercicio => {
													html += `
											<tr>
												<td>${ejercicio.nomb_ejer}</td>
												<td>${ejercicio.repeticiones}</td>
												<td>${ejercicio.descanso}</td>
											</tr>
											`;
												});
											} else {
												html += '<tr><td colspan="3">No hay ejercicios para este día.</td></tr>';
											}
											html += '</tbody></table></div>';
										});
									} else {
										html += '<p>Esta rutina no tiene días ni ejercicios asignados.</p>';
									}
									detallesContainer.innerHTML = html;
								})
								.catch(error => {
									console.error('Error al cargar la rutina:', error);
									detallesContainer.innerHTML = `<div class="alert alert-danger">Error al cargar los detalles: ${error.message}</div>`;
								});
						}
					});
				}
			}
			// --- FIN: CÓDIGO PARA VER DETALLES DE RUTINA ---


			// --- INICIO: CÓDIGO PARA EL CONSTRUCTOR DE RUTINAS ---
			const diasContainer = document.getElementById('dias-container');
			const btnAgregarDia = document.getElementById('btn-agregar-dia');
			const formCrearRutina = document.getElementById('formCrearRutina');
			const templateDia = document.getElementById('template-dia');
			const templateEjercicio = document.getElementById('template-ejercicio');

			if (btnAgregarDia && diasContainer && templateDia && templateEjercicio && formCrearRutina) {
				btnAgregarDia.addEventListener('click', () => {
					const diaClone = templateDia.content.cloneNode(true);
					diasContainer.appendChild(diaClone);
				});

				diasContainer.addEventListener('click', function (e) {
					const btnEliminarDia = e.target.closest('.btn-eliminar-dia');
					if (btnEliminarDia) {
						btnEliminarDia.closest('.day-block').remove();
						return;
					}

					const btnAgregarEjercicio = e.target.closest('.btn-agregar-ejercicio');
					if (btnAgregarEjercicio) {
						const ejerciciosContainer = btnAgregarEjercicio.closest('.day-block').querySelector('.ejercicios-container');
						const ejercicioClone = templateEjercicio.content.cloneNode(true);
						ejerciciosContainer.appendChild(ejercicioClone);
						return;
					}

					const btnEliminarEjercicio = e.target.closest('.btn-eliminar-ejercicio');
					if (btnEliminarEjercicio) {
						btnEliminarEjercicio.closest('.ejercicio-row').remove();
					}
				});

				formCrearRutina.addEventListener('submit', function (e) {
					e.preventDefault();

					const rutinaData = {
						nombre: document.getElementById('nombre').value,
						nivel_dificultad: document.getElementById('nivel_dificultad').value,
						descripcion: document.getElementById('descripcion').value,
						dias: []
					};

					diasContainer.querySelectorAll('.day-block').forEach((dayBlock, index) => {
						const diaData = {
							dia_semana: dayBlock.querySelector('select[name="dia_semana[]"]').value,
							ejercicios: []
						};

						dayBlock.querySelectorAll('.ejercicio-row').forEach(ejercicioRow => {
							const ejercicioData = {
								idejercicio: ejercicioRow.querySelector('select[name="idejercicio[]"]').value,
								repeticiones: ejercicioRow.querySelector('input[name="repeticiones[]"]').value,
								descanso: ejercicioRow.querySelector('input[name="descanso[]"]').value
							};
							diaData.ejercicios.push(ejercicioData);
						});
						rutinaData.dias.push(diaData);
					});

					console.log('Datos a enviar:', JSON.stringify(rutinaData, null, 2));

					fetch('crear_rutina.php', {
						method: 'POST',
						headers: { 'Content-Type': 'application/json' },
						body: JSON.stringify(rutinaData)
					})
						.then(response => response.json())
						.then(data => {
							console.log(data);
							if (data.success) {
								const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgregar'));
								modal.hide();
								location.reload();
							} else {
								alert('Error al guardar la rutina: ' + data.message);
							}
						})
						.catch(error => {
							console.error('Error:', error);
							alert('Hubo un error de conexión al guardar la rutina.');
						});
				});
			}
			// --- FIN: CÓDIGO PARA EL CONSTRUCTOR DE RUTINAS ---
		});
	</script>

</body>

</html>