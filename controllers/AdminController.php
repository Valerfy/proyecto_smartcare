<?php
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/MedicoModel.php';

class AdminController {

    public function index() {
        $this->requireAuth();

        $uid        = $_SESSION['usuario_id'];
        $userData   = UsuarioModel::findById($uid);
        $fotoActual = !empty($userData['foto']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/SmartCareCNS_MVC/SmartCareCNS_MVC/assets/img/' . $userData['foto'])
        ? '/SmartCareCNS_MVC/SmartCareCNS_MVC/assets/img/' . $userData['foto']
            : 'https://i.pravatar.cc/100';

        $mensaje      = '';
        $editarUsuario = null;
        $editarMedico  = null;

        if (isset($_GET['editar'])) {
            $editarUsuario = UsuarioModel::findById((int)$_GET['editar']);
            if ($editarUsuario && $editarUsuario['rol'] === 'medico') {
                $editarMedico = MedicoModel::findByUsuarioId($editarUsuario['id']);
            }
        }

        if (isset($_GET['toggle'])) {
            $u = UsuarioModel::findById((int)$_GET['toggle']);
            if ($u) {
                UsuarioModel::cambiarEstado($u['id'], $u['estado'] ? 0 : 1);
            }
            header('Location: index.php?controller=admin&action=index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mensaje = $this->procesarFormulario();
            if (!$mensaje || strpos($mensaje, 'correctamente') !== false) {
                header('Location: index.php?controller=admin&action=index&msg=' . urlencode($mensaje));
                exit();
            }
        }

        if (isset($_GET['msg'])) {
            $mensaje = htmlspecialchars($_GET['msg']);
        }

        $usuarios       = UsuarioModel::listar();
        $especialidades = MedicoModel::especialidades();

        require_once __DIR__ . '/../views/admin/admin.php';
    }

    private function procesarFormulario(): string {
        $id             = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $nombre         = trim($_POST['nombre']    ?? '');
        $apellido       = trim($_POST['apellido']  ?? '');
        $ci             = trim($_POST['ci']        ?? '');
        $correo         = trim($_POST['correo']    ?? '');
        $rol            = $_POST['rol']            ?? 'paciente';
        $matricula      = trim($_POST['matricula'] ?? '');
        $especialidadId = !empty($_POST['especialidad']) ? (int)$_POST['especialidad'] : null;

        if ($rol === 'medico' && !$especialidadId) {
            return 'Error: Seleccione una especialidad para el médico.';
        }

        if ($id) {
            try {
                UsuarioModel::actualizar($id, $nombre, $apellido, $ci, $correo, $rol, $matricula,
                                         null, null, $especialidadId);
                return 'Usuario actualizado correctamente.';
            } catch (PDOException $e) {
                return $this->mensajeErrorBD($e, 'actualizar');
            } catch (InvalidArgumentException $e) {
                return 'Error: ' . $e->getMessage();
            }
        }

        $clave     = trim($_POST['clave']     ?? '');
        $telefono  = trim($_POST['telefono']  ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $ocupacion = trim($_POST['ocupacion'] ?? '');
        $fecha_nac = $_POST['fecha_nac']      ?? date('Y-m-d');

        $claveTexto = $clave ?: 'smartcare123';

        try {
            UsuarioModel::registrar(
                $nombre, $apellido, $ci, $telefono, $correo,
                $claveTexto, $fecha_nac, $direccion, $matricula,
                $ocupacion, null, $rol, $especialidadId
            );
            return 'Usuario creado correctamente.';
        } catch (PDOException $e) {
            return $this->mensajeErrorBD($e, 'crear');
        } catch (InvalidArgumentException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    private function mensajeErrorBD(PDOException $e, string $accion): string {
        if ($e->getCode() === '23000') {
            $msg = $e->getMessage();
            if (strpos($msg, 'uq_usuario_ci') !== false) {
                return 'Error: El CI ingresado ya está registrado.';
            }
            if (strpos($msg, 'uq_usuario_correo') !== false) {
                return 'Error: El correo ingresado ya está registrado.';
            }
            if (strpos($msg, 'uq_medico_matricula') !== false) {
                return 'Error: La matrícula ingresada ya está registrada.';
            }
            if (stripos($msg, 'foreign key') !== false) {
                return 'Error: No se puede cambiar el rol de un usuario que ya tiene citas o historial.';
            }
            return 'Error: Ya existe un usuario con esos datos.';
        }
        return 'Error inesperado al ' . $accion . ' el usuario.';
    }

    private function requireAuth(): void {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
    }
}
?>