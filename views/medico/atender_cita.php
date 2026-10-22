<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Atención Médica - SmartCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">Registrar Consulta: <?= htmlspecialchars($cita['nombre'] . ' ' . $cita['apellido']) ?></h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="index.php?controller=medico&action=guardarDiagnostico" method="POST">
                         <input type="hidden" name="cita_id" value="<?= $cita['id'] ?>">

                        <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-clipboard2-pulse me-1"></i> Diagnóstico y tratamiento
                        </h6>

                    <div class="mb-3">
                     <label class="form-label fw-bold">Diagnóstico <span class="text-danger">*</span></label>
                     <textarea name="diagnostico" class="form-control" rows="3" required
                     placeholder="Ej: Faringitis aguda, hipertensión arterial..."></textarea>
                    </div>

                    <div class="mb-4">
                     <label class="form-label fw-bold">Observaciones / Receta</label>
                     <textarea name="observacion" class="form-control" rows="4"
                     placeholder="Ej: Reposo 3 días, paracetamol 500mg cada 8 horas..."></textarea>
                    </div>

                     <hr class="my-4">

                    <h6 class="fw-bold text-primary mb-3">
                    <i class="bi bi-heart-pulse me-1"></i> Datos clínicos del paciente
                    </h6>

    <div class="row g-3 mb-3">
         <div class="col-md-4">
            <label class="form-label fw-bold">Tipo de sangre</label>
            <select name="tipo_sangre" class="form-select">
                <option value="">— Seleccionar —</option>
                <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $ts): ?>
                    <option value="<?= $ts ?>"><?= $ts ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">Peso (kg)</label>
            <input type="number" name="peso" class="form-control"
                   step="0.1" min="1" max="300" placeholder="Ej: 70.5">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">Talla (m)</label>
            <input type="number" name="talla" class="form-control"
                   step="0.01" min="0.5" max="2.5" placeholder="Ej: 1.75">
        </div>
        </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Presión arterial</label>
        <input type="text" name="presion_arterial" class="form-control"
               placeholder="Ej: 120/80 mmHg">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Enfermedades preexistentes</label>
        <textarea name="enfermedades" class="form-control" rows="2"
            placeholder="Ej: Diabetes tipo 2, asma bronquial..."></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Alergias</label>
        <textarea name="alergias" class="form-control" rows="2"
            placeholder="Ej: Penicilina, ibuprofeno, látex..."></textarea>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold">Medicamentos actuales</label>
        <textarea name="medicamentos" class="form-control" rows="2"
            placeholder="Ej: Metformina 500mg, enalapril 10mg..."></textarea>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-circle me-1"></i> Guardar y Finalizar Cita
        </button>
        <a href="index.php?controller=medico&action=index" class="btn btn-outline-secondary">
            Cancelar
        </a>
    </div>
</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>