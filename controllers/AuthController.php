<?php
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController {

    private const MAX_INTENTOS = 5;      // intentos fallidos de login
    private const BLOQUEO_SEG  = 60;     // segundos de espera tras superarlos
    private const DOMINIOS_BLOQUEADOS = ['@cns.com', '@cns.org.bo', '@cns.rec.bo'];
    private const REGEX_NOMBRE = '/^[A-Za-zÁÉÍÓÚÜáéíóúüÑñ]+(?: [A-Za-zÁÉÍÓÚÜáéíóúüÑñ]+)*$/u';

    public function index() {
        $this->login();
    }

    /** Muestra la vista unificada (login + registro) con el panel indicado. */
    private function render(string $modo, array $old = [], array $errores = [], string $error = '') {
        require __DIR__ . '/../views/auth/auth.php';
    }

    /* ------------------------------------------------------------------ LOGIN */
    public function login() {
        $error = '';
        $old   = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = trim($_POST['correo'] ?? '');
            $clave  = $_POST['clave'] ?? '';
            $old['correo'] = $correo;

            $espera = ($_SESSION['login_bloqueo'] ?? 0) - time();

            if ($espera > 0) {
                $error = "Demasiados intentos fallidos. Intenta de nuevo en {$espera} segundos.";
            } elseif ($correo === '' || $clave === '') {
                $error = 'Todos los campos son obligatorios.';
            } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $error = 'Correo inválido.';
            } else {
                try {
                    $usuario = UsuarioModel::autenticar($correo, $clave);
                } catch (PDOException $e) {
                    error_log('Login DB error: ' . $e->getMessage());
                    $usuario = null;
                    $error   = 'No se pudo conectar con la base de datos. Intenta nuevamente en unos segundos.';
                }

                if ($error === '') {
                    if (!$usuario) {
                        $_SESSION['login_fallos'] = ($_SESSION['login_fallos'] ?? 0) + 1;
                        if ($_SESSION['login_fallos'] >= self::MAX_INTENTOS) {
                            $_SESSION['login_bloqueo'] = time() + self::BLOQUEO_SEG;
                            $_SESSION['login_fallos']  = 0;
                        }
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
                            case 'admin':         header('Location: index.php?controller=admin');  break;
                            case 'medico':        header('Location: index.php?controller=medico'); break;
                            case 'recepcionista': header('Location: index.php?controller=helper'); break;
                            default:              header('Location: index.php?controller=paciente'); break;
                        }
                        exit();
                    }
                }
            }
        }

        $this->render('login', $old, [], $error);
    }

    /* --------------------------------------------------------------- REGISTRO */
    public function register() {
        $old     = [];
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campos = ['nombre', 'apellido', 'ci', 'telefono', 'correo', 'fecha_nac', 'direccion', 'ocupacion', 'matricula'];
            foreach ($campos as $c) {
                $old[$c] = trim($_POST[$c] ?? '');
            }
            $old['nombre']   = preg_replace('/\s+/', ' ', $old['nombre']);
            $old['apellido'] = preg_replace('/\s+/', ' ', $old['apellido']);
            $old['ci']       = strtoupper($old['ci']);
            $old['terminos'] = !empty($_POST['terminos']) ? '1' : '';

            $clave  = $_POST['clave']  ?? '';
            $clave2 = $_POST['clave2'] ?? '';
            extract($old, EXTR_SKIP);

            // Nombre y apellido
            foreach (['nombre' => 'Nombre', 'apellido' => 'Apellido'] as $k => $et) {
                $len = mb_strlen($old[$k]);
                if ($old[$k] === '')                                   $errores[$k] = "Ingresa tu " . strtolower($et) . ".";
                elseif ($len < 2 || $len > 40)                         $errores[$k] = "$et: entre 2 y 40 caracteres.";
                elseif (!preg_match(self::REGEX_NOMBRE, $old[$k]))     $errores[$k] = "$et: solo letras y espacios.";
            }

            // CI (5-9 dígitos, complemento opcional: 1234567-1A)
            if ($ci === '')                                            $errores['ci'] = 'Ingresa tu CI.';
            elseif (!preg_match('/^\d{5,9}(-[0-9A-Z]{1,2})?$/', $ci))  $errores['ci'] = 'CI inválido (5 a 9 dígitos, complemento opcional: 1234567-1A).';

            // Celular
            if ($telefono === '')                                      $errores['telefono'] = 'Ingresa tu celular.';
            elseif (!preg_match('/^[67]\d{7}$/', $telefono))           $errores['telefono'] = 'Celular inválido (8 dígitos, empieza con 6 o 7).';

            // Fecha de nacimiento
            if ($fecha_nac === '') {
                $errores['fecha_nac'] = 'Ingresa tu fecha de nacimiento.';
            } else {
                $f = DateTime::createFromFormat('Y-m-d', $fecha_nac);
                if (!$f || $f->format('Y-m-d') !== $fecha_nac)         $errores['fecha_nac'] = 'Fecha de nacimiento inválida.';
                elseif ($f > new DateTime())                           $errores['fecha_nac'] = 'La fecha no puede ser futura.';
                else {
                    $edad = (new DateTime())->diff($f)->y;
                    if ($edad < 18)                                    $errores['fecha_nac'] = 'Debes ser mayor de 18 años.';
                    elseif ($edad > 110)                               $errores['fecha_nac'] = 'Fecha de nacimiento no válida.';
                }
            }

            // Ocupación, dirección, matrícula
            $lo = mb_strlen($ocupacion);
            if ($ocupacion === '')                                     $errores['ocupacion'] = 'Ingresa tu ocupación.';
            elseif ($lo < 3 || $lo > 50 || !preg_match('/^[A-Za-zÁÉÍÓÚÜáéíóúüÑñ .,\-]+$/u', $ocupacion))
                                                                       $errores['ocupacion'] = 'Ocupación inválida (3 a 50 letras).';
            $ld = mb_strlen($direccion);
            if ($direccion === '')                                     $errores['direccion'] = 'Ingresa tu dirección.';
            elseif ($ld < 5 || $ld > 150)                              $errores['direccion'] = 'La dirección debe tener entre 5 y 150 caracteres.';
            if ($matricula === '')                                     $errores['matricula'] = 'Ingresa tu número de matrícula.';
            elseif (!preg_match('/^[A-Za-z0-9\-]{3,20}$/', $matricula)) $errores['matricula'] = 'Matrícula inválida (3 a 20 letras, números o guiones).';

            // Correo
            if ($correo === '')                                        $errores['correo'] = 'Ingresa tu correo.';
            elseif (mb_strlen($correo) > 120 || !filter_var($correo, FILTER_VALIDATE_EMAIL))
                                                                       $errores['correo'] = 'Correo electrónico inválido.';
            else {
                foreach (self::DOMINIOS_BLOQUEADOS as $dom) {
                    if (str_ends_with(strtolower($correo), $dom)) {
                        $errores['correo'] = 'No puedes usar un correo institucional.';
                        break;
                    }
                }
            }

            // Contraseña
            if (strlen($clave) < 8 || strlen($clave) > 72)             $errores['clave'] = 'La contraseña debe tener entre 8 y 72 caracteres.';
            elseif (!preg_match('/[A-Za-z]/', $clave) || !preg_match('/\d/', $clave))
                                                                       $errores['clave'] = 'La contraseña debe incluir al menos una letra y un número.';
            if ($clave2 === '' || $clave !== $clave2)                  $errores['clave2'] = 'Las contraseñas no coinciden.';

            // Términos
            if ($old['terminos'] !== '1')                              $errores['terminos'] = 'Debes aceptar los términos y condiciones.';

            // Foto (opcional): solo imágenes reales JPG/PNG de hasta 2 MB
            $extFoto = null;
            if (!empty($_FILES['foto']['name'])) {
                if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                    $errores['foto'] = 'No se pudo subir la foto. Intenta con otra imagen.';
                } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                    $errores['foto'] = 'La foto no debe superar 2 MB.';
                } else {
                    $info = @getimagesize($_FILES['foto']['tmp_name']);
                    $mimes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
                    if (!$info || !isset($mimes[$info['mime']])) $errores['foto'] = 'La foto debe ser JPG o PNG.';
                    else $extFoto = $mimes[$info['mime']];
                }
            }

            // Duplicados (solo si lo anterior es válido para no consultar de más)
            if (!$errores) {
                try {
                    $dup = UsuarioModel::camposDuplicados($ci, $correo, $telefono);
                } catch (PDOException $e) {
                    error_log('Registro DB error: ' . $e->getMessage());
                    $this->render('register', $old, [], 'No se pudo conectar con la base de datos. Intenta nuevamente.');
                    return;
                }
                if (in_array('ci', $dup, true))       $errores['ci']       = 'Este CI ya está registrado.';
                if (in_array('correo', $dup, true))   $errores['correo']   = 'Este correo ya está registrado.';
                if (in_array('telefono', $dup, true)) $errores['telefono'] = 'Este celular ya está registrado.';
            }

            if ($errores) {
                $this->render('register', $old, $errores, 'Revisa los campos marcados en rojo.');
                return;
            }

            $foto_nombre = null;
            if ($extFoto) {
                $ruta = __DIR__ . '/../assets/img/';
                if (!is_dir($ruta)) mkdir($ruta, 0755, true);
                $foto_nombre = 'perfil_' . bin2hex(random_bytes(8)) . '.' . $extFoto;
                move_uploaded_file($_FILES['foto']['tmp_name'], $ruta . $foto_nombre);
            }

            try {
                UsuarioModel::registrar(
                    $nombre, $apellido, $ci, $telefono, $correo,
                    $clave, $fecha_nac, $direccion, $matricula,
                    $ocupacion, $foto_nombre
                );
            } catch (Throwable $e) {
                error_log('Registro error: ' . $e->getMessage());
                $this->render('register', $old, [], 'No se pudo completar el registro. Intenta nuevamente.');
                return;
            }

            header('Location: index.php?controller=auth&action=login&registro=ok');
            exit();
        }

        $this->render('register', $old, $errores);
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
