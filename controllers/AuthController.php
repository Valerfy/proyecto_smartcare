<?php
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController {

    public function index() {
        $this->login();
    }

    public function login() {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = trim($_POST['correo'] ?? '');
            $clave  = $_POST['clave'] ?? '';

            if (empty($correo) || empty($clave)) {
                $error = 'Todos los campos son obligatorios.';
            } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $error = 'Correo inválido.';
            } else {
                $usuario = UsuarioModel::autenticar($correo, $clave);

                if (!$usuario) {
                    $error = 'Correo o contraseña incorrectos.';
                } elseif ((int)$usuario['estado'] === 0) {
                    $error = 'Usuario desactivado.';
                } else {
                    $_SESSION = [];
                    session_regenerate_id(true);
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['nombre']     = $usuario['nombre'];
                    $_SESSION['apellido']   = $usuario['apellido'];
                    $_SESSION['rol']        = $usuario['rol'];

                    switch ($usuario['rol']) {
                        case 'admin':
                            header('Location: index.php?controller=admin');
                            break;
                        case 'medico':
                            header('Location: index.php?controller=medico');
                            break;
                        case 'recepcionista':
                            header('Location: index.php?controller=helper');
                            break;
                            default:
                        case 'paciente':
                            header('Location: index.php?controller=paciente');
                            break;
                    }
                    exit();
                }
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        $error     = '';
        $nombre    = '';
        $apellido  = '';
        $ci        = '';
        $telefono  = '';
        $correo    = '';
        $fecha_nac = '';
        $direccion = '';
        $ocupacion = '';
        $matricula = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = trim($_POST['nombre']    ?? '');
            $apellido = trim($_POST['apellido']  ?? '');
            $ci       = trim($_POST['ci']        ?? '');
            $telefono = trim($_POST['telefono']  ?? '');
            $correo   = trim($_POST['correo']    ?? '');
            $clave    = $_POST['clave']          ?? '';
            $clave2   = $_POST['clave2']         ?? '';
            $fecha_nac= $_POST['fecha_nac']      ?? '';
            $direccion= trim($_POST['direccion'] ?? '');
            $ocupacion= trim($_POST['ocupacion'] ?? '');
            $matricula= trim($_POST['matricula'] ?? '');

            $bloqueados = ['@cns.com', '@cns.org.bo', '@cns.rec.bo'];

            if (empty($nombre) || empty($apellido) || empty($ci) || empty($telefono) || empty($correo)) {
                $error = 'Todos los campos son obligatorios.';
            } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $error = 'Correo electrónico inválido.';
            } else {
                foreach ($bloqueados as $dominio) {
                    if (str_ends_with(strtolower($correo), $dominio)) {
                        $error = 'No puedes usar un correo institucional.';
                        break;
                    }
                }
            }
            if (!$error && !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+(?:\s+[A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$/u', $nombre)) {
                $error = 'Nombre inválido.';
            }
            if (!$error && !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+(?:\s+[A-Za-zÁÉÍÓÚáéíóúÑñ]+)*$/u', $apellido)) {
                $error = 'Apellido inválido.';
            }

            if (!$error && strlen($clave) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres.';
            }
            if (!$error && $clave !== $clave2) {
                $error = 'Las contraseñas no coinciden.';
            }
            if (!$error && !preg_match('/^[67]\d{7}$/', $telefono)) {
                $error = 'Celular inválido (8 dígitos, empieza con 6 o 7).';
            }
            if (!$error && (empty($fecha_nac) || empty($direccion) || empty($ocupacion))) {
                $error = 'Todos los campos son obligatorios.';
            }
            if (!$error) {
                $fecha = DateTime::createFromFormat('Y-m-d', $fecha_nac);
                if (!$fecha || $fecha->format('Y-m-d') !== $fecha_nac)
                    {
                    $error = 'Fecha de nacimiento inválida.';
                    } elseif ((int)$fecha->format('Y') > date('Y'))
                    {
                        $error = 'El año ingresado no es válido.';
                        } elseif ($fecha > new DateTime()) {
                            $error = 'La fecha no puede ser futura.';
                            } elseif ((new DateTime())->diff($fecha)->y < 18) {
                                 $error = 'Debes ser mayor de 18 años.';
                                 }
            }
            if (!$error && UsuarioModel::existeUsuario($ci, $correo, $telefono)) {
                $error = 'El CI, correo o teléfono ya están registrados.';
            }

            if ($error) {
                require_once __DIR__ . '/../views/auth/registrarse.php';
                return;
            }

            $foto_nombre = null;
            if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $ruta = __DIR__ . '/../assets/img/';
                if (!is_dir($ruta)) mkdir($ruta, 0755, true);
                $ext         = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_nombre = 'perfil_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], $ruta . $foto_nombre);
            }

            UsuarioModel::registrar(
                $nombre, $apellido, $ci, $telefono, $correo,
                $clave, $fecha_nac, $direccion, $matricula,
                $ocupacion, $foto_nombre
            );

            header('Location: index.php?controller=auth&action=login&registro=ok');
            exit();
        }

        require_once __DIR__ . '/../views/auth/registrarse.php';
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: index.php');
        exit();
    }
}
?>