<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Mi Perfil</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/syles_medico.css">
<body class="bg-light">

<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>
    <main class="main-content flex-grow-1 p-4">
        <nav class="topbar navbar px-4 py-3 bg-white shadow-sm mb-4">
            <div class="container-fluid">
                <button class="sidebar-toggle me-3 d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold">Mi Perfil</h5>
            </div>
        </nav>

        <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show mx-0" role="alert">
            <i class="bi bi-check-circle me-2"></i> <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center p-4">
                    <img src="<?= htmlspecialchars($fotoActual) ?>" alt="Doctor"
                         class="rounded-circle shadow-sm mx-auto mb-3" width="120"
                         onerror="this.src='https://cdn-icons-png.flaticon.com/512/3774/3774299.png'">
                    <h5 class="fw-bold"><?= htmlspecialchars($datos['nombre'] . ' ' . $datos['apellido']) ?></h5>
                    <p class="text-muted mb-1"><?= htmlspecialchars($datos['especialidad'] ?? 'Sin especialidad') ?></p>
                    <p class="text-muted mb-0"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($datos['horario'] ?? 'No asignado') ?></p>
                    <span class="badge mt-3 <?= $datos['estado'] == 1 ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $datos['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>


            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4">
                    <h6 class="fw-bold mb-4 border-bottom pb-2">Información del Médico</h6>
                    <form method="POST" action="index.php?controller=medico&action=perfil">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Nombre</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($datos['nombre']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Apellido</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($datos['apellido']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">CI</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($datos['ci']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Matrícula</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($datos['matricula'] ?? '—') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Correo</label>
                                <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($datos['correo']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Especialidad</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($datos['especialidad'] ?? '—') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Teléfono <span class="text-primary">(editable)</span></label>
                                <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Dirección <span class="text-primary">(editable)</span></label>
                                <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="alert alert-info border-0 mb-3" style="font-size:14px;">
                            <i class="bi bi-info-circle me-2"></i>
                            Para modificar nombre, especialidad u otros datos, contacta al administrador.
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>
