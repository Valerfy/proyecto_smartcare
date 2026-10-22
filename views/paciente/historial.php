<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Historial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1">
        <nav class="topbar navbar navbar-expand-lg px-4 py-3">
            <div class="container-fluid">
                <button class="sidebar-toggle d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 text-dark fw-bold">Caja Nacional de Salud, La Paz</h5>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <img src="<?= $fotoPerfil ?>" class="rounded-circle" width="40" alt="Foto de perfil"
                         onerror="this.src='https://i.pravatar.cc/48?u=<?= $uid ?>';">
                    <div>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['nombre'].' '.$_SESSION['apellido']) ?></span>
                        <small class="d-block text-muted"><?= htmlspecialchars($_SESSION['rol']) ?></small>
                    </div>
                </div>
            </div>
        </nav>

        <div class="p-4">
            <h2 class="mb-4">Historial Clínico</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Especialidad</th>
                            <th>Médico</th>
                            <th>Diagnóstico</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($historial)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                Aún no tienes registros en tu historial clínico.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historial as $i => $h):
                            $badge = match($h['estado']) {
                                'completada', 'confirmada' => 'bg-success',
                                'programada' => 'bg-primary',
                                'cancelada'  => 'bg-danger',
                                default      => 'bg-warning text-dark'
                            };
                        ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($h['fecha'])) ?></td>
                            <td><?= date('h:i A', strtotime($h['hora'])) ?></td>
                            <td><?= htmlspecialchars($h['especialidad']) ?></td>
                            <td><?= htmlspecialchars($h['medico']) ?></td>
                            <td>
                                <?php if (!empty($h['diagnostico'])): ?>
                                    <?= htmlspecialchars(mb_strimwidth($h['diagnostico'], 0, 40, '...')) ?>
                                <?php else: ?>
                                    <span class="text-muted">— Sin diagnóstico</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge <?= $badge ?>"><?= ucfirst($h['estado']) ?></span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border" title="Ver detalle"
                                    onclick="verDetalle(<?= $i ?>)">
                                    <i class="bi bi-eye text-primary"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-muted small">
                Mostrando <?= count($historial) ?> registro(s) de historial
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-file-earmark-medical me-2"></i>Detalle del Diagnóstico</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const historial = <?= json_encode(array_values($historial)) ?>;

function verDetalle(i) {
    const h    = historial[i];
    const fecha = new Date(h.fecha).toLocaleDateString('es-BO', {day:'2-digit', month:'long', year:'numeric'});
    const hora  = h.hora ? h.hora.substring(0, 5) : '—';

    document.getElementById('modalBody').innerHTML = `
        <div class="row g-3">
            <div class="col-md-6"><strong>Fecha:</strong> ${fecha}</div>
            <div class="col-md-6"><strong>Hora:</strong> ${hora}</div>
            <div class="col-md-6"><strong>Médico:</strong> ${h.medico}</div>
            <div class="col-md-6"><strong>Especialidad:</strong> ${h.especialidad}</div>
            <div class="col-12"><hr class="my-1"></div>
            <div class="col-md-3"><strong>Tipo de sangre:</strong><br>${h.tipo_sangre || '—'}</div>
            <div class="col-md-3"><strong>Peso:</strong><br>${h.peso ? h.peso + ' kg' : '—'}</div>
            <div class="col-md-3"><strong>Talla:</strong><br>${h.talla ? h.talla + ' m' : '—'}</div>
            <div class="col-md-3"><strong>Presión arterial:</strong><br>${h.presion_arterial || '—'}</div>
            <div class="col-12"><hr class="my-1"></div>
            <div class="col-md-6"><strong>Enfermedades:</strong><br>${h.enfermedades || '—'}</div>
            <div class="col-md-6"><strong>Alergias:</strong><br>${h.alergias || '—'}</div>
            <div class="col-12"><strong>Medicamentos:</strong><br>${h.medicamentos || '—'}</div>
            <div class="col-12"><hr class="my-1"></div>
            <div class="col-12"><strong>Diagnóstico:</strong><br>${h.diagnostico || '— Sin diagnóstico'}</div>
            <div class="col-12"><strong>Observación:</strong><br>${h.observacion || '— Sin observación'}</div>
        </div>`;

    new bootstrap.Modal(document.getElementById('modalDetalle')).show();
}

function cerrarSesion() {
    if (confirm('¿Cerrar sesión?')) window.location.href = 'index.php?controller=auth&action=logout';
}
</script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>