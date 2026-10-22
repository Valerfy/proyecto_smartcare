<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Agenda Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/syles_medico.css">
</head>
<body class="bg-light">
<div class="d-flex">
   <?php include 'includes/sidebar.php'; ?>

    <main class="main-content flex-grow-1">
        <nav class="topbar navbar px-4 py-3 bg-white shadow-sm mb-4">
            <button class="sidebar-toggle me-3 d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
                <i class="bi bi-list"></i>
            </button>
            <div class="container-fluid">
                <h5 class="fw-bold mb-0">Gestión de Consultas - <?= date('d/m/Y') ?></h5>
            </div>
        </nav>

        <div class="container-fluid px-4">
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'exito'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> Paciente atendido y guardado en historial.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50">Citas de Hoy</h6>
                                    <h2 class="fw-bold mb-0"><?= $contadores['total_programado'] ?? 0 ?></h2>
                                </div>
                                <i class="bi bi-calendar2-week fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50">Atendidos</h6>
                                    <h2 class="fw-bold mb-0"><?= $contadores['pacientes_atendidos'] ?? 0 ?></h2>
                                </div>
                                <i class="bi bi-person-check fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-success">
                        <i class="bi bi-calendar3 me-2"></i>Calendario de Citas
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <?php
                            $mes_prev_anio = $cal_mes == 1 ? $cal_anio - 1 : $cal_anio;
                            $mes_prev      = $cal_mes == 1 ? 12 : $cal_mes - 1;
                            $mes_next_anio = $cal_mes == 12 ? $cal_anio + 1 : $cal_anio;
                            $mes_next      = $cal_mes == 12 ? 1 : $cal_mes + 1;
                            $nombres_mes   = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                                              'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                        ?>
                        <a href="index.php?controller=medico&action=index&cal_anio=<?= $mes_prev_anio ?>&cal_mes=<?= $mes_prev ?>"
                           class="btn btn-sm btn-outline-secondary rounded-circle" title="Mes anterior">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                        <span class="fw-semibold text-dark" style="min-width:160px;text-align:center;">
                            <?= $nombres_mes[$cal_mes] . ' ' . $cal_anio ?>
                        </span>
                        <a href="index.php?controller=medico&action=index&cal_anio=<?= $mes_next_anio ?>&cal_mes=<?= $mes_next ?>"
                           class="btn btn-sm btn-outline-secondary rounded-circle" title="Mes siguiente">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <?php
                        $primer_dia    = mktime(0,0,0,$cal_mes,1,$cal_anio);
                        $dias_en_mes   = (int)date('t', $primer_dia);
                        $inicio_sem    = ((int)date('w', $primer_dia) + 6) % 7;
                        $hoy_d         = (int)date('d');
                        $hoy_m         = (int)date('m');
                        $hoy_a         = (int)date('Y');
                    ?>
                    <table class="table table-bordered mb-0 text-center" style="table-layout:fixed;">
                        <thead>
                            <tr>
                                <?php foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d): ?>
                                    <th class="text-muted fw-semibold small py-2"><?= $d ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $celda = 0;
                            $dia   = 1;
                            echo '<tr>';
                            for ($i = 0; $i < $inicio_sem; $i++) {
                                echo '<td class="bg-light text-muted" style="height:70px;"></td>';
                                $celda++;
                            }
                            while ($dia <= $dias_en_mes) {
                                $es_hoy   = ($dia == $hoy_d && $cal_mes == $hoy_m && $cal_anio == $hoy_a);
                                $tiene    = isset($citas_por_dia[$dia]);
                                $n_citas  = $tiene ? count($citas_por_dia[$dia]) : 0;

                                $bg   = $es_hoy   ? 'background:#0f5132;color:white;'
                                      : ($tiene   ? 'background:#d1e7dd;'
                                                  : '');
                                $cls  = 'align-top p-1';

                                echo "<td class=\"$cls\" style=\"height:70px;$bg\">";
                                echo "<div class='fw-bold " . ($es_hoy ? 'text-white' : 'text-dark') . " mb-1'>" . $dia . "</div>";

                                if ($tiene) {
                                    foreach ($citas_por_dia[$dia] as $cita) {
                                        $hora  = date("H:i", strtotime($cita['hora']));
                                        $nomb  = htmlspecialchars($cita['nombre'] . ' ' . $cita['apellido']);
                                        $badge = $cita['estado'] === 'completada' ? 'bg-success' : 'bg-primary';
                                        echo "<a href='index.php?controller=medico&action=atenderCita&id={$cita['id']}'
                                                 class='d-block text-decoration-none mb-1'
                                                 title='$nomb · $hora'
                                                 style='font-size:0.68rem;'>
                                                <span class='badge $badge w-100 text-truncate'>$hora $nomb</span>
                                              </a>";
                                    }
                                }
                                echo '</td>';

                                $celda++;
                                if ($celda % 7 === 0 && $dia < $dias_en_mes) {
                                    echo '</tr><tr>';
                                }
                                $dia++;
                            }
                            while ($celda % 7 !== 0) {
                                echo '<td class="bg-light" style="height:70px;"></td>';
                                $celda++;
                            }
                            echo '</tr>';
                        ?>
                        </tbody>
                    </table>
                    <?php if (!empty($citas_mes)): ?>
                    <div class="mt-2 d-flex gap-3 flex-wrap">
                        <small class="text-muted"><span class="badge bg-primary">●</span> Pendiente</small>
                        <small class="text-muted"><span class="badge bg-success">●</span> Completada</small>
                        <small class="text-muted"><span style="display:inline-block;width:14px;height:14px;background:#0f5132;border-radius:3px;vertical-align:middle;"></span> Hoy</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary">Pacientes Esperando Atención</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Hora</th>
                                <th>Paciente</th>
                                <th>CI / Documento</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($citas_hoy)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-emoji-slight-smile fs-2 d-block mb-2"></i>
                                        No hay pacientes pendientes por el momento.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($citas_hoy as $cita): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary"><?= date("H:i", strtotime($cita['hora'])) ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($cita['nombre'] . ' ' . $cita['apellido']) ?></div>
                                        <small class="text-muted">Cita Programada</small>
                                    </td>
                                    <td><?= htmlspecialchars($cita['ci']) ?></td>
                                    <td class="text-center">
                                        <a href="index.php?controller=medico&action=atenderCita&id=<?= $cita['id'] ?>" class="btn btn-sm btn-primary px-3 rounded-pill">
                                        <i class="bi bi-pencil-square me-1"></i> Atender
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>
</html>