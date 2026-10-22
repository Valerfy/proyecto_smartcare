<?php
$currentAction = $_GET['action'] ?? 'index';
$sidebarFoto = $fotoPerfil ?? $foto ?? '';
$sidebarUid = $_SESSION['usuario_id'] ?? 0;
$sidebarNombre = $_SESSION['nombre'] ?? '';
$sidebarApellido = $_SESSION['apellido'] ?? '';
$sidebarRol = $_SESSION['rol'] ?? 'Paciente';
?>
<nav class="sidebar text-white p-3 d-flex flex-column">
    <div class="d-flex align-items-center gap-3 mb-4 px-3">
        <span class="logo-atom text-info">⚛️</span>
        <div>
            <h4 class="mb-0 text-white">SMARTCARE CNS</h4>
            <small class="text-info">Salud inteligente</small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3 mb-4 px-3">
        <img src="<?= $sidebarFoto ?>" class="rounded-circle border border-light" width="50" alt="Foto de perfil" onerror="this.src='<https://i.pravatar.cc/64?u=><?= $sidebarUid ?>';">
        <div>
            <h6 class="mb-0 text-white"><?= htmlspecialchars($sidebarNombre . ' ' . $sidebarApellido) ?></h6>
            <small class="text-light"><?= htmlspecialchars($sidebarRol) ?></small>
        </div>
    </div>
    <hr class="border-light">
    <div class="flex-grow-1">
        <a href="index.php?controller=paciente&action=index"          class="nav-link d-flex align-items-center gap-3 <?= $currentAction === 'index' ? 'active' : '' ?>"><i class="bi bi-house-door fs-5"></i> Inicio</a>
        <a href="index.php?controller=paciente&action=solicitarCita"  class="nav-link d-flex align-items-center gap-3 <?= $currentAction === 'solicitarCita' ? 'active' : '' ?>"><i class="bi bi-calendar-check fs-5"></i> Solicitar Cita</a>
        <a href="index.php?controller=paciente&action=misCitas"       class="nav-link d-flex align-items-center gap-3 <?= $currentAction === 'misCitas' ? 'active' : '' ?>"><i class="bi bi-geo-alt fs-5"></i> Mis Citas</a>
        <a href="index.php?controller=paciente&action=historial"      class="nav-link d-flex align-items-center gap-3 <?= $currentAction === 'historial' ? 'active' : '' ?>"><i class="bi bi-journal-text fs-5"></i> Historial</a>
        <a href="index.php?controller=paciente&action=seguro"         class="nav-link d-flex align-items-center gap-3 <?= $currentAction === 'seguro' ? 'active' : '' ?>"><i class="bi bi-shield-check fs-5"></i> Seguro </a>
        <a href="index.php?controller=paciente&action=perfil"         class="nav-link d-flex align-items-center gap-3 <?= $currentAction === 'perfil' ? 'active' : '' ?>"><i class="bi bi-person fs-5"></i> Mi Perfil</a>
        <a href="index.php?controller=paciente&action=notificaciones" class="nav-link d-flex align-items-center gap-3 <?= $currentAction === 'notificaciones' ? 'active' : '' ?>"><i class="bi bi-bell fs-5"></i> Notificaciones</a>
        <a href="index.php?controller=paciente&action=ayuda"          class="nav-link d-flex align-items-center gap-3 <?= $currentAction === 'ayuda' ? 'active' : '' ?>"><i class="bi bi-question-circle fs-5"></i> Ayuda</a>
    </div>
    <div class="mt-auto px-3">
        <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light w-100 d-flex align-items-center gap-2" onclick="return confirm('¿Cerrar sesión?')"><i class="bi bi-box-arrow-left"></i> Cerrar sesión</a>
    </div>
</nav>