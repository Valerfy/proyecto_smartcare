<?php
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/CitaModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/MedicoModel.php';

class MedicoController {

    public static function index() {
        self::requireAuth('medico');
        $uid        = $_SESSION['usuario_id'];
        $medico     = MedicoModel::findByUsuarioId($uid);
        if (!$medico) die('Error: No se encontró el perfil del médico.');
        $medicoId   = $medico['medico_id'];
        $foto       = self::fotoUrl($medico['foto'] ?? '');
        $citas_hoy   = CitaModel::citasHoyMedico($medicoId);
        $contadores  = CitaModel::contadoresHoy($medicoId);
        $cal_anio    = isset($_GET['cal_anio']) ? (int)$_GET['cal_anio'] : (int)date('Y');
        $cal_mes     = isset($_GET['cal_mes'])  ? (int)$_GET['cal_mes']  : (int)date('m');
        if ($cal_mes < 1)  { $cal_mes = 12; $cal_anio--; }
        if ($cal_mes > 12) { $cal_mes = 1;  $cal_anio++; }
        $citas_mes   = CitaModel::citasMesMedico($medicoId, $cal_anio, $cal_mes);
        $citas_por_dia = [];
        foreach ($citas_mes as $c) {
            $dia = (int)date('j', strtotime($c['fecha']));
            $citas_por_dia[$dia][] = $c;
        }
        require_once __DIR__ . '/../views/medico/index.php';
    }

    public static function citas() {
        self::requireAuth('medico');
        $uid      = $_SESSION['usuario_id'];
        $medico   = MedicoModel::findByUsuarioId($uid);
        $foto     = self::fotoUrl($medico['foto'] ?? '');
        $medicoId = $medico['medico_id'];
        $citas    = CitaModel::citasMedico($medicoId);
        require_once __DIR__ . '/../views/medico/citas_doctor.php';
    }

    public static function atenderCita() {
        self::requireAuth('medico');
        $id   = (int)($_GET['id'] ?? 0);
        $cita = CitaModel::findById($id);
        if (!$cita) {
            header('Location: index.php?controller=medico&action=index');
            exit();
        }
        $uid    = $_SESSION['usuario_id'];
        $medico = MedicoModel::findByUsuarioId($uid);
        $foto   = self::fotoUrl($medico['foto'] ?? '');
        require_once __DIR__ . '/../views/medico/atender_cita.php';
    }

   public static function guardarDiagnostico() {
    self::requireAuth('medico');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?controller=medico&action=index');
        exit();
    }

    $citaId           = (int)($_POST['cita_id']          ?? 0);
    $diagnostico      = trim($_POST['diagnostico']        ?? '');
    $observacion      = trim($_POST['observacion']        ?? '');
    $tipo_sangre      = trim($_POST['tipo_sangre']        ?? '');
    $enfermedades     = trim($_POST['enfermedades']       ?? '');
    $alergias         = trim($_POST['alergias']           ?? '');
    $medicamentos     = trim($_POST['medicamentos']       ?? '');
    $presion_arterial = trim($_POST['presion_arterial']   ?? '');
    $peso             = $_POST['peso']  !== '' ? (float)$_POST['peso']  : null;
    $talla            = $_POST['talla'] !== '' ? (float)$_POST['talla'] : null;

    if (!$citaId || empty($diagnostico)) {
        header('Location: index.php?controller=medico&action=index');
        exit();
    }

