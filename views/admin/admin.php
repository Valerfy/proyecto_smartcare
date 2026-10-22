<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - SMARTCARE CNS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles_adm.css">
</head>
<body>

<div class="d-flex">
    <div class="sidebar p-4">
        <div class="d-flex align-items-center gap-3 mb-5">
            <span class="fs-2">⚛️</span>
            <h4 class="mb-0 fw-bold">SMARTCARE CNS</h4>
        </div>
        <div class="text-center mb-5">
            <img src="<?= htmlspecialchars($fotoActual) ?>"
                 class="rounded-circle border border-light shadow-sm mb-3" width="90">
            <h6 class="mb-1"><?= htmlspecialchars($userData['nombre'] . ' ' . $userData['apellido']) ?></h6>
            <small class="text-light opacity-75">Administrador</small>
        </div>
        <a href="#" class="active"><i class="bi bi-people-fill"></i> Usuarios</a>
        <a href="index.php?controller=auth&action=logout" class="mt-auto text-danger">
            <i class="bi bi-box-arrow-left"></i> Cerrar sesión
        </a>
    </div>

    <div class="main-content flex-grow-1 p-4">

        <button class="sidebar-toggle mb-3 d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
            <i class="bi bi-list"></i>
        </button>

        <?php if ($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <h2 class="fw-bold text-white mb-4">Gestión de Usuarios</h2>

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-person-plus me-2"></i>
                <?= $editarUsuario ? 'Actualizar Usuario' : 'Crear Nuevo Usuario' ?>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="index.php?controller=admin&action=index<?= $editarUsuario ? '&editar=' . $editarUsuario['id'] : '' ?>"
                      class="row g-3">
                    <?php if ($editarUsuario): ?>
                        <input type="hidden" name="id" value="<?= $editarUsuario['id'] ?>">
                    <?php endif; ?>

                    <div class="col-md-3">
                        <input type="text" name="nombre" class="form-control"
                               value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['nombre']) : '' ?>"
                               placeholder="Nombre" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="apellido" class="form-control"
                               value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['apellido']) : '' ?>"
                               placeholder="Apellido" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="ci" class="form-control"
                               value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['ci'] ?? '') : '' ?>"
                               placeholder="CI" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="matricula" class="form-control"
                               value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['matricula'] ?? '') : '' ?>"
                               placeholder="Matrícula / Nº asegurado">
                    </div>

                    <div class="col-md-6" id="campo-especialidad" style="display: none;">
                        <select name="especialidad" class="form-control">
                            <option value="">Seleccione especialidad</option>
                            <?php foreach ($especialidades as $e): ?>
                                <option value="<?= $e['id'] ?>"
                                    <?= (isset($editarMedico['especialidad_id']) && $editarMedico['especialidad_id'] == $e['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <input type="email" name="correo" id="correo" class="form-control"
                               value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['correo']) : '' ?>"
                               placeholder="Correo electrónico" required>
                    </div>
                    <div class="col-md-3">
                        <input type="password" name="clave" class="form-control"
                               placeholder="<?= $editarUsuario ? 'Nueva contraseña (opcional)' : 'Contraseña' ?>"
                               autocomplete="new-password"
                               <?= $editarUsuario ? '' : 'required' ?>>
                    </div>
                    <div class="col-md-3">
                        <select name="rol" id="rol" class="form-select" required>
                            <option value="paciente"      <?= ($editarUsuario && $editarUsuario['rol'] == 'paciente')      ? 'selected' : '' ?>>Paciente</option>
                            <option value="medico"        <?= ($editarUsuario && $editarUsuario['rol'] == 'medico')        ? 'selected' : '' ?>>Médico</option>
                            <option value="recepcionista" <?= ($editarUsuario && $editarUsuario['rol'] == 'recepcionista') ? 'selected' : '' ?>>Recepcionista</option>
                            <option value="admin"         <?= ($editarUsuario && $editarUsuario['rol'] == 'admin')         ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-<?= $editarUsuario ? 'save' : 'plus-circle' ?>"></i>
                            <?= $editarUsuario ? 'Actualizar Usuario' : 'Crear Usuario' ?>
                        </button>
                        <?php if ($editarUsuario): ?>
                            <a href="index.php?controller=admin&action=index" class="btn btn-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body pt-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-white">Lista de Usuarios</h5>
                    <input type="text" id="buscador" class="form-control search-input"
                           placeholder="Buscar por nombre, correo o CI...">
                </div>
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item"><button class="nav-link active"  data-bs-toggle="tab" data-bs-target="#todos">Todos</button></li>
                    <li class="nav-item"><button class="nav-link"         data-bs-toggle="tab" data-bs-target="#pacientes">Pacientes</button></li>
                    <li class="nav-item"><button class="nav-link"         data-bs-toggle="tab" data-bs-target="#medicos">Médicos</button></li>
                    <li class="nav-item"><button class="nav-link"         data-bs-toggle="tab" data-bs-target="#recepcionistas">Recepcionistas</button></li>
                    <li class="nav-item"><button class="nav-link"         data-bs-toggle="tab" data-bs-target="#admins">Administradores</button></li>
                </ul>
                <div class="tab-content">
                    <?= tabUsuarios($usuarios, null,             'todos',           true) ?>
                    <?= tabUsuarios($usuarios, 'paciente',       'pacientes') ?>
                    <?= tabUsuarios($usuarios, 'medico',         'medicos') ?>
                    <?= tabUsuarios($usuarios, 'recepcionista',  'recepcionistas') ?>
                    <?= tabUsuarios($usuarios, 'admin',          'admins') ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
function tabUsuarios(array $usuarios, ?string $rol, string $id, bool $activo = false): string {
    $active = $activo ? 'show active' : '';
    ob_start(); ?>
    <div class="tab-pane fade <?= $active ?>" id="<?= $id ?>">
        <div class="table-responsive">
            <table class="table table-striped tabla align-middle">
                <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>CI</th>
                        <th>Matrícula</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <?php if ($rol === 'medico' || $rol === null): ?><th>Especialidad</th><?php endif; ?>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $u):
                    if ($rol && $u['rol'] !== $rol) continue; ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></td>
                        <td><?= htmlspecialchars($u['ci'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($u['matricula'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($u['correo']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($u['rol'])) ?></span></td>
                        <?php if ($u['rol'] === 'medico' || $rol === null): ?>
                            <td><?= htmlspecialchars($u['especialidad_nombre'] ?? '—') ?></td>
                        <?php endif; ?>
                        <td>
                            <?php if ($u['estado'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?controller=admin&action=index&editar=<?= $u['id'] ?>"
                               class="btn btn-sm btn-primary me-1">Editar</a>
                            <a href="index.php?controller=admin&action=index&toggle=<?= $u['id'] ?>"
                               class="btn btn-sm btn-warning">Estado</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php return ob_get_clean();
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('buscador').addEventListener('keyup', function () {
    const valor = this.value.toLowerCase();
    document.querySelectorAll('.tabla tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(valor) ? '' : 'none';
    });
});

const rolSelect     = document.getElementById('rol');
const espDiv        = document.getElementById('campo-especialidad');
const correoInput   = document.getElementById('correo');
const nombreInput   = document.querySelector('input[name="nombre"]');
const apellidoInput = document.querySelector('input[name="apellido"]');
let correoManual    = false;

function toggleMedicoFields() {
    const esMedico = rolSelect.value === 'medico';
    espDiv.style.display = esMedico ? 'block' : 'none';
    espDiv.querySelector('select').required = esMedico;
}

function generarCorreo() {
    if (correoManual) return;
    const dominios = { admin: '@cns.com', medico: '@cns.org.bo', recepcionista: '@cns.rec.bo' };
    const dominio  = dominios[rolSelect.value] ?? '';
    const nombre   = nombreInput.value.trim().toLowerCase();
    const apellido = apellidoInput.value.trim().toLowerCase();
    if (nombre && apellido && dominio) {
        correoInput.value    = `${nombre}.${apellido}${dominio}`;
        correoInput.readOnly = true;
    } else {
        correoInput.readOnly = false;
    }
}

rolSelect.addEventListener('change',  () => { toggleMedicoFields(); generarCorreo(); });
nombreInput.addEventListener('input',  generarCorreo);
apellidoInput.addEventListener('input', generarCorreo);
correoInput.addEventListener('input',  () => { correoManual = true; });

window.addEventListener('load', () => { toggleMedicoFields(); generarCorreo(); });
</script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>