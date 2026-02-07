-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-02-2026 a las 02:22:31
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sysaccess`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `access_logs`
--

CREATE TABLE `access_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `action` enum('ARRIVAL','BREAK_START','BREAK_END','EXIT') NOT NULL,
  `occurred_at` datetime NOT NULL DEFAULT current_timestamp(),
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `access_logs`
--

INSERT INTO `access_logs` (`id`, `employee_id`, `action`, `occurred_at`, `note`) VALUES
(1, 1, 'ARRIVAL', '2026-02-06 20:55:16', ''),
(2, 4, 'ARRIVAL', '2026-02-06 20:55:50', 'Todo bien'),
(3, 5, 'BREAK_START', '2026-02-06 20:57:11', ''),
(4, 5, 'ARRIVAL', '2026-02-06 20:57:25', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departments`
--

CREATE TABLE `departments` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'Arte'),
(2, 'Diseño'),
(3, 'Tecnologia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employees`
--

CREATE TABLE `employees` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `department_id` tinyint(3) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `employees`
--

INSERT INTO `employees` (`id`, `code`, `full_name`, `department_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'E001', 'Ana Pérez', 1, 1, '2026-02-07 00:53:28', NULL),
(2, 'E002', 'Luis Gómez', 2, 0, '2026-02-07 00:53:28', '2026-02-07 00:56:34'),
(3, 'E003', 'María López', 3, 1, '2026-02-07 00:53:28', NULL),
(4, 'E004', 'JORGE PEREIRA', 3, 1, '2026-02-07 00:55:39', NULL),
(5, 'E019', 'Raiza Mileva', 1, 1, '2026-02-07 00:56:26', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_date` (`occurred_at`),
  ADD KEY `idx_logs_emp_date` (`employee_id`,`occurred_at`);

--
-- Indices de la tabla `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_emp_dept` (`department_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `departments`
--
ALTER TABLE `departments`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `access_logs`
--
ALTER TABLE `access_logs`
  ADD CONSTRAINT `fk_log_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_emp_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
