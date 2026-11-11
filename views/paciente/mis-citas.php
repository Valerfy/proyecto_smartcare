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

            <?php
            $alertas = [
                'cita_agendada'      => ['success', 'Tu cita fue agendada. Tienes 3 minutos para cancelarla si lo necesitas.'],
                'cita_ok'            => ['success', 'La cita fue cancelada correctamente.'],
                'cita_expirada'      => ['warning', 'No se pudo cancelar: ya pasó el tiempo permitido (3 minutos).'],
                'cita_no_cancelable' => ['danger',  'Esta cita no se puede cancelar.'],
                'cita_error'         => ['danger',  'Ocurrió un error al cancelar la cita. Intenta nuevamente.'],
            ];
            $msgKey = $_GET['msg'] ?? '';
            if (isset($alertas[$msgKey])): ?>
                <div class="alert alert-<?= $alertas[$msgKey][0] ?> alert-dismissible fade show" role="alert">
                    <?= $alertas[$msgKey][1] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Especialidad</th>
                            <th>Médico</th>
                            <th>Estado</th>
                            <th>Cancelar</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($todas_citas)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
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
                            <td>
                                <?php
                                $cancelable = in_array($c['estado'], ['pendiente', 'confirmada'], true);
                                $segundos   = (int)$c['segundos_restantes'];
                                ?>
                                <?php if ($cancelable && $segundos > 0): ?>
                                    <form method="POST" action="index.php?controller=paciente&action=cancelarCita"
                                          class="d-flex align-items-center gap-2 form-cancelar"
                                          data-segundos="<?= $segundos ?>">
                                        <input type="hidden" name="cita_id" value="<?= (int)$c['cita_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('¿Seguro que deseas cancelar esta cita?');">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </button>
                                        <span class="badge bg-light text-danger border contador" title="Tiempo restante para cancelar">
                                            <i class="bi bi-clock"></i> <span class="contador-tiempo">--:--</span>
                                        </span>
                                    </form>
                                <?php elseif ($cancelable): ?>
                                    <small class="text-muted"><i class="bi bi-lock"></i> Tiempo agotado</small>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
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

    (function () {
        var forms = document.querySelectorAll('.form-cancelar');
        if (!forms.length) return;

        var fin = Date.now();
        var items = [];
        forms.forEach(function (f) {
            items.push({
                form: f,
                label: f.querySelector('.contador-tiempo'),
                finaliza: fin + parseInt(f.dataset.segundos, 10) * 1000
            });
        });

        function formato(s) {
            var m = Math.floor(s / 60), r = s % 60;
            return (m < 10 ? '0' : '') + m + ':' + (r < 10 ? '0' : '') + r;
        }

        function tick() {
            var ahora = Date.now(), activos = 0;
            items.forEach(function (it) {
                if (!it.form.isConnected || it.vencido) return;
                var restante = Math.ceil((it.finaliza - ahora) / 1000);
                if (restante <= 0) {
                    it.vencido = true;
                    it.form.outerHTML = '<small class="text-muted"><i class="bi bi-lock"></i> Tiempo agotado</small>';
                } else {
                    it.label.textContent = formato(restante);
                    activos++;
                }
            });
            if (activos === 0) clearInterval(timer);
        }

        tick();
        var timer = setInterval(tick, 1000);
    })();
</script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>