<?php
require_once __DIR__ . '/../config/conexion.php';

class ReporteRecepcionModel {

    public static function listarFichas() {

        $db = conexion::conectar();

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

            ORDER BY fecha DESC, hora DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute();

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