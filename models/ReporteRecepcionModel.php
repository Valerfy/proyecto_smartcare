<?php
require_once __DIR__ . '/../config/conexion.php';

class ReporteRecepcionModel {
        public static function filtrosDesdeGet(array $get): array {
        $f = [
            'q'           => trim($get['q'] ?? ''),
            'medico_id'   => (int)($get['medico_id'] ?? 0),
            'fecha_desde' => trim($get['fecha_desde'] ?? ''),
            'fecha_hasta' => trim($get['fecha_hasta'] ?? ''),
            'tipo'        => in_array(($get['tipo'] ?? ''), ['Nuevo', 'Antiguo'], true) ? $get['tipo'] : '',
        ];
        foreach (['fecha_desde', 'fecha_hasta'] as $k) {
            if ($f[$k] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $f[$k])) {
                $f[$k] = '';
            }
        }
        return $f;
    }

    public static function listarFichas(array $f = []) {

        $db = conexion::conectar();

        $where  = [];
        $params = [];

        if (!empty($f['q'])) {
            $where[] = "(CONCAT(paciente_nombre, ' ', paciente_apellido) LIKE :q1
                         OR paciente_ci LIKE :q2)";
            $params[':q1'] = '%' . $f['q'] . '%';
            $params[':q2'] = '%' . $f['q'] . '%';
        }
        if (!empty($f['medico_id'])) {
            $where[] = 'medico_id = :medico';
            $params[':medico'] = (int)$f['medico_id'];
        }
        if (!empty($f['fecha_desde'])) {
            $where[] = 'fecha >= :desde';
            $params[':desde'] = $f['fecha_desde'];
        }
        if (!empty($f['fecha_hasta'])) {
            $where[] = 'fecha <= :hasta';
            $params[':hasta'] = $f['fecha_hasta'];
        }
        if (($f['tipo'] ?? '') === 'Nuevo') {
            $where[] = 'DATE(paciente_creado_en) = fecha';
        } elseif (($f['tipo'] ?? '') === 'Antiguo') {
            $where[] = 'DATE(paciente_creado_en) != fecha';
        }

        $sql = "SELECT
                cita_id AS id,

                paciente_nombre,
                paciente_apellido,
                paciente_ci AS ci,

                medico_nombre,
                medico_apellido,

                paciente_creado_en AS creado_en,
                fecha,
                hora,

                CASE
                    WHEN DATE(paciente_creado_en) = fecha THEN 'Nuevo'
                    ELSE 'Antiguo'
                END AS tipo_usuario

            FROM vw_citas_detalle
        ";

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY fecha DESC, hora DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function totalFichas() {

        $db = conexion::conectar();

        $sql = "SELECT COUNT(*) as total
            FROM cita
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function pacientesNuevos() {

        $db = conexion::conectar();

        $sql = "SELECT COUNT(*) as total
            FROM vw_citas_detalle
            WHERE DATE(paciente_creado_en) = fecha
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function pacientesAntiguos() {

        $db = conexion::conectar();

        $sql = "SELECT COUNT(*) as total
            FROM vw_citas_detalle
            WHERE DATE(paciente_creado_en) != fecha
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}