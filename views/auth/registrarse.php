<?php
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Registrarse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .preview-img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 3px solid #0d6efd; }
    </style>
</head>
<body class="login-body">
    <div class="login-container row g-0">
        <div class="col-md-6 login-form">
            <div class="d-flex align-items-center gap-2 mb-4">
                <span class="logo-atom text-info fs-3">⚛️</span>
                <h4 class="mb-0">SMARTCARE CNS</h4>
            </div>
            <small class="d-block mb-4 text-muted">Sistema inteligente de gestión y optimización citas médicas</small>

            <ul class="nav nav-tabs mb-4">
                <li class="nav-item"><a class="nav-link" href="index.php?controller=auth&action=login">Iniciar sesión</a></li>
                <li class="nav-item"><a class="nav-link active" href="index.php?controller=auth&action=register">Registrarse</a></li>
            </ul>

            <h5 class="mb-4">Crear una cuenta</h5>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?controller=auth&action=register" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Apellido <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($apellido) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">CI (Cédula de Identidad) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                        <input type="text" name="ci" class="form-control" value="<?= htmlspecialchars($ci) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Número de celular <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="tel" name="telefono" class="form-control" placeholder="Ej: 71234567" maxlength="8" value="<?= htmlspecialchars($telefono) ?>" required>
                    </div>
                    <small class="text-muted">8 dígitos (empieza con 6 o 7)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha de nacimiento <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        <input type="date" name="fecha_nac" class="form-control" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($fecha_nac) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" name="direccion" class="form-control" placeholder="Ej: Calle Murillo 123, La Paz" value="<?= htmlspecialchars($direccion) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ocupación <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                        <input type="text" name="ocupacion" class="form-control" placeholder="Ej: Ingeniero, Docente, Estudiante" value="<?= htmlspecialchars($ocupacion) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($correo) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="clave" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="clave2" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Foto de perfil (opcional)</label>
                    <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png">
                    <div id="preview" class="mt-3 text-center d-none">
                        <img id="img-preview" class="preview-img" alt="Vista previa">
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Número de Matrícula <span class="text-danger">*</span></h6>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-list"></i></span>
                            <input type="text" name="matricula" class="form-control" placeholder="Número de matrícula" value="<?= htmlspecialchars($matricula) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">Acepto los Términos y Condiciones</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Registrarse</button>
                <small class="d-block text-center">¿Ya tienes una cuenta? <a href="index.php?controller=auth&action=login" style="color: #0d6efd;">Inicia sesión</a></small>
            </form>
        </div>

        <div class="col-md-6 illustration text-center">
            <img src="../assets/img/Preview.jpg" alt="Ilustración" class="img-fluid">
            <h5 class="mt-3">Gestiona tus citas médicas de forma rápida y sencilla</h5>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('input[name="foto"]').addEventListener('change', function(e) {
            const preview = document.getElementById('preview');
            const img = document.getElementById('img-preview');
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    img.src = ev.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
</body>
</html>