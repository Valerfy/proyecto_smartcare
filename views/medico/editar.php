<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Paciente - SmartCare CNS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/syles_medico.css">
</head>
<body class="bg-light">
<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content flex-grow-1 p-4">
        <div class="d-flex align-items-center gap-3 mb-4">
            <button class="sidebar-toggle d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
                <i class="bi bi-list"></i>
            </button>
            <a href="index.php?controller=medico&action=misPacientes" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0 fw-bold">Editar Paciente</h4>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 p-4" style="max-width:700px;">
            <form method="POST" action="index.php?controller=medico&action=editarPaciente&id=<?= $paciente['id'] ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                               value="<?= htmlspecialchars($paciente['nombre']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control"
                               value="<?= htmlspecialchars($paciente['apellido']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CI</label>
                        <input type="text" name="ci" class="form-control"
                               value="<?= htmlspecialchars($paciente['ci'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nac" class="form-control"
                               value="<?= htmlspecialchars($paciente['fecha_nac'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="<?= htmlspecialchars($paciente['telefono'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                               value="<?= htmlspecialchars($paciente['direccion'] ?? '') ?>">
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Actualizar Paciente
                    </button>
                    <a href="index.php?controller=medico&action=misPacientes" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>