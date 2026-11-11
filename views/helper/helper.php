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
<div style="margin-top:20px;"><?php include __DIR__ . '/../components/volver.php'; ?></div>
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

        <form method="GET" action="index.php" class="form-grid" style="margin-bottom:20px;">
            <input type="hidden" name="controller" value="helper">
            <input type="hidden" name="action" value="index">
            <input type="hidden" name="tab" value="reportes">

            <input type="text" name="q" placeholder="Buscar por nombre, apellido o CI"
                   value="<?= htmlspecialchars($filtros['q']) ?>">

            <select name="medico_id">
                <option value="">Todos los médicos</option>
                <?php foreach ($medicos as $med): ?>
                    <option value="<?= $med['medico_id'] ?>"
                        <?= ($filtros['medico_id'] == $med['medico_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($med['nombre'] . ' ' . $med['apellido']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

             <div>
                <small style="color:#666; display:block; margin-bottom:4px;">Desde</small>
                <input type="date" name="fecha_desde"
                       value="<?= htmlspecialchars($filtros['fecha_desde']) ?>">
            </div>
            <div>
                <small style="color:#666; display:block; margin-bottom:4px;">Hasta</small>
                <input type="date" name="fecha_hasta"
                       value="<?= htmlspecialchars($filtros['fecha_hasta']) ?>">
            </div>

            <select name="tipo">
                <option value="">Nuevos y antiguos</option>
                <option value="Nuevo"   <?= $filtros['tipo'] === 'Nuevo'   ? 'selected' : '' ?>>Solo nuevos</option>
                <option value="Antiguo" <?= $filtros['tipo'] === 'Antiguo' ? 'selected' : '' ?>>Solo antiguos</option>
            </select>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn-success">Filtrar</button>
                <a href="index.php?controller=helper&action=index&tab=reportes"
                   style="padding:10px 16px; text-decoration:none; color:#555;">Limpiar</a>
            </div>
        </form>

        <?php
            // ---- Datos para los gráficos de pastel (respetan los filtros) ----
            $cNuevos = 0; $cAntiguos = 0; $porMedico = [];
            foreach ($reporteFichas as $r) {
                if (($r['tipo_usuario'] ?? '') === 'Nuevo') $cNuevos++; else $cAntiguos++;
                $nm = trim(($r['medico_nombre'] ?? '') . ' ' . ($r['medico_apellido'] ?? '')) ?: 'Sin nombre';
                $porMedico[$nm] = ($porMedico[$nm] ?? 0) + 1;
            }
            arsort($porMedico);
        ?>
        <?php if (!empty($reporteFichas)): ?>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:20px; margin-bottom:25px;">
            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:15px;">
                <h3 style="font-size:16px; margin-bottom:10px;">Pacientes nuevos vs. antiguos</h3>
                <div style="position:relative; height:280px;"><canvas id="pieTipo"></canvas></div>
            </div>
            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:15px;">
                <h3 style="font-size:16px; margin-bottom:10px;">Fichas por médico</h3>
                <div style="position:relative; height:280px;"><canvas id="pieMedicos"></canvas></div>
            </div>
        </div>
        <?php endif; ?>

        <p style="margin-bottom:12px;"><strong><?= count($reporteFichas) ?></strong> ficha(s) encontrada(s)</p>

        <a href="index.php?controller=helper&action=exportarReportePDF&q=<?= urlencode($filtros['q']) ?>&medico_id=<?= (int)$filtros['medico_id'] ?>&fecha_desde=<?= urlencode($filtros['fecha_desde']) ?>&fecha_hasta=<?= urlencode($filtros['fecha_hasta']) ?>&tipo=<?= urlencode($filtros['tipo']) ?>"
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

<?php if ($tab === 'reportes' && !empty($reporteFichas)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
function crearPastel(id, labels, data, colors) {
    const el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
        type: 'pie',
        data: { labels: labels, datasets: [{ data: data, backgroundColor: colors, borderColor: '#fff', borderWidth: 2 }] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: function (ctx) {
                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                    const pct = total ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                    return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                } } }
            }
        }
    });
}
crearPastel('pieTipo', ['Nuevos', 'Antiguos'], [<?= (int)$cNuevos ?>, <?= (int)$cAntiguos ?>], ['#198754', '#0d6efd']);
crearPastel('pieMedicos', <?= json_encode(array_keys($porMedico)) ?>, <?= json_encode(array_values($porMedico)) ?>,
    ['#0d6efd','#198754','#ffc107','#dc3545','#6f42c1','#fd7e14','#0dcaf0','#d63384','#6c757d','#20c997']);
</script>
<?php endif; ?>
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