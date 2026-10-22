<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Historial Clínico</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/syles_medico.css">

</head>
<body>
<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1">
        <nav class="topbar navbar px-4 py-3 bg-white shadow-sm mb-4">
            <button class="sidebar-toggle me-3 d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
                <i class="bi bi-list"></i>
            </button>
            <div class="container-fluid">
                <h5 class="mb-0 fw-bold">Historial Clínico</h5>
                <div class="ms-auto">
                   <form class="position-relative d-none d-md-block" method="GET" action="index.php">
                       <input type="hidden" name="controller" value="medico">
                        <input type="hidden" name="action" value="historial">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="buscar"
                               class="form-control ps-5 border-0 bg-light rounded-pill"
                               style="width:300px;"
                               placeholder="Buscar por nombre o CI..."
                               value="<?= htmlspecialchars($busqueda) ?>">
                    </form>
                </div>
            </div>
        </nav>

        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="text-muted mb-0">
                    <?= count($historiales ?? []) ?> registro(s)
                    <?= $busqueda ? 'para "' . htmlspecialchars($busqueda) . '"' : 'encontrado(s)' ?>
                </p>
                <?php if ($busqueda): ?>
                    <a href="index.php?controller=medico&action=historial" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x"></i> Limpiar búsqueda
                    </a>
                <?php endif; ?>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                    
                    <thead>
<tr class="bg-light">
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
        <div class="fw-bold"><?= date("d/m/Y", strtotime($h['fecha'])) ?></div>
        <small class="text-muted"><?= date("H:i", strtotime($h['hora'])) ?></small>
    </td>

    <td>
        <div class="fw-bold">
            <?= htmlspecialchars($h['nombre'] . ' ' . $h['apellido']) ?>
        </div>
    </td>

    <td class="text-muted">
        <?= htmlspecialchars($h['ci']) ?>
    </td>

    <td>
        <?= htmlspecialchars($h['motivo'] ?? '—') ?>
    </td>

    <td>
        <span class="text-truncate d-inline-block"
              style="max-width:200px;"
              title="<?= htmlspecialchars($h['diagnostico']) ?>">
            <?= htmlspecialchars($h['diagnostico']) ?>
        </span>
    </td>

    <td class="text-center">
        <?php if (!empty($h['tipo_sangre'])): ?>
            <span class="badge bg-danger">
                <?= htmlspecialchars($h['tipo_sangre']) ?>
            </span>
        <?php else: ?>
            <span class="text-muted">—</span>
        <?php endif; ?>
    </td>

    <td>
        <?= htmlspecialchars($h['presion_arterial'] ?? '—') ?>
    </td>

    <td>
        <?php
        $datos = [];

        if (!empty($h['peso'])) {
            $datos[] = $h['peso'] . ' kg';
        }

        if (!empty($h['talla'])) {
            $datos[] = $h['talla'] . ' m';
        }

        echo !empty($datos)
            ? implode(' / ', $datos)
            : '—';
        ?>
    </td>

    <td>
        <?= !empty($h['medicamentos'])
            ? htmlspecialchars($h['medicamentos'])
            : '—'; ?>
    </td>

</tr>
<?php endforeach; ?>
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
