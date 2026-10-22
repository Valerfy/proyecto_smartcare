<?php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Seguro </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="d-flex">

    <?php include __DIR__ . '/includes/sidebar.php'; ?>

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
            <h2 class="mb-4">Seguro</h2>
            <div class="row">

                <div class="col-lg-7">

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong><i class="bi bi-person-vcard"></i> Datos del Asegurado</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Tipo de sangre:</strong> <?= htmlspecialchars($usuario['tipo_sangre'] ?? 'No registrado') ?></p>
                                    <p><strong>Ocupación:</strong> <?= htmlspecialchars($usuario['ocupacion'] ?? 'No registrada') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>CI:</strong> <?= htmlspecialchars($usuario['ci'] ?? 'No registrado') ?></p>
                                    <p><strong>Estado:</strong>
                                        <?php if ((int)($usuario['estado'] ?? 0) === 1): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($seguro): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header <?= (($seguro['estado'] ?? '') === 'activo') ? 'bg-success' : 'bg-secondary' ?> text-white">
                            <strong><i class="bi bi-shield-check"></i> Datos del Seguro</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nro. Asegurado:</strong> <?= htmlspecialchars($seguro['num_asegurado'] ?? '-') ?></p>
                                    <p><strong>Empresa:</strong> <?= htmlspecialchars($seguro['empresa'] ?? '-') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Estado del seguro:</strong>
                                        <span class="badge <?= (($seguro['estado'] ?? '') === 'activo') ? 'bg-success' : 'bg-danger' ?>">
                                            <?= htmlspecialchars(ucfirst($seguro['estado'] ?? 'Desconocido')) ?>
                                        </span>
                                    </p>
                                    <p><strong>Vigencia:</strong> <?= htmlspecialchars($seguro['vigencia'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-warning">No tienes un seguro registrado.</div>
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
