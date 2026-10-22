<?php
require_once __DIR__ . '/../config/conexion.php';

class UsuarioModel {

    private const ROLES_BD = ['paciente', 'medico', 'recepcionista', 'administrador'];

    private static function nombreRolBD($rol): string {
        $rol = (string)$rol;
        return $rol === 'admin' ? 'administrador' : $rol;
    }

    private static function tablaSubtipo(string $rolBD): string {
        return $rolBD;
    }

    public static function autenticar($correo, $clave) {
        $conexion = conexion::conectar();
        $sql  = "SELECT u.*,
                        CASE r.nombre WHEN 'administrador' THEN 'admin' ELSE r.nombre END AS rol
                 FROM usuario u
                 INNER JOIN rol r ON r.id = u.rol_id
                 WHERE u.correo = :correo LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario || empty($usuario['clave']) || !password_verify($clave, $usuario['clave'])) {
            return false;
        }
        return $usuario;
    }

    public static function findById($id) {
        $conexion = conexion::conectar();
        $sql  = "SELECT * FROM vw_usuarios_roles WHERE id = :id LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByCorreo($correo) {
        $conexion = conexion::conectar();
        $sql  = "SELECT * FROM vw_usuarios_roles WHERE correo = :correo LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function listar() {
        $conexion = conexion::conectar();
        $sql  = "SELECT * FROM vw_usuarios_roles ORDER BY id DESC";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function registrar($nombre, $apellido, $ci, $telefono, $correo,
                                      $clave, $fecha_nac, $direccion, $matricula,
                                      $ocupacion, $foto, $rol = 'paciente', $especialidadId = null) {
        $rolBD = self::nombreRolBD($rol);
        if (!in_array($rolBD, self::ROLES_BD, true)) {
            throw new InvalidArgumentException('Rol inválido.');
        }
        if ($rolBD === 'medico' && !$especialidadId) {
            throw new InvalidArgumentException('Un médico necesita especialidad.');
        }

        $conexion  = conexion::conectar();
        $claveHash = password_hash($clave, PASSWORD_DEFAULT);
        $fecha_nac = ($fecha_nac !== null && $fecha_nac !== '') ? $fecha_nac : null;

        try {
            $conexion->beginTransaction();

            $sql = "INSERT INTO usuario
                        (rol_id, ci, nombre, apellido, correo, clave, telefono,
                         fecha_nac, direccion, foto, estado)
                    VALUES
                        ((SELECT id FROM rol WHERE nombre = :rol), :ci, :nombre, :apellido,
                         :correo, :clave, :telefono, :fecha_nac, :direccion, :foto, 1)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':rol'       => $rolBD,
                ':ci'        => $ci,
                ':nombre'    => $nombre,
                ':apellido'  => $apellido,
                ':correo'    => $correo,
                ':clave'     => $claveHash,
                ':telefono'  => $telefono,
                ':fecha_nac' => $fecha_nac,
                ':direccion' => $direccion,
                ':foto'      => $foto,
            ]);
            $nuevoId = (int)$conexion->lastInsertId();

            self::crearSubtipo($conexion, $nuevoId, $rolBD, $matricula, $ocupacion, $especialidadId);

            $conexion->commit();
            return $nuevoId;
        } catch (Exception $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $e;
        }
    }

    private static function crearSubtipo(PDO $conexion, int $id, string $rolBD,
                                         $matricula = null, $ocupacion = null, $especialidadId = null): void {
        switch ($rolBD) {
            case 'paciente':
                $stmt = $conexion->prepare("INSERT INTO paciente (usuario_id, ocupacion) VALUES (:id, :ocupacion)");
                $stmt->execute([
                    ':id'        => $id,
                    ':ocupacion' => ($ocupacion !== null && $ocupacion !== '') ? $ocupacion : null,
                ]);
                break;

            case 'medico':
                $stmt = $conexion->prepare(
                    "INSERT INTO medico (usuario_id, especialidad_id, matricula) VALUES (:id, :esp, :matricula)"
                );
                $stmt->execute([
                    ':id'        => $id,
                    ':esp'       => (int)$especialidadId,
                    ':matricula' => ($matricula !== null && $matricula !== '') ? $matricula : null,
                ]);
                break;

            case 'recepcionista':
            case 'administrador':
                $tabla = self::tablaSubtipo($rolBD);
                $stmt  = $conexion->prepare("INSERT INTO $tabla (usuario_id) VALUES (:id)");
                $stmt->execute([':id' => $id]);
                break;
        }
    }

    public static function actualizar($id, $nombre, $apellido, $ci, $correo,
                                       $rol, $matricula, $telefono = null, $direccion = null,
                                       $especialidadId = null) {
        $rolNuevo = self::nombreRolBD($rol);
        if (!in_array($rolNuevo, self::ROLES_BD, true)) {
            throw new InvalidArgumentException('Rol inválido.');
        }

        $conexion = conexion::conectar();
        try {
            $conexion->beginTransaction();

            $s = $conexion->prepare("SELECT r.nombre FROM usuario u INNER JOIN rol r ON r.id = u.rol_id WHERE u.id = :id");
            $s->execute([':id' => $id]);
            $rolActual = $s->fetchColumn();
            if ($rolActual === false) {
                $conexion->rollBack();
                return false;
            }

            $sql  = "UPDATE usuario
                     SET nombre = :nombre, apellido = :apellido, ci = :ci,
                         correo = :correo,
                         rol_id = (SELECT id FROM rol WHERE nombre = :rol),
                         telefono  = COALESCE(:telefono, telefono),
                         direccion = COALESCE(:direccion, direccion)
                     WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':id'        => $id,
                ':nombre'    => $nombre,
                ':apellido'  => $apellido,
                ':ci'        => $ci,
                ':correo'    => $correo,
                ':rol'       => $rolNuevo,
                ':telefono'  => $telefono,
                ':direccion' => $direccion,
            ]);

            if ($rolActual !== $rolNuevo) {
                $del = $conexion->prepare("DELETE FROM " . self::tablaSubtipo($rolActual) . " WHERE usuario_id = :id");
                $del->execute([':id' => $id]);
                self::crearSubtipo($conexion, (int)$id, $rolNuevo, $matricula, null, $especialidadId);
            } elseif ($rolNuevo === 'medico' && $especialidadId) {
                $upd = $conexion->prepare(
                    "UPDATE medico SET especialidad_id = :esp, matricula = :matricula WHERE usuario_id = :id"
                );
                $upd->execute([
                    ':esp'       => (int)$especialidadId,
                    ':matricula' => ($matricula !== null && $matricula !== '') ? $matricula : null,
                    ':id'        => $id,
                ]);
            }

            $conexion->commit();
            return true;
        } catch (Exception $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $e;
        }
    }

    public static function actualizarPaciente($id, $nombre, $apellido, $ci,
                                               $fecha_nac, $telefono, $direccion) {
        $conexion = conexion::conectar();
        $fecha_nac = ($fecha_nac !== null && $fecha_nac !== '') ? $fecha_nac : null;
        $sql  = "UPDATE usuario
                 SET nombre = :nombre, apellido = :apellido, ci = :ci,
                     fecha_nac = :fecha_nac, telefono = :telefono, direccion = :direccion
                 WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id',        $id);
        $stmt->bindParam(':nombre',    $nombre);
        $stmt->bindParam(':apellido',  $apellido);
        $stmt->bindParam(':ci',        $ci);
        $stmt->bindParam(':fecha_nac', $fecha_nac);
        $stmt->bindParam(':telefono',  $telefono);
        $stmt->bindParam(':direccion', $direccion);
        return $stmt->execute();
    }

    public static function cambiarEstado($id, $estado) {
        $conexion = conexion::conectar();
        $sql  = "UPDATE usuario SET estado = :estado WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id',     $id);
        return $stmt->execute();
    }

    public static function existeUsuario($ci, $correo, $telefono) {
        $conexion = conexion::conectar();
        $sql  = "SELECT id FROM usuario WHERE ci = :ci OR correo = :correo OR telefono = :telefono LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':ci',       $ci);
        $stmt->bindParam(':correo',   $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->execute();
        return (bool)$stmt->fetch();
    }

    public static function obtenerNotificaciones($uid) {
        $conexion = conexion::conectar();
        $sql  = "SELECT n.titulo, n.mensaje, tn.nombre AS tipo, n.leida, n.creado_en
                 FROM notificacion n
                 INNER JOIN tipo_notificacion tn ON tn.id = n.tipo_notificacion_id
                 WHERE n.paciente_id = :uid
                 ORDER BY n.creado_en DESC, n.id DESC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function marcarNotificacionesLeidas($uid) {
        $conexion = conexion::conectar();
        $sql  = "UPDATE notificacion SET leida = 1 WHERE paciente_id = :uid";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        return $stmt->execute();
    }

    public static function obtenerSeguro($uid) {
        $conexion = conexion::conectar();
        $sql  = "SELECT s.id, s.paciente_id,
                        s.nro_asegurado AS num_asegurado,
                        COALESCE(emp.nombre, 'Sin empresa') AS empresa,
                        CASE WHEN s.activo = 1 THEN 'activo' ELSE 'inactivo' END AS estado,
                        NULL AS vigencia
                 FROM seguro s
                 LEFT JOIN empresa emp ON emp.id = s.empresa_id
                 WHERE s.paciente_id = :uid LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerPagos($uid) {
        $conexion = conexion::conectar();
        $sql  = "SELECT id, concepto, monto, estado, fecha
                 FROM vw_pagos_paciente
                 WHERE paciente_id = :uid
                 ORDER BY fecha DESC, id DESC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerSaldoPendiente($uid) {
        $conexion = conexion::conectar();
        $sql  = "SELECT COALESCE(SUM(monto), 0) AS saldo
                 FROM vw_pagos_paciente
                 WHERE paciente_id = :uid AND estado IN ('pendiente','vencido')";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    public static function actualizarFoto($id, $foto) {
        $conexion = conexion::conectar();
        $sql  = "UPDATE usuario SET foto = :foto WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':id',   $id);
        return $stmt->execute();
    }

    public static function listarPacientes() {
        $conexion = conexion::conectar();
        $sql  = "SELECT id, nombre, apellido, ci, telefono
                 FROM vw_usuarios_roles
                 WHERE rol = 'paciente' AND estado = 1
                 ORDER BY apellido ASC";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crearNotificacion($pacienteId, $titulo, $mensaje, $tipo = 'info', $citaId = null) {
        $db   = conexion::conectar();
        $sql  = "INSERT INTO notificacion (paciente_id, cita_id, tipo_notificacion_id, titulo, mensaje)
                 VALUES (:paciente_id, :cita_id,
                         COALESCE((SELECT id FROM tipo_notificacion WHERE nombre = :tipo),
                                  (SELECT id FROM tipo_notificacion WHERE nombre = 'info')),
                         :titulo, :mensaje)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':paciente_id' => $pacienteId,
            ':cita_id'     => $citaId,
            ':tipo'        => $tipo,
            ':titulo'      => $titulo,
            ':mensaje'     => $mensaje,
        ]);
    }
}
?>
