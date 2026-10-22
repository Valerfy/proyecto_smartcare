<?php
$horarios = ['08:00:00','08:30:00','09:00:00','09:30:00','10:00:00','10:30:00','11:00:00','11:30:00',
             '14:00:00','14:30:00','15:00:00','15:30:00','16:00:00','16:30:00','17:00:00'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción - Helper</title>
    <link rel="stylesheet" href="../assets/css/styles_helper.css">
</head>
<body>

<div class="header">
    <h1>Recepción</h1>
    <div>Bienvenido, <strong><?= htmlspecialchars($userData['nombre'] ?? 'Helper') ?></strong></div>
    <a href="index.php?controller=auth&action=logout" style="color:var(--danger); text-decoration:none; font-weight:500;">Cerrar Sesión</a>
</div>

<div class="container" style="max-width:1100px; margin:0 auto; padding:0 20px;">

    <?php if (!empty($mensaje)): ?>
        <div class="alert <?= strpos($mensaje, 'correctamente') !== false ? 'alert-success' : (strpos($mensaje, 'ocupado') !== false ? 'alert-warning' : 'alert-danger') ?>">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'actualizado'): ?>
        <div class="alert alert-success">Cita actualizada correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'ocupado'): ?>
        <div class="alert alert-warning">El médico ya tiene una cita en ese horario.</div>
    <?php endif; ?>

   <div class="tabs" style="margin-bottom:35px;">
    <a href="index.php?controller=helper&action=index&tab=agenda"
       class="tab <?= ($tab === 'agenda' || $tab === '') ? 'active' : '' ?>">
       Agenda
    </a>

    <a href="index.php?controller=helper&action=index&tab=agendar"
       class="tab <?= $tab === 'agendar' ? 'active' : '' ?>">
       Nueva Cita
    </a>

    <a href="index.php?controller=helper&action=index&tab=fichas"
       class="tab <?= $tab === 'fichas' ? 'active' : '' ?>">
       Fichas
    </a>

    <a href="index.php?controller=helper&action=index&tab=reportes"
       class="tab <?= $tab === 'reportes' ? 'active' : '' ?>">
       Reportes
    </a>
