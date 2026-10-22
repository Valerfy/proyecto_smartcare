<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Mis Pacientes</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/syles_medico.css">
</head>
<body class="bg-light">
<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1">
        <nav class="navbar px-4 py-3 bg-white shadow-sm sticky-top">
            <div class="container-fluid">
                <button class="sidebar-toggle me-3 d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold">Mis Pacientes</h5>
                <div class="ms-auto">
                   <form method="GET" action="index.php" class="position-relative d-none d-md-block">
                      <input type="hidden" name="controller" value="medico">
                      <input type="hidden" name="action" value="misPacientes">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="buscar"
                               class="form-control ps-5 border-0 bg-light rounded-pill"
                               style="width:280px;"
                               placeholder="Buscar por nombre o CI..."
                               value="<?= htmlspecialchars($busqueda) ?>">
                    </form>
                </div>
            </div>
        </nav>

        <div class="p-4">
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'actualizado'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> Datos del paciente actualizados correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="text-muted mb-0">
                    <?= count($pacientes) ?> paciente(s)
                    <?= $busqueda ? 'para "' . htmlspecialchars($busqueda) . '"' : 'en tu lista' ?>
                </p>
                <?php if ($busqueda): ?>
                <a href="index.php?controller=medico&action=misPacientes" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x"></i> Limpiar búsqueda
                </a>
                <?php endif; ?>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-4 py-3">Paciente</th>
                                <th>CI</th>
                                <th>Teléfono</th>
                                <th>Última cita</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pacientes)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-people d-block fs-1 mb-2"></i>
                                    <?= $busqueda ? 'No se encontraron pacientes.' : 'Aún no tienes pacientes registrados.' ?>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($pacientes as $p): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width:38px;height:38px;border-radius:50%;background:#E6F1FB;color:#185FA5;display:flex;align-items:center;justify-content:center;font-weight:500;font-size:13px;flex-shrink:0;">
                                                <?= strtoupper(substr($p['nombre'],0,1) . substr($p['apellido'],0,1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?></div>
                                                <?php if (!empty($p['fecha_nac']) && $p['fecha_nac'] !== '0000-00-00'): ?>
                                                <div class="text-muted" style="font-size:11px;">
                                                    <?= date_diff(date_create($p['fecha_nac']), date_create('today'))->y ?> años
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?= htmlspecialchars($p['ci']) ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($p['telefono'] ?? '—') ?></td>
                                    <td>
                                        <?php if ($p['ultima_fecha']): ?>
                                        <span class="text-muted" style="font-size:13px;"><?= date('d/m/Y', strtotime($p['ultima_fecha'])) ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $estado = $p['ultimo_estado'] ?? 'pendiente';
                                        [$bg, $label] = match($estado) {
                                            'completada' => ['bg-success-subtle text-success border-success', 'Atendido'],
                                            'confirmada' => ['bg-info-subtle text-info border-info', 'Confirmada'],
                                            'cancelada'  => ['bg-danger-subtle text-danger border-danger', 'Cancelada'],
                                            default      => ['bg-warning-subtle text-warning border-warning', 'Pendiente'],
                                        };
                                        ?>
                                        <span class="badge rounded-pill border <?= $bg ?>"><?= $label ?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="index.php?controller=medico&action=historial&buscar=<?= urlencode($p['ci']) ?>"
                                               class="btn btn-sm btn-light border" title="Ver historial">
                                                <i class="bi bi-file-earmark-medical text-info"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>
