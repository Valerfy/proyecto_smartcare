<?php
header("Content-Type: text/html; charset=UTF-8");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte Médico</title>

<style>
body{
    font-family: Arial, sans-serif;
    margin:30px;
    color:#333;
}

h1{
    text-align:center;
    margin-bottom:20px;
}

h2{
    margin-top:30px;
    color:#0d6efd;
}

.info{
    margin-bottom:20px;
    padding:10px;
    border:1px solid #ddd;
    border-radius:5px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

table, th, td{
    border:1px solid #000;
}

th{
    background:#f2f2f2;
}

th, td{
    padding:8px;
    font-size:11px;
}

.text-center{
    text-align:center;
}

@media print {
    @page {
        margin: 10mm;
        size: landscape;
    }
}
</style>
</head>

<body>

<h1>Reporte Médico</h1>

<div class="info">
    <strong>Médico:</strong> <?= $nombreMedico ?><br>
    <strong>Especialidad:</strong> <?= $especialidad ?><br>
    <strong>Fecha de generación:</strong> <?= date('d/m/Y H:i:s') ?>
</div>

<div class="resumen">

    <h2>Estadísticas</h2>

    <p>
        <strong>Pacientes únicos atendidos:</strong>
        <?= $estadisticas['pacientes_unicos'] ?? 0 ?>
    </p>

    <table>
        <thead>
            <tr>
                <th>Estado de Cita</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
        <?php if(!empty($estadisticas['citas_por_estado'])): ?>
            <?php foreach($estadisticas['citas_por_estado'] as $fila): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['estado']) ?></td>
                    <td><?= htmlspecialchars($fila['total']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2" class="text-center">
                    No existen estadísticas.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>

<h2>Historiales Clínicos</h2>

<table>
    <thead>
        <tr>
            <th>Paciente</th>
            <th>Fecha</th>
            <th>Motivo</th>
            <th>Diagnóstico</th>
            <th>Tipo Sangre</th>
            <th>Presión</th>
            <th>Peso</th>
            <th>Talla</th>
            <th>Medicamentos</th>
        </tr>
    </thead>

    <tbody>

    <?php if(!empty($historiales)): ?>

        <?php foreach($historiales as $h): ?>

        <tr>

            <td>
                <?= htmlspecialchars(($h['nombre'] ?? '') . ' ' . ($h['apellido'] ?? '')) ?>
            </td>

            <td><?= htmlspecialchars($h['fecha'] ?? '') ?></td>
            <td><?= htmlspecialchars($h['motivo'] ?? '') ?></td>
            <td><?= htmlspecialchars($h['diagnostico'] ?? '') ?></td>
            <td><?= htmlspecialchars($h['tipo_sangre'] ?? '') ?></td>
            <td><?= htmlspecialchars($h['presion_arterial'] ?? '') ?></td>

            <td>
                <?= !empty($h['peso']) ? htmlspecialchars($h['peso']) . ' kg' : '' ?>
            </td>

            <td>
                <?= !empty($h['talla']) ? htmlspecialchars($h['talla']) . ' m' : '' ?>
            </td>

            <td><?= htmlspecialchars($h['medicamentos'] ?? '') ?></td>

        </tr>

        <?php endforeach; ?>

    <?php else: ?>
        <tr>
            <td colspan="9" class="text-center">
                No existen registros.
            </td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

<script>
window.onload = function() {
    window.print();
};
</script>

</body>
</html>