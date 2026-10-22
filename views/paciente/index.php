<?php
$usuario  = $usuario  ?? [];
$foto     = $fotoPerfil ?? '';
$proximas = $proximas ?? [];
$nombre   = $usuario['nombre']   ?? 'Usuario';
$apellido = $usuario['apellido'] ?? '';
$rol      = $usuario['rol']      ?? 'paciente';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="d-flex">

    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1">
        <nav class="topbar navbar navbar-expand-lg px-4 py-3 bg-white shadow-sm">
            <div class="container-fluid">
                <button class="sidebar-toggle d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 text-dark fw-bold">Caja Nacional de Salud, La Paz</h5>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <img src="<?= htmlspecialchars($foto) ?>" class="rounded-circle" width="40" alt="Foto">
                    <div>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($nombre . ' ' . $apellido) ?></span>
                        <small class="d-block text-muted"><?= htmlspecialchars($rol) ?></small>
                    </div>
                </div>
            </div>
        </nav>

        <div class="p-4">
            <h2 class="mb-2">¡Bienvenido, <?= htmlspecialchars($nombre) ?>!</h2>
            <p class="mb-4 text-muted">Gestiona tus citas médicas de forma rápida y sencilla.</p>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm text-center p-3 h-100">
                        <i class="bi bi-plus-circle fs-1 text-primary mb-2"></i>
                        <h6>Solicitar una nueva cita</h6>
                        <a href="index.php?controller=paciente&action=solicitarCita" class="btn btn-primary mt-2">Solicitar</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm text-center p-3 h-100">
                        <i class="bi bi-calendar-event fs-1 text-success mb-2"></i>
                        <h6>Mis próximas citas</h6>
                        <a href="index.php?controller=paciente&action=misCitas" class="btn btn-success mt-2">Ver próximas</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm text-center p-3 h-100">
                        <i class="bi bi-journal-text fs-1 text-info mb-2"></i>
                        <h6>Historial de citas</h6>
                        <a href="index.php?controller=paciente&action=historial" class="btn btn-info mt-2">Ver historial</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm text-center p-3 h-100">
                        <i class="bi bi-person-circle fs-1 text-secondary mb-2"></i>
                        <h6>Editar perfil</h6>
                        <a href="index.php?controller=paciente&action=perfil" class="btn btn-secondary mt-2">Editar</a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Próximas Citas</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($proximas)): ?>
                        <p class="text-center text-muted py-5">Aún no tienes ninguna cita registrada.</p>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr><th>Fecha</th><th>Hora</th><th>Médico</th><th>Especialidad</th><th>Estado</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proximas as $c): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                                    <td><?= date('H:i', strtotime($c['hora'])) ?></td>
                                    <td><?= htmlspecialchars($c['medico'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($c['especialidad'] ?? '-') ?></td>
                                    <td><span class="badge bg-warning text-dark"><?= ucfirst($c['estado']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>