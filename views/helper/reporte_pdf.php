<?php
header("Content-Type: text/html; charset=UTF-8");

if (!isset($fichas)) {
    http_response_code(403);
    exit('Acceso no permitido.');
}
require_once __DIR__ . '/../components/reporte_utils.php';

$filtros      = $filtros      ?? ['q' => '', 'medico_id' => 0, 'fecha_desde' => '', 'fecha_hasta' => '', 'tipo' => ''];
$medicoNombre = $medicoNombre ?? '';
$periodoAnt   = $periodoAnt   ?? null;
$antTotal     = $antTotal     ?? null;
$antNuevos    = $antNuevos    ?? null;
$antAntiguos  = $antAntiguos  ?? null;

// ---- Resumen ----
$total = count($fichas);
$cNuevos = 0; $porMedico = [];
foreach ($fichas as $f) {
    if (($f['tipo_usuario'] ?? '') === 'Nuevo') $cNuevos++;
    $nm = trim(($f['medico_nombre'] ?? '') . ' ' . ($f['medico_apellido'] ?? '')) ?: 'Sin nombre';
    $porMedico[$nm] = ($porMedico[$nm] ?? 0) + 1;
}
$cAntiguos = $total - $cNuevos;
arsort($porMedico);
$nMedicos = count($porMedico);

// Para el gráfico: máximo 8 porciones (el resto se agrupa en "Otros")
$medGraf = $porMedico;
if (count($medGraf) > 8) {
    $top  = array_slice($medGraf, 0, 7, true);
    $top['Otros'] = array_sum(array_slice($medGraf, 7, null, true));
    $medGraf = $top;
}

// [etiqueta, valor, color, ¿muestra % del total?, valor anterior, ¿subir es bueno?]
$tarjetas = [
    ['Total de fichas',     $total,     '#0b6b57', false, $antTotal,    null],
    ['Pacientes nuevos',    $cNuevos,   '#198754', true,  $antNuevos,   null],
    ['Pacientes antiguos',  $cAntiguos, '#0d6efd', true,  $antAntiguos, null],
    ['Médicos',             $nMedicos,  '#6f42c1', false, null,         null],
];

// ---- Textos de filtros ----
$desde = $filtros['fecha_desde'] ?? '';
$hasta = $filtros['fecha_hasta'] ?? '';
if ($desde && $hasta)  $periodoTxt = rep_fecha($desde) . '  al  ' . rep_fecha($hasta);
elseif ($desde)        $periodoTxt = 'Desde el ' . rep_fecha($desde);
elseif ($hasta)        $periodoTxt = 'Hasta el ' . rep_fecha($hasta);
else                   $periodoTxt = 'Todas las fechas';

$tipoTxt = ($filtros['tipo'] ?? '') === 'Nuevo' ? 'Solo nuevos'
         : (($filtros['tipo'] ?? '') === 'Antiguo' ? 'Solo antiguos' : 'Nuevos y antiguos');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Recepción</title>
<link rel="stylesheet" href="../assets/css/reporte_pdf.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
</head>
<body>

<div class="no-print"><button type="button" onclick="window.print()">Imprimir / Guardar PDF</button></div>

<div class="rep-header">
    <div class="brand">
        <img src="../assets/img/logo_cns.png" alt="CNS">
        <div>
            <div class="org">Caja Nacional de Salud</div>
            <div class="sys">SmartCare CNS</div>
        </div>
    </div>
    <div class="doc-title">
        <h1>Reporte de Fichas Emitidas</h1>
        <small>Generado el <?= date('d/m/Y H:i') ?></small>
    </div>
</div>

<div class="info-grid">
    <div><div class="k">Periodo</div><div class="v"><?= htmlspecialchars($periodoTxt) ?></div></div>
    <div><div class="k">Médico</div><div class="v"><?= $medicoNombre !== '' ? htmlspecialchars($medicoNombre) : 'Todos los médicos' ?></div></div>
    <div><div class="k">Tipo de paciente</div><div class="v"><?= $tipoTxt ?></div></div>
    <div><div class="k">Búsqueda</div><div class="v"><?= ($filtros['q'] ?? '') !== '' ? htmlspecialchars($filtros['q']) : '—' ?></div></div>
</div>