</div>

    <?php if ($tab === 'agendar'): ?>
    <div class="card">
        <h2>Agendar Nueva Cita</h2>
        <form method="POST" action="index.php?controller=helper&action=index&tab=agendar" class="form-grid">
            <select name="paciente">
                <option value="">Seleccionar paciente registrado</option>
                <?php foreach ($pacientes as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?>
                        <?= $p['ci'] ? '(CI: ' . $p['ci'] . ')' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div style="grid-column: 1 / -1; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <p><strong>O crear paciente nuevo:</strong></p>
                <input type="text" name="nombre_nuevo"   placeholder="Nombre">
                <input type="text" name="apellido_nuevo" placeholder="Apellido">
                <input type="text" name="ci_nuevo"       placeholder="Carnet de Identidad (CI)">
            </div>
            <select name="medico" required>
                <option value="">Seleccionar Médico</option>
                <?php foreach ($medicos as $med): ?>
                    <option value="<?= $med['medico_id'] ?>">
                        <?= htmlspecialchars($med['nombre'] . ' ' . $med['apellido']) ?>
                    </option>
                    
                <?php endforeach; ?>
            </select>
            <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
            <select name="hora" required>
                <option value="">Seleccionar hora</option>
                <?php foreach ($horarios as $h): ?>
                    <option value="<?= $h ?>"><?= substr($h, 0, 5) ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="motivo" rows="3" placeholder="Motivo de la consulta (opcional)"></textarea>
            <button type="submit" name="agendar" class="btn-success">Agendar Cita</button>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($cita_editar): ?>
    <div class="card">
        <h2>Editar Cita</h2>
        <p style="margin-bottom:20px;"><strong>Paciente:</strong>
            <?= htmlspecialchars($cita_editar['nombre'] . ' ' . $cita_editar['apellido']) ?>
        </p>
        <form method="POST" action="index.php?controller=helper&action=editarCita" class="form-grid">
            <input type="hidden" name="cita_id" value="<?= $cita_editar['id'] ?>">
            <select name="medico" required>
                <option value="">Seleccionar Médico</option>
                <?php foreach ($medicos as $med): ?>
                    <option value="<?= $med['medico_id'] ?>" <?= ($med['medico_id'] == $cita_editar['medico_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($med['nombre'] . ' ' . $med['apellido']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="fecha" value="<?= htmlspecialchars($cita_editar['fecha']) ?>" required>
            <select name="hora" required>
                <?php foreach ($horarios as $h): ?>
                    <option value="<?= $h ?>" <?= ($h == $cita_editar['hora']) ? 'selected' : '' ?>>
                        <?= substr($h, 0, 5) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <textarea name="motivo" rows="3"><?= htmlspecialchars($cita_editar['motivo'] ?? '') ?></textarea>
            <div style="grid-column: 1 / -1; display: flex; gap: 15px;">
                <button type="submit" class="btn-success">Guardar Cambios</button>
                <a href="index.php?controller=helper&action=index&tab=agenda&fecha_filtro=<?= htmlspecialchars($fecha_filtro) ?>"
                   style="padding:14px 28px; background:#6c757d; color:white; text-decoration:none; border-radius:8px;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($tab === 'agenda' || $tab === ''): ?>
    <div class="card">
        <h2>Agenda — <?= date('d/m/Y', strtotime($fecha_filtro)) ?></h2>
        <form method="GET" action="index.php" style="margin-bottom:25px; display:flex; gap:10px; align-items:center;">
            <input type="hidden" name="controller" value="helper">
            <input type="hidden" name="action"     value="index">
            <input type="hidden" name="tab"        value="agenda">
            <input type="date" name="fecha_filtro" value="<?= htmlspecialchars($fecha_filtro) ?>" style="width:auto;">
            <button type="submit" class="btn-primary" style="padding:10px 20px;">Ver</button>
            <a href="index.php?controller=helper&action=index&tab=agenda&fecha_filtro=<?= date('Y-m-d') ?>"
               class="btn-primary" style="padding:10px 20px; text-decoration:none;">Hoy</a>
        </form>
        <?php if (empty($listaCitas)): ?>
         <p>No hay citas programadas para esta fecha.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Hora</th><th>Paciente</th><th>CI</th><th>Médico</th>
                    <th>Motivo</th><th>Estado</th><th>Acciones</th>
                </tr>
                <?php foreach ($listaCitas as $c): ?>
                <tr>
                    <td><strong><?= substr($c['hora'], 0, 5) ?></strong></td>
                    <td><?= htmlspecialchars($c['paciente_nombre'] . ' ' . $c['paciente_apellido']) ?></td>
                    <td><?= htmlspecialchars($c['ci'] ?? '-') ?></td>
                    <td><?= htmlspecialchars(trim(($c['medico_nombre'] ?? '') . ' ' . ($c['medico_apellido'] ?? ''))) ?: 'Sin nombre' ?></td>

                    <td><?= htmlspecialchars($c['motivo'] ?: '-') ?></td>
                    <td><span class="estado <?= htmlspecialchars($c['estado']) ?>"><?= ucfirst($c['estado']) ?></span></td>
                    <td>
                        <select onchange="cambiarEstado(<?= $c['id'] ?>, this.value, '<?= htmlspecialchars($fecha_filtro) ?>')"
                                style="padding:6px; border-radius:6px; width:auto;">
                            <option value="pendiente"  <?= $c['estado']=='pendiente'  ? 'selected' : '' ?>>Pendiente</option>
                            <option value="confirmada" <?= $c['estado']=='confirmada' ? 'selected' : '' ?>>Confirmada</option>
                            <option value="completada" <?= $c['estado']=='completada' ? 'selected' : '' ?>>Completada</option>
                            <option value="cancelada"  <?= $c['estado']=='cancelada'  ? 'selected' : '' ?>>Cancelada</option>
                        </select>
                        <a href="index.php?controller=helper&action=index&editar=<?= $c['id'] ?>&tab=agenda&fecha_filtro=<?= htmlspecialchars($fecha_filtro) ?>"
                           class="btn-warning" style="padding:6px 12px; font-size:13px; text-decoration:none;">Editar</a>
                        <a href="index.php?controller=helper&action=ficha&id=<?= $c['id'] ?>"
                           target="_blank" class="btn-primary" style="padding:6px 12px; font-size:13px; text-decoration:none;">Ficha</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($tab === 'fichas'): ?>
    <div class="card">
        <h2>Fichas de Atención</h2>
        <?php if (empty($listaCitas)): ?>
            <p>No hay citas disponibles hoy.</p>
        <?php else: ?>
            <table>
                <tr><th>Hora</th><th>Paciente</th><th>Médico</th><th>Estado</th><th>Acción</th></tr>
                <?php foreach ($listaCitas as $c): ?>
                <tr>
                    <td><?= substr($c['hora'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($c['paciente_nombre'] . ' ' . $c['paciente_apellido']) ?></td>
                    <td><?= htmlspecialchars(trim(($c['medico_nombre'] ?? '') . ' ' . ($c['medico_apellido'] ?? ''))) ?: 'Sin nombre' ?></td>
                    <td><span class="estado <?= htmlspecialchars($c['estado']) ?>"><?= ucfirst($c['estado']) ?></span></td>
                    <td>
                        <a href="index.php?controller=helper&action=ficha&id=<?= $c['id'] ?>"
                           target="_blank" class="btn-primary" style="padding:6px 12px; font-size:13px; text-decoration:none;">
                            Generar Ficha
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($tab === 'reportes'): ?>
    <div class="card">
        <h2>Reporte de Fichas Emitidas</h2>

        <a href="../views/helper/reporte_pdf.php"
   target="_blank"
   class="btn btn-danger mb-3">

   Generar Reporte PDF

</a>
        <?php if (empty($reporteFichas)): ?>
            <p>No existen fichas generadas.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                     <th>CI</th>
                    <th>Fecha Cita</th>
                    <th>Hora</th>
                    <th>Fecha Emisión</th>
                   <th>Tipo</th>
</tr>

<?php foreach ($reporteFichas as $r): ?>

<tr>

    <td><?= $r['id'] ?></td>

    <td>
        <?= htmlspecialchars(
            $r['paciente_nombre'] . ' ' .
            $r['paciente_apellido']
        ) ?>
    </td>

    <td>
        <?= htmlspecialchars(
            $r['medico_nombre'] . ' ' .
            $r['medico_apellido']
        ) ?>
    </td>

    <td>
        <?= htmlspecialchars($r['ci']) ?>
    </td>

    <td>
        <?= htmlspecialchars($r['fecha']) ?>
    </td>

    <td>
        <?= substr($r['hora'], 0, 5) ?>
    </td>

    <td>
        <?= htmlspecialchars($r['creado_en']) ?>
    </td>

    <td>
        <?= htmlspecialchars($r['tipo_usuario']) ?>
    </td>

</tr>




                <?php endforeach; ?>

            </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<script>
function cambiarEstado(citaId, nuevoEstado, fecha) {
    if (confirm('¿Cambiar el estado de esta cita a "' + nuevoEstado + '"?')) {
        window.location.href = 'index.php?controller=helper&action=cambiarEstado&id=' + citaId + '&estado=' + nuevoEstado + '&fecha=' + fecha;
    } else {
        location.reload();
    }
}
</script>
</body>
</html>