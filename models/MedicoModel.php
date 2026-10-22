<?php
require_once __DIR__ . '/../config/conexion.php';

class MedicoModel {

    private const HORARIOS_DEFECTO = ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00'];
    private const MINUTOS_POR_CITA = 30;

    public static function listar() {
        $conexion = conexion::conectar();
        $sql = "SELECT id, nombre, apellido, correo, foto,
                       medico_id, especialidad_id, horario, disponible, especialidad
                FROM vw_medico_perfil
                WHERE estado = 1
                ORDER BY apellido";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByUsuarioId($usuarioId) {
        $conexion = conexion::conectar();
        $sql  = "SELECT * FROM vw_medico_perfil WHERE id = :usuario_id LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function especialidades() {
        $conexion = conexion::conectar();
        $sql  = "SELECT * FROM especialidad ORDER BY nombre";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($usuarioId, $especialidadId, $matricula = null) {
        $conexion = conexion::conectar();
        $sql  = "INSERT INTO medico (usuario_id, especialidad_id, matricula)
                 VALUES (:usuario_id, :especialidad_id, :matricula)";
        $stmt = $conexion->prepare($sql);
        return $stmt->execute([
            ':usuario_id'      => $usuarioId,
            ':especialidad_id' => $especialidadId,
            ':matricula'       => ($matricula !== null && $matricula !== '') ? $matricula : null,
        ]);
    }

    public static function actualizarEspecialidad($usuarioId, $especialidadId) {
        $conexion = conexion::conectar();
        $sql  = "UPDATE medico SET especialidad_id = :especialidad_id WHERE usuario_id = :usuario_id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':especialidad_id', $especialidadId);
        $stmt->bindParam(':usuario_id',      $usuarioId);
        return $stmt->execute();
    }

    public static function pacientesDelMedico($medicoId, $buscar = '') {
        $conexion = conexion::conectar();
        $sql  = "SELECT DISTINCT u2.id, u2.nombre, u2.apellido, u2.ci, u2.telefono, u2.fecha_nac,
                        (SELECT ec2.nombre FROM cita c2
                         INNER JOIN estado_cita ec2 ON ec2.id = c2.estado_cita_id
                         WHERE c2.paciente_id = u2.id AND c2.medico_id = :medico_id1
                         ORDER BY c2.fecha DESC, c2.hora DESC LIMIT 1) AS ultimo_estado,
                        (SELECT c3.fecha FROM cita c3
                         WHERE c3.paciente_id = u2.id AND c3.medico_id = :medico_id2
                         ORDER BY c3.fecha DESC, c3.hora DESC LIMIT 1) AS ultima_fecha
                 FROM usuario u2
                 JOIN cita c ON c.paciente_id = u2.id
                 WHERE c.medico_id = :medico_id3";

        if (!empty($buscar)) {
            $sql .= " AND (u2.nombre LIKE :buscar OR u2.apellido LIKE :buscar2 OR u2.ci LIKE :buscar3)";
        }

        $sql .= " ORDER BY u2.apellido ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':medico_id1', $medicoId);
        $stmt->bindParam(':medico_id2', $medicoId);
        $stmt->bindParam(':medico_id3', $medicoId);

        if (!empty($buscar)) {
            $like = "%$buscar%";
            $stmt->bindParam(':buscar',  $like);
            $stmt->bindParam(':buscar2', $like);
            $stmt->bindParam(':buscar3', $like);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function horariosPorMedico(array $medicoIds): array {
        $resultado = [];
        foreach ($medicoIds as $id) {
            $resultado[(int)$id] = [];
        }
        if (empty($resultado)) {
            return $resultado;
        }

        $conexion = conexion::conectar();
        $stmt = $conexion->prepare(
            "SELECT medico_id, dia_semana, hora_inicio, hora_fin
             FROM horario_medico
             ORDER BY medico_id, dia_semana, hora_inicio"
        );
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $f) {
            $mid = (int)$f['medico_id'];
            if (!isset($resultado[$mid])) continue;
            $dia = (int)$f['dia_semana'];
            $resultado[$mid][$dia] = array_merge(
                $resultado[$mid][$dia] ?? [],
                self::generarHoras($f['hora_inicio'], $f['hora_fin'])
            );
        }

        foreach ($resultado as $mid => $dias) {
            if (empty($dias)) {
                for ($d = 1; $d <= 5; $d++) {
                    $resultado[$mid][$d] = self::HORARIOS_DEFECTO;
                }
            }
        }
        return $resultado;
    }

    public static function atiendeEn($medicoId, $fecha, $hora): bool {
        $ts = strtotime($fecha);
        if ($ts === false) return false;
        $dia = (int)date('N', $ts);
        $horas = self::horariosPorMedico([$medicoId])[(int)$medicoId][$dia] ?? [];
        return in_array(substr((string)$hora, 0, 5), $horas, true);
    }

    private static function generarHoras($inicio, $fin): array {
        $horas = [];
        $t = strtotime('1970-01-01 ' . $inicio);
        $f = strtotime('1970-01-01 ' . $fin);
        if ($t === false || $f === false) return $horas;
        $paso = self::MINUTOS_POR_CITA * 60;
        for (; $t + $paso <= $f; $t += $paso) {
            $horas[] = date('H:i', $t);
        }
        return $horas;
    }
}
?>
