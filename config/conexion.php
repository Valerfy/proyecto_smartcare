<?php
class conexion {
    public static function conectar() {
        $host     = "localhost";
        $db       = "cns_sistema_v2";
        $user     = "root";
        $password = "";

        try {
            $conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
?>