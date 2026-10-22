<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/syles_medico.css">
</head>
<body>
<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1">
        <nav class="topbar navbar px-4 py-3 bg-white shadow-sm mb-4">
            <div class="container-fluid">
                <button class="sidebar-toggle me-3 d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reportes Clínicos</h5>
            </div>
        </nav>

        <div class="p-4">

            <?php
                $totalCompletadas = 0; $totalPendientes = 0; $totalCanceladas = 0;
                foreach ($estadisticas['citas_por_estado'] as $e) {
                    if ($e['estado'] === 'completada') $totalCompletadas = $e['total'];
                    if ($e['estado'] === 'pendiente')  $totalPendientes  = $e['total'];
                    if ($e['estado'] === 'cancelada')  $totalCanceladas  = $e['total'];
                }
            ?>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <i class="bi bi-check-circle-fill text-success fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0"><?= $totalCompletadas ?></h3>
                        <small class="text-muted">Citas Completadas</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <i class="bi bi-clock-fill text-warning fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0"><?= $totalPendientes ?></h3>
                        <small class="text-muted">Citas Pendientes</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <i class="bi bi-x-circle-fill text-danger fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0"><?= $totalCanceladas ?></h3>
                        <small class="text-muted">Citas Canceladas</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <i class="bi bi-people-fill text-primary fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0"><?= $estadisticas['pacientes_unicos'] ?></h3>
                        <small class="text-muted">Pacientes Únicos</small>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filtrar Reporte</h6>
                    <form method="GET" action="index.php" class="row g-3 align-items-end">
                        <input type="hidden" name="controller" value="medico">
                        <input type="hidden" name="action" value="reportes">

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control"
                                   value="<?= htmlspecialchars($fechaDesde) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control"
                                   value="<?= htmlspecialchars($fechaHasta) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Paciente (opcional)</label>
                            <select name="paciente_id" class="form-select">
                                <option value="">— Todos los pacientes —</option>
                                <?php foreach ($pacientes as $p): ?>
                                    <option value="<?= $p['id'] ?>"
                                        <?= ($pacienteId == $p['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['apellido'] . ', ' . $p['nombre'] . ' (' . $p['ci'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-search me-1"></i> Filtrar
                            </button>
                            <a href="index.php?controller=medico&action=reportes"
                               class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-bold">
                        <i class="bi bi-journal-medical me-2"></i>
                        Historial Clínico
                        <span class="badge bg-secondary ms-2"><?= count($historiales) ?> registros</span>
                    </span>
                    <?php if (!empty($historiales)): ?>
                    <a href="index.php?controller=medico&action=exportarReportePDF
                        <?= !empty($fechaDesde)  ? '&fecha_desde='  . urlencode($fechaDesde)  : '' ?>
                        <?= !empty($fechaHasta)  ? '&fecha_hasta='  . urlencode($fechaHasta)  : '' ?>
                        <?= !empty($pacienteId)  ? '&paciente_id='  . urlencode($pacienteId)  : '' ?>"
                       target="_blank"
                       class="btn btn-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
                    </a>
                    <?php endif; ?>
                </div>

                <?php if (empty($historiales)): ?>
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No se encontraron registros con los filtros aplicados.
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Fecha</th>
                                <th>Paciente</th>
                                <th>CI</th>
                                <th>Motivo</th>
                                <th>Diagnóstico</th>
                                <th>Tipo Sangre</th>
                                <th>Presión</th>
                                <th>Peso / Talla</th>
                                <th>Medicamentos</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($historiales as $h): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= date('d/m/Y', strtotime($h['fecha'])) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($h['hora'])) ?></small>
                                </td>
                                <td class="fw-bold"><?= htmlspecialchars($h['nombre'] . ' ' . $h['apellido']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($h['ci']) ?></td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width:120px;"
                                          title="<?= htmlspecialchars($h['motivo'] ?? '') ?>">
                                        <?= htmlspecialchars($h['motivo'] ?? '—') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width:160px;"
                                          title="<?= htmlspecialchars($h['diagnostico']) ?>">
                                        <?= htmlspecialchars($h['diagnostico']) ?>
                                    </span>
                                    <?php if (!empty($h['alergias'])): ?>
                                        <br><small class="text-warning">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <?= htmlspecialchars($h['alergias']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($h['tipo_sangre'])): ?>
                                        <span class="badge bg-danger"><?= htmlspecialchars($h['tipo_sangre']) ?></span>
                                    <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($h['presion_arterial'] ?? '—') ?></td>
                                <td class="text-muted">
                                    <?php
                                        $l = [];
                                        if (!empty($h['peso']))  $l[] = $h['peso']  . ' kg';
                                        if (!empty($h['talla'])) $l[] = $h['talla'] . ' m';
                                        echo $l ? implode(' / ', $l) : '—';
                                    ?>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block text-info" style="max-width:140px;"
                                          title="<?= htmlspecialchars($h['medicamentos'] ?? '') ?>">
                                        <?= htmlspecialchars($h['medicamentos'] ?? '—') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>