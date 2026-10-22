SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `cns_sistema_v2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cns_sistema_v2`;

CREATE TABLE `administrador` (
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `administrador` (`usuario_id`) VALUES
(1),
(21),
(22),
(23),
(24);

CREATE TABLE `antecedente` (
  `id` int(10) UNSIGNED NOT NULL,
  `paciente_id` int(10) UNSIGNED NOT NULL,
  `tipo` enum('enfermedad','alergia') NOT NULL,
  `descripcion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cita` (
  `id` int(10) UNSIGNED NOT NULL,
  `paciente_id` int(10) UNSIGNED NOT NULL,
  `medico_id` int(10) UNSIGNED NOT NULL,
  `recepcionista_id` int(10) UNSIGNED DEFAULT NULL,
  `estado_cita_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cita` (`id`, `paciente_id`, `medico_id`, `recepcionista_id`, `estado_cita_id`, `fecha`, `hora`, `motivo`, `creado_en`) VALUES
(1, 9, 4, 2, 3, '2026-09-02', '08:30:00', 'Control general anual', '2026-10-05 02:48:26'),
(2, 20, 5, 2, 3, '2026-09-01', '09:00:00', 'Fiebre y tos de 3 días', '2026-10-05 02:48:26'),
(3, 11, 6, 3, 3, '2026-09-07', '14:30:00', 'Dolor en el pecho al esfuerzo', '2026-10-05 02:48:26'),
(4, 12, 7, 3, 3, '2026-09-08', '08:30:00', 'Control prenatal', '2026-10-05 02:48:26'),
(6, 13, 8, 3, 3, '2026-09-14', '09:30:00', 'Dolor de rodilla derecha', '2026-10-05 02:48:26'),
(7, 14, 4, 2, 3, '2026-09-18', '14:30:00', 'Dolor abdominal', '2026-10-05 02:48:26'),
(8, 15, 6, 2, 3, '2026-09-21', '15:00:00', 'Control de presión arterial', '2026-10-05 02:48:26'),
(9, 19, 5, 3, 4, '2026-09-22', '10:00:00', 'Vacunas y control de crecimiento', '2026-10-05 02:48:26'),
(10, 16, 7, 2, 3, '2026-09-24', '09:00:00', 'Examen ginecológico de rutina', '2026-10-05 02:48:26'),
(11, 9, 4, 2, 2, '2026-10-07', '08:30:00', 'Resultados de laboratorio', '2026-10-05 02:48:26'),
(12, 10, 6, 3, 2, '2026-10-05', '14:30:00', 'Palpitaciones', '2026-10-05 02:48:26'),
(13, 17, 8, 3, 1, '2026-10-09', '10:00:00', 'Dolor lumbar', '2026-10-05 02:48:26'),
(14, 18, 5, 2, 2, '2026-10-06', '09:30:00', 'Alergia en la piel', '2026-10-05 02:48:26'),
(15, 11, 6, 3, 1, '2026-10-07', '15:00:00', 'Seguimiento de cardiología', '2026-10-05 02:48:26'),
(16, 20, 5, 2, 1, '2026-10-08', '09:00:00', 'Control posterior a la fiebre', '2026-10-05 02:48:26'),
(17, 12, 7, 3, 2, '2026-10-08', '08:30:00', 'Control prenatal mensual', '2026-10-05 02:48:26'),
(18, 14, 4, 2, 1, '2026-10-09', '14:30:00', 'Control de dolor abdominal', '2026-10-05 02:48:26'),
(19, 13, 8, NULL, 1, '2026-10-12', '09:30:00', 'Revisión de rodilla con radiografía', '2026-10-05 02:48:26'),
(20, 9, 4, 2, 1, '2026-10-14', '08:30:00', 'Control de glucosa', '2026-10-05 02:48:26');

CREATE TABLE `empresa` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `empresa` (`id`, `nombre`) VALUES
(1, 'Banco Unión S.A.'),
(2, 'Entel S.A.'),
(3, 'Ferretería El Constructor'),
(4, 'Gobierno Autónomo Municipal de La Paz');

CREATE TABLE `especialidad` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `costo_consulta` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `especialidad` (`id`, `nombre`, `costo_consulta`) VALUES
(1, 'Medicina General', 100.00),
(2, 'Pediatría', 120.00),
(3, 'Cardiología', 200.00),
(4, 'Ginecología', 150.00),
(5, 'Traumatología', 150.00),
(6, 'Medicina Interna', 150.00),
(7, 'Dermatología', 100.00);

CREATE TABLE `estado_cita` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `estado_cita` (`id`, `nombre`) VALUES
(4, 'cancelada'),
(3, 'completada'),
(2, 'confirmada'),
(1, 'pendiente');

CREATE TABLE `historial` (
  `id` int(10) UNSIGNED NOT NULL,
  `cita_id` int(10) UNSIGNED NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `presion_arterial` varchar(20) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `talla` decimal(4,2) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `historial` (`id`, `cita_id`, `diagnostico`, `observacion`, `presion_arterial`, `peso`, `talla`, `creado_en`) VALUES
(1, 1, 'Paciente sano. Sin hallazgos relevantes.', 'Se recomienda control anual y mantener actividad física.', NULL, NULL, NULL, '2026-10-05 02:48:26'),
(2, 2, 'Infección respiratoria aguda.', 'Reposo, hidratación y paracetamol según indicación. Control en una semana.', NULL, NULL, NULL, '2026-10-05 02:48:26'),
(3, 3, 'Angina de esfuerzo en estudio.', 'Se solicita electrocardiograma y ecocardiograma. Evitar esfuerzos intensos.', NULL, NULL, NULL, '2026-10-05 02:48:26'),
(4, 4, 'Embarazo de 20 semanas, evolución normal.', 'Continuar con ácido fólico y hierro. Próximo control en 4 semanas.', NULL, NULL, NULL, '2026-10-05 02:48:26'),
(5, 6, 'Gonartrosis leve de rodilla derecha.', 'Fisioterapia y antiinflamatorios. Solicitar radiografía de control.', NULL, NULL, NULL, '2026-10-05 02:48:26'),
(6, 7, 'Gastritis aguda.', 'Dieta blanda, protector gástrico. Control en 3 semanas.', NULL, NULL, NULL, '2026-10-05 02:48:26'),
(7, 8, 'Hipertensión arterial controlada.', 'Mantener tratamiento actual. Dieta baja en sal.', NULL, NULL, NULL, '2026-10-05 02:48:26'),
(8, 10, 'Control ginecológico normal.', 'Se realiza Papanicolaou. Resultados en 15 días.', NULL, NULL, NULL, '2026-10-05 02:48:26');

CREATE TABLE `horario_medico` (
  `id` int(10) UNSIGNED NOT NULL,
  `medico_id` int(10) UNSIGNED NOT NULL,
  `dia_semana` tinyint(3) UNSIGNED NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `horario_medico` (`id`, `medico_id`, `dia_semana`, `hora_inicio`, `hora_fin`) VALUES
(1, 4, 1, '08:00:00', '12:00:00'),
(2, 4, 3, '08:00:00', '12:00:00'),
(3, 4, 5, '14:00:00', '18:00:00'),
(4, 5, 2, '09:00:00', '13:00:00'),
(5, 5, 4, '09:00:00', '13:00:00'),
(6, 6, 1, '14:00:00', '18:00:00'),
(7, 6, 3, '14:00:00', '18:00:00'),
(8, 7, 2, '08:00:00', '13:00:00'),
(9, 7, 4, '08:00:00', '13:00:00'),
(10, 8, 1, '09:00:00', '13:00:00'),
(11, 8, 5, '09:00:00', '13:00:00'),
(12, 29, 1, '08:00:00', '12:00:00'),
(13, 29, 3, '08:00:00', '12:00:00'),
(14, 30, 2, '14:00:00', '18:00:00'),
(15, 30, 4, '14:00:00', '18:00:00'),
(16, 31, 1, '14:00:00', '18:00:00'),
(17, 31, 5, '08:00:00', '12:00:00'),
(18, 32, 3, '14:00:00', '18:00:00'),
(19, 32, 5, '14:00:00', '18:00:00');

CREATE TABLE `medico` (
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `especialidad_id` tinyint(3) UNSIGNED NOT NULL,
  `matricula` varchar(30) DEFAULT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `medico` (`usuario_id`, `especialidad_id`, `matricula`, `disponible`) VALUES
(4, 1, 'MP-1001', 1),
(5, 2, 'MP-1002', 1),
(6, 3, 'MP-1003', 1),
(7, 4, 'MP-1004', 1),
(8, 5, 'MP-1005', 1),
(29, 1, 'MP-2001', 1),
(30, 2, 'MP-2002', 1),
(31, 3, 'MP-2003', 1),
(32, 4, 'MP-2004', 1);

CREATE TABLE `notificacion` (
  `id` int(10) UNSIGNED NOT NULL,
  `paciente_id` int(10) UNSIGNED NOT NULL,
  `cita_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo_notificacion_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notificacion` (`id`, `paciente_id`, `cita_id`, `tipo_notificacion_id`, `titulo`, `mensaje`, `leida`, `creado_en`) VALUES
(1, 9, NULL, 2, 'Cita confirmada', 'Tu cita del 07/10/2026 a las 08:30 con el Dr. Andrés Vargas fue confirmada.', 0, '2026-10-05 02:48:26'),
(2, 10, NULL, 2, 'Cita confirmada', 'Tu cita del 05/10/2026 a las 14:30 con el Dr. Javier Torrez fue confirmada.', 0, '2026-10-05 02:48:26'),
(3, 10, NULL, 3, 'Pago pendiente', 'Tienes un pago pendiente de Bs. 120,00 por reserva de consulta.', 0, '2026-10-05 02:48:26'),
(4, 11, NULL, 1, 'Resultados disponibles', 'Ya puedes ver tu ecocardiograma en la sección Documentos.', 1, '2026-10-05 02:48:26'),
(5, 11, NULL, 3, 'Pago pendiente', 'Tienes un pago pendiente de Bs. 250,00 por ecocardiograma.', 0, '2026-10-05 02:48:26'),
(6, 12, NULL, 1, 'Recordatorio de cita', 'Tu control prenatal es el 08/10/2026 a las 08:30.', 0, '2026-10-05 02:48:26'),
(7, 13, NULL, 4, 'Pago vencido', 'Tu pago de Bs. 70,00 por radiografía de rodilla está vencido.', 0, '2026-10-05 02:48:26'),
(8, 14, NULL, 3, 'Pago pendiente', 'Tienes un pago pendiente de Bs. 150,00 por ecografía abdominal.', 1, '2026-10-05 02:48:26'),
(9, 15, NULL, 4, 'Seguro inactivo', 'Tu seguro figura como inactivo. Comunícate con recepción para regularizarlo.', 0, '2026-10-05 02:48:26'),
(10, 16, NULL, 2, 'Resultados disponibles', 'Tu resultado de Papanicolaou ya está disponible.', 1, '2026-10-05 02:48:26');

CREATE TABLE `paciente` (
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `tipo_sangre` varchar(3) DEFAULT NULL,
  `ocupacion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `paciente` (`usuario_id`, `tipo_sangre`, `ocupacion`) VALUES
(9, NULL, 'Contador'),
(10, NULL, 'Docente'),
(11, NULL, 'Comerciante'),
(12, NULL, 'Abogada'),
(13, NULL, 'Albañil'),
(14, NULL, 'Enfermera'),
(15, NULL, 'Ingeniero'),
(16, NULL, 'Estudiante'),
(17, NULL, 'Jubilado'),
(18, NULL, 'Estudiante'),
(19, NULL, NULL),
(20, NULL, NULL),
(33, NULL, 'Empleado'),
(34, NULL, 'Docente'),
(35, NULL, 'Estudiante'),
(36, NULL, 'Comerciante');

CREATE TABLE `recepcionista` (
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `recepcionista` (`usuario_id`) VALUES
(2),
(3),
(25),
(26),
(27),
(28);

CREATE TABLE `receta` (
  `id` int(10) UNSIGNED NOT NULL,
  `historial_id` int(10) UNSIGNED NOT NULL,
  `medicamento` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rol` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rol` (`id`, `nombre`) VALUES
(4, 'administrador'),
(2, 'medico'),
(1, 'paciente'),
(3, 'recepcionista');

CREATE TABLE `seguro` (
  `id` int(10) UNSIGNED NOT NULL,
  `paciente_id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED DEFAULT NULL,
  `nro_asegurado` varchar(30) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `seguro` (`id`, `paciente_id`, `empresa_id`, `nro_asegurado`, `activo`) VALUES
(1, 9, 1, 'CNS-2024-0001', 1),
(2, 10, 4, 'CNS-2024-0002', 1),
(3, 11, 3, 'CNS-2024-0003', 1),
(4, 12, 2, 'CNS-2024-0004', 1),
(5, 13, 3, 'CNS-2024-0005', 1),
(6, 14, 4, 'CNS-2024-0006', 1),
(7, 15, NULL, 'CNS-2024-0007', 0);

CREATE TABLE `tipo_notificacion` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tipo_notificacion` (`id`, `nombre`) VALUES
(3, 'advertencia'),
(2, 'exito'),
(1, 'info'),
(4, 'peligro');

CREATE TABLE `usuario` (
  `id` int(10) UNSIGNED NOT NULL,
  `rol_id` tinyint(3) UNSIGNED NOT NULL,
  `ci` varchar(20) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `clave` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuario` (`id`, `rol_id`, `ci`, `nombre`, `apellido`, `correo`, `clave`, `telefono`, `fecha_nac`, `direccion`, `foto`, `estado`, `creado_en`) VALUES
(1, 4, '4521001', 'Carlos', 'Mamani Quispe', 'admin@cns.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '71234501', NULL, NULL, NULL, 1, '2026-10-05 02:48:26'),
(2, 3, '4521002', 'Rosa', 'Choque Flores', 'rosa.choque@cns.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '71234502', NULL, NULL, NULL, 1, '2026-10-05 02:48:26'),
(3, 3, '4521003', 'Luis', 'Condori Apaza', 'luis.condori@cns.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '71234503', NULL, NULL, NULL, 1, '2026-10-05 02:48:26'),
(4, 2, '5632004', 'Andrés', 'Vargas Rojas', 'andres.vargas@cns.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '72345604', NULL, NULL, NULL, 1, '2026-10-05 02:48:26'),
(5, 2, '5632005', 'Patricia', 'Salazar Mendoza', 'patricia.salazar@cns.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '72345605', NULL, NULL, NULL, 1, '2026-10-05 02:48:26'),
(6, 2, '5632006', 'Javier', 'Torrez Camacho', 'javier.torrez@cns.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '72345606', NULL, NULL, NULL, 1, '2026-10-05 02:48:26'),
(7, 2, '5632007', 'Mariana', 'Gutiérrez Ortiz', 'mariana.gutierrez@cns.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '72345607', NULL, NULL, NULL, 1, '2026-10-05 02:48:26'),
(8, 2, '5632008', 'Fernando', 'Ríos Cussi', 'fernando.rios@cns.bo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '72345608', '1980-03-15', 'Calle 21 de Calacoto Nº 345, La Paz', NULL, 1, '2026-10-05 02:48:26'),
(9, 1, '6743009', 'Juan', 'Pérez Gómez', 'juan.perez@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123409', '1985-07-22', 'Av. Buenos Aires Nº 1020, La Paz', NULL, 1, '2026-10-05 02:48:26'),
(10, 1, '6743010', 'María', 'Fernández Lima', 'maria.fernandez@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123410', '1990-11-05', 'Calle Sucre Nº 78, La Paz', NULL, 1, '2026-10-05 02:48:26'),
(11, 1, '6743011', 'Pedro', 'Quispe Mamani', 'pedro.quispe@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123411', '1962-01-30', 'Zona Villa Fátima, Calle 5 Nº 12, La Paz', NULL, 1, '2026-10-05 02:48:26'),
(12, 1, '6743012', 'Lucía', 'Mendoza Paz', 'lucia.mendoza@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123412', '1994-09-12', 'Av. Arce Nº 2500, La Paz', NULL, 1, '2026-10-05 02:48:26'),
(13, 1, '6743013', 'Jorge', 'Calle Huanca', 'jorge.calle@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123413', '1975-05-18', 'Zona Sopocachi, Calle Rosendo Gutiérrez 410', NULL, 1, '2026-10-05 02:48:26'),
(14, 1, '6743014', 'Ana', 'Rojas Vega', 'ana.rojas@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123414', '1988-12-02', 'Av. Camacho Nº 890, La Paz', NULL, 1, '2026-10-05 02:48:26'),
(15, 1, '6743015', 'Diego', 'Flores Aliaga', 'diego.flores@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123415', '1970-08-27', 'Zona Obrajes, Calle 3 Nº 150, La Paz', NULL, 1, '2026-10-05 02:48:26'),
(16, 1, '6743016', 'Carmen', 'Ticona Yujra', 'carmen.ticona@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123416', '1996-04-09', 'Calle Murillo Nº 560, La Paz', NULL, 1, '2026-10-05 02:48:26'),
(17, 1, '6743017', 'Roberto', 'Álvarez Soria', 'roberto.alvarez@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123417', '1958-10-14', 'Av. 6 de Agosto Nº 2100, La Paz', NULL, 1, '2026-10-05 02:48:26'),
(18, 1, '6743018', 'Sofía', 'Condori Limachi', 'sofia.condori@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123418', '2001-02-25', 'Zona Miraflores, Calle Pinilla Nº 33', NULL, 1, '2026-10-05 02:48:26'),
(19, 1, '9854019', 'Miguel', 'Tarqui Nina', 'miguel.tarqui@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123419', '2012-06-30', 'Zona Villa Copacabana, Calle 7 Nº 21', NULL, 1, '2026-10-05 02:48:26'),
(20, 1, '9854020', 'Valeria', 'Nina Copa', 'valeria.nina@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '60123420', '2015-09-18', 'Zona Villa Copacabana, Calle 7 Nº 21', NULL, 1, '2026-10-05 02:48:26'),
(21, 4, '7000021', 'Admin', 'Uno Prueba', 'admin1@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000021', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(22, 4, '7000022', 'Admin', 'Dos Prueba', 'admin2@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000022', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(23, 4, '7000023', 'Admin', 'Tres Prueba', 'admin3@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000023', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(24, 4, '7000024', 'Admin', 'Cuatro Prueba', 'admin4@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000024', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(25, 3, '7000025', 'Recepcionista', 'Uno Prueba', 'recepcionista1@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000025', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(26, 3, '7000026', 'Recepcionista', 'Dos Prueba', 'recepcionista2@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000026', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(27, 3, '7000027', 'Recepcionista', 'Tres Prueba', 'recepcionista3@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000027', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(28, 3, '7000028', 'Recepcionista', 'Cuatro Prueba', 'recepcionista4@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000028', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(29, 2, '7000029', 'Médico', 'Uno Prueba', 'medico1@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000029', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(30, 2, '7000030', 'Médico', 'Dos Prueba', 'medico2@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000030', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(31, 2, '7000031', 'Médico', 'Tres Prueba', 'medico3@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000031', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(32, 2, '7000032', 'Médico', 'Cuatro Prueba', 'medico4@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000032', NULL, NULL, NULL, 1, '2026-10-05 02:52:25'),
(33, 1, '7000033', 'Paciente', 'Uno Prueba', 'paciente1@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000033', '1990-01-15', 'Calle de Prueba Nº 1, La Paz', NULL, 1, '2026-10-05 02:52:25'),
(34, 1, '7000034', 'Paciente', 'Dos Prueba', 'paciente2@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000034', '1985-05-20', 'Calle de Prueba Nº 2, La Paz', NULL, 1, '2026-10-05 02:52:25'),
(35, 1, '7000035', 'Paciente', 'Tres Prueba', 'paciente3@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000035', '2000-09-10', 'Calle de Prueba Nº 3, La Paz', NULL, 1, '2026-10-05 02:52:25'),
(36, 1, '7000036', 'Paciente', 'Cuatro Prueba', 'paciente4@gmail.com', '$2y$10$./ABCDEFGHIJKLMNOPQRSOP7dl9Dbl9Ni05dO7mWijVg5wepwXZh2', '70000036', '1975-12-01', 'Calle de Prueba Nº 4, La Paz', NULL, 1, '2026-10-05 02:52:25');

CREATE TABLE `vw_agenda_citas` (
`cita_id` int(10) unsigned
,`fecha` date
,`hora` time
,`paciente_ci` varchar(20)
,`paciente` varchar(161)
,`medico` varchar(161)
,`especialidad` varchar(60)
,`estado` varchar(20)
,`motivo` varchar(255)
);

CREATE TABLE `vw_antecedentes_paciente` (
`paciente_id` int(10) unsigned
,`ci` varchar(20)
,`paciente` varchar(161)
,`tipo_sangre` varchar(3)
,`enfermedades` mediumtext
,`alergias` mediumtext
);

CREATE TABLE `vw_citas_por_especialidad` (
`especialidad` varchar(60)
,`total_citas` bigint(21)
,`pendientes` decimal(23,0)
,`confirmadas` decimal(23,0)
,`completadas` decimal(23,0)
,`canceladas` decimal(23,0)
);

CREATE TABLE `vw_citas_proximas` (
`cita_id` int(10) unsigned
,`fecha` date
,`hora` time
,`dias_restantes` int(7)
,`paciente` varchar(161)
,`telefono_paciente` varchar(20)
,`medico` varchar(161)
,`especialidad` varchar(60)
,`estado` varchar(20)
);

CREATE TABLE `vw_historial_clinico` (
`historial_id` int(10) unsigned
,`cita_id` int(10) unsigned
,`fecha` date
,`paciente` varchar(161)
,`medico` varchar(161)
,`especialidad` varchar(60)
,`diagnostico` text
,`observacion` text
,`presion_arterial` varchar(20)
,`peso` decimal(5,2)
,`talla` decimal(4,2)
,`imc` decimal(9,1)
,`num_medicamentos` bigint(21)
);

CREATE TABLE `vw_horario_medicos` (
`medico` varchar(161)
,`especialidad` varchar(60)
,`dia` varchar(9)
,`dia_semana` tinyint(3) unsigned
,`hora_inicio` time
,`hora_fin` time
,`disponibilidad` varchar(13)
);

CREATE TABLE `vw_notificaciones_resumen` (
`paciente_id` int(10) unsigned
,`paciente` varchar(161)
,`total_notificaciones` bigint(21)
,`no_leidas` decimal(23,0)
,`ultima_notificacion` timestamp
);

CREATE TABLE `vw_paciente_perfil` (
`paciente_id` int(10) unsigned
,`ci` varchar(20)
,`paciente` varchar(161)
,`fecha_nac` date
,`edad` bigint(21)
,`telefono` varchar(20)
,`tipo_sangre` varchar(3)
,`ocupacion` varchar(100)
,`empresa_seguro` varchar(100)
,`nro_asegurado` varchar(30)
,`estado_seguro` varchar(10)
);

DROP TABLE IF EXISTS `vw_agenda_citas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_agenda_citas`  AS SELECT `c`.`id` AS `cita_id`, `c`.`fecha` AS `fecha`, `c`.`hora` AS `hora`, `up`.`ci` AS `paciente_ci`, concat(`up`.`nombre`,' ',`up`.`apellido`) AS `paciente`, concat(`um`.`nombre`,' ',`um`.`apellido`) AS `medico`, `e`.`nombre` AS `especialidad`, `ec`.`nombre` AS `estado`, `c`.`motivo` AS `motivo` FROM (((((`cita` `c` join `usuario` `up` on(`up`.`id` = `c`.`paciente_id`)) join `usuario` `um` on(`um`.`id` = `c`.`medico_id`)) join `medico` `m` on(`m`.`usuario_id` = `c`.`medico_id`)) join `especialidad` `e` on(`e`.`id` = `m`.`especialidad_id`)) join `estado_cita` `ec` on(`ec`.`id` = `c`.`estado_cita_id`)) ORDER BY `c`.`fecha` ASC, `c`.`hora` ASC ;

DROP TABLE IF EXISTS `vw_antecedentes_paciente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_antecedentes_paciente`  AS SELECT `u`.`id` AS `paciente_id`, `u`.`ci` AS `ci`, concat(`u`.`nombre`,' ',`u`.`apellido`) AS `paciente`, `pa`.`tipo_sangre` AS `tipo_sangre`, group_concat(case when `a`.`tipo` = 'enfermedad' then `a`.`descripcion` end separator '; ') AS `enfermedades`, group_concat(case when `a`.`tipo` = 'alergia' then `a`.`descripcion` end separator '; ') AS `alergias` FROM ((`paciente` `pa` join `usuario` `u` on(`u`.`id` = `pa`.`usuario_id`)) left join `antecedente` `a` on(`a`.`paciente_id` = `pa`.`usuario_id`)) GROUP BY `u`.`id`, `u`.`ci`, `u`.`nombre`, `u`.`apellido`, `pa`.`tipo_sangre` ORDER BY `u`.`apellido` ASC, `u`.`nombre` ASC ;

DROP TABLE IF EXISTS `vw_citas_por_especialidad`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_citas_por_especialidad`  AS SELECT `e`.`nombre` AS `especialidad`, count(`c`.`id`) AS `total_citas`, coalesce(sum(`ec`.`nombre` = 'pendiente'),0) AS `pendientes`, coalesce(sum(`ec`.`nombre` = 'confirmada'),0) AS `confirmadas`, coalesce(sum(`ec`.`nombre` = 'completada'),0) AS `completadas`, coalesce(sum(`ec`.`nombre` = 'cancelada'),0) AS `canceladas` FROM (((`especialidad` `e` left join `medico` `m` on(`m`.`especialidad_id` = `e`.`id`)) left join `cita` `c` on(`c`.`medico_id` = `m`.`usuario_id`)) left join `estado_cita` `ec` on(`ec`.`id` = `c`.`estado_cita_id`)) GROUP BY `e`.`id`, `e`.`nombre` ORDER BY count(`c`.`id`) DESC, `e`.`nombre` ASC ;

DROP TABLE IF EXISTS `vw_citas_proximas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_citas_proximas`  AS SELECT `c`.`id` AS `cita_id`, `c`.`fecha` AS `fecha`, `c`.`hora` AS `hora`, to_days(`c`.`fecha`) - to_days(curdate()) AS `dias_restantes`, concat(`up`.`nombre`,' ',`up`.`apellido`) AS `paciente`, `up`.`telefono` AS `telefono_paciente`, concat(`um`.`nombre`,' ',`um`.`apellido`) AS `medico`, `e`.`nombre` AS `especialidad`, `ec`.`nombre` AS `estado` FROM (((((`cita` `c` join `usuario` `up` on(`up`.`id` = `c`.`paciente_id`)) join `usuario` `um` on(`um`.`id` = `c`.`medico_id`)) join `medico` `m` on(`m`.`usuario_id` = `c`.`medico_id`)) join `especialidad` `e` on(`e`.`id` = `m`.`especialidad_id`)) join `estado_cita` `ec` on(`ec`.`id` = `c`.`estado_cita_id`)) WHERE `c`.`fecha` >= curdate() AND `ec`.`nombre` in ('pendiente','confirmada') ORDER BY `c`.`fecha` ASC, `c`.`hora` ASC ;

DROP TABLE IF EXISTS `vw_historial_clinico`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_historial_clinico`  AS SELECT `h`.`id` AS `historial_id`, `c`.`id` AS `cita_id`, `c`.`fecha` AS `fecha`, concat(`up`.`nombre`,' ',`up`.`apellido`) AS `paciente`, concat(`um`.`nombre`,' ',`um`.`apellido`) AS `medico`, `e`.`nombre` AS `especialidad`, `h`.`diagnostico` AS `diagnostico`, `h`.`observacion` AS `observacion`, `h`.`presion_arterial` AS `presion_arterial`, `h`.`peso` AS `peso`, `h`.`talla` AS `talla`, CASE WHEN `h`.`peso` is not null AND `h`.`talla` is not null THEN round(`h`.`peso` / (`h`.`talla` * `h`.`talla`),1) END AS `imc`, (select count(0) from `receta` `r` where `r`.`historial_id` = `h`.`id`) AS `num_medicamentos` FROM (((((`historial` `h` join `cita` `c` on(`c`.`id` = `h`.`cita_id`)) join `usuario` `up` on(`up`.`id` = `c`.`paciente_id`)) join `usuario` `um` on(`um`.`id` = `c`.`medico_id`)) join `medico` `m` on(`m`.`usuario_id` = `c`.`medico_id`)) join `especialidad` `e` on(`e`.`id` = `m`.`especialidad_id`)) ORDER BY `c`.`fecha` DESC ;

DROP TABLE IF EXISTS `vw_horario_medicos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_horario_medicos`  AS SELECT concat(`u`.`nombre`,' ',`u`.`apellido`) AS `medico`, `e`.`nombre` AS `especialidad`, elt(`h`.`dia_semana`,'Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo') AS `dia`, `h`.`dia_semana` AS `dia_semana`, `h`.`hora_inicio` AS `hora_inicio`, `h`.`hora_fin` AS `hora_fin`, if(`m`.`disponible` = 1,'Disponible','No disponible') AS `disponibilidad` FROM (((`horario_medico` `h` join `medico` `m` on(`m`.`usuario_id` = `h`.`medico_id`)) join `usuario` `u` on(`u`.`id` = `m`.`usuario_id`)) join `especialidad` `e` on(`e`.`id` = `m`.`especialidad_id`)) ORDER BY concat(`u`.`nombre`,' ',`u`.`apellido`) ASC, `h`.`dia_semana` ASC, `h`.`hora_inicio` ASC ;

DROP TABLE IF EXISTS `vw_notificaciones_resumen`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_notificaciones_resumen`  AS SELECT `u`.`id` AS `paciente_id`, concat(`u`.`nombre`,' ',`u`.`apellido`) AS `paciente`, count(`n`.`id`) AS `total_notificaciones`, coalesce(sum(`n`.`leida` = 0),0) AS `no_leidas`, max(`n`.`creado_en`) AS `ultima_notificacion` FROM ((`paciente` `pa` join `usuario` `u` on(`u`.`id` = `pa`.`usuario_id`)) left join `notificacion` `n` on(`n`.`paciente_id` = `pa`.`usuario_id`)) GROUP BY `u`.`id`, `u`.`nombre`, `u`.`apellido` ORDER BY coalesce(sum(`n`.`leida` = 0),0) DESC, concat(`u`.`nombre`,' ',`u`.`apellido`) ASC ;

DROP TABLE IF EXISTS `vw_paciente_perfil`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_paciente_perfil`  AS SELECT `u`.`id` AS `paciente_id`, `u`.`ci` AS `ci`, concat(`u`.`nombre`,' ',`u`.`apellido`) AS `paciente`, `u`.`fecha_nac` AS `fecha_nac`, timestampdiff(YEAR,`u`.`fecha_nac`,curdate()) AS `edad`, `u`.`telefono` AS `telefono`, `pa`.`tipo_sangre` AS `tipo_sangre`, `pa`.`ocupacion` AS `ocupacion`, coalesce(`emp`.`nombre`,'Sin seguro') AS `empresa_seguro`, `s`.`nro_asegurado` AS `nro_asegurado`, CASE WHEN `s`.`id` is null THEN 'Sin seguro' WHEN `s`.`activo` = 1 THEN 'Activo' ELSE 'Inactivo' END AS `estado_seguro` FROM (((`paciente` `pa` join `usuario` `u` on(`u`.`id` = `pa`.`usuario_id`)) left join `seguro` `s` on(`s`.`paciente_id` = `pa`.`usuario_id`)) left join `empresa` `emp` on(`emp`.`id` = `s`.`empresa_id`)) ORDER BY `u`.`apellido` ASC, `u`.`nombre` ASC ;

DROP VIEW IF EXISTS `vw_citas_detalle`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_citas_detalle`  AS SELECT `c`.`id` AS `cita_id`, `c`.`fecha` AS `fecha`, `c`.`hora` AS `hora`, `c`.`motivo` AS `motivo`, `c`.`creado_en` AS `creado_en`, `c`.`paciente_id` AS `paciente_id`, `up`.`ci` AS `paciente_ci`, `up`.`nombre` AS `paciente_nombre`, `up`.`apellido` AS `paciente_apellido`, `up`.`creado_en` AS `paciente_creado_en`, `c`.`medico_id` AS `medico_id`, `um`.`nombre` AS `medico_nombre`, `um`.`apellido` AS `medico_apellido`, `m`.`especialidad_id` AS `especialidad_id`, `e`.`nombre` AS `especialidad`, `e`.`costo_consulta` AS `costo_consulta`, `c`.`recepcionista_id` AS `recepcionista_id`, `ec`.`nombre` AS `estado` FROM (((((`cita` `c` join `usuario` `up` on(`up`.`id` = `c`.`paciente_id`)) join `usuario` `um` on(`um`.`id` = `c`.`medico_id`)) join `medico` `m` on(`m`.`usuario_id` = `c`.`medico_id`)) join `especialidad` `e` on(`e`.`id` = `m`.`especialidad_id`)) join `estado_cita` `ec` on(`ec`.`id` = `c`.`estado_cita_id`)) ;

DROP VIEW IF EXISTS `vw_medico_perfil`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_medico_perfil`  AS SELECT `u`.`id` AS `id`, `u`.`id` AS `medico_id`, 'medico' AS `rol`, `u`.`ci` AS `ci`, `u`.`nombre` AS `nombre`, `u`.`apellido` AS `apellido`, `u`.`correo` AS `correo`, `u`.`telefono` AS `telefono`, `u`.`fecha_nac` AS `fecha_nac`, `u`.`direccion` AS `direccion`, `u`.`foto` AS `foto`, `u`.`estado` AS `estado`, `m`.`matricula` AS `matricula`, `m`.`especialidad_id` AS `especialidad_id`, `e`.`nombre` AS `especialidad`, `e`.`costo_consulta` AS `costo_consulta`, `m`.`disponible` AS `disponible`, (select group_concat(concat(elt(`h`.`dia_semana`,'Lun','Mar','Mié','Jue','Vie','Sáb','Dom'),' ',date_format(`h`.`hora_inicio`,'%H:%i'),'-',date_format(`h`.`hora_fin`,'%H:%i')) order by `h`.`dia_semana`,`h`.`hora_inicio` ASC separator ', ') from `horario_medico` `h` where `h`.`medico_id` = `m`.`usuario_id`) AS `horario` FROM ((`medico` `m` join `usuario` `u` on(`u`.`id` = `m`.`usuario_id`)) join `especialidad` `e` on(`e`.`id` = `m`.`especialidad_id`)) ;

DROP VIEW IF EXISTS `vw_pagos_paciente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_pagos_paciente`  AS SELECT `c`.`id` AS `id`, `c`.`paciente_id` AS `paciente_id`, concat('Consulta ',`e`.`nombre`) AS `concepto`, `e`.`costo_consulta` AS `monto`, CASE WHEN `ec`.`nombre` = 'completada' THEN 'pagado' WHEN `c`.`fecha` < curdate() THEN 'vencido' ELSE 'pendiente' END AS `estado`, `c`.`fecha` AS `fecha` FROM (((`cita` `c` join `medico` `m` on(`m`.`usuario_id` = `c`.`medico_id`)) join `especialidad` `e` on(`e`.`id` = `m`.`especialidad_id`)) join `estado_cita` `ec` on(`ec`.`id` = `c`.`estado_cita_id`)) WHERE `ec`.`nombre` <> 'cancelada' ;

DROP VIEW IF EXISTS `vw_usuarios_roles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_usuarios_roles`  AS SELECT `u`.`id` AS `id`, `u`.`rol_id` AS `rol_id`, CASE `r`.`nombre` WHEN 'administrador' THEN 'admin' ELSE `r`.`nombre` END AS `rol`, `u`.`ci` AS `ci`, `u`.`nombre` AS `nombre`, `u`.`apellido` AS `apellido`, `u`.`correo` AS `correo`, `u`.`telefono` AS `telefono`, `u`.`fecha_nac` AS `fecha_nac`, `u`.`direccion` AS `direccion`, `u`.`foto` AS `foto`, `u`.`estado` AS `estado`, `u`.`creado_en` AS `creado_en`, `m`.`usuario_id` AS `medico_id`, `m`.`matricula` AS `matricula`, `m`.`especialidad_id` AS `especialidad_id`, `e`.`nombre` AS `especialidad_nombre`, `pa`.`ocupacion` AS `ocupacion`, `pa`.`tipo_sangre` AS `tipo_sangre` FROM (((((`usuario` `u` join `rol` `r` on(`r`.`id` = `u`.`rol_id`)) left join `medico` `m` on(`m`.`usuario_id` = `u`.`id`)) left join `especialidad` `e` on(`e`.`id` = `m`.`especialidad_id`)) left join `paciente` `pa` on(`pa`.`usuario_id` = `u`.`id`))) ;

ALTER TABLE `administrador`
  ADD PRIMARY KEY (`usuario_id`);

ALTER TABLE `antecedente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_antecedente` (`paciente_id`,`tipo`,`descripcion`);

ALTER TABLE `cita`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cita_medico_horario` (`medico_id`,`fecha`,`hora`),
  ADD UNIQUE KEY `uq_cita_paciente_horario` (`paciente_id`,`fecha`,`hora`),
  ADD KEY `idx_cita_fecha` (`fecha`),
  ADD KEY `idx_cita_estado` (`estado_cita_id`),
  ADD KEY `idx_cita_recepcionista` (`recepcionista_id`);

ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_empresa_nombre` (`nombre`);

ALTER TABLE `especialidad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_especialidad_nombre` (`nombre`);

ALTER TABLE `estado_cita`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_estado_cita_nombre` (`nombre`);

ALTER TABLE `historial`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_historial_cita` (`cita_id`);

ALTER TABLE `horario_medico`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_horario` (`medico_id`,`dia_semana`,`hora_inicio`);

ALTER TABLE `medico`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `uq_medico_matricula` (`matricula`),
  ADD KEY `idx_medico_especialidad` (`especialidad_id`);

ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_paciente` (`paciente_id`,`leida`),
  ADD KEY `idx_notif_cita` (`cita_id`),
  ADD KEY `idx_notif_tipo` (`tipo_notificacion_id`);

ALTER TABLE `paciente`
  ADD PRIMARY KEY (`usuario_id`);

ALTER TABLE `recepcionista`
  ADD PRIMARY KEY (`usuario_id`);

ALTER TABLE `receta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_receta_historial` (`historial_id`);

ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_rol_nombre` (`nombre`);

ALTER TABLE `seguro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_seguro_paciente` (`paciente_id`),
  ADD UNIQUE KEY `uq_seguro_nro` (`nro_asegurado`),
  ADD KEY `idx_seguro_empresa` (`empresa_id`);

ALTER TABLE `tipo_notificacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tipo_notificacion_nombre` (`nombre`);

ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuario_ci` (`ci`),
  ADD UNIQUE KEY `uq_usuario_correo` (`correo`),
  ADD KEY `idx_usuario_rol` (`rol_id`);

ALTER TABLE `antecedente`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `cita`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `empresa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `especialidad`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `estado_cita`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `historial`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `horario_medico`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `notificacion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `receta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `rol`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `seguro`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `tipo_notificacion`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `usuario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

ALTER TABLE `administrador`
  ADD CONSTRAINT `fk_administrador_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `antecedente`
  ADD CONSTRAINT `fk_antecedente_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `cita`
  ADD CONSTRAINT `fk_cita_estado` FOREIGN KEY (`estado_cita_id`) REFERENCES `estado_cita` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cita_medico` FOREIGN KEY (`medico_id`) REFERENCES `medico` (`usuario_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cita_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`usuario_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cita_recepcionista` FOREIGN KEY (`recepcionista_id`) REFERENCES `recepcionista` (`usuario_id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `historial`
  ADD CONSTRAINT `fk_historial_cita` FOREIGN KEY (`cita_id`) REFERENCES `cita` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `horario_medico`
  ADD CONSTRAINT `fk_horario_medico` FOREIGN KEY (`medico_id`) REFERENCES `medico` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `medico`
  ADD CONSTRAINT `fk_medico_especialidad` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidad` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_medico_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `notificacion`
  ADD CONSTRAINT `fk_notif_cita` FOREIGN KEY (`cita_id`) REFERENCES `cita` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notif_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notif_tipo` FOREIGN KEY (`tipo_notificacion_id`) REFERENCES `tipo_notificacion` (`id`) ON UPDATE CASCADE;

ALTER TABLE `paciente`
  ADD CONSTRAINT `fk_paciente_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `recepcionista`
  ADD CONSTRAINT `fk_recepcionista_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `receta`
  ADD CONSTRAINT `fk_receta_historial` FOREIGN KEY (`historial_id`) REFERENCES `historial` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `seguro`
  ADD CONSTRAINT `fk_seguro_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_seguro_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
