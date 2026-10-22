<?php
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/CitaModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/MedicoModel.php';

class PacienteController {

    public static function index() {
        self::requireAuth('paciente');
        $uid        = $_SESSION['usuario_id'];
        $usuario    = UsuarioModel::findById($uid);
        $fotoPerfil = self::fotoUrl($usuario['foto'] ?? '', $uid);
        $proximas   = CitaModel::proximasPaciente($uid, 10);
        require_once __DIR__ . '/../views/paciente/index.php';
    }

    public static function solicitarCita() {
    self::requireAuth('paciente');
    $uid            = $_SESSION['usuario_id'];
    $usuario        = UsuarioModel::findById($uid);
    $fotoPerfil     = self::fotoUrl($usuario['foto'] ?? '', $uid);
    $medicos        = MedicoModel::listar();
    $especialidades = MedicoModel::especialidades();
    $mensaje        = '';
    $error          = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $medicoId = (int)($_POST['medico_id'] ?? 0);
        $fecha    = $_POST['fecha']    ?? '';
        $hora     = $_POST['hora']     ?? '';
        $motivo   = trim($_POST['motivo'] ?? '');

        if (!$medicoId || !$fecha || !$hora) {
            $error = 'Faltan datos para agendar la cita.';
        } elseif (!MedicoModel::atiendeEn($medicoId, $fecha, $hora)) {
            $error = 'El médico no atiende en ese día u horario. Por favor elige otro.';
        } elseif ($fecha . ' ' . substr($hora, 0, 5) < date('Y-m-d H:i')) {
            $error = 'No puedes agendar una cita en una fecha u hora que ya pasó.';
        } elseif (!CitaModel::horarioDisponible($medicoId, $fecha, $hora)) {
            $error = 'Ese horario ya está ocupado. Por favor elige otro.';
        } elseif (!CitaModel::pacienteDisponible($uid, $fecha, $hora)) {
            $error = 'Ya tienes otra cita a esa misma fecha y hora.';
        } else {
            $citaId = CitaModel::agendar($uid, $medicoId, $fecha, $hora, $motivo);
            if ($citaId) {
                UsuarioModel::crearNotificacion(
                    $uid,
                    'Cita agendada',
                    'Tu cita fue registrada correctamente. Te esperamos puntualmente.',
                    'exito',
                    $citaId
                );
                header('Location: index.php?controller=paciente&action=misCitas&msg=cita_agendada');
                exit();
            }
            $error = 'No se pudo registrar la cita. Es posible que el horario acabe de ser tomado; intenta con otro.';
        }
    }

    $idsMedicos        = array_map(function ($m) { return (int)$m['medico_id']; }, $medicos);
    $horariosPorMedico = MedicoModel::horariosPorMedico($idsMedicos);
    $ocupados          = CitaModel::horariosOcupados();

    $medicosData = [];
    foreach ($medicos as $m) {
        $mid   = (int)$m['medico_id'];
        $espId = $m['especialidad_id'] ?? 0;
        $medicosData[$espId][] = [
            'id'       => $mid,
            'nombre'   => 'Dr(a). ' . $m['nombre'] . ' ' . $m['apellido'],
            'horarios' => $horariosPorMedico[$mid] ?? [],
        ];
    }
    require_once __DIR__ . '/../views/paciente/solicitar-cita.php';
}

    public static function misCitas() {
        self::requireAuth('paciente');
        $uid         = $_SESSION['usuario_id'];
        $usuario     = UsuarioModel::findById($uid);
        $fotoPerfil  = self::fotoUrl($usuario['foto'] ?? '', $uid);
        $todas_citas = CitaModel::todasCitasPaciente($uid);
        require_once __DIR__ . '/../views/paciente/mis-citas.php';
    }

    public static function historial() {
        self::requireAuth('paciente');
        $uid        = $_SESSION['usuario_id'];
        $usuario    = UsuarioModel::findById($uid);
        $fotoPerfil = self::fotoUrl($usuario['foto'] ?? '', $uid);
        $historial  = HistorialModel::historialPaciente($uid);
        require_once __DIR__ . '/../views/paciente/historial.php';
    }

    public static function perfil() {
        self::requireAuth('paciente');
        $uid        = $_SESSION['usuario_id'];
        $usuario    = UsuarioModel::findById($uid);
        $fotoPerfil = self::fotoUrl($usuario['foto'] ?? '', $uid);
        $mensaje    = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre      = trim($_POST['nombre']    ?? '');
            $apellido    = trim($_POST['apellido']  ?? '');
            $telefono    = trim($_POST['telefono']  ?? '');
            $fecha_nac   = $_POST['fecha_nac']      ?? '';
            $direccion   = trim($_POST['direccion'] ?? '');
            $foto_nombre = $usuario['foto'] ?? null;
            if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $ruta = __DIR__ . '/../assets/img/';
                if (!is_dir($ruta)) mkdir($ruta, 0755, true);
                $ext         = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_nombre = 'perfil_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], $ruta . $foto_nombre);
            }
            UsuarioModel::actualizarPaciente(
                $uid, $nombre, $apellido,
                $usuario['ci'], $fecha_nac, $telefono, $direccion
            );
            if ($foto_nombre !== ($usuario['foto'] ?? null)) {
                UsuarioModel::actualizarFoto($uid, $foto_nombre);
            }
            $_SESSION['nombre']   = $nombre;
            $_SESSION['apellido'] = $apellido;
            $mensaje    = 'Perfil actualizado correctamente.';
            $usuario    = UsuarioModel::findById($uid);
            $fotoPerfil = self::fotoUrl($usuario['foto'] ?? '', $uid);
        }
        $userData = $usuario;
        require_once __DIR__ . '/../views/paciente/mi-perfil.php';
    }

    public static function notificaciones() {
        self::requireAuth('paciente');
        $uid        = $_SESSION['usuario_id'];
        $usuario    = UsuarioModel::findById($uid);
        $fotoPerfil = self::fotoUrl($usuario['foto'] ?? '', $uid);
        $userData   = $usuario;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_leidas'])) {
            UsuarioModel::marcarNotificacionesLeidas($uid);
            header('Location: index.php?controller=paciente&action=notificaciones');
            exit();
        }
        $notifs = UsuarioModel::obtenerNotificaciones($uid);
        require_once __DIR__ . '/../views/paciente/notificaciones.php';
    }

    public static function seguro() {
        self::requireAuth('paciente');
        $uid        = $_SESSION['usuario_id'];
        $usuario    = UsuarioModel::findById($uid);
        $fotoPerfil = self::fotoUrl($usuario['foto'] ?? '', $uid);
        $seguro     = UsuarioModel::obtenerSeguro($uid);
        require_once __DIR__ . '/../views/paciente/seguro.php';
    }

    public static function pagos() {
        self::requireAuth('paciente');
        $uid        = $_SESSION['usuario_id'];
        $usuario    = UsuarioModel::findById($uid);
        $fotoPerfil = self::fotoUrl($usuario['foto'] ?? '', $uid);
        $pagos      = UsuarioModel::obtenerPagos($uid);
        $saldo      = UsuarioModel::obtenerSaldoPendiente($uid);
        require_once __DIR__ . '/../views/paciente/pagos.php';
    }

    public static function ayuda() {
        self::requireAuth('paciente');
        $uid        = $_SESSION['usuario_id'];
        $usuario    = UsuarioModel::findById($uid);
        $fotoPerfil = self::fotoUrl($usuario['foto'] ?? '', $uid);
        require_once __DIR__ . '/../views/paciente/ayuda.php';
    }

    private static function fotoUrl(?string $foto, int $uid): string {
    $ruta = $_SERVER['DOCUMENT_ROOT'] . '/SmartCareCNS_MVC/SmartCareCNS_MVC/assets/img/' . $foto;
    return (!empty($foto) && file_exists($ruta))
        ? '/SmartCareCNS_MVC/SmartCareCNS_MVC/assets/img/' . htmlspecialchars($foto)
        : 'https://i.pravatar.cc/64?u=' . $uid;
}

    private static function requireAuth(string $rol): void {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== $rol) {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
    }
}
?>