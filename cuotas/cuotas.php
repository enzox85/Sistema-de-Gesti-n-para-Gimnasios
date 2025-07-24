        <?php
        include("../conexion.php");
        $con = conectar();

        // Consulta con JOIN para obtener datos de ambas tablas
        $sql = "SELECT c.idcuota, c.idsocio, c.monto, c.fecha_emision, 
                    c.fecha_vencimiento, c.fecha_pago, c.estado, 
                    c.metodo_pago, c.observaciones,
                    s.nombre, s.apellido, s.foto 
                FROM cuotas c
                JOIN socios s ON c.idsocio = s.idsocio
                ORDER BY c.idsocio";
        $query = mysqli_query($con, $sql);
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cuotas</title>
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
                
                <div class="flex-grow-1 p-4">
                    <h2>Gestión de Cuotas</h2>
                <br>
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Fecha Vto.</th>
                                <th class="acciones-btn">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                             <tr>
                                <td>
                                    <?php if(!empty($row['foto'])): ?>
                                        <img src="<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $row['foto']); ?>" class="foto-perfil" alt="Foto de <?php echo $row['nombre']; ?>">
                                    <?php else: ?>
                                        <div class="foto-perfil bg-secondary d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                                    <td>$<?php echo number_format($row['monto'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $row['estado'] == 'PAGADA' ? 'success' : 
                                                ($row['estado'] == 'VENCIDA' ? 'danger' : 'warning'); 
                                        ?>">
                                            <?php echo htmlspecialchars($row['estado']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['fecha_vencimiento']); ?></td>
                                    
                                    <td class="acciones-btn">
                                        <button class="btn btn-warning btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#myModal"
                                                data-id="<?php echo $row['idcuota']; ?>">
                                            Pagar
                                        </button>
                                    </td>
										<!-- Modal Registro Pago -->
                                    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel">Registrar Pago</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <form action="pagar.php" method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="idcuota" id="idcuotaHidden">
                                                        <div class="mb-3">
                                                            <label class="form-label">Monto Pagado</label>
                                                            <input type="number" class="form-control" name="monto" required step="0.01">
                                                        </div>
                                                    </div>
													
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Registrar Pago</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                <script>
                                // Pasa el ID de la cuota al modal
                                document.addEventListener('DOMContentLoaded', function() {
                                    var modal = document.getElementById('myModal');
                                    modal.addEventListener('show.bs.modal', function(event) {
                                        var button = event.relatedTarget;
                                        var idcuota = button.getAttribute('data-id');
                                        document.getElementById('idcuotaHidden').value = idcuota;
                                    });
                                });
                                </script>          
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
        </div>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>