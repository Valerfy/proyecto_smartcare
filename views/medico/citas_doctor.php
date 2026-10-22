<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SmartCare CNS - Mis Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/syles_medico.css">
</head>
<body>

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content flex-grow-1">
        <nav class="topbar navbar px-4 py-3 bg-white shadow-sm mb-4">
            <button class="sidebar-toggle me-3 d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
                <i class="bi bi-list"></i>
            </button>
            <div class="container-fluid">
                <h5 class="fw-bold mb-0">Control de Citas</h5>
            </div>
        </nav>

        <div class="p-4">
            <div class="mb-4">
                <h4 class="fw-bold text-dark">Citas Programadas</h4>
                <p class="text-muted small">Listado completo de atenciones y estados.</p>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Fecha / Hora</th>
                                <th>Paciente</th>
                                <th>Motivo</th>
                                <th>Diagnóstico</th>
                                <th>Observación</th>
                                <th>Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($citas as $c):
                                $atendido = !empty($c['diagnostico']);
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= date("d/m/Y", strtotime($c['fecha'])) ?></div>
                                    <small class="text-info fw-medium"><?= $c['hora'] ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold small"><?= htmlspecialchars($c['nombre'].' '.$c['apellido']) ?></div>
                                    <small class="text-muted">CI: <?= $c['ci'] ?></small>
                                </td>
                                <td><span class="small text-muted"><?= htmlspecialchars($c['motivo'] ?: '-') ?></span></td>
                                <td><span class="small text-muted"><?= htmlspecialchars($c['diagnostico'] ?: '-') ?></span></td>
                                <td><span class="small text-muted"><?= htmlspecialchars($c['observacion'] ?: '-') ?></span></td>
                                <td>
                                    <?php if($atendido): ?>
                                        <span class="badge bg-success-subtle text-success border border-success px-3">Atendido</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning px-3">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(!$atendido): ?>
                                        <a href="index.php?controller=medico&action=atenderCita&id=<?= $c['id'] ?>" class="btn btn-atender shadow-sm">
                                            Atender
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-light btn-sm text-muted border fw-bold" style="font-size:0.8rem;" disabled>FINALIZADO</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($citas)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No hay citas registradas.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>