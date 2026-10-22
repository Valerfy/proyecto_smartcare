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
    <title>SmartCare CNS - Mi Perfil</title>
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
            <h2 class="mb-4">Mi Perfil</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <div class="row">

                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4 text-center">
                        <div class="card-body">
                            <img src="<?= $fotoPerfil ?>" class="rounded-circle mb-3" width="150"
                                 alt="Foto de perfil"
                                 onerror="this.src='https://i.pravatar.cc/150?u=<?= $_SESSION['usuario_id'] ?>';">
                            <h4><?= htmlspecialchars($userData['nombre'] . ' ' . $userData['apellido']) ?></h4>
                            <p class="text-muted"><?= ucfirst(htmlspecialchars($userData['rol'])) ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header"><strong>Datos Personales</strong></div>
                        <div class="card-body">
                            <form method="POST"
                                  action="index.php?controller=paciente&action=perfil"
                                  enctype="multipart/form-data">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" name="nombre" class="form-control"
                                               value="<?= htmlspecialchars($userData['nombre']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apellido</label>
                                        <input type="text" name="apellido" class="form-control"
                                               value="<?= htmlspecialchars($userData['apellido']) ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control"
                                           value="<?= htmlspecialchars($userData['correo']) ?>" disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" name="telefono" class="form-control"
                                           value="<?= htmlspecialchars($userData['telefono'] ?? '') ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" name="fecha_nac" class="form-control"
                                           value="<?= htmlspecialchars($userData['fecha_nac'] ?? '') ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Dirección</label>
                                    <textarea name="direccion" class="form-control"
                                              rows="2"><?= htmlspecialchars($userData['direccion'] ?? '') ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Foto de perfil</label>
                                    <input type="file" name="foto" class="form-control"
                                           accept="image/jpeg,image/png">
                                </div>

                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>