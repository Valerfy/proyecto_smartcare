<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SmartCare CNS - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
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
            <li class="nav-item"><a class="nav-link active" href="#">Iniciar sesión</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?controller=auth&action=register">Registrarse</a></li>
        </ul>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['registro']) && $_GET['registro'] === 'ok'): ?>
            <div class="alert alert-success">Registro exitoso. Ya puedes iniciar sesión.</div>
        <?php endif; ?>
        <form method="POST" action="index.php?controller=auth&action=login">
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="correo" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="clave" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Iniciar Sesión</button>
            <small class="d-block text-center mt-3">
                ¿No tienes cuenta? <a href="index.php?controller=auth&action=register" style="color: #2e1f8d;">
    Regístrate
</a>
            </small>
        </form>
    </div>
    <div class="col-md-6 illustration text-center">
        <img src="../assets/img/Preview.jpg" alt="Ilustración" class="img-fluid">
        <h5 class="mt-3">Gestiona tus citas médicas de forma rápida y sencilla</h5>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
