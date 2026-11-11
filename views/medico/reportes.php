<?php require_once __DIR__ . '/../components/reporte_utils.php'; ?>
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
            <?php include __DIR__ . '/../components/volver.php'; ?>

            <?php
                $periodoAnt      = $periodoAnt      ?? null;
                $estadisticasAnt = $estadisticasAnt ?? null;

                $totalCitas       = rep_total_citas($estadisticas);
                $totalCompletadas = rep_estado($estadisticas, 'completada');
                $totalPendientes  = rep_estado($estadisticas, 'pendiente');
                $totalCanceladas  = rep_estado($estadisticas, 'cancelada');
                $pacUnicos        = (int)$estadisticas['pacientes_unicos'];

                // Valores del periodo anterior (null = no hay comparación)
                $antCompletadas = $estadisticasAnt ? rep_estado($estadisticasAnt, 'completada') : null;
                $antPendientes  = $estadisticasAnt ? rep_estado($estadisticasAnt, 'pendiente')  : null;
                $antCanceladas  = $estadisticasAnt ? rep_estado($estadisticasAnt, 'cancelada')  : null;
                $antPacientes   = $estadisticasAnt ? (int)$estadisticasAnt['pacientes_unicos']   : null;
            ?>
            <div class="row g-3 mb-2">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3 h-100">
                        <i class="bi bi-check-circle-fill text-success fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0"><?= $totalCompletadas ?></h3>
                        <small class="text-muted">Citas Completadas</small>
                        <div class="small fw-semibold mt-1"><?= rep_pct($totalCompletadas, $totalCitas) ?> del total</div>
                        <div class="mt-1"><?= rep_delta($totalCompletadas, $antCompletadas, true) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3 h-100">
                        <i class="bi bi-clock-fill text-warning fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0"><?= $totalPendientes ?></h3>
                        <small class="text-muted">Citas Pendientes</small>
                        <div class="small fw-semibold mt-1"><?= rep_pct($totalPendientes, $totalCitas) ?> del total</div>
                        <div class="mt-1"><?= rep_delta($totalPendientes, $antPendientes, null) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3 h-100">
                        <i class="bi bi-x-circle-fill text-danger fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0"><?= $totalCanceladas ?></h3>
                        <small class="text-muted">Citas Canceladas</small>
                        <div class="small fw-semibold mt-1"><?= rep_pct($totalCanceladas, $totalCitas) ?> del total</div>
                        <div class="mt-1"><?= rep_delta($totalCanceladas, $antCanceladas, false) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3 h-100">
                        <i class="bi bi-people-fill text-primary fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0"><?= $pacUnicos ?></h3>
                        <small class="text-muted">Pacientes Únicos</small>
                        <div class="small fw-semibold mt-1">&nbsp;</div>
                        <div class="mt-1"><?= rep_delta($pacUnicos, $antPacientes, true) ?></div>
                    </div>
                </div>
            </div>
            <p class="small text-muted mb-4">
                <?php if ($periodoAnt): ?>
                    <i class="bi bi-arrow-left-right me-1"></i>
                    Comparado con el periodo anterior (<?= rep_fecha($periodoAnt[0]) ?> al <?= rep_fecha($periodoAnt[1]) ?>).
                <?php else: ?>
                    <i class="bi bi-info-circle me-1"></i>
                    Indica <strong>Desde</strong> y <strong>Hasta</strong> en el filtro para comparar con el periodo anterior.
                <?php endif; ?>
                Porcentajes sobre el total de citas del periodo (<?= $totalCitas ?>, incluye confirmadas).
            </p>

            <?php
                // ---- Datos para los gráficos de pastel ----
                $coloresEstado = ['completada'=>'#198754','pendiente'=>'#ffc107','confirmada'=>'#0d6efd','cancelada'=>'#dc3545'];
                $pieEstadoLabels = []; $pieEstadoData = []; $pieEstadoColors = [];
                foreach ($estadisticas['citas_por_estado'] as $e) {
                    $pieEstadoLabels[] = ucfirst($e['estado']);
                    $pieEstadoData[]   = (int)$e['total'];
                    $pieEstadoColors[] = $coloresEstado[$e['estado']] ?? '#6c757d';
                }
                $edadConteo   = rep_edad_rangos($historiales);
                $sangreConteo = rep_sangre_pacientes($historiales);
            ?>
            <div class="row g-3 mb-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill me-2"></i>Citas por estado</h6>
                            <?php if (array_sum($pieEstadoData) === 0): ?>
                                <p class="text-muted text-center py-5 mb-0">Sin datos para graficar.</p>
                            <?php else: ?>
                                <div style="position:relative; height:280px;"><canvas id="pieEstados"></canvas></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Pacientes por rango de edad</h6>
                            <?php if (empty($edadConteo)): ?>
                                <p class="text-muted text-center py-5 mb-0">Sin datos para graficar.</p>
                            <?php else: ?>
                                <div style="position:relative; height:280px;"><canvas id="pieEdad"></canvas></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-droplet-fill me-2"></i>Pacientes por tipo de sangre</h6>
                            <?php if (empty($sangreConteo)): ?>
                                <p class="text-muted text-center py-5 mb-0">Sin datos para graficar.</p>
                            <?php else: ?>
                                <div style="position:relative; height:280px;"><canvas id="pieSangre"></canvas></div>
                            <?php endif; ?>
                        </div>
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
                    <?php
                        $qsPdf = http_build_query(array_filter([
                            'controller'  => 'medico',
                            'action'      => 'exportarReportePDF',
                            'fecha_desde' => $fechaDesde,
                            'fecha_hasta' => $fechaHasta,
                            'paciente_id' => $pacienteId,
                        ], fn($v) => $v !== '' && $v !== null));
                    ?>
                    <a href="index.php?<?= htmlspecialchars($qsPdf) ?>"
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
function crearPastel(id, labels, data, colors) {
    const el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
        type: 'pie',
        data: { labels: labels, datasets: [{ data: data, backgroundColor: colors, borderColor: '#fff', borderWidth: 2 }] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
}
crearPastel('pieEstados', <?= json_encode($pieEstadoLabels) ?>, <?= json_encode($pieEstadoData) ?>, <?= json_encode($pieEstadoColors) ?>);
crearPastel('pieEdad',    <?= json_encode(array_keys($edadConteo)) ?>, <?= json_encode(array_values($edadConteo)) ?>,
    ['#2a9d82','#0d6efd','#ffc107','#fd7e14','#6f42c1','#6c757d']);
crearPastel('pieSangre',  <?= json_encode(array_keys($sangreConteo)) ?>, <?= json_encode(array_values($sangreConteo)) ?>,
    ['#dc3545','#fd7e14','#ffc107','#198754','#0dcaf0','#0d6efd','#6f42c1','#d63384','#6c757d']);
</script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>