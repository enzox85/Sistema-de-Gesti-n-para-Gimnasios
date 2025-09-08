<?php
header('Content-Type: application/json');
require_once '../conexion.php';
require_once 'modelo_ejercicios.php';

$response = ['success' => false, 'message' => 'ID de ejercicio no proporcionado.'];

if (isset($_GET['id'])) {
	$con = conectar();
	$id_ejercicio = intval($_GET['id']);

	// Reutilizamos la función del modelo para mantener la lógica centralizada
	$data = obtenerDetallesEjercicio($con, $id_ejercicio);

	if ($data && $data['ejercicio']) {
		$ejercicio = $data['ejercicio'];
		$media = $data['media'];

		// Usamos output buffering para construir el HTML de forma limpia
		ob_start();
		?>

		<form id="form-modificar-ejercicio" enctype="multipart/form-data">
			<input type="hidden" name="idejercicio" value="<?php echo htmlspecialchars($ejercicio['idejercicio'] ?? ''); ?>">

			<div class="row g-3 mb-3">
				<div class="col-md-6">
					<label class="form-label">Nombre del ejercicio</label>
					<input type="text" name="nomb_ejer" class="form-control"
						value="<?php echo htmlspecialchars($ejercicio['nomb_ejer'] ?? ''); ?>" required>
				</div>
				<div class="col-md-6">
					<label class="form-label">Grupo muscular</label>
					<select name="grupo_mus" class="form-select" required>
						<?php
						$grupos = ['PIERNA', 'BRAZO', 'PECHO', 'ESPALDA', 'HOMBRO', 'ABDOMEN'];
						$current_grupo = $ejercicio['grupo_mus'] ?? '';
						foreach ($grupos as $grupo) {
							$selected = ($current_grupo == $grupo) ? 'selected' : '';
							echo "<option value=\"$grupo\" $selected>$grupo</option>";
						}
						?>
					</select>
				</div>
				<div class="col-md-12">
					<label class="form-label">Nivel de dificultad</label>
					<select name="nivel_dificultad" class="form-select" required>
						<?php
						$niveles = ['PRINCIPIANTE', 'INTERMEDIO', 'AVANZADO'];
						$current_nivel = $ejercicio['nivel_dificultad'] ?? '';
						foreach ($niveles as $nivel) {
							$selected = ($current_nivel == $nivel) ? 'selected' : '';
							echo "<option value=\"$nivel\" $selected>$nivel</option>";
						}
						?>
					</select>
				</div>
				<div class="col-md-12">
					<label class="form-label">Descripción</label>
					<textarea name="descripcion" class="form-control"
						rows="3"><?php echo htmlspecialchars($ejercicio['descripcion'] ?? ''); ?></textarea>
				</div>
			</div>
			<hr>

			<h5 class="mb-3">Archivos Multimedia Existentes</h5>
			<?php if (empty($media)): ?>
				<p>No hay archivos multimedia para este ejercicio.</p>
			<?php else: ?>
				<div class="row g-2">
					<?php foreach ($media as $item): ?>
						<div class="col-auto" id="media-item-<?php echo htmlspecialchars($item['id_media'] ?? ''); ?>">
							<div class="card">
								<?php if (($item['tipo_media'] ?? '') === 'IMAGEN'): ?>
									<img src="<?php echo htmlspecialchars($item['url_media'] ?? ''); ?>"
										style="width: 100px; height: 100px; object-fit: cover;" alt="Imagen de ejercicio">
								<?php elseif (($item['tipo_media'] ?? '') === 'VIDEO_LINK'): ?>
									<?php
									$video_id = getYouTubeID($item['url_media'] ?? '');
									$thumbnail_url = $video_id ? "https://img.youtube.com/vi/{$video_id}/mqdefault.jpg" : '';
									?>
									<?php if ($thumbnail_url): ?>
										<img src="<?php echo htmlspecialchars($thumbnail_url); ?>"
											style="width: 100px; height: 100px; object-fit: cover;" alt="Vista previa de YouTube">
									<?php else: ?>
										<div class="text-center p-3" style="width: 100px; height: 100px; background-color: #e9ecef;"
											title="<?php echo htmlspecialchars($item['url_media'] ?? ''); ?>">
											<i class="fab fa-youtube fa-3x text-danger"></i>
										</div>
									<?php endif; ?>
								<?php endif; ?>
								<div class="card-body p-1 text-center">
									<input type="checkbox" name="media_a_eliminar[]"
										value="<?php echo htmlspecialchars($item['id_media'] ?? ''); ?>" class="form-check-input">
									<label class="form-check-label small">Eliminar</label>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<hr>
			<h5 class="mb-3">Añadir Nuevos Medios</h5>
			<div id="new-media-container-modificar" class="vstack gap-3"></div>
			<div class="mt-3">
				<button type="button" class="btn btn-outline-primary btn-add-image-modificar"><i
						class="fas fa-image me-1"></i>Añadir Imagen/GIF</button>
				<button type="button" class="btn btn-outline-info btn-add-video-modificar"><i
						class="fab fa-youtube me-1"></i>Añadir Link de Video</button>
			</div>
		</form>

		<?php
		$html_form = ob_get_clean();

		$response = [
			'success' => true,
			'html_form' => $html_form
		];

	} else {
		$response['message'] = 'Ejercicio no encontrado.';
	}

	$con->close();
}

echo json_encode($response);
?>