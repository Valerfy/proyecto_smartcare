<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Atención #<?= $numero_ficha ?></title>
    <link rel="stylesheet" href="../assets/css/styles_helper.css">
</head>
<body>

<div class="ficha">
    <div class="ficha-header">
        <h2>RESERVA GENERADA EXITOSAMENTE</h2>
    </div>
    <div class="ficha-body">
        <div class="info">
            <span class="label">Ficha Número:</span>
            <strong style="font-size: 32px;"><?= $numero_ficha ?></strong>
        </div>
        <div class="info">
            <span class="label">Fecha de Atención</span>
            <?= date('d/m/Y', strtotime($cita['fecha'])) ?> <?= substr($cita['hora'], 0, 5) ?>
        </div>
        <div class="info">
            <span class="label">Módulo de Atención</span>
            Consultorio — 
            <?= htmlspecialchars($cita['medico_nombre']  . ' ' . $cita['medico_apellido']) ?>
        </div>
        <div class="info">
            <span class="label">Motivo</span>
            <?= htmlspecialchars($cita['motivo'] ?: 'Consulta general') ?>
        </div>
        <div class="qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=Ficha<?= $numero_ficha ?>" alt="QR Code">
        </div>
        <div class="paciente">
            <?= htmlspecialchars($cita['paciente_nombre'] . ' ' . $cita['paciente_apellido']) ?>
        </div>
    </div>
    <div class="ficha-footer">La Paz, Bolivia</div>
</div>

<div class="btn-acciones">
    <button onclick="window.print()" class="btn btn-print">Imprimir Ficha</button>
    <a href="index.php?controller=helper&action=index&tab=agenda" class="btn btn-back">← Regresar al Panel</a>
</div>

</body>
</html>