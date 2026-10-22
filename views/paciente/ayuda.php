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
    <title>SmartCare CNS - Ayuda</title>
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
            <h2 class="mb-4">Ayuda</h2>
            <div class="row">

                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header"><strong>Preguntas Frecuentes (FAQs)</strong></div>
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                        ¿Cómo solicito una cita médica?
                                    </button>
                                </h2>
                                <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Ve a la sección "Solicitar Cita", elige la especialidad, el médico, la fecha y hora, y describe el motivo. Luego confirma.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                        ¿Cómo verifico el estado de mi seguro?
                                    </button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        En "Seguro y Trámites", puedes ver el estado actual, historial de aportes y descargar documentos.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                        ¿Dónde veo mi historial médico?
                                    </button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Accede a "Historial" para consultar atenciones pasadas, diagnósticos y estudios realizados.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                        ¿Cómo cambio mis datos personales?
                                    </button>
                                </h2>
                                <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Ve a "Mi Perfil" y edita tus datos en el formulario. No olvides guardar los cambios.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
                                        ¿Qué hago si olvido mi contraseña?
                                    </button>
                                </h2>
                                <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Usa la opción "Olvidé mi contraseña" en la página de login para restablecerla vía email.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4 card-illustration">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-headset fs-1 text-info mb-3"></i>
                            <h5>Contacto con Soporte</h5>
                            <p class="text-muted">¿No encuentras la respuesta? Contáctanos.</p>
                            <button class="btn btn-primary w-100 mb-2">💬 Chat en Vivo</button>
                            <button class="btn btn-outline-primary w-100 mb-2">✉️ soporte@smartcarecns.com</button>
                            <button class="btn btn-outline-primary w-100">📞 +591 1234-5678</button>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5>Guías Rápidas</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <a href="index.php?controller=paciente&action=solicitarCita" class="text-decoration-none">Cómo agendar una cita</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="index.php?controller=paciente&action=pagos" class="text-decoration-none">Gestión de pagos</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="index.php?controller=paciente&action=historial" class="text-decoration-none">Acceso a historial</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="index.php?controller=paciente&action=perfil" class="text-decoration-none">Actualizar perfil</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php require_once ROOT . '/views/components/chatbot.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>

</html>