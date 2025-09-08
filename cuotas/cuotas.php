<?php
include("../conexion.php");
$con = conectar();

// Consulta con JOIN para obtener los datos necesarios
$sql = "SELECT c.idcuota, c.monto, c.fecha_vencimiento, c.estado,
               p.nombre, p.apellido, s.foto 
        FROM cuotas c
        JOIN socios s ON c.idsocio = s.idsocio
        JOIN personas p ON s.id_persona = p.id_persona
        ORDER BY c.fecha_vencimiento DESC";

$query = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuotas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <?php include $_SERVER['DOCUMENT_ROOT'].'/spartanproject/includes/sidebar.php'; ?>
    
    <main class="flex-grow-1 p-4">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h2 class="mb-0">Gestión de Cuotas</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Fecha Vto.</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td>
                                        <?php if(!empty($row['foto'])): ?>
                                           <img src="../Socios/uploads/<?php echo htmlspecialchars($row['foto']); ?>" class="foto-perfil" alt="Foto de <?php echo htmlspecialchars($row['nombre']); ?>">
                                        <?php else: ?>
                                            <div class="foto-perfil bg-secondary d-flex align-items-center justify-content-center">
                                                <i class="bi bi-person-fill text-white fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                                    <td>$<?php echo number_format($row['monto'], 2); ?></td>
                                    <td>
                                        <?php 
                                            $estado_clase = 'warning'; // Pendiente por defecto
                                            if ($row['estado'] == 'PAGADA') $estado_clase = 'success';
                                            if ($row['estado'] == 'VENCIDA') $estado_clase = 'danger';
                                        ?>
                                        <span class="badge bg-<?php echo $estado_clase; ?>">
                                            <?php echo htmlspecialchars($row['estado']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date("d/m/Y", strtotime($row['fecha_vencimiento'])); ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalPagarCuota"
                                                data-id="<?php echo $row['idcuota']; ?>"
                                                data-monto="<?php echo $row['monto']; ?>">
                                            Pagar
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal para Registrar Pago -->
<div class="modal fade" id="modalPagarCuota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="pagar.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="idcuota" id="idcuota_pagar">
                    <div class="mb-3">
                        <label class="form-label">Monto a Pagar</label>
                        <input type="number" class="form-control" name="monto" id="monto_pagar" required step="0.01">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ===== JAVASCRIPT LIMPIO Y FUERA DEL BUCLE =====
document.addEventListener('DOMContentLoaded', function() {
    const modalPagarCuota = document.getElementById('modalPagarCuota');
    modalPagarCuota.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const idcuota = button.getAttribute('data-id');
        const monto = button.getAttribute('data-monto');
        
        const modalInputId = modalPagarCuota.querySelector('#idcuota_pagar');
        const modalInputMonto = modalPagarCuota.querySelector('#monto_pagar');
        
        modalInputId.value = idcuota;
        modalInputMonto.value = monto;
    });
});
</script>
</body>
</html