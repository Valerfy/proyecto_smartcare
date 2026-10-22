<?php

class ChatbotController
{
    private const OLLAMA_URL = 'http://localhost:11434/api/generate';
    private const MODEL      = 'qwen3:4b';

    public function enviar()
    {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) session_start();

        $mensaje   = trim($_POST['mensaje']    ?? '');
        $sessionId = $_POST['session_id']      ?? ($_SESSION['usuario_id'] ?? 'guest');

        if (empty($mensaje)) {
            echo json_encode([
                'respuesta' => $this->mensajeBienvenida(),
                'success'   => true,
                'tipo'      => 'bienvenida',
            ]);
            return;
        }

        if (strlen($mensaje) > 600) {
            echo json_encode(['respuesta' => 'Tu mensaje es demasiado largo. Por favor sé más breve.', 'success' => false]);
            return;
        }

        $intent = $this->detectarIntent($mensaje);

        $accionDirecta = $this->manejarAccionDirecta($intent, $mensaje, $sessionId);
        if ($accionDirecta !== null) {
            echo json_encode($accionDirecta);
            return;
        }

        $historial    = $this->obtenerHistorial($sessionId);
        $contexto     = $this->obtenerContextoSistema();
        $prompt       = $this->construirPrompt($mensaje, $historial, $contexto, $intent);

        $data = [
            'model'   => self::MODEL,
            'prompt'  => $prompt,
            'stream'  => false,
            'options' => [
                'temperature' => 0.6,
                'top_p'       => 0.9,
                'top_k'       => 40,
                'num_ctx'     => 4096,
            ],
        ];

        $respuesta = $this->llamarOllama($data);

        if ($respuesta === false) {
            echo json_encode([
                'respuesta' => '⚠️ Estoy teniendo problemas técnicos en este momento. Por favor intenta de nuevo en unos segundos.',
                'success'   => false,
            ]);
            return;
        }

        $respuesta = $this->limpiarRespuesta($respuesta);

        $this->guardarHistorial($sessionId, $mensaje, $respuesta);

        echo json_encode(['respuesta' => $respuesta, 'success' => true, 'intent' => $intent]);
    }

    private function detectarIntent(string $msg): string
    {
        $msg = mb_strtolower($msg, 'UTF-8');

        $intents = [
            'agendar_cita'    => ['agendar', 'pedir cita', 'reservar', 'quiero una cita', 'necesito cita', 'programar cita', 'solicitar cita', 'hacer cita'],
            'ver_citas'       => ['mis citas', 'ver citas', 'tengo cita', 'próxima cita', 'citas pendientes', 'cuándo es mi cita'],
            'cancelar_cita'   => ['cancelar cita', 'anular cita', 'quitar cita', 'eliminar cita'],
            'ver_medicos'     => ['médicos', 'medicos', 'doctores', 'especialidades', 'lista de médicos', 'qué médicos', 'que medicos', 'ver doctores'],
            'horarios'        => ['horario', 'horarios', 'hora disponible', 'cuándo atiende', 'disponibilidad'],
            'plataforma'      => ['cómo funciona', 'como funciona', 'usar la plataforma', 'ayuda con la plataforma', 'qué es smartcare', 'que es smartcare'],
            'saludo'          => ['hola', 'buenos días', 'buenas tardes', 'buenas noches', 'hey', 'saludos'],
            'urgencia'        => ['urgencia', 'emergencia', 'urgente', 'me duele mucho', 'dolor fuerte', 'necesito ayuda urgente'],
        ];

        foreach ($intents as $intent => $palabras) {
            foreach ($palabras as $p) {
                if (str_contains($msg, $p)) return $intent;
            }
        }

        return 'general';
    }

    private function manejarAccionDirecta(string $intent, string $mensaje, string $sessionId): ?array
    {
        switch ($intent) {

            case 'ver_medicos':
                return $this->respuestaMedicos();

            case 'ver_citas':
                return $this->respuestaMisCitas();

            case 'horarios':
                return $this->respuestaHorarios();

            case 'urgencia':
                return [
                    'respuesta' => '🚨 <strong>¿Es una emergencia?</strong><br><br>Si tienes una emergencia médica llama al <strong>165 (CNS)</strong> o dirígete a urgencias inmediatamente.<br><br>Si no es urgente, puedo ayudarte a <a href="index.php?controller=paciente&action=solicitarCita" style="color:#0d6efd">agendar una cita aquí</a>.',
                    'success'   => true,
                    'intent'    => $intent,
                ];

            case 'agendar_cita':
                return [
                    'respuesta' => '📅 <strong>¡Con gusto te ayudo a agendar una cita!</strong><br><br>Puedes hacerlo directamente desde la plataforma:<br><br><a href="index.php?controller=paciente&action=solicitarCita" class="btn-chat-link">➡️ Ir a Solicitar Cita</a><br><br>¿Necesitas saber qué médicos están disponibles primero? Escribe <em>"ver médicos"</em> y te muestro la lista.',
                    'success'   => true,
                    'intent'    => $intent,
                ];

            case 'cancelar_cita':
                return [
                    'respuesta' => '❌ <strong>Cancelar una cita</strong><br><br>Para cancelar una cita ve a:<br><a href="index.php?controller=paciente&action=misCitas" class="btn-chat-link">➡️ Mis Citas</a><br><br>Busca la cita y presiona el botón <strong>"Cancelar"</strong>. Recuerda hacerlo con al menos 24 horas de anticipación.',
                    'success'   => true,
                    'intent'    => $intent,
                ];

            case 'plataforma':
                return [
                    'respuesta' => 'ℹ️ <strong>SmartCare CNS</strong> es la plataforma digital de la Caja Nacional de Salud.<br><br>Desde aquí puedes:<br>• 📅 Agendar y gestionar citas médicas<br>• 📋 Ver tu historial clínico<br>• 🩺 Consultar tus médicos asignados<br>• 🔔 Recibir notificaciones de tus citas<br>• 📄 Gestionar trámites de seguro<br><br>¿En qué parte necesitas ayuda?',
                    'success'   => true,
                    'intent'    => $intent,
                ];
        }

        return null;
    }

    private function respuestaMedicos(): array
    {
        try {
            require_once ROOT . '/models/MedicoModel.php';
            $medicos = MedicoModel::listar();

            if (empty($medicos)) {
                return [
                    'respuesta' => '😕 En este momento no hay médicos registrados en el sistema. Por favor contacta con administración.',
                    'success'   => true,
                ];
            }

            $porEspecialidad = [];
            foreach ($medicos as $m) {
                $esp = $m['especialidad'] ?? 'General';
                $porEspecialidad[$esp][] = 'Dr(a). ' . htmlspecialchars($m['nombre']) . ' ' . htmlspecialchars($m['apellido']);
            }

            $html  = '🩺 <strong>Médicos disponibles en SmartCare CNS:</strong><br><br>';
            foreach ($porEspecialidad as $esp => $lista) {
                $html .= '<strong>' . htmlspecialchars($esp) . '</strong><br>';
                foreach ($lista as $doc) {
                    $html .= '&nbsp;&nbsp;• ' . $doc . '<br>';
                }
                $html .= '<br>';
            }
            $html .= '<a href="index.php?controller=paciente&action=solicitarCita" class="btn-chat-link">📅 Agendar cita con uno de ellos</a>';

            return ['respuesta' => $html, 'success' => true, 'intent' => 'ver_medicos'];

        } catch (Exception $e) {
            return ['respuesta' => '⚠️ No pude cargar la lista de médicos en este momento.', 'success' => false];
        }
    }

    private function respuestaMisCitas(): array
    {
        $pacienteId = $_SESSION['usuario_id'] ?? null;

        if (!$pacienteId) {
            return [
                'respuesta' => '🔒 Para ver tus citas necesitas estar logueado. <a href="index.php?controller=auth&action=login">Iniciar sesión</a>',
                'success'   => true,
            ];
        }

        try {
            require_once ROOT . '/models/CitaModel.php';
            $citas = CitaModel::proximasPaciente($pacienteId, 5);

            if (empty($citas)) {
                return [
                    'respuesta' => '📭 No tienes citas próximas programadas.<br><br><a href="index.php?controller=paciente&action=solicitarCita" class="btn-chat-link">📅 Agendar una cita ahora</a>',
                    'success'   => true,
                ];
            }

            $html = '📋 <strong>Tus próximas citas:</strong><br><br>';
            foreach ($citas as $c) {
                $fecha    = date('d/m/Y', strtotime($c['fecha']));
                $hora     = substr($c['hora'], 0, 5);
                $estado   = $c['estado'];
                $emoji    = match($estado) {
                    'pendiente'   => '🟡',
                    'confirmada'  => '🟢',
                    'completada'  => '✅',
                    'cancelada'   => '🔴',
                    default       => '🔵',
                };
                $html .= "{$emoji} <strong>{$c['especialidad']}</strong> — {$c['medico']}<br>";
                $html .= "&nbsp;&nbsp;&nbsp;📅 {$fecha} a las ⏰ {$hora}<br><br>";
            }
            $html .= '<a href="index.php?controller=paciente&action=misCitas" class="btn-chat-link">Ver todas mis citas</a>';

            return ['respuesta' => $html, 'success' => true, 'intent' => 'ver_citas'];

        } catch (Exception $e) {
            return ['respuesta' => '⚠️ No pude cargar tus citas. Intenta desde el menú principal.', 'success' => false];
        }
    }

    private function respuestaHorarios(): array
    {
        try {
            require_once ROOT . '/models/MedicoModel.php';
            $medicos = MedicoModel::listar();

            if (empty($medicos)) {
                return ['respuesta' => 'No se encontraron médicos con horarios disponibles.', 'success' => true];
            }

            $html = '🕐 <strong>Horarios de atención:</strong><br><br>';
            foreach ($medicos as $m) {
                if (!empty($m['horario'])) {
                    $html .= '• <strong>Dr(a). ' . htmlspecialchars($m['nombre']) . ' ' . htmlspecialchars($m['apellido']) . '</strong>';
                    $html .= ' (' . htmlspecialchars($m['especialidad'] ?? 'General') . '): ';
                    $html .= htmlspecialchars($m['horario']) . '<br>';
                }
            }
            $html .= '<br><a href="index.php?controller=paciente&action=solicitarCita" class="btn-chat-link">📅 Reservar un horario</a>';

            return ['respuesta' => $html, 'success' => true, 'intent' => 'horarios'];

        } catch (Exception $e) {
            return ['respuesta' => '⚠️ No pude obtener los horarios en este momento.', 'success' => false];
        }
    }

    private function obtenerContextoSistema(): string
    {
        $ctx = "Información actual del sistema SmartCare CNS:\n";

        try {
            require_once ROOT . '/models/MedicoModel.php';
            $medicos = MedicoModel::listar();
            if (!empty($medicos)) {
                $ctx .= "Médicos disponibles:\n";
                foreach ($medicos as $m) {
                    $ctx .= "- Dr(a). {$m['nombre']} {$m['apellido']} | Especialidad: " . ($m['especialidad'] ?? 'General') . "\n";
                }
            }
        } catch (Exception $e) {
            $ctx .= "(No se pudo cargar la lista de médicos)\n";
        }

        $pacienteId = $_SESSION['usuario_id'] ?? null;
        if ($pacienteId) {
            $ctx .= "\nEl paciente actual tiene ID: {$pacienteId}, nombre: " . ($_SESSION['nombre'] ?? '') . " " . ($_SESSION['apellido'] ?? '') . "\n";
            try {
                require_once ROOT . '/models/CitaModel.php';
                $proximas = CitaModel::proximasPaciente($pacienteId, 3);
                if (!empty($proximas)) {
                    $ctx .= "Sus próximas citas:\n";
                    foreach ($proximas as $c) {
                        $ctx .= "- {$c['especialidad']} con {$c['medico']} el {$c['fecha']} a las {$c['hora']} (estado: {$c['estado']})\n";
                    }
                } else {
                    $ctx .= "No tiene citas próximas programadas.\n";
                }
            } catch (Exception $e) {}
        }

        return $ctx;
    }

    private function construirPrompt(string $mensaje, array $historial, string $contexto, string $intent): string
    {
        $systemPrompt = <<<PROMPT
Eres SmartCare Assistant, el asistente virtual oficial de SmartCare CNS (Caja Nacional de Salud de Bolivia).
Eres amable, empático, profesional y conciso. Siempre respondes en español.

Tu función es:
- Guiar a los pacientes en el uso de la plataforma
- Informar sobre médicos, especialidades y horarios disponibles
- Orientar para agendar, modificar o cancelar citas
- Resolver dudas sobre la plataforma y trámites

Reglas estrictas:
1. NUNCA des diagnósticos médicos ni consejos clínicos. Ante dudas de salud, recomienda agendar una cita.
2. Si el usuario pregunta algo que no corresponde al sistema de salud, indica amablemente que solo puedes ayudar con temas de SmartCare CNS.
3. Usa formato HTML simple (etiquetas <strong>, <br>, listas con •) para que se vea bonito en el chat.
4. Sé breve: máximo 5-6 líneas de respuesta. Si necesitas más, usa viñetas.
5. Cuando corresponda, incluye el enlace: <a href="index.php?controller=paciente&action=solicitarCita">Agendar Cita</a>

CONTEXTO ACTUAL DEL SISTEMA:
{$contexto}

HISTORIAL DE CONVERSACIÓN:
PROMPT;

        foreach ($historial as $turno) {
            $systemPrompt .= "\nPaciente: " . $turno['user'];
            $systemPrompt .= "\nSmartCare: " . $turno['assistant'];
        }

        $systemPrompt .= "\n\nPaciente: {$mensaje}\nSmartCare:";

        return $systemPrompt;
    }

    private function llamarOllama(array $data): string|false
    {
        $ch = curl_init(self::OLLAMA_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 45,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode !== 200) {
            error_log("Ollama Error: {$error} | HTTP: {$httpCode}");
            return false;
        }

        $resultado = json_decode($response, true);
        return $resultado['response'] ?? false;
    }

    private function limpiarRespuesta(string $texto): string
    {
        $texto = preg_replace('/<think>.*?<\/think>/si', '', $texto);
        return trim($texto);
    }

    private function obtenerHistorial(string $sessionId): array
    {
        return $_SESSION['chat_historial'][$sessionId] ?? [];
    }

    private function guardarHistorial(string $sessionId, string $userMsg, string $assistantMsg): void
    {
        if (!isset($_SESSION['chat_historial'][$sessionId])) {
            $_SESSION['chat_historial'][$sessionId] = [];
        }
        $_SESSION['chat_historial'][$sessionId][] = [
            'user'      => $userMsg,
            'assistant' => strip_tags($assistantMsg),
        ];
        if (count($_SESSION['chat_historial'][$sessionId]) > 12) {
            array_shift($_SESSION['chat_historial'][$sessionId]);
        }
    }

    public function limpiarHistorial()
    {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        $sessionId = $_SESSION['usuario_id'] ?? 'guest';
        unset($_SESSION['chat_historial'][$sessionId]);
        echo json_encode(['success' => true]);
    }

    private function mensajeBienvenida(): string
    {
        $nombre = $_SESSION['nombre'] ?? '';
        $saludo = !empty($nombre) ? "¡Hola, <strong>{$nombre}</strong>! 👋" : '👋 ¡Hola!';
        return "{$saludo} Soy <strong>SmartCare Assistant</strong>, tu asistente virtual.<br><br>Puedo ayudarte con:<br>• 📅 Agendar o cancelar citas<br>• 🩺 Ver médicos y especialidades<br>• 🕐 Consultar horarios<br>• ℹ️ Usar la plataforma<br><br>¿En qué puedo ayudarte hoy?";
    }
}