<h2 class="sec">Resumen</h2>
<div class="cards">
    <?php foreach ($tarjetas as [$label, $valor, $color, $conPct, $previo, $subeBueno]): ?>
    <div class="card" style="--c:<?= $color ?>;">
        <div class="n"><?= (int)$valor ?></div>
        <div class="l"><?= $label ?></div>
        <div class="p"><?= $conPct ? rep_pct($valor, $total) . ' del total' : '&nbsp;' ?></div>
        <div class="d"><?= rep_delta($valor, $previo, $subeBueno) ?></div>
    </div>
    <?php endforeach; ?>
</div>
<p class="nota">
    <?php if ($periodoAnt): ?>
        Comparado con el periodo anterior (<?= rep_fecha($periodoAnt[0]) ?> al <?= rep_fecha($periodoAnt[1]) ?>).
    <?php else: ?>
        Indica fechas Desde y Hasta en el reporte para ver la comparación con el periodo anterior.
    <?php endif; ?>
</p>

<?php if ($total > 0): ?>
<h2 class="sec">Gráficos</h2>
<div class="charts">
    <div class="chart-box">
        <h3>Pacientes nuevos vs. antiguos</h3>
        <canvas id="pieTipo" width="300" height="240"></canvas>
    </div>
    <div class="chart-box">
        <h3>Fichas por médico</h3>
        <canvas id="pieMedicos" width="300" height="240"></canvas>
    </div>
</div>
<?php endif; ?>

<h2 class="sec">Detalle de fichas <span class="count">(<?= $total ?> registros)</span></h2>

<table class="data">
    <thead>
        <tr>
            <th>ID</th>
            <th>Paciente</th>
            <th>Médico</th>
            <th>CI</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th class="c">Tipo</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($fichas)): ?>
        <?php foreach ($fichas as $f): ?>
        <tr>
            <td class="muted"><?= (int)($f['id'] ?? 0) ?></td>
            <td><strong><?= htmlspecialchars(($f['paciente_nombre'] ?? '') . ' ' . ($f['paciente_apellido'] ?? '')) ?></strong></td>
            <td><?= htmlspecialchars(($f['medico_nombre'] ?? '') . ' ' . ($f['medico_apellido'] ?? '')) ?></td>
            <td class="muted"><?= htmlspecialchars($f['ci'] ?? '') ?></td>
            <td><?= rep_fecha($f['fecha'] ?? '') ?></td>
            <td><?= htmlspecialchars(substr($f['hora'] ?? '', 0, 5)) ?></td>
            <td class="c">
                <?php $esNuevo = ($f['tipo_usuario'] ?? '') === 'Nuevo'; ?>
                <span class="badge <?= $esNuevo ? 'nuevo' : 'antiguo' ?>"><?= $esNuevo ? 'Nuevo' : 'Antiguo' ?></span>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7" class="vacio">No existen registros para los filtros aplicados.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<div class="firmas">
    <div class="firma">
        Responsable de recepción
        <small>Firma y sello</small>
    </div>
</div>

<div class="pie-doc">
    Documento generado por SmartCare CNS el <?= date('d/m/Y H:i:s') ?>
</div>

<script>
function pastel(id, labels, data, colors) {
    var el = document.getElementById(id);
    if (!el || typeof Chart === 'undefined') return;
    var total = data.reduce(function (a, b) { return a + b; }, 0);
    var etiquetas = labels.map(function (l, i) {
        var pct = total ? Math.round(data[i] / total * 100) : 0;
        return l + ' — ' + data[i] + ' (' + pct + '%)';
    });
    new Chart(el, {
        type: 'pie',
        data: { labels: etiquetas, datasets: [{ data: data, backgroundColor: colors, borderColor: '#fff', borderWidth: 2 }] },
        options: {
            animation: false,
            responsive: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 11, font: { size: 10 } } } }
        }
    });
}
pastel('pieTipo', ['Nuevos', 'Antiguos'], [<?= (int)$cNuevos ?>, <?= (int)$cAntiguos ?>], ['#198754', '#0d6efd']);
pastel('pieMedicos', <?= json_encode(array_keys($medGraf)) ?>, <?= json_encode(array_values($medGraf)) ?>,
       ['#0b6b57','#0d6efd','#ffc107','#dc3545','#6f42c1','#fd7e14','#0dcaf0','#d63384','#6c757d']);

window.onload = function () { setTimeout(function () { window.print(); }, 300); };
</script>

</body>
</html>
