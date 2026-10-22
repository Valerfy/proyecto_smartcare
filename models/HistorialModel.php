<?php
require_once __DIR__ . '/../config/conexion.php';

class HistorialModel {

    private const SQL_MEDICAMENTOS =
        "(SELECT GROUP_CONCAT(r.medicamento ORDER BY r.id SEPARATOR ', ')
          FROM receta r WHERE r.historial_id = h.id) AS medicamentos";

    public static function historialPaciente($pacienteId) {
        $conexion = conexion::conectar();

        $sql = "SELECT h.diagnostico, h.observacion,
                       a.tipo_sangre, a.enfermedades, a.alergias,
                       " . self::SQL_MEDICAMENTOS . ",
                       h.presion_arterial, h.peso, h.talla,
                       c.fecha, c.hora, ec.nombre AS estado, c.motivo,
                       CONCAT(um.nombre, ' ', um.apellido) AS medico,
                       COALESCE(e.nombre, 'Sin especialidad') AS especialidad
                FROM historial h
                INNER JOIN cita c         ON h.cita_id = c.id
                INNER JOIN estado_cita ec ON ec.id = c.estado_cita_id
                INNER JOIN usuario um     ON um.id = c.medico_id
                INNER JOIN medico m       ON m.usuario_id = c.medico_id
                LEFT JOIN especialidad e  ON m.especialidad_id = e.id
                LEFT JOIN vw_antecedentes_paciente a ON a.paciente_id = c.paciente_id
                WHERE c.paciente_id = :paciente_id
                ORDER BY c.fecha DESC, c.hora DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':paciente_id', $pacienteId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function historialMedico($medicoId, $buscar = '') {
        $conexion = conexion::conectar();

        $sql = "SELECT h.diagnostico, h.observacion,
                       a.tipo_sangre, a.enfermedades, a.alergias,
                       " . self::SQL_MEDICAMENTOS . ",
                       h.presion_arterial, h.peso, h.talla,
                       c.fecha, c.hora, c.motivo,
                       u.nombre, u.apellido, u.ci
                FROM historial h
                INNER JOIN cita c         ON h.cita_id = c.id
                INNER JOIN estado_cita ec ON ec.id = c.estado_cita_id
                INNER JOIN usuario u      ON c.paciente_id = u.id
                LEFT JOIN vw_antecedentes_paciente a ON a.paciente_id = c.paciente_id
                WHERE c.medico_id = :medico_id
                  AND ec.nombre = 'completada'";

        if (!empty($buscar)) {
            $sql .= " AND (
                        u.nombre LIKE :buscar
                        OR u.apellido LIKE :buscar2
                        OR u.ci LIKE :buscar3
                      )";
        }

        $sql .= " ORDER BY c.fecha DESC";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':medico_id', $medicoId);

        if (!empty($buscar)) {
            $like = "%$buscar%";
            $stmt->bindParam(':buscar', $like);
            $stmt->bindParam(':buscar2', $like);
            $stmt->bindParam(':buscar3', $like);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function separarLista($texto, int $maxLargo): array {
        $items = [];
        foreach (preg_split('/[\r\n,;]+/', (string)$texto) as $parte) {
            $parte = trim($parte);
            if ($parte === '') continue;
            $items[mb_strtolower($parte, 'UTF-8')] = mb_substr($parte, 0, $maxLargo, 'UTF-8');
        }
        return array_values($items);
    }

    public static function guardarDiagnostico($citaId, $diagnostico, $observacion,
                                               $tipo_sangre, $enfermedades, $alergias,
                                               $medicamentos, $presion_arterial, $peso, $talla) {
        $conexion = conexion::conectar();
        try {
            $conexion->beginTransaction();

            $c = $conexion->prepare("SELECT paciente_id FROM cita WHERE id = :id");
            $c->execute([':id' => $citaId]);
            $pacienteId = $c->fetchColumn();
            if ($pacienteId === false) {
                $conexion->rollBack();
                return false;
            }

            $datos = [
                ':diagnostico'      => $diagnostico,
                ':observacion'      => ($observacion !== '' ? $observacion : null),
                ':presion_arterial' => ($presion_arterial !== '' ? $presion_arterial : null),
                ':peso'             => $peso,
                ':talla'            => $talla,
            ];

            $h = $conexion->prepare("SELECT id FROM historial WHERE cita_id = :cita_id");
            $h->execute([':cita_id' => $citaId]);
            $historialId = $h->fetchColumn();

            if ($historialId === false) {
                $ins = $conexion->prepare(
                    "INSERT INTO historial (cita_id, diagnostico, observacion, presion_arterial, peso, talla)
                     VALUES (:cita_id, :diagnostico, :observacion, :presion_arterial, :peso, :talla)"
                );
                $ins->execute($datos + [':cita_id' => $citaId]);
                $historialId = (int)$conexion->lastInsertId();
            } else {
                $upd = $conexion->prepare(
                    "UPDATE historial
                     SET diagnostico = :diagnostico, observacion = :observacion,
                         presion_arterial = :presion_arterial, peso = :peso, talla = :talla
                     WHERE id = :id"
                );
                $upd->execute($datos + [':id' => $historialId]);
                $conexion->prepare("DELETE FROM receta WHERE historial_id = :id")
                         ->execute([':id' => $historialId]);
            }

            $rec = $conexion->prepare("INSERT INTO receta (historial_id, medicamento) VALUES (:historial_id, :medicamento)");
            foreach (self::separarLista($medicamentos, 150) as $med) {
                $rec->execute([':historial_id' => $historialId, ':medicamento' => $med]);
            }

            if ($tipo_sangre !== '' && $tipo_sangre !== null) {
                $conexion->prepare("UPDATE paciente SET tipo_sangre = :ts WHERE usuario_id = :id")
                         ->execute([':ts' => $tipo_sangre, ':id' => $pacienteId]);
            }

            $ant = $conexion->prepare(
                "INSERT IGNORE INTO antecedente (paciente_id, tipo, descripcion) VALUES (:paciente_id, :tipo, :descripcion)"
            );
            foreach (self::separarLista($enfermedades, 200) as $d) {
                $ant->execute([':paciente_id' => $pacienteId, ':tipo' => 'enfermedad', ':descripcion' => $d]);
            }
            foreach (self::separarLista($alergias, 200) as $d) {
                $ant->execute([':paciente_id' => $pacienteId, ':tipo' => 'alergia', ':descripcion' => $d]);
            }

            $conexion->prepare(
                "UPDATE cita
                 SET estado_cita_id = (SELECT id FROM estado_cita WHERE nombre = 'completada')
                 WHERE id = :cita_id"
            )->execute([':cita_id' => $citaId]);

            $conexion->commit();
            return true;
        } catch (Exception $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log('Error guardarDiagnostico: ' . $e->getMessage());
            return false;
        }
    }
}
?>
