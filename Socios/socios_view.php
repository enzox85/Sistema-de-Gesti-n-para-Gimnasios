<?php
// Recibe $socios y $busqueda desde el controlador
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
        .profile-pic { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        .preview-pic { max-height: 150px; border-radius: 0.375rem; }
    </style>
</head>
<body class="d-flex">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/spartanproject/includes/sidebar.php'; ?>
    <main class="flex-grow-1 p-4">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><i class="fas fa-users me-2"></i>Gestión de Socios</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-4 g-3 align-items-center">
                        <div class="col-md-8">
                            <form action="socios_controller.php" method="POST" class="d-flex">
                                <div class="input-group"><input type="text" class="form-control" name="busqueda" placeholder="Buscar..." value="<?php echo htmlspecialchars($busqueda); ?>"><button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button></div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoSocio"><i class="fas fa-user-plus me-1"></i>Nuevo Socio</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr><th>Foto</th><th>Nombre</th><th>Apellido</th><th>DNI</th><th>Teléfono</th><th>Email</th><th class="text-center">Opciones</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($socios)): ?>
                                    <tr><td colspan="7" class="text-center fst-italic">No se encontraron socios.</td></tr>
                                <?php else: foreach ($socios as $socio): ?>
                                    <tr id="socio-row-<?php echo $socio['id_persona']; ?>">
                                        <td>
                                            <?php if (!empty($socio['foto'])): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($socio['foto']); ?>" class="profile-pic">
                                            <?php else: ?>
                                                <div class="profile-pic bg-secondary d-flex align-items-center justify-content-center"><i class="bi bi-person text-white fs-4"></i></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($socio['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($socio['apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($socio['dni']); ?></td>
                                        <td><?php echo htmlspecialchars($socio['telefono']); ?></td>
                                        <td><?php echo htmlspecialchars($socio['email']); ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-warning me-1 btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditarSocio" data-socio='<?php echo json_encode($socio); ?>'><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-danger" onclick="prepararEliminacion(<?php echo $socio['id_persona']; ?>)" data-bs-toggle="modal" data-bs-target="#confirmarEliminarModal"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</main>

    <!-- Modal Nuevo Socio -->
    <div class="modal fade" id="modalNuevoSocio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg"><div class="modal-content">
            <div class="modal-header bg-success text-white"><h5 class="modal-title">Registrar Socio</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="formNuevoSocio" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6"><div class="mb-3"><label class="form-label">Nombre:</label><input type="text" name="nombre" class="form-control" required></div><div class="mb-3"><label class="form-label">Apellido:</label><input type="text" name="apellido" class="form-control" required></div><div class="mb-3"><label class="form-label">DNI:</label><input type="text" name="dni" class="form-control" required></div><div class="mb-3"><label class="form-label">Fecha de Nacimiento:</label><input type="date" name="fecha_nacimiento" class="form-control"></div></div>
                        <div class="col-md-6"><div class="mb-3"><label class="form-label">Teléfono:</label><input type="tel" name="telefono" class="form-control"></div><div class="mb-3"><label class="form-label">Email:</label><input type="email" name="email" class="form-control"></div><div class="mb-3"><label class="form-label">Dirección:</label><input type="text" name="direccion" class="form-control"></div><div class="mb-3"><label class="form-label">Fecha de Alta:</label><input type="date" name="fechalta" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Foto de Perfil:</label><input type="file" name="foto" class="form-control" accept="image/*" onchange="previsualizarFoto(event, 'foto-preview-nuevo')"><div class="mt-2 text-center"><img id="foto-preview-nuevo" class="preview-pic" style="display:none;"></div></div>
                    <div class="mb-3"><label class="form-label">Observaciones:</label><textarea name="probfis" class="form-control" rows="2"></textarea></div>
                    <div id="nuevoSocioError" class="alert alert-danger" style="display:none;"></div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Guardar</button></div>
                </form>
            </div>
        </div></div>
    </div>

    <!-- Modal Editar Socio -->
    <div class="modal fade" id="modalEditarSocio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg"><div class="modal-content">
            <div class="modal-header bg-warning"><h5 class="modal-title">Editar Socio</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="formEditarSocio" enctype="multipart/form-data">
                    <input type="hidden" name="id_persona">
                    <input type="hidden" name="foto_actual">
                    <div class="row">
                        <div class="col-md-6"><div class="mb-3"><label class="form-label">Nombre:</label><input type="text" name="nombre" class="form-control" required></div><div class="mb-3"><label class="form-label">Apellido:</label><input type="text" name="apellido" class="form-control" required></div><div class="mb-3"><label class="form-label">DNI:</label><input type="text" name="dni" class="form-control" required></div><div class="mb-3"><label class="form-label">Fecha de Nacimiento:</label><input type="date" name="fecha_nacimiento" class="form-control"></div></div>
                        <div class="col-md-6"><div class="mb-3"><label class="form-label">Teléfono:</label><input type="tel" name="telefono" class="form-control"></div><div class="mb-3"><label class="form-label">Email:</label><input type="email" name="email" class="form-control"></div><div class="mb-3"><label class="form-label">Dirección:</label><input type="text" name="direccion" class="form-control"></div><div class="mb-3"><label class="form-label">Fecha de Alta:</label><input type="date" name="fechalta" class="form-control" required></div></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Foto de Perfil:</label><input type="file" name="foto" class="form-control" accept="image/*" onchange="previsualizarFoto(event, 'foto-preview-editar')"><div class="mt-2 text-center"><img id="foto-preview-editar" class="preview-pic" style="display:none;"></div></div>
                    <div class="mb-3"><label class="form-label">Observaciones:</label><textarea name="probfis" class="form-control" rows="2"></textarea></div>
                    <div id="editarSocioError" class="alert alert-danger" style="display:none;"></div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning">Actualizar Cambios</button></div>
                </form>
            </div>
        </div></div>
    </div>

    <!-- Modal Eliminar Socio -->
    <div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Confirmar Eliminación</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">¿Estás seguro? Esta acción no se puede deshacer.</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="socios_controller.php" method="GET"><input type="hidden" name="accion" value="eliminar"><input type="hidden" id="id_persona_eliminar" name="id_persona"><button type="submit" class="btn btn-danger">Sí, Eliminar</button></form>
            </div>
        </div></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Previsualizar foto en ambos modales
        window.previsualizarFoto = function(event, previewId) {
            const reader = new FileReader();
            const preview = document.getElementById(previewId);
            reader.onload = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
            }
            if (event.target.files[0]) { reader.readAsDataURL(event.target.files[0]); }
        }

        // Preparar el ID para el modal de eliminación
        window.prepararEliminacion = function(id_persona) {
            document.getElementById('id_persona_eliminar').value = id_persona;
        }

        // AJAX para crear nuevo socio
        document.getElementById('formNuevoSocio').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const errorDiv = document.getElementById('nuevoSocioError');
            fetch('socios_controller.php?accion=crear', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        errorDiv.textContent = data.message;
                        errorDiv.style.display = 'block';
                    }
                }).catch(error => {
                    errorDiv.textContent = 'Error de conexión al crear.';
                    errorDiv.style.display = 'block';
                });
        });

        // Lógica para abrir y poblar el modal de edición
        const modalEditarSocio = document.getElementById('modalEditarSocio');
        modalEditarSocio.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const socio = JSON.parse(button.getAttribute('data-socio'));
            const form = this.querySelector('form');
            
            form.querySelector('[name="id_persona"]').value = socio.id_persona;
            form.querySelector('[name="nombre"]').value = socio.nombre;
            form.querySelector('[name="apellido"]').value = socio.apellido;
            form.querySelector('[name="dni"]').value = socio.dni;
            form.querySelector('[name="fecha_nacimiento"]').value = socio.fecha_nacimiento;
            form.querySelector('[name="telefono"]').value = socio.telefono;
            form.querySelector('[name="email"]').value = socio.email;
            form.querySelector('[name="direccion"]').value = socio.direccion;
            form.querySelector('[name="fechalta"]').value = socio.fechalta;
            form.querySelector('[name="probfis"]').textContent = socio.probfis;
            form.querySelector('[name="foto_actual"]').value = socio.foto;

            const preview = document.getElementById('foto-preview-editar');
            if (socio.foto) {
                preview.src = 'uploads/' + socio.foto;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });

        // AJAX para editar socio (aún apunta a actualizarSocio.php, puedes migrar luego)
        document.getElementById('formEditarSocio').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const errorDiv = document.getElementById('editarSocioError');
            fetch('actualizarSocio.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        errorDiv.textContent = data.message;
                        errorDiv.style.display = 'block';
                    }
                }).catch(error => {
                    errorDiv.textContent = 'Error de conexión al actualizar.';
                    errorDiv.style.display = 'block';
                });
        });
    });
    </script>
</body>
</html>
