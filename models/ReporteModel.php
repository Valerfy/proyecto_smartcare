<?php
require_once __DIR__ . '/../config/conexion.php';

class ReporteModel {

    public static function historialParaReporte($medicoId, $fechaDesde = '', $fechaHasta = '', $pacienteId = null) {
        $conexion = conexion::conectar();

        $sql = "SELECT h.diagnostico, h.observacion,
                       a.tipo_sangre, a.enfermedades, a.alergias,
                       (SELECT GROUP_CONCAT(r.medicamento ORDER BY r.id SEPARATOR ', ')
                        FROM receta r WHERE r.historial_id = h.id) AS medicamentos,
                       h.presion_arterial, h.peso, h.talla,
                       c.fecha, c.hora, c.motivo,
                       u.nombre, u.apellido, u.ci, u.fecha_nac,
                       COALESCE(e.nombre, 'Sin especialidad') AS especialidad
                FROM historial h
                INNER JOIN cita c         ON h.cita_id = c.id
                INNER JOIN estado_cita ec ON ec.id = c.estado_cita_id
                INNER JOIN usuario u      ON c.paciente_id = u.id
                INNER JOIN medico m       ON c.medico_id = m.usuario_id
                LEFT JOIN especialidad e  ON m.especialidad_id = e.id
                LEFT JOIN vw_antecedentes_paciente a ON a.paciente_id = c.paciente_id
                WHERE c.medico_id = :medico_id
                  AND ec.nombre = 'completada'";

        if (!empty($fechaDesde)) {
            $sql .= " AND c.fecha >= :fecha_desde";
        }
        if (!empty($fechaHasta)) {
            $sql .= " AND c.fecha <= :fecha_hasta";
        }
        if (!empty($pacienteId)) {
            $sql .= " AND c.paciente_id = :paciente_id";
        }

        $sql .= " ORDER BY c.fecha DESC, c.hora DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':medico_id', $medicoId);

        if (!empty($fechaDesde))  $stmt->bindParam(':fecha_desde',  $fechaDesde);
        if (!empty($fechaHasta))  $stmt->bindParam(':fecha_hasta',  $fechaHasta);
        if (!empty($pacienteId))  $stmt->bindParam(':paciente_id',  $pacienteId);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function estadisticasMedico($medicoId, $fechaDesde = '', $fechaHasta = '') {
        $conexion = conexion::conectar();

        $where = "WHERE c.medico_id = :medico_id";
        if (!empty($fechaDesde)) $where .= " AND c.fecha >= :fecha_desde";
        if (!empty($fechaHasta)) $where .= " AND c.fecha <= :fecha_hasta";

        $sql = "SELECT ec.nombre AS estado, COUNT(*) AS total
                FROM cita c
                INNER JOIN estado_cita ec ON ec.id = c.estado_cita_id
                $where
                GROUP BY ec.nombre";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':medico_id', $medicoId);
        if (!empty($fechaDesde)) $stmt->bindParam(':fecha_desde', $fechaDesde);
        if (!empty($fechaHasta)) $stmt->bindParam(':fecha_hasta', $fechaHasta);
        $stmt->execute();
        $citasPorEstado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql2 = "SELECT COUNT(DISTINCT c.paciente_id) AS pacientes_unicos
                 FROM cita c
                 INNER JOIN estado_cita ec ON ec.id = c.estado_cita_id
                 $where AND ec.nombre = 'completada'";
        $stmt2 = $conexion->prepare($sql2);
        $stmt2->bindParam(':medico_id', $medicoId);
        if (!empty($fechaDesde)) $stmt2->bindParam(':fecha_desde', $fechaDesde);
        if (!empty($fechaHasta)) $stmt2->bindParam(':fecha_hasta', $fechaHasta);
        $stmt2->execute();
        $pacientesUnicos = $stmt2->fetchColumn();

        return [
            'citas_por_estado' => $citasPorEstado,
            'pacientes_unicos' => $pacientesUnicos,
        ];
    }

    public static function pacientesParaFiltro($medicoId) {
        $conexion = conexion::conectar();
        $sql = "SELECT DISTINCT u.id, u.nombre, u.apellido, u.ci
                FROM usuario u
                JOIN cita c ON c.paciente_id = u.id
                JOIN estado_cita ec ON ec.id = c.estado_cita_id
                WHERE c.medico_id = :medico_id AND ec.nombre = 'completada'
                ORDER BY u.apellido ASC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':medico_id', $medicoId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>