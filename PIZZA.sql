-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20241206.16f3583c6d
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 18-09-2025 a las 00:30:56
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pizza`
--
CREATE DATABASE IF NOT EXISTS `pizza` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE `pizza`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredientes`
--

CREATE TABLE `ingredientes` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `unidad` enum('gramo','ml','unidad') COLLATE utf8mb4_spanish_ci DEFAULT 'gramo',
  `stock` decimal(10,2) NOT NULL,
  `stock_minimo` decimal(10,2) DEFAULT '0.00',
  `vencimiento` date DEFAULT NULL,
  `creado` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `ingredientes`
--

INSERT INTO `ingredientes` (`id`, `nombre`, `costo`, `unidad`, `stock`, `stock_minimo`, `vencimiento`, `creado`) VALUES
(15, 'Carne de res', 25.00, 'gramo', 5000.00, 500.00, '2025-09-23', '2025-09-17 02:38:29'),
(16, 'Piña', 10.00, 'gramo', 1000.00, 300.00, '2025-09-20', '2025-09-17 02:38:53'),
(17, 'Pollo', 20.00, 'gramo', 4000.00, 200.00, '2025-09-21', '2025-09-17 02:39:14'),
(18, 'Queso mozzarella', 10.00, 'gramo', 10000.00, 1000.00, '2025-09-30', '2025-09-17 02:39:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inversion`
--

CREATE TABLE `inversion` (
  `id` int NOT NULL,
  `idinsumo` int NOT NULL,
  `concepto` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad` enum('gramo','ml','unidad') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'gramo',
  `costo` decimal(10,2) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registrado_por` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `inversion`
--

INSERT INTO `inversion` (`id`, `idinsumo`, `concepto`, `cantidad`, `unidad`, `costo`, `creado_en`, `registrado_por`) VALUES
(11, 15, 'Carne de res', 5000.00, 'gramo', 125000.00, '2025-09-17 02:38:29', 'admin'),
(12, 16, 'Piña', 1000.00, 'gramo', 10000.00, '2025-09-17 02:38:53', 'admin'),
(13, 17, 'Pollo', 4000.00, 'gramo', 80000.00, '2025-09-17 02:39:14', 'admin'),
(14, 18, 'Queso mozzarella', 10000.00, 'gramo', 100000.00, '2025-09-17 02:39:51', 'admin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inversion`
--
ALTER TABLE `inversion`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `inversion`
--
ALTER TABLE `inversion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
