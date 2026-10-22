<?php
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/CitaModel.php';
require_once __DIR__ . '/../models/MedicoModel.php';
require_once __DIR__ . '/../models/ReporteRecepcionModel.php';

class HelperController {

    public static function index() {
        self::requireAuth('recepcionista');
        $uid          = $_SESSION['usuario_id'];
        $userData     = UsuarioModel::findById($uid);
        $mensaje      = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendar'])) {
            $mensaje = self::agendarCita();
        }
        $tab          = $_GET['tab']          ?? 'agenda';
        $fecha_filtro = $_GET['fecha_filtro'] ?? date('Y-m-d');
        $cita_editar  = null;
        if (isset($_GET['editar'])) {
            $cita_editar = CitaModel::findById((int)$_GET['editar']);
        }
        
        $listaCitas = CitaModel::citasPorFecha($fecha_filtro);
$medicos    = MedicoModel::listar();
$pacientes  = UsuarioModel::listarPacientes();
$reporteFichas = [];
if ($tab === 'reportes') {
    $reporteFichas = ReporteRecepcionModel::listarFichas();
}

require_once __DIR__ . '/../views/helper/helper.php';
    }

    public static function editarCita() {
        self::requireAuth('recepcionista');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=helper&action=index');
            exit();
        }
        $cita_id   = (int)($_POST['cita_id']  ?? 0);
        $medico_id = (int)($_POST['medico']    ?? 0);
        $fecha     = $_POST['fecha']           ?? '';
        $hora      = $_POST['hora']            ?? '';
        $motivo    = trim($_POST['motivo']     ?? '');
        if (!$cita_id || !$medico_id || !$fecha || !$hora) {
            header('Location: index.php?controller=helper&action=index&error=datos_incompletos');
            exit();
        }
        $ok = CitaModel::editarCita($cita_id, $medico_id, $fecha, $hora, $motivo);
        if ($ok === 'ocupado') {
            header("Location: index.php?controller=helper&action=index&editar={$cita_id}&error=ocupado");
        } else {
            header("Location: index.php?controller=helper&action=index&tab=agenda&fecha_filtro={$fecha}&msg=actualizado");
        }
        exit();
    }

    public static function cambiarEstado() {
        self::requireAuth('recepcionista');
        $id      = (int)($_GET['id']    ?? 0);
        $estado  = trim($_GET['estado'] ?? '');
        $validos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
        if ($id && in_array($estado, $validos)) {
            CitaModel::cambiarEstado($id, $estado);
        }
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        header("Location: index.php?controller=helper&action=index&tab=agenda&fecha_filtro={$fecha}");
        exit();
    }

    public static function ficha() {
    self::requireAuth('recepcionista');

    $id = (int)($_GET['id'] ?? 0);

    $cita = CitaModel::fichaCompleta($id);

    if (!$cita) {
        die('Cita no encontrada.');
    }

    $paciente = trim($cita['paciente_nombre'] . ' ' . $cita['paciente_apellido']);
    $medico   = trim($cita['medico_nombre'] . ' ' . $cita['medico_apellido']);

    $numero_ficha = str_pad($id, 4, '0', STR_PAD_LEFT);

    require_once __DIR__ . '/../views/helper/ficha.php';
}

    private static function agendarCita(): string {
        $medicoId   = (int)($_POST['medico'] ?? 0);
        $fecha      = $_POST['fecha']        ?? '';
        $hora       = $_POST['hora']         ?? '';
        $motivo     = trim($_POST['motivo']  ?? '');
        $pacienteId = null;
        if (!empty($_POST['paciente'])) {
            $pacienteId = (int)$_POST['paciente'];
        } elseif (!empty($_POST['nombre_nuevo']) && !empty($_POST['apellido_nuevo'])) {
            $ci     = trim($_POST['ci_nuevo'] ?? '');
            $correo = 'temp.' . time() . rand(100, 999) . '@temp.com';
            try {
                $pacienteId = UsuarioModel::registrar(
                    trim($_POST['nombre_nuevo']),
                    trim($_POST['apellido_nuevo']),
                    $ci ?: ('TEMP' . time()),
                    '00000000', $correo,
                    password_hash('smartcare', PASSWORD_DEFAULT),
                    '2000-01-01', 'No especificada', '0', 'No especificada', null
                );
            } catch (PDOException $e) {
                return '<div class="alert alert-danger">No se pudo registrar al paciente: el CI ya existe.</div>';
            }
        }
        if (!$pacienteId || !$medicoId || !$fecha || !$hora) {
            return '<div class="alert alert-danger">Faltan datos para agendar la cita.</div>';
        }
        if (!CitaModel::horarioDisponible($medicoId, $fecha, $hora)) {
            return '<div class="alert alert-warning">El horario ya está ocupado.</div>';
        }
        if (!CitaModel::pacienteDisponible($pacienteId, $fecha, $hora)) {
            return '<div class="alert alert-warning">El paciente ya tiene otra cita a esa fecha y hora.</div>';
        }
        $citaId = CitaModel::agendar($pacienteId, $medicoId, $fecha, $hora, $motivo, $_SESSION['usuario_id'] ?? null);
        if (!$citaId) {
            return '<div class="alert alert-danger">No se pudo agendar la cita. Intenta con otro horario.</div>';
        }
        return '<div class="alert alert-success">Cita agendada correctamente.</div>';
    }

    private static function requireAuth(string $rol): void {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== $rol) {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
    }
}
?>