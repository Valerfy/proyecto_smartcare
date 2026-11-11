<?php
header("Content-Type: text/html; charset=UTF-8");

if (!isset($historiales, $estadisticas)) {
    http_response_code(403);
    exit('Acceso no permitido.');
}
require_once __DIR__ . '/../components/reporte_utils.php';

$fechaDesde      = $fechaDesde      ?? '';
$fechaHasta      = $fechaHasta      ?? '';
$pacienteId      = $pacienteId      ?? null;
$periodoAnt      = $periodoAnt      ?? null;
$estadisticasAnt = $estadisticasAnt ?? null;

// ---- Resumen numérico ----
$totalCitas   = rep_total_citas($estadisticas);
$pacUnicos    = (int)($estadisticas['pacientes_unicos'] ?? 0);
$antTotal     = $estadisticasAnt ? rep_total_citas($estadisticasAnt) : null;
$antPacientes = $estadisticasAnt ? (int)($estadisticasAnt['pacientes_unicos'] ?? 0) : null;
$ant = function (string $estado) use ($estadisticasAnt) {
    return $estadisticasAnt ? rep_estado($estadisticasAnt, $estado) : null;
};

// [etiqueta, valor, color, ¿muestra % del total?, valor anterior, ¿subir es bueno?]
$tarjetas = [
    ['Total de citas', $totalCitas,                           '#0b6b57', false, $antTotal,            null],
    ['Completadas',    rep_estado($estadisticas,'completada'), '#198754', true,  $ant('completada'),   true],
    ['Confirmadas',    rep_estado($estadisticas,'confirmada'), '#0d6efd', true,  $ant('confirmada'),   null],
    ['Pendientes',     rep_estado($estadisticas,'pendiente'),  '#d97706', true,  $ant('pendiente'),    null],
    ['Canceladas',     rep_estado($estadisticas,'cancelada'),  '#dc3545', true,  $ant('cancelada'),    false],
    ['Pacientes únicos', $pacUnicos,                          '#6f42c1', false, $antPacientes,        true],
];

// ---- Datos de los gráficos ----
$coloresEstado = ['completada'=>'#198754','confirmada'=>'#0d6efd','pendiente'=>'#ffc107','cancelada'=>'#dc3545'];
$estLabels = []; $estData = []; $estColors = [];
foreach (($estadisticas['citas_por_estado'] ?? []) as $e) {
    $estLabels[] = ucfirst($e['estado']);
    $estData[]   = (int)$e['total'];
    $estColors[] = $coloresEstado[$e['estado']] ?? '#6c757d';
}
$edad   = rep_edad_rangos($historiales);
$sangre = rep_sangre_pacientes($historiales);

// ---- Textos de filtros ----
if ($fechaDesde && $fechaHasta)      $periodoTxt = rep_fecha($fechaDesde) . '  al  ' . rep_fecha($fechaHasta);
elseif ($fechaDesde)                 $periodoTxt = 'Desde el ' . rep_fecha($fechaDesde);
elseif ($fechaHasta)                 $periodoTxt = 'Hasta el ' . rep_fecha($fechaHasta);
else                                 $periodoTxt = 'Todo el historial';

if (!empty($pacienteId) && !empty($historiales)) {
    $pacienteTxt = trim(($historiales[0]['nombre'] ?? '') . ' ' . ($historiales[0]['apellido'] ?? ''))
                 . ' (CI ' . ($historiales[0]['ci'] ?? '') . ')';
} elseif (!empty($pacienteId)) {
    $pacienteTxt = 'Paciente seleccionado';
} else {
    $pacienteTxt = 'Todos los pacientes';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte Clínico</title>
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
        <h1>Reporte Clínico</h1>
        <small>Generado el <?= date('d/m/Y H:i') ?></small>
    </div>
</div>

<div class="info-grid">
    <div><div class="k">Médico</div><div class="v"><?= $nombreMedico ?></div></div>
    <div><div class="k">Especialidad</div><div class="v"><?= $especialidad !== '' ? $especialidad : '—' ?></div></div>
    <div><div class="k">Periodo</div><div class="v"><?= htmlspecialchars($periodoTxt) ?></div></div>
    <div><div class="k">Paciente</div><div class="v"><?= htmlspecialchars($pacienteTxt) ?></div></div>
</div>

<h2 class="sec">Resumen del periodo</h2>
<div class="cards">
    <?php foreach ($tarjetas as [$label, $valor, $color, $conPct, $previo, $subeBueno]): ?>
    <div class="card" style="--c:<?= $color ?>;">
        <div class="n"><?= (int)$valor ?></div>
        <div class="l"><?= $label ?></div>
        <div class="p"><?= $conPct ? rep_pct($valor, $totalCitas) . ' del total' : '&nbsp;' ?></div>
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
    Los porcentajes se calculan sobre el total de citas del periodo (<?= $totalCitas ?>).
</p>

<h2 class="sec">Gráficos</h2>
<div class="charts">
    <div class="chart-box">
        <h3>Citas por estado</h3>
        <?php if (array_sum($estData) > 0): ?>
            <canvas id="pieEstados" width="270" height="230"></canvas>
        <?php else: ?><div class="sin-datos">Sin datos</div><?php endif; ?>
    </div>
    <div class="chart-box">
        <h3>Pacientes por rango de edad</h3>
        <?php if (!empty($edad)): ?>
            <canvas id="pieEdad" width="270" height="230"></canvas>
        <?php else: ?><div class="sin-datos">Sin datos</div><?php endif; ?>
    </div>
    <div class="chart-box">
        <h3>Pacientes por tipo de sangre</h3>
        <?php if (!empty($sangre)): ?>
            <canvas id="pieSangre" width="270" height="230"></canvas>
        <?php else: ?><div class="sin-datos">Sin datos</div><?php endif; ?>
    </div>
</div>

<h2 class="sec">Historiales clínicos <span class="count">(<?= count($historiales) ?> registros)</span></h2>

<table class="data">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Paciente</th>
            <th>CI</th>
            <th>Motivo</th>
            <th>Diagnóstico</th>
            <th class="c">Sangre</th>
            <th>Presión</th>
            <th>Peso / Talla</th>
            <th>Medicamentos</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($historiales)): ?>
        <?php foreach ($historiales as $h): ?>
        <tr>
            <td style="white-space:nowrap;">
                <strong><?= rep_fecha($h['fecha'] ?? '') ?></strong><br>
                <span class="muted"><?= !empty($h['hora']) ? date('H:i', strtotime($h['hora'])) : '' ?></span>
            </td>
            <td><strong><?= htmlspecialchars(($h['nombre'] ?? '') . ' ' . ($h['apellido'] ?? '')) ?></strong></td>
            <td class="muted"><?= htmlspecialchars($h['ci'] ?? '') ?></td>
            <td><?= htmlspecialchars($h['motivo'] ?? '—') ?></td>
            <td>
                <?= htmlspecialchars($h['diagnostico'] ?? '') ?>
                <?php if (!empty($h['alergias'])): ?>
                    <br><span class="alerta">⚠ Alergias: <?= htmlspecialchars($h['alergias']) ?></span>
                <?php endif; ?>
            </td>
            <td class="c">
                <?php if (!empty($h['tipo_sangre'])): ?>
                    <span class="badge sangre"><?= htmlspecialchars($h['tipo_sangre']) ?></span>
                <?php else: ?>—<?php endif; ?>
            </td>
            <td><?= htmlspecialchars($h['presion_arterial'] ?? '—') ?></td>
            <td style="white-space:nowrap;">
                <?php
                    $pt = [];
                    if (!empty($h['peso']))  $pt[] = htmlspecialchars($h['peso'])  . ' kg';
                    if (!empty($h['talla'])) $pt[] = htmlspecialchars($h['talla']) . ' m';
                    echo $pt ? implode(' / ', $pt) : '—';
                ?>
            </td>
            <td><?= htmlspecialchars($h['medicamentos'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="9" class="vacio">No existen registros para los filtros aplicados.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<div class="firmas">
    <div class="firma">
        <?= $nombreMedico ?>
        <small>Firma y sello del médico</small>
    </div>
</div>

<div class="pie-doc">
    Documento generado por SmartCare CNS el <?= date('d/m/Y H:i:s') ?> · Información clínica confidencial
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
pastel('pieEstados', <?= json_encode($estLabels) ?>, <?= json_encode($estData) ?>, <?= json_encode($estColors) ?>);
pastel('pieEdad',    <?= json_encode(array_keys($edad)) ?>, <?= json_encode(array_values($edad)) ?>,
       ['#2a9d82','#0d6efd','#ffc107','#fd7e14','#6f42c1','#6c757d']);
pastel('pieSangre',  <?= json_encode(array_keys($sangre)) ?>, <?= json_encode(array_values($sangre)) ?>,
       ['#dc3545','#fd7e14','#ffc107','#198754','#0dcaf0','#0d6efd','#6f42c1','#d63384','#6c757d']);

window.onload = function () { setTimeout(function () { window.print(); }, 300); };
</script>

</body>
</html>
