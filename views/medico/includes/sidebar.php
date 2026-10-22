<?php
$nombre        = $_SESSION['nombre'] ?? 'Doctor';
$apellido      = $_SESSION['apellido'] ?? '';
$fotoSidebar   = $foto ?? 'https://cdn-icons-png.flaticon.com/512/3774/3774299.png';
$especialidad  = $medico['especialidad'] ?? 'Médico';
$horario       = $medico['horario'] ?? '';
?>

<nav class="sidebar d-flex flex-column" id="sidebar">

    <div class="perfil text-center p-3">

        <div class="foto-container">
            <img src="<?= htmlspecialchars($fotoSidebar) ?>"
                 alt="Foto médico"
                 class="foto-medico">
        </div>

        <h5 class="mt-3 fw-bold text-white">
            Dr(a). <?= htmlspecialchars($nombre . ' ' . $apellido) ?>
        </h5>

        <span class="badge bg-success px-3 py-2 mb-2">
            <?= htmlspecialchars($especialidad) ?>
        </span>

        <?php if (!empty($horario)): ?>
            <p class="small text-light mb-0">
                <i class="bi bi-clock-history"></i>
                <?= htmlspecialchars($horario) ?>
            </p>
        <?php endif; ?>

    </div>

    <ul class="nav flex-column px-2 flex-grow-1">

        <li class="nav-item">
            <a class="nav-link menu-link" href="index.php?controller=medico&action=index">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link menu-link" href="index.php?controller=medico&action=citas">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Mis Citas</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link menu-link" href="index.php?controller=medico&action=misPacientes">
                <i class="bi bi-people-fill"></i>
                <span>Mis Pacientes</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link menu-link" href="index.php?controller=medico&action=historial">
                <i class="bi bi-journal-medical"></i>
                <span>Historial Clínico</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link menu-link" href="index.php?controller=medico&action=reportes">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Reportes</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link menu-link" href="index.php?controller=medico&action=perfil">
                <i class="bi bi-person-circle"></i>
                <span>Mi Perfil</span>
            </a>
        </li>

    </ul>

    <div class="logout-container p-2 mt-auto">
        <a class="nav-link logout-btn"
           href="index.php?controller=auth&action=logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>Cerrar Sesión</span>
        </a>
    </div>

</nav>