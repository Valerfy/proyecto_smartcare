<?php
require_once __DIR__ . '/../config/conexion.php';

class CitaModel {

    public static function citasHoyMedico($medicoId) {
        $conexion = conexion::conectar();
        $sql  = "SELECT c.*, ec.nombre AS estado, u.nombre, u.apellido, u.ci
                 FROM cita c
                 JOIN usuario u       ON c.paciente_id = u.id
                 JOIN estado_cita ec  ON ec.id = c.estado_cita_id
                 WHERE c.medico_id = :medico_id
                   AND c.fecha = CURDATE()
                   AND ec.nombre IN ('pendiente','confirmada')
                 ORDER BY c.hora ASC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':medico_id', $medicoId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function citasMedico($medicoId) {
        $conexion = conexion::conectar();
        $sql  = "SELECT c.*, ec.nombre AS estado, u.nombre, u.apellido, u.ci, h.diagnostico, h.observacion
                 FROM cita c
                 JOIN usuario u       ON c.paciente_id = u.id
                 JOIN estado_cita ec  ON ec.id = c.estado_cita_id
                 LEFT JOIN historial h ON h.cita_id = c.id
                 WHERE c.medico_id = :medico_id
                 ORDER BY c.fecha ASC, c.hora ASC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':medico_id', $medicoId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function proximasPaciente($pacienteId, $limite = 3) {
        $conexion = conexion::conectar();
        $sql  = "SELECT fecha, hora, especialidad,
                        CONCAT('Dr(a). ', medico_nombre, ' ', medico_apellido) AS medico,
                        estado
                 FROM vw_citas_detalle
                 WHERE paciente_id = :paciente_id
                 ORDER BY fecha ASC, hora ASC
                 LIMIT :limite";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':paciente_id', $pacienteId);
        $stmt->bindParam(':limite',      $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function todasCitasPaciente($pacienteId) {
        $conexion = conexion::conectar();
        $sql  = "SELECT fecha, hora, especialidad,
                        CONCAT('Dr(a). ', medico_nombre, ' ', medico_apellido) AS medico,
                        estado,
                        (fecha >= CURDATE()) AS es_proxima
                 FROM vw_citas_detalle
                 WHERE paciente_id = :paciente_id
                 ORDER BY fecha DESC, hora DESC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':paciente_id', $pacienteId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function horarioDisponible($medicoId, $fecha, $hora) {
        $conexion = conexion::conectar();
        $sql  = "SELECT COUNT(*)
                 FROM cita c
                 JOIN estado_cita ec ON ec.id = c.estado_cita_id
                 WHERE c.medico_id = :medico_id
                   AND c.fecha = :fecha
                   AND c.hora  = :hora
                   AND ec.nombre <> 'cancelada'";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':medico_id', $medicoId);
        $stmt->bindParam(':fecha',     $fecha);
        $stmt->bindParam(':hora',      $hora);
        $stmt->execute();
        return $stmt->fetchColumn() == 0;
    }

    public static function pacienteDisponible($pacienteId, $fecha, $hora, $excluirCitaId = 0) {
        $conexion = conexion::conectar();
        $sql  = "SELECT COUNT(*)
                 FROM cita c
                 JOIN estado_cita ec ON ec.id = c.estado_cita_id
                 WHERE c.paciente_id = :paciente_id
                   AND c.fecha = :fecha
                   AND c.hora  = :hora
                   AND c.id   <> :excluir
                   AND ec.nombre <> 'cancelada'";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':paciente_id', $pacienteId);
        $stmt->bindParam(':fecha',       $fecha);
        $stmt->bindParam(':hora',        $hora);
        $stmt->bindParam(':excluir',     $excluirCitaId);
        $stmt->execute();
        return $stmt->fetchColumn() == 0;
    }

    public static function horariosOcupados(): array {
        $conexion = conexion::conectar();
        $sql  = "SELECT c.medico_id, c.fecha, c.hora
                 FROM cita c
                 JOIN estado_cita ec ON ec.id = c.estado_cita_id
                 WHERE c.fecha >= CURDATE() AND ec.nombre <> 'cancelada'";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $ocupados = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $f) {
            $ocupados[(int)$f['medico_id']][] = $f['fecha'] . ' ' . substr($f['hora'], 0, 5);
        }
        return $ocupados;
    }

    private static function liberarCancelados(PDO $conexion, $pacienteId, $medicoId, $fecha, $hora, $excluirCitaId = 0): void {
        $s = $conexion->prepare(
            "SELECT c.id
             FROM cita c
             JOIN estado_cita ec ON ec.id = c.estado_cita_id
             WHERE ec.nombre = 'cancelada'
               AND c.id <> :excluir
               AND c.fecha = :fecha
               AND c.hora  = :hora
               AND (c.medico_id = :medico_id OR c.paciente_id = :paciente_id)"
        );
        $s->execute([
            ':excluir'     => $excluirCitaId,
            ':fecha'       => $fecha,
            ':hora'        => $hora,
            ':medico_id'   => $medicoId,
            ':paciente_id' => $pacienteId,
        ]);
        $del = $conexion->prepare("DELETE FROM cita WHERE id = :id");
        foreach ($s->fetchAll(PDO::FETCH_COLUMN) as $idCancelada) {
            $del->execute([':id' => $idCancelada]);
        }
    }

    public static function agendar($pacienteId, $medicoId, $fecha, $hora, $motivo, $recepcionistaId = null) {
        $conexion = conexion::conectar();
        try {
            $conexion->beginTransaction();
            self::liberarCancelados($conexion, $pacienteId, $medicoId, $fecha, $hora);

            $sql  = "INSERT INTO cita (paciente_id, medico_id, recepcionista_id, estado_cita_id, fecha, hora, motivo)
                     VALUES (:paciente_id, :medico_id, :recepcionista_id,
                             (SELECT id FROM estado_cita WHERE nombre = 'pendiente'),
                             :fecha, :hora, :motivo)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':paciente_id'      => $pacienteId,
                ':medico_id'        => $medicoId,
                ':recepcionista_id' => $recepcionistaId,
                ':fecha'            => $fecha,
                ':hora'             => $hora,
                ':motivo'           => ($motivo !== null && $motivo !== '') ? $motivo : null,
            ]);
            $citaId = (int)$conexion->lastInsertId();
            $conexion->commit();
            return $citaId;
        } catch (PDOException $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log('Error agendar cita: ' . $e->getMessage());
            return false;
        }
    }

    public static function cambiarEstado($citaId, $estado) {
        $conexion = conexion::conectar();
        $sql  = "UPDATE cita
                 SET estado_cita_id = (SELECT id FROM estado_cita WHERE nombre = :estado)
                 WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id',     $citaId);
        return $stmt->execute();
    }

    public static function findById($citaId) {
        $conexion = conexion::conectar();
        $sql  = "SELECT c.*, ec.nombre AS estado, u.nombre, u.apellido
                 FROM cita c
                 JOIN usuario u      ON c.paciente_id = u.id
                 JOIN estado_cita ec ON ec.id = c.estado_cita_id
                 WHERE c.id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $citaId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function todas($buscar = '', $estado = '', $fecha = '') {
        $conexion = conexion::conectar();
        $sql  = "SELECT cita_id AS id, fecha, hora, estado, motivo,
                        CONCAT(paciente_nombre, ' ', paciente_apellido) AS paciente,
                        paciente_ci AS ci,
                        CONCAT('Dr(a). ', medico_nombre, ' ', medico_apellido) AS medico,
                        especialidad
                 FROM vw_citas_detalle
                 WHERE 1=1";

        if (!empty($buscar)) {
            $sql .= " AND (paciente_nombre LIKE :buscar OR paciente_apellido LIKE :buscar2 OR paciente_ci LIKE :buscar3)";
        }
        if (!empty($estado)) {
            $sql .= " AND estado = :estado";
        }
        if (!empty($fecha)) {
            $sql .= " AND fecha = :fecha";
        }

        $sql .= " ORDER BY fecha DESC, hora DESC";

        $stmt = $conexion->prepare($sql);

        if (!empty($buscar)) {
            $like = "%$buscar%";
            $stmt->bindParam(':buscar',  $like);
            $stmt->bindParam(':buscar2', $like);
            $stmt->bindParam(':buscar3', $like);
        }
        if (!empty($estado)) {
            $stmt->bindParam(':estado', $estado);
        }
        if (!empty($fecha)) {
            $stmt->bindParam(':fecha', $fecha);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contadoresHoy($medicoId) {
        $conexion = conexion::conectar();

        $s1 = $conexion->prepare(
            "SELECT COUNT(*) FROM cita c
             JOIN estado_cita ec ON ec.id = c.estado_cita_id
             WHERE c.medico_id = :id AND c.fecha = CURDATE() AND ec.nombre <> 'cancelada'"
        );
        $s1->bindParam(':id', $medicoId);
        $s1->execute();

        $s2 = $conexion->prepare(
            "SELECT COUNT(*) FROM cita c
             JOIN estado_cita ec ON ec.id = c.estado_cita_id
             WHERE c.medico_id = :id AND c.fecha = CURDATE() AND ec.nombre = 'completada'"
        );
        $s2->bindParam(':id', $medicoId);
        $s2->execute();

        return [
            'total_programado'    => $s1->fetchColumn(),
            'pacientes_atendidos' => $s2->fetchColumn(),
        ];
    }

    public static function fichaCompleta($citaId) {
        $conexion = conexion::conectar();
        $sql  = "SELECT fecha, hora, motivo,
                        paciente_nombre, paciente_apellido, paciente_ci AS ci,
                        medico_nombre, medico_apellido
                 FROM vw_citas_detalle
                 WHERE cita_id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $citaId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function citasPorFecha($fecha) {
        $conexion = conexion::conectar();
        $sql = "SELECT cita_id AS id, fecha, hora, estado, motivo,
                       paciente_nombre, paciente_apellido, paciente_ci AS ci,
                       medico_nombre, medico_apellido, especialidad
                FROM vw_citas_detalle
                WHERE fecha = :fecha
                ORDER BY hora ASC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function citasMesMedico($medicoId, $anio = null, $mes = null) {
        $conexion = conexion::conectar();
        $anio = $anio ?? date('Y');
        $mes  = $mes  ?? date('m');
        $sql  = "SELECT c.id, c.fecha, c.hora, ec.nombre AS estado, c.motivo,
                        u.nombre, u.apellido
                 FROM cita c
                 JOIN usuario u      ON c.paciente_id = u.id
                 JOIN estado_cita ec ON ec.id = c.estado_cita_id
                 WHERE c.medico_id = :medico_id
                   AND YEAR(c.fecha)  = :anio
                   AND MONTH(c.fecha) = :mes
                   AND ec.nombre <> 'cancelada'
                 ORDER BY c.fecha ASC, c.hora ASC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':medico_id', $medicoId);
        $stmt->bindParam(':anio',      $anio, PDO::PARAM_INT);
        $stmt->bindParam(':mes',       $mes,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function editarCita($citaId, $medicoId, $fecha, $hora, $motivo) {
        $conexion = conexion::conectar();

        $cita = $conexion->prepare("SELECT paciente_id FROM cita WHERE id = :id");
        $cita->execute([':id' => $citaId]);
        $pacienteId = $cita->fetchColumn();
        if ($pacienteId === false) {
            return 'error';
        }

        $check = $conexion->prepare(
            "SELECT COUNT(*) FROM cita c
             JOIN estado_cita ec ON ec.id = c.estado_cita_id
             WHERE c.fecha = :fecha
               AND c.hora  = :hora
               AND c.id   <> :cita_id
               AND ec.nombre <> 'cancelada'
               AND (c.medico_id = :medico_id OR c.paciente_id = :paciente_id)"
        );
        $check->execute([
            ':fecha'       => $fecha,
            ':hora'        => $hora,
            ':cita_id'     => $citaId,
            ':medico_id'   => $medicoId,
            ':paciente_id' => $pacienteId,
        ]);
        if ($check->fetchColumn() > 0) {
            return 'ocupado';
        }

        try {
            $conexion->beginTransaction();
            self::liberarCancelados($conexion, $pacienteId, $medicoId, $fecha, $hora, $citaId);

            $sql  = "UPDATE cita
                     SET medico_id = :medico_id,
                         fecha     = :fecha,
                         hora      = :hora,
                         motivo    = :motivo
                     WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':medico_id' => $medicoId,
                ':fecha'     => $fecha,
                ':hora'      => $hora,
                ':motivo'    => ($motivo !== null && $motivo !== '') ? $motivo : null,
                ':id'        => $citaId,
            ]);
            $conexion->commit();
            return 'ok';
        } catch (PDOException $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log('Error editarCita: ' . $e->getMessage());
            return 'error';
        }
    }
}
?>