    $ok = HistorialModel::guardarDiagnostico(
        $citaId, $diagnostico, $observacion,
        $tipo_sangre, $enfermedades, $alergias,
        $medicamentos, $presion_arterial, $peso, $talla
    );
    if ($ok) {
        $cita = CitaModel::findById($citaId);
        if ($cita && !empty($cita['paciente_id'])) {
            UsuarioModel::crearNotificacion(
                $cita['paciente_id'],
                'Diagnóstico disponible',
                'El médico ha registrado tu diagnóstico. Puedes verlo en tu historial clínico.',
                'info',
                $citaId
            );
        }
        header('Location: index.php?controller=medico&action=index&msg=exito');
    } else {
        header("Location: index.php?controller=medico&action=atenderCita&id=$citaId&error=1");
    }
    exit();
}

    public static function misPacientes() {
        self::requireAuth('medico');
        $uid       = $_SESSION['usuario_id'];
        $medico    = MedicoModel::findByUsuarioId($uid);
        $foto      = self::fotoUrl($medico['foto'] ?? '');
        $medicoId  = $medico['medico_id'];
        $busqueda  = trim($_GET['buscar'] ?? '');
        $pacientes = MedicoModel::pacientesDelMedico($medicoId, $busqueda);
        require_once __DIR__ . '/../views/medico/mis_pacientes.php';
    }

    public static function historial() {
        self::requireAuth('medico');
        $uid         = $_SESSION['usuario_id'];
        $medico      = MedicoModel::findByUsuarioId($uid);
        $foto        = self::fotoUrl($medico['foto'] ?? '');
        $medicoId    = $medico['medico_id'];
        $busqueda    = trim($_GET['buscar'] ?? '');
        $historiales = HistorialModel::historialMedico($medicoId, $busqueda);
        require_once __DIR__ . '/../views/medico/historial-doctor.php';
    }

    public static function perfil() {
        self::requireAuth('medico');
        $uid     = $_SESSION['usuario_id'];
        $medico  = MedicoModel::findByUsuarioId($uid);
        $foto    = self::fotoUrl($medico['foto'] ?? '');
        $mensaje = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $telefono  = trim($_POST['telefono']  ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            UsuarioModel::actualizar(
                $uid, $medico['nombre'], $medico['apellido'],
                $medico['ci'], $medico['correo'], $medico['rol'],
                $medico['matricula'] ?? '', $telefono, $direccion
            );
            $mensaje = 'Datos actualizados correctamente.';
            $medico  = MedicoModel::findByUsuarioId($uid);
        }
        $datos      = $medico;
        $fotoActual = self::fotoUrl($medico['foto'] ?? '');
        require_once __DIR__ . '/../views/medico/mi-perfil.php';
    }
public static function reportes() {
        self::requireAuth('medico');
        require_once __DIR__ . '/../models/ReporteModel.php';

        $uid      = $_SESSION['usuario_id'];
        $medico   = MedicoModel::findByUsuarioId($uid);
        $foto     = self::fotoUrl($medico['foto'] ?? '');
        $medicoId = $medico['medico_id'];

        $fechaDesde = trim($_GET['fecha_desde'] ?? '');
        $fechaHasta = trim($_GET['fecha_hasta'] ?? '');
        $pacienteId = !empty($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : null;

        $historiales  = ReporteModel::historialParaReporte($medicoId, $fechaDesde, $fechaHasta, $pacienteId);
        $estadisticas = ReporteModel::estadisticasMedico($medicoId, $fechaDesde, $fechaHasta);
        $pacientes    = ReporteModel::pacientesParaFiltro($medicoId);

        require_once __DIR__ . '/../views/medico/reportes.php';
    }

    public static function exportarReportePDF() {
        self::requireAuth('medico');
        require_once __DIR__ . '/../models/ReporteModel.php';

        $uid      = $_SESSION['usuario_id'];
        $medico   = MedicoModel::findByUsuarioId($uid);
        $medicoId = $medico['medico_id'];

        $fechaDesde = trim($_GET['fecha_desde'] ?? '');
        $fechaHasta = trim($_GET['fecha_hasta'] ?? '');
        $pacienteId = !empty($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : null;

        $historiales  = ReporteModel::historialParaReporte($medicoId, $fechaDesde, $fechaHasta, $pacienteId);
        $estadisticas = ReporteModel::estadisticasMedico($medicoId, $fechaDesde, $fechaHasta);

        $nombreMedico = 'Dr(a). ' . htmlspecialchars($medico['nombre'] . ' ' . $medico['apellido']);
        $especialidad = htmlspecialchars($medico['especialidad'] ?? '');

        require_once __DIR__ . '/../views/medico/reporte_pdf.php';
        exit();
    }
   private static function fotoUrl(?string $foto): string {
    $ruta = $_SERVER['DOCUMENT_ROOT'] . '/SmartCareCNS_MVC/SmartCareCNS_MVC/assets/img/' . $foto;
    return (!empty($foto) && file_exists($ruta))
        ? '/SmartCareCNS_MVC/SmartCareCNS_MVC/assets/img/' . htmlspecialchars($foto)
        : 'https://cdn-icons-png.flaticon.com/512/3774/3774299.png';
}

    private static function requireAuth(string $rol): void {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== $rol) {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
    }
}
?>