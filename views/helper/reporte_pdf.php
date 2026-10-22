<?php
header("Content-Type: text/html; charset=UTF-8");

require_once __DIR__ . '/../../models/ReporteRecepcionModel.php';

$fichas = ReporteRecepcionModel::listarFichas();
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Reporte de Recepción</title>

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

<h1>Reporte de Fichas Emitidas</h1>

<div class="info">

    <strong>Fecha de generación:</strong>
    <?= date('d/m/Y H:i:s') ?>

    <br><br>

    <strong>Total de fichas:</strong>
    <?= count($fichas) ?>

</div>

<table>

<thead>

<tr>
    <th>ID</th>
    <th>Paciente</th>
    <th>Médico</th>
    <th>CI</th>
    <th>Fecha</th>
    <th>Hora</th>
    <th>Tipo</th>
</tr>

</thead>

<tbody>

<?php if(!empty($fichas)): ?>

    <?php foreach($fichas as $f): ?>

    <tr>

        <td><?= $f['id'] ?></td>

        <td>
            <?= htmlspecialchars(
                ($f['paciente_nombre'] ?? '') . ' ' .
                ($f['paciente_apellido'] ?? '')
            ) ?>
        </td>

        <td>
            <?= htmlspecialchars(
                ($f['medico_nombre'] ?? '') . ' ' .
                ($f['medico_apellido'] ?? '')
            ) ?>
        </td>

        <td><?= htmlspecialchars($f['ci'] ?? '') ?></td>

        <td><?= htmlspecialchars($f['fecha'] ?? '') ?></td>

        <td><?= htmlspecialchars(substr($f['hora'] ?? '',0,5)) ?></td>

        <td><?= htmlspecialchars($f['tipo_usuario'] ?? '') ?></td>

    </tr>

    <?php endforeach; ?>

<?php else: ?>

<tr>
    <td colspan="7" class="text-center">
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