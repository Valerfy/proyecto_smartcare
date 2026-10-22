<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Pagos</title>
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
                    <img src="<?= htmlspecialchars($fotoPerfil) ?>"
                         class="rounded-circle" width="40" alt="Foto de perfil"
                         onerror="this.src='https://i.pravatar.cc/48?u=<?= $usuario['id'] ?>';">
                    <div>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></span>
                        <small class="d-block text-muted"><?= htmlspecialchars($usuario['rol']) ?></small>
                    </div>
                </div>
            </div>
        </nav>

        <div class="p-4">
            <h2 class="mb-4">Pagos</h2>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header"><h5 class="mb-0">Historial de Pagos</h5></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr><th>Fecha</th><th>Concepto</th><th>Monto</th><th>Estado</th></tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($pagos)): ?>
                                        <tr><td colspan="4" class="text-center text-muted">Sin pagos registrados</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($pagos as $p):
                                            $badge = match($p['estado']) {
                                                'pagado'  => 'bg-success',
                                                'vencido' => 'bg-danger',
                                                default   => 'bg-warning text-dark'
                                            }; ?>
                                        <tr>
                                            <td><?= date('d M Y', strtotime($p['fecha'])) ?></td>
                                            <td><?= htmlspecialchars($p['concepto']) ?></td>
                                            <td>Bs. <?= number_format($p['monto'], 2) ?></td>
                                            <td><span class="badge <?= $badge ?>"><?= ucfirst($p['estado']) ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body text-center">
                            <i class="bi bi-wallet2 fs-1 text-primary mb-3"></i>
                            <h5>Saldo Pendiente Total</h5>
                            <h3 class="text-danger fw-bold">Bs. <?= number_format($saldo, 2) ?></h3>
                            <small class="text-muted">Suma de pagos pendientes y vencidos</small>
                            <button class="btn btn-primary w-100 mt-2 rounded-pill shadow"
                                    data-bs-toggle="modal" data-bs-target="#modalQR">
                                <i class="bi bi-qr-code"></i> Pagar Ahora
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalQR" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100">Pago con QR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Escanea el QR con tu app bancaria</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=SMARTCARE|USER:<?= $usuario['id'] ?>|MONTO:<?= $saldo ?>"
                     alt="QR de pago" class="img-fluid mb-3 border rounded p-2">
                <h5 class="text-danger">Bs. <?= number_format($saldo, 2) ?></h5>
                <small class="text-muted">Monto a pagar</small>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center">
                <button class="btn btn-success px-4" onclick="simularPago()">Ya pagué</button>
                <button class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function simularPago() {
    alert('Pago registrado correctamente.');
    location.reload();
}
</script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>