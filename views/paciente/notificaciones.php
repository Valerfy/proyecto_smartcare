<?php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}
$iconos = [
    'info'        => 'text-primary',
    'exito'       => 'text-success',
    'advertencia' => 'text-warning',
    'peligro'     => 'text-danger',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Notificaciones</title>
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
                    <img src="<?= $fotoPerfil ?>" class="rounded-circle" width="40"
                         alt="Foto de perfil"
                         onerror="this.src='https://i.pravatar.cc/48?u=<?= $_SESSION['usuario_id'] ?>';">
                    <div>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?></span>
                        <small class="d-block text-muted"><?= htmlspecialchars($_SESSION['rol']) ?></small>
                    </div>
                </div>
            </div>
        </nav>

        <div class="p-4">
            <h2 class="mb-4">Notificaciones</h2>
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tus Notificaciones</h5>
                    <form method="POST" action="index.php?controller=paciente&action=notificaciones">
                        <button name="marcar_leidas" class="btn btn-sm btn-outline-primary">
                            Marcar todas como leídas
                        </button>
                    </form>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($notifs)): ?>
                        <div class="list-group-item text-center text-muted py-4">Sin notificaciones</div>
                    <?php else: ?>
                        <?php foreach ($notifs as $n):
                            $color = $iconos[$n['tipo']] ?? 'text-secondary'; ?>
                        <div class="list-group-item d-flex align-items-start gap-3 <?= !$n['leida'] ? 'bg-light' : '' ?>">
                            <i class="bi bi-bell-fill fs-4 <?= $color ?>"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?= htmlspecialchars($n['titulo']) ?></h6>
                                <p class="text-muted small mb-1"><?= htmlspecialchars($n['mensaje']) ?></p>
                                <small class="text-muted"><?= date('d M Y H:i', strtotime($n['creado_en'])) ?></small>
                            </div>
                            <?php if (!$n['leida']): ?>
                                <span class="badge bg-primary">Nuevo</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
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