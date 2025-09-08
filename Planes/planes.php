<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/conexion.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/Planes/modelo_planes.php';

$conexion = conectar();

// 1. Obtener todos los planes asignados para la tabla principal
$planes = obtenerPlanesAsignados($conexion);

// 2. Obtener la lista de socios para el formulario (CONSULTA CORREGIDA)
$sql_socios = "SELECT s.idsocio, p.nombre, p.apellido 
               FROM socios s
               JOIN personas p ON s.id_persona = p.id_persona 
               ORDER BY p.apellido, p.nombre";
$res_socios = mysqli_query($conexion, $sql_socios);
$lista_socios = mysqli_fetch_all($res_socios, MYSQLI_ASSOC);

mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Planes de Entrenamiento</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
	<style>
		.foto-perfil {
			width: 50px;
			height: 50px;
			object-fit: cover;
			border-radius: 50%;
		}
	</style>
</head>

<body class="d-flex">
	<?php include $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/includes/sidebar.php'; ?>

	<main class="flex-grow-1 p-4">
		<div class="container-fluid">
			<div class="card shadow-sm">
				<div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
					<h2 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Planes de Entrenamiento</h2>
					<button type="button" class="btn btn-success" data-bs-toggle="modal"
						data-bs-target="#modalAsignarPlan">
						<i class="fas fa-plus-circle me-1"></i>Asignar Nuevo Plan
					</button>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped table-hover align-middle">
							<thead class="table-dark">
								<tr>
									<th>Foto</th>
									<th>Socio</th>
									<th>Tipo de Plan</th>
									<th>Fecha Inicio</th>
									<th>Fecha Fin</th>
									<th class="text-center">Activo</th>
									<th class="text-center">Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php if (!empty($planes)):
									foreach ($planes as $plan): ?>
										<tr>
											<td>
												<?php if (!empty($plan['foto'])): ?>
													<img src="../Socios/uploads/<?php echo htmlspecialchars($plan['foto']); ?>"
														class="foto-perfil">
												<?php else: ?>
													<div
														class="foto-perfil bg-secondary d-flex align-items-center justify-content-center">
														<i class="bi bi-person-fill text-white fs-4"></i>
													</div>
												<?php endif; ?>
											</td>
											<td><?php echo htmlspecialchars($plan['nombre_socio'] . ' ' . $plan['apellido_socio']); ?>
											</td>
											<td><?php echo htmlspecialchars($plan['tipo_plan']); ?></td>
											<td><?php echo htmlspecialchars(date("d/m/Y", strtotime($plan['fecha_inicio']))); ?>
											</td>
											<td><?php echo $plan['fecha_fin'] ? htmlspecialchars(date("d/m/Y", strtotime($plan['fecha_fin']))) : 'Indefinido'; ?>
											</td>
											<td class="text-center">
												<?php echo $plan['activo'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-danger">No</span>'; ?>
											</td>
											<td class="text-center">
												<button class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></button>
												<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
											</td>
										</tr>
									<?php endforeach; else: ?>
									<tr>
										<td colspan="7" class="text-center fst-italic">No hay planes asignados todavía.</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</main>

	<!-- Modal para Asignar Plan -->
	<div class="modal fade" id="modalAsignarPlan" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Asignar Nuevo Plan</h5><button type="button" class="btn-close"
						data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<form id="formAsignarPlan">
						<div class="mb-3"><label for="idsocio" class="form-label">Socio</label><select
								class="form-select" name="idsocio" required>
								<option value="" disabled selected>Selecciona...</option>
								<?php foreach ($lista_socios as $socio): ?>
									<option value="<?php echo $socio['idsocio']; ?>">
										<?php echo htmlspecialchars($socio['apellido'] . ', ' . $socio['nombre']); ?>
									</option><?php endforeach; ?>
							</select></div>
						<div class="mb-3"><label for="tipo_plan" class="form-label">Objetivo Principal</label><select
								class="form-select" name="tipo_plan" required>
								<option value="" disabled selected>Selecciona...</option>
								<option value="Aumento de Masa Muscular">Aumento de Masa Muscular</option>
								<option value="Pérdida de Peso">Pérdida de Peso</option>
								<option value="Mantenimiento">Mantenimiento</option>
							</select></div>
						<div class="row">
							<div class="col-md-6 mb-3"><label for="peso_actual" class="form-label">Peso Actual
									(kg)</label><input type="number" step="0.1" class="form-control" name="peso_actual">
							</div>
							<div class="col-md-6 mb-3"><label for="altura" class="form-label">Altura (cm)</label><input
									type="number" class="form-control" name="altura"></div>
						</div>
						<div class="mb-3"><label for="nivel_experiencia" class="form-label">Nivel de
								Experiencia</label><select class="form-select" name="nivel_experiencia">
								<option value="Principiante">Principiante</option>
								<option value="Intermedio" selected>Intermedio</option>
								<option value="Avanzado">Avanzado</option>
							</select></div>
						<div class="row">
							<div class="col-md-6 mb-3"><label for="fecha_inicio" class="form-label">Fecha de
									Inicio</label><input type="date" class="form-control" name="fecha_inicio"
									value="<?php echo date('Y-m-d'); ?>" required></div>
							<div class="col-md-6 mb-3"><label for="fecha_fin" class="form-label">Fecha de Fin
									(Opcional)</label><input type="date" class="form-control" name="fecha_fin"></div>
						</div>
						<div class="mb-3"><label for="disponibilidad" class="form-label">Disponibilidad
								Semanal</label><input type="text" class="form-control" name="disponibilidad"
								placeholder="Ej: 3 días/semana"></div>
						<div class="mb-3"><label for="observaciones" class="form-label">Observaciones</label><textarea
								class="form-control" name="observaciones" rows="3"></textarea></div>
						<div id="asignarPlanError" class="alert alert-danger" style="display:none;"></div>
					</form>
				</div>
				<div class="modal-footer"><button type="button" class="btn btn-secondary"
						data-bs-dismiss="modal">Cerrar</button><button type="submit" form="formAsignarPlan"
						class="btn btn-success">Asignar Plan</button></div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const form = document.getElementById('formAsignarPlan');
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				const formData = new FormData(form);
				const errorDiv = document.getElementById('asignarPlanError');
				errorDiv.style.display = 'none';

				fetch('controlador_planes.php?accion=asignar', {
					method: 'POST',
					body: formData
				})
				.then(response => {
					if (!response.ok) {
						// Si la respuesta no es OK, lee el texto del error y lánzalo
						return response.text().then(text => {
							throw new Error('Respuesta del servidor no fue OK: ' + text);
						});
					}
					return response.json(); // Si es OK, procesa como JSON
				})
				.then(data => {
					if (data.status === 'success') {
						alert(data.message);
						window.location.reload();
					} else {
						errorDiv.textContent = data.message || 'Ocurrió un error desconocido.';
						errorDiv.style.display = 'block';
					}
				})
				.catch(error => {
					// Este catch ahora recibirá el error detallado
					errorDiv.textContent = error.message;
					errorDiv.style.display = 'block';
				});
			});
		});
	</script>
</body>

</html>