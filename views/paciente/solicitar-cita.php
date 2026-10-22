<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare CNS - Solicitar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">

</head>
<body>

<div class="d-flex">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1">
        <nav class="topbar navbar navbar-expand-lg px-4 py-3">
            <div class="container-fluid">
                <button class="sidebar-toggle d-md-none" type="button" onclick="toggleSidebar()" aria-label="Abrir menu">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 text-dark fw-bold">Caja Nacional de Salud, La Paz</h5>
                <div class="ms-auto d-flex align-items-center gap-4">
                    
                    
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?= $fotoPerfil ?>" class="rounded-circle" width="40" alt="Foto de perfil"onerror="this.src='https://i.pravatar.cc/48?u=<?= $uid ?>';">
                        <div>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['nombre'].' '.$_SESSION['apellido']) ?></span>
                            <small class="d-block text-muted"><?= htmlspecialchars($_SESSION['rol']) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="p-4">
            <h2 class="mb-4">Solicitar Cita</h2>

            <?php if ($mensaje): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error ?>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-4">
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">1. Especialidad</label>
                                <select id="select-especialidad" class="form-select form-select-lg" onchange="actualizarDoctores()">
                                    <option value="">Seleccione una especialidad</option>
                                    <?php foreach ($especialidades as $esp): ?>
                                        <option value="<?= $esp['id'] ?>"><?= htmlspecialchars($esp['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Centro Médico</label>
                                <select class="form-select form-select-lg">
                                    <option>Caja Nacional de Salud - La Paz</option>
                                    <option>Caja Nacional de Salud - El Alto</option>
                                </select>
                            </div>
                        </div>

                        <div id="section-doctores" class="mb-4 d-none">
                            <label class="form-label fw-bold">2. Seleccione su Médico</label>
                            <div id="doctor-list" class="row g-3"></div>
                        </div>

                        <div id="section-calendario" class="mb-4 d-none">
                            <label class="form-label fw-bold">3. Seleccione la Fecha</label>
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <button id="btn-mes-anterior" class="btn btn-sm btn-outline-secondary" onclick="cambiarMes(-1)">Anterior</button>
                                    <h5 class="mb-0" id="current-month">-</h5>
                                    <button id="btn-mes-siguiente" class="btn btn-sm btn-outline-secondary" onclick="cambiarMes(1)">Siguiente</button>
                                </div>
                                <div class="calendar-grid text-center fw-bold mb-2 small text-muted">
                                    <div>LUN</div><div>MAR</div><div>MIE</div><div>JUE</div><div>VIE</div><div>SAB</div><div>DOM</div>
                                </div>
                                <div id="calendar-days" class="calendar-grid text-center"></div>
                            </div>
                        </div>

                        <div id="section-horarios" class="mb-4 d-none">
                            <label class="form-label fw-bold">4. Horarios Disponibles</label>
                            <div id="time-slots" class="d-flex flex-wrap gap-2"></div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">5. Motivo de la consulta</label>
                            <textarea id="motivo-consulta" class="form-control" rows="3" placeholder="Describe el motivo de tu cita (opcional)"></textarea>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button onclick="agendarCita()" id="btn-agendar" class="btn btn-primary btn-lg w-100" disabled>
                                Confirmar y Agendar Cita
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm bg-primary text-white p-4 sticky-top" style="top: 20px;">
                        <h5 class="mb-4">Resumen de Cita</h5>
                        <div id="resumen">
                            <p class="small mb-1 opacity-75">Médico:</p>
                            <p class="fw-bold mb-3" id="res-medico">-</p>
                            <p class="small mb-1 opacity-75">Especialidad:</p>
                            <p class="fw-bold mb-3" id="res-especialidad">-</p>
                            <p class="small mb-1 opacity-75">Fecha y Hora:</p>
                            <p class="fw-bold mb-0" id="res-fecha">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="form-agendar" method="POST" action="index.php?controller=paciente&action=solicitarCita" class="d-none">
    <input type="hidden" name="medico_id" id="form_medico_id">
    <input type="hidden" name="fecha" id="form_fecha">
    <input type="hidden" name="hora" id="form_hora">
    <input type="hidden" name="motivo" id="form_motivo">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const data = <?= json_encode($medicosData, JSON_UNESCAPED_UNICODE) ?>;
const ocupados = <?= json_encode($ocupados ?? new stdClass(), JSON_UNESCAPED_UNICODE) ?>;

const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

const hoy = new Date();
let calMes = hoy.getMonth();
let calAnio = hoy.getFullYear();

let seleccion = {especialidad_id: '',medico_id: null,doctor: null,fecha: null,fechaDisplay: '',hora: null};

function actualizarDoctores() {
    const espId = document.getElementById('select-especialidad').value;
    const container = document.getElementById('doctor-list');
    const section = document.getElementById('section-doctores');
    
    container.innerHTML = '';
    seleccion = { especialidad_id: espId, medico_id: null, doctor: null, fecha: null, fechaDisplay: '', hora: null };
    actualizarResumen();

    if (!espId) {
        section.classList.add('d-none');
        return;
    }

    section.classList.remove('d-none');
    
    if (data[espId]) {
        data[espId].forEach(doc => {
            container.innerHTML += `
                <div class="col-md-6">
                    <div class="card doctor-card p-3 shadow-sm h-100" onclick="seleccionarDoctor(this, ${doc.id}, '${doc.nombre}')">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-person-circle fs-2 text-secondary"></i>
                            <div>
                                <h6 class="mb-0">${doc.nombre}</h6>
                                <small class="text-muted">Disponible</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
}

function seleccionarDoctor(element, id, nombre) {
    document.querySelectorAll('.doctor-card').forEach(c => c.classList.remove('selected'));
    element.classList.add('selected');
    
    seleccion.medico_id = id;
    seleccion.doctor = nombre;
    seleccion.fecha = null;
    seleccion.fechaDisplay = '';

    calMes = hoy.getMonth();
    calAnio = hoy.getFullYear();
    
    document.getElementById('section-calendario').classList.remove('d-none');
    generarCalendario();
    actualizarResumen();
}

function cambiarMes(delta) {
    calMes += delta;

    if (calMes < 0) {
        calMes = 11;
        calAnio--;
    } else if (calMes > 11) {
        calMes = 0;
        calAnio++;
    }

    generarCalendario();
}

function diaBD(date) {
    const d = date.getDay();
    return d === 0 ? 7 : d;
}

function doctorActual() {
    const lista = data[seleccion.especialidad_id] || [];
    return lista.find(d => d.id === seleccion.medico_id) || null;
}

function formatearFecha(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

function generarCalendario() {
    document.getElementById('current-month').textContent = `${meses[calMes]} ${calAnio}`;

    const esMesActual = (calAnio === hoy.getFullYear() && calMes === hoy.getMonth());
    document.getElementById('btn-mes-anterior').disabled = esMesActual;

    const container = document.getElementById('calendar-days');
    container.innerHTML = '';

    const primerDiaSemana = new Date(calAnio, calMes, 1).getDay();
    const offset = (primerDiaSemana + 6) % 7;

    const diasEnMes = new Date(calAnio, calMes + 1, 0).getDate();

    for (let i = 0; i < offset; i++) {
        const vacio = document.createElement('div');
        container.appendChild(vacio);
    }

    const hoySinHora = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());

    for (let i = 1; i <= diasEnMes; i++) {
        const fechaCelda = new Date(calAnio, calMes, i);
        const fechaStr = formatearFecha(fechaCelda);
        const diaSemana = fechaCelda.getDay();

        const day = document.createElement('div');
        day.className = 'calendar-day';
        day.textContent = i;

        const esFinDeSemana = (diaSemana === 0 || diaSemana === 6);
        const esPasado = fechaCelda < hoySinHora;
        const doc = doctorActual();
        const atiende = doc && (doc.horarios[diaBD(fechaCelda)] || []).length > 0;

        if (esFinDeSemana || esPasado || !atiende) {
            day.classList.add('disabled');
        } else {
            day.onclick = () => {
                document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                day.classList.add('selected');

                seleccion.fecha = fechaStr;
                seleccion.fechaDisplay = `${i} de ${meses[calMes]}, ${calAnio}`;

                mostrarHorarios();
                actualizarResumen();
            };
        }

        if (seleccion.fecha === fechaStr) {
            day.classList.add('selected');
        }

        container.appendChild(day);
    }
}

function mostrarHorarios() {
    const section = document.getElementById('section-horarios');
    const container = document.getElementById('time-slots');
    section.classList.remove('d-none');
    container.innerHTML = '';

    const espData = data[seleccion.especialidad_id];
    if (!espData) return;
    
    const doctorData = espData.find(d => d.id === seleccion.medico_id);
    if (!doctorData) return;

    const [anio, mes, dia] = seleccion.fecha.split('-').map(Number);
    const fechaSel = new Date(anio, mes - 1, dia);
    const tomados = ocupados[doctorData.id] || [];
    const ahora = new Date();
    const esHoy = formatearFecha(ahora) === seleccion.fecha;
    const horaActual = String(ahora.getHours()).padStart(2, '0') + ':' + String(ahora.getMinutes()).padStart(2, '0');
    seleccion.hora = null;
    document.getElementById('btn-agendar').disabled = true;

    const libres = (doctorData.horarios[diaBD(fechaSel)] || []).filter(hora =>
        !tomados.includes(seleccion.fecha + ' ' + hora) && !(esHoy && hora <= horaActual)
    );

    if (libres.length === 0) {
        container.innerHTML = '<span class="text-muted">No hay horarios disponibles para este día.</span>';
        return;
    }

    libres.forEach(hora => {
        const btn = document.createElement('button');
        btn.className = 'btn btn-outline-dark px-4 time-btn';
        btn.textContent = hora;
        btn.onclick = () => {
            document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            seleccion.hora = hora;
            document.getElementById('btn-agendar').disabled = false;
            actualizarResumen();
        };
        container.appendChild(btn);
    });
}

function actualizarResumen() {
    document.getElementById('res-medico').textContent = seleccion.doctor || '-';
    document.getElementById('res-especialidad').textContent = 
        document.getElementById('select-especialidad').selectedOptions[0]?.text || '-';
    document.getElementById('res-fecha').textContent = 
        seleccion.fechaDisplay ? `${seleccion.fechaDisplay} - ${seleccion.hora || ''}` : '-';
}

function agendarCita() {
    if (!seleccion.medico_id || !seleccion.fecha || !seleccion.hora) {
        alert(" Por favor completa toda la selección");
        return;
    }

    document.getElementById('form_medico_id').value = seleccion.medico_id;
    document.getElementById('form_fecha').value = seleccion.fecha;
    document.getElementById('form_hora').value = seleccion.hora;
    document.getElementById('form_motivo').value = document.getElementById('motivo-consulta').value || '';

    if (confirm('¿Confirmar la cita con ' + seleccion.doctor + '?\n📅 ' + seleccion.fechaDisplay + ' a las ' + seleccion.hora)) {
        document.getElementById('form-agendar').submit();
    }
}

function cerrarSesion() {
    if (confirm('¿Cerrar sesión?')) window.location.href = '../../views/auth/login.php';
}
</script>

<script src="../assets/js/sidebar.js"></script>
</body>
</html>