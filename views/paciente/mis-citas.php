<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Mis Citas</title>
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
            <h2 class="mb-4">Mis Citas</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Especialidad</th>
                            <th>Médico</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($todas_citas)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                Aún no tienes ninguna cita registrada.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($todas_citas as $c):
                            $badge = match($c['estado']) {
                                'confirmada', 'completada' => 'bg-success',
                                'pendiente'                => 'bg-warning text-dark',
                                'programada'               => 'bg-primary',
                                'cancelada'                => 'bg-danger',
                                default                    => 'bg-secondary'
                            };
                        ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($c['fecha'])) ?></td>
                            <td><?= date('h:i A', strtotime($c['hora'])) ?></td>
                            <td><?= htmlspecialchars($c['especialidad']) ?></td>
                            <td><?= htmlspecialchars($c['medico']) ?></td>
                            <td>
                                <span class="badge <?= $badge ?> rounded-pill">
                                    <?= ucfirst($c['estado']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($todas_citas)): ?>
            <div class="mt-3 text-muted small">
                Mostrando <?= count($todas_citas) ?> cita(s) en total
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function cerrarSesion() {
        if (confirm('¿Cerrar sesión?')) window.location.href = '../../views/auth/login.php';
    }
</script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>