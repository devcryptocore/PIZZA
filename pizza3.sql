-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20241206.16f3583c6d
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 03-10-2025 a las 22:37:14
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
-- Estructura de tabla para la tabla `active_products`
--

CREATE TABLE `active_products` (
  `id` int NOT NULL,
  `id_producto` int NOT NULL,
  `unidades` decimal(10,3) NOT NULL,
  `porciones` int DEFAULT NULL,
  `precio` int NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `active_products`
--

INSERT INTO `active_products` (`id`, `id_producto`, `unidades`, `porciones`, `precio`, `sucursal`, `usuario`) VALUES
(39, 10, 4.000, 0, 25000, 'las_americas', 'admin'),
(40, 14, 1.000, 0, 5000, 'las_americas', 'admin'),
(42, 19, 0.000, 0, 7500, 'las_americas', 'admin'),
(43, 21, 0.000, 0, 5625, 'las_americas', 'root');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `id` int NOT NULL,
  `estado` enum('1','0') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT '0',
  `base` int NOT NULL,
  `ventas` int NOT NULL,
  `descuentos` int NOT NULL,
  `ingresos` int NOT NULL,
  `egresos` int NOT NULL,
  `usuario` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `codcaja` int NOT NULL,
  `fecha` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `documento` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL,
  `direccion` varchar(150) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `total_comprado` int DEFAULT '0',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `documento`, `direccion`, `telefono`, `total_comprado`, `fecha_registro`) VALUES
(1, 'Anna Does', '15847885', 'Cra 21 #54-99 Pasto', '3105488822', 14000, '2025-10-03 07:40:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devoluciones`
--

CREATE TABLE `devoluciones` (
  `id` int NOT NULL,
  `id_venta` int NOT NULL,
  `id_producto` int NOT NULL,
  `producto` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `usuario` varchar(100) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `sucursal` varchar(100) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `motivo` text COLLATE utf8mb4_spanish_ci,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `devoluciones`
--

INSERT INTO `devoluciones` (`id`, `id_venta`, `id_producto`, `producto`, `cantidad`, `precio`, `total`, `usuario`, `sucursal`, `motivo`, `fecha`) VALUES
(1, 35, 19, 'Jugo Hit caja', 1, 7500.00, 7500.00, NULL, 'las_americas', 'El cliente había pedido de otra marca y sabor', '2025-10-03 05:39:35'),
(2, 55, 14, 'Pizza Ranchera Big', 1, 5000.00, 5000.00, 'root', 'las_americas', 'Solo era una', '2025-10-03 22:23:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidades`
--

CREATE TABLE `entidades` (
  `id` int NOT NULL,
  `entidad` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `inicial` int NOT NULL DEFAULT '0',
  `monto` int NOT NULL DEFAULT '0',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `entidades`
--

INSERT INTO `entidades` (`id`, `entidad`, `inicial`, `monto`, `fecha`) VALUES
(1, 'Efectivo', 500000, 500000, '2025-10-03 16:47:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredientes`
--

CREATE TABLE `ingredientes` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `costo` int NOT NULL,
  `unidad` enum('gramo','ml','unidad') COLLATE utf8mb4_spanish_ci DEFAULT 'gramo',
  `stock` decimal(10,3) NOT NULL DEFAULT '0.000',
  `stock_minimo` int DEFAULT '0',
  `vencimiento` date DEFAULT NULL,
  `creado` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `ingredientes`
--

INSERT INTO `ingredientes` (`id`, `nombre`, `costo`, `unidad`, `stock`, `stock_minimo`, `vencimiento`, `creado`, `sucursal`, `usuario`) VALUES
(15, 'Carne de res', 25, 'gramo', 6737.500, 500, '2025-10-29', '2025-09-17 02:38:29', 'las_americas', 'admin'),
(16, 'Piña', 10, 'gramo', 2500.000, 300, '2025-10-29', '2025-09-17 02:38:53', 'las_americas', 'admin'),
(17, 'Pollo', 20, 'gramo', 1975.000, 200, '2025-10-30', '2025-09-17 02:39:14', 'las_americas', 'admin'),
(18, 'Queso mozzarella', 10, 'gramo', 5337.500, 1000, '2025-10-31', '2025-09-17 02:39:51', 'las_americas', 'admin'),
(19, 'Masa', 1, 'gramo', 7650.000, 200, '2025-10-11', '2025-09-26 00:21:55', 'las_americas', 'admin'),
(21, 'Coca Cola 1.5 Litros', 5000, 'unidad', 1.000, 2, '2025-12-06', '2025-09-26 22:02:19', 'las_americas', 'admin'),
(22, 'Jugo Hit Caja', 4000, 'unidad', 0.000, 2, '2025-10-30', '2025-09-26 22:40:20', 'las_americas', 'admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inversion`
--

CREATE TABLE `inversion` (
  `id` int NOT NULL,
  `idinsumo` int NOT NULL,
  `concepto` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `cantidad` int NOT NULL,
  `unidad` enum('gramo','ml','unidad') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'gramo',
  `costo` int NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registrado_por` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `inversion`
--

INSERT INTO `inversion` (`id`, `idinsumo`, `concepto`, `cantidad`, `unidad`, `costo`, `creado_en`, `registrado_por`, `sucursal`) VALUES
(11, 15, 'Carne de res', 5000, 'gramo', 125000, '2025-09-17 02:38:29', 'admin', 'las_americas'),
(12, 16, 'Piña', 1000, 'gramo', 10000, '2025-09-17 02:38:53', 'admin', 'las_americas'),
(13, 17, 'Pollo', 4000, 'gramo', 80000, '2025-09-17 02:39:14', 'admin', 'las_americas'),
(14, 18, 'Queso mozzarella', 10000, 'gramo', 100000, '2025-09-17 02:39:51', 'admin', 'las_americas'),
(15, 15, 'Carne de res', 5000, 'gramo', 125000, '2025-09-19 20:50:55', 'admin', 'las_americas'),
(16, 19, 'Masa', 10000, 'gramo', 10000, '2025-09-26 00:21:55', 'admin', 'las_americas'),
(17, 15, 'Carne de res', 1000, 'gramo', 25000, '2025-09-26 14:19:50', 'admin', 'las_americas'),
(19, 21, 'Coca Cola 1.5 Litros', 10, 'unidad', 50000, '2025-09-26 22:02:19', 'admin', 'las_americas'),
(20, 21, 'Coca Cola 1.5 Litros', 1, 'unidad', 5000, '2025-09-26 22:31:40', 'admin', 'las_americas'),
(21, 22, 'Jugo Hit Caja', 10, 'unidad', 40000, '2025-09-26 22:40:20', 'admin', 'las_americas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operadores`
--

CREATE TABLE `operadores` (
  `id` int NOT NULL,
  `nombre` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `apellido` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `documento` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `telefono` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `direccion` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `email` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `foto` varchar(256) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `fecharegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `operadores`
--

INSERT INTO `operadores` (`id`, `nombre`, `apellido`, `documento`, `telefono`, `direccion`, `email`, `foto`, `fecharegistro`) VALUES
(1, 'Jhon Doe', 'Do Does', '10548777', '3158847712', 'Cra 3a #15-18 Centro', 'sofiandrea94@outlook.es', NULL, '2025-09-26 13:48:20'),
(2, 'Anna Do', 'Does ', '15847885', '321 4101419', 'Cra 3a #15-18 Centro', 'sofiandrea94@outlook.es', NULL, '2025-09-26 13:50:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int NOT NULL,
  `producto` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `precio` decimal(11,0) NOT NULL,
  `categoria` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `talla` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'innecesario',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `oferta` tinyint NOT NULL DEFAULT '0',
  `vencimiento` date DEFAULT NULL,
  `portada` text COLLATE utf8mb4_spanish_ci,
  `foto_1` text COLLATE utf8mb4_spanish_ci,
  `foto_2` text COLLATE utf8mb4_spanish_ci,
  `foto_3` text COLLATE utf8mb4_spanish_ci,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `producto`, `precio`, `categoria`, `descripcion`, `talla`, `estado`, `oferta`, `vencimiento`, `portada`, `foto_1`, `foto_2`, `foto_3`, `fecha_registro`, `sucursal`, `usuario`) VALUES
(10, 'Hamburguesa Phoenix', 25000, 'hamburguesa', 'una hamburguesa', 'innecesario', 0, 0, NULL, '../res/images/products/hamburguesaHamburguesa_Phoenix/Hamburguesa_Phoenix_68d6f66359700.jpg', '../res/images/products/hamburguesaPhoenix/Phoenix_68d20be217c76.png', '../res/images/products/hamburguesaPhoenix/Phoenix_68d20be217d6f.png', '../res/images/products/hamburguesaPhoenix/Phoenix_68d20be217f72.jpeg', '2025-09-23 02:54:26', 'las_americas', 'admin'),
(14, 'Pizza Ranchera Big', 40000, 'pizza', 'Pizza ranchera del rancho verde y ni tan verde por que todo está lleno de polvo y acabado como la cara de tu tía', 'L', 1, 0, NULL, '../res/images/products/pizzaPizza_Ranchera_Big/Pizza_Ranchera_Big_68d5e4a75a9ce.png', NULL, '../res/images/products/pizzaPizza_Ranchera_Big/Pizza_Ranchera_Big_68d5e4a75ab1e.png', NULL, '2025-09-26 00:56:07', 'las_americas', 'admin'),
(19, 'Jugo Hit caja', 7500, 'jugo', 'jugo hit', 'innecesario', 0, 0, NULL, NULL, NULL, NULL, NULL, '2025-09-26 22:42:01', 'las_americas', 'admin'),
(21, 'Pizza Italiana', 45000, 'pizza', 'Pizza Italiana con Pepperonni\r\n- Pepperoni\r\n- Masa\r\n- Queso Mozzarella\r\n- Salsa', 'L', 0, 0, NULL, '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68de034491f08.png', '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68de03449206e.png', '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68de034492192.png', '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68de034492287.png', '2025-10-02 04:44:52', 'las_americas', 'root');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_ingredients`
--

CREATE TABLE `product_ingredients` (
  `id` int NOT NULL,
  `id_product` int NOT NULL,
  `ingrediente` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `cantidad` int NOT NULL,
  `medida` enum('gramo','ml','unidad') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'gramo',
  `costo` int NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `product_ingredients`
--

INSERT INTO `product_ingredients` (`id`, `id_product`, `ingrediente`, `cantidad`, `medida`, `costo`, `sucursal`, `usuario`) VALUES
(6, 6, '15', 100, 'gramo', 2500, 'las_americas', 'admin'),
(7, 6, '16', 100, 'gramo', 1000, 'las_americas', 'admin'),
(8, 6, '18', 100, 'gramo', 1000, 'las_americas', 'admin'),
(9, 7, '16', 200, 'gramo', 2000, 'las_americas', 'admin'),
(10, 7, '17', 400, 'gramo', 8000, 'las_americas', 'admin'),
(11, 7, '18', 300, 'gramo', 3000, 'las_americas', 'admin'),
(12, 9, '15', 100, 'gramo', 2500, 'las_americas', 'admin'),
(13, 9, '16', 100, 'gramo', 1000, 'las_americas', 'admin'),
(14, 9, '17', 100, 'gramo', 2000, 'las_americas', 'admin'),
(15, 9, '18', 100, 'gramo', 1000, 'las_americas', 'admin'),
(16, 10, '15', 150, 'gramo', 3750, 'las_americas', 'admin'),
(17, 10, '16', 200, 'gramo', 2000, 'las_americas', 'admin'),
(18, 10, '17', 100, 'gramo', 2000, 'las_americas', 'admin'),
(19, 10, '18', 100, 'gramo', 1000, 'las_americas', 'admin'),
(20, 11, '15', 150, 'gramo', 3750, 'las_americas', 'admin'),
(21, 11, '17', 100, 'gramo', 2000, 'las_americas', 'admin'),
(22, 11, '18', 150, 'gramo', 1500, 'las_americas', 'admin'),
(23, 14, '15', 150, 'gramo', 3750, 'las_americas', 'admin'),
(24, 14, '17', 200, 'gramo', 4000, 'las_americas', 'admin'),
(25, 14, '18', 300, 'gramo', 3000, 'las_americas', 'admin'),
(26, 14, '19', 400, 'gramo', 400, 'las_americas', 'admin'),
(27, 16, '21', 1, 'unidad', 5000, 'las_americas', 'admin'),
(28, 19, '22', 5, 'unidad', 20000, 'las_americas', 'admin'),
(29, 21, '15', 300, 'gramo', 7500, 'las_americas', 'root'),
(30, 21, '17', 300, 'gramo', 6000, 'las_americas', 'root'),
(31, 21, '18', 400, 'gramo', 4000, 'las_americas', 'root'),
(32, 21, '19', 300, 'gramo', 300, 'las_americas', 'root');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sell_cart`
--

CREATE TABLE `sell_cart` (
  `id` int NOT NULL,
  `unico` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `documento` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `role` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `contrasena` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `estado` int NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `documento`, `role`, `usuario`, `contrasena`, `sucursal`, `estado`, `fecha`) VALUES
(1, '10548777', 'administrator', 'root', '$2y$12$W/9NSV3NV.q/7QqUzhgoD.jgQ/m9Z6l7bz3lPUo90NfEDx/ctISSq', 'las_americas', 1, '2025-09-26 14:08:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int NOT NULL,
  `consecutivo` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `idventa` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `idcaja` int NOT NULL,
  `id_producto` int NOT NULL,
  `producto` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `cantidad` int NOT NULL,
  `porciones` int NOT NULL DEFAULT '0',
  `precio` int NOT NULL,
  `total` int NOT NULL,
  `recibido` int NOT NULL DEFAULT '0',
  `unico` int NOT NULL,
  `descuento` int NOT NULL DEFAULT '0',
  `cliente` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `clidoc` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `usuario` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fechareg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `consecutivo`, `idventa`, `idcaja`, `id_producto`, `producto`, `cantidad`, `porciones`, `precio`, `total`, `recibido`, `unico`, `descuento`, `cliente`, `clidoc`, `usuario`, `sucursal`, `fechareg`) VALUES
(34, '00000001', '68de24cc980e1', 1, 14, 'Pizza Ranchera Big', 2, 2, 4000, 8000, 20000, 1, 2000, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-02 07:07:56'),
(36, '00000002', '68de24cdeda2c', 1, 21, 'Pizza Italiana', 3, 3, 5625, 16875, 50000, 2, 0, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-02 07:07:57'),
(37, '00000003', '68de24cf66210', 1, 10, 'Hamburguesa Phoenix', 2, 2, 25000, 50000, 50000, 3, 0, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-02 07:07:59'),
(38, '00000004', '68def7db11908', 1, 14, 'Pizza Ranchera Big', 2, 2, 4000, 8000, 10000, 1, 2000, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-02 22:08:27'),
(39, '00000005', '68df01f19317f', 1, 21, 'Pizza Italiana', 1, 1, 5625, 5625, 10000, 1, 0, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-02 22:51:29'),
(40, '00000006', '68df2dafbbb20', 1, 14, 'Pizza Ranchera Big', 4, 4, 4000, 16000, 50000, 1, 4000, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-03 01:58:07'),
(41, '00000006', '68df2dafbbb20', 1, 21, 'Pizza Italiana', 3, 3, 5625, 16875, 50000, 1, 0, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-03 01:58:07'),
(42, '00000007', '68df8be957681', 1, 14, 'Pizza Ranchera Big', 1, 1, 4000, 4000, 20000, 1, 1000, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-03 08:40:09'),
(43, '00000008', '68df8e0762a72', 1, 19, 'Jugo Hit caja', 1, 1, 7500, 7500, 10000, 1, 0, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-03 08:49:11'),
(44, '00000009', '68df8e83e3e6c', 1, 21, 'Pizza Italiana', 1, 1, 5625, 5625, 6000, 1, 0, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-03 08:51:15'),
(45, '00000010', '68df8f0f026e6', 1, 14, 'Pizza Ranchera Big', 1, 1, 4000, 4000, 5000, 1, 1000, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-03 08:53:35'),
(46, '00000011', '68df8f4528d89', 1, 10, 'Hamburguesa Phoenix', 1, 1, 25000, 25000, 25000, 1, 0, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-03 08:54:29'),
(47, '00000012', '68df8f8382833', 1, 21, 'Pizza Italiana', 1, 1, 5625, 5625, 6000, 1, 0, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-03 08:55:31'),
(48, '00000013', '68df8fa006130', 1, 14, 'Pizza Ranchera Big', 1, 1, 4000, 4000, 10000, 1, 1000, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-03 08:56:00'),
(49, '00000014', '68df902ae0882', 1, 14, 'Pizza Ranchera Big', 1, 1, 4000, 4000, 10000, 1, 1000, 'Indefinido', '123456789', 'root', 'las_americas', '2025-10-03 08:58:18'),
(50, '00000015', '68df91230994e', 1, 21, 'Pizza Italiana', 1, 1, 5625, 5625, 6000, 1, 0, 'Indefinido', '0000000000', 'root', 'las_americas', '2025-10-03 09:02:27'),
(51, '00000016', '68df926fd27c3', 1, 14, 'Pizza Ranchera Big', 1, 1, 4000, 4000, 10000, 1, 1000, 'Anna Does', '15847885', 'root', 'las_americas', '2025-10-03 09:07:59'),
(52, '00000016', '68df926fd27c3', 1, 21, 'Pizza Italiana', 1, 1, 5625, 5625, 10000, 1, 0, 'Anna Does', '15847885', 'root', 'las_americas', '2025-10-03 09:07:59'),
(53, '00000017', '68df955cd343d', 1, 21, 'Pizza Italiana', 1, 1, 5625, 5625, 6000, 1, 0, 'Indefinido', '0000000000', 'root', 'las_americas', '2025-10-03 09:20:28'),
(54, '00000018', '68df957a271cb', 1, 14, 'Pizza Ranchera Big', 1, 1, 4000, 4000, 5000, 1, 1000, 'Anna Does', '15847885', 'root', 'las_americas', '2025-10-03 09:20:58'),
(55, '00000019', '68e04c92c8d2b', 1, 14, 'Pizza Ranchera Big', 1, 2, 5000, 5000, 20000, 1, 0, 'Anna Does', '15847885', 'root', 'las_americas', '2025-10-03 22:22:10');

--
-- Disparadores `ventas`
--
DELIMITER $$
CREATE TRIGGER `after_delete_venta` AFTER DELETE ON `ventas` FOR EACH ROW BEGIN
                DECLARE v_unidades DECIMAL(10,3);
                DECLARE v_porciones INT;

                SELECT unidades, porciones
                INTO v_unidades, v_porciones
                FROM active_products
                WHERE id_producto = OLD.id_producto
                AND sucursal = OLD.sucursal
                LIMIT 1;

                IF v_porciones > 0 THEN
                    SET v_porciones = v_porciones + OLD.cantidad;
                    SET v_unidades = v_porciones / 8;
                ELSE
                    SET v_unidades = v_unidades + OLD.cantidad;
                    SET v_porciones = 0;
                END IF;

                UPDATE active_products
                SET unidades = v_unidades,
                    porciones = v_porciones
                WHERE id_producto = OLD.id_producto
                AND sucursal   = OLD.sucursal;

                IF v_unidades > 0 OR v_porciones > 0 THEN
                    UPDATE productos
                    SET estado = 1
                    WHERE id = OLD.id_producto
                    AND sucursal = OLD.sucursal;
                END IF;
            END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_venta` AFTER INSERT ON `ventas` FOR EACH ROW BEGIN
                DECLARE v_unidades DECIMAL(10,3);
                DECLARE v_porciones INT;

                SELECT unidades, porciones
                INTO v_unidades, v_porciones
                FROM active_products
                WHERE id_producto = NEW.id_producto
                AND sucursal   = NEW.sucursal
                LIMIT 1;

                IF v_porciones > 0 THEN
                    SET v_porciones = v_porciones - NEW.cantidad;
                    SET v_unidades = v_porciones / 8;
                ELSE
                    SET v_unidades = v_unidades - NEW.cantidad;
                    SET v_porciones = 0;
                END IF;

                UPDATE active_products
                SET unidades = v_unidades,
                    porciones = v_porciones
                WHERE id_producto = NEW.id_producto
                AND sucursal   = NEW.sucursal;

                IF v_unidades <= 0 OR v_porciones <= 0 THEN
                    UPDATE productos
                    SET estado = 0
                    WHERE id = NEW.id_producto
                    AND sucursal = NEW.sucursal;
                END IF;

                DELETE FROM sell_cart
                WHERE unico = NEW.unico;
            END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_venta` AFTER UPDATE ON `ventas` FOR EACH ROW BEGIN
                DECLARE diff INT;
                DECLARE v_unidades DECIMAL(10,3);
                DECLARE v_porciones INT;

                -- diferencia de cantidad (antes vs después)
                SET diff = OLD.cantidad - NEW.cantidad;

                IF diff > 0 THEN
                    -- se devolvieron diff unidades
                    SELECT unidades, porciones
                    INTO v_unidades, v_porciones
                    FROM active_products
                    WHERE id_producto = NEW.id_producto
                    AND sucursal = NEW.sucursal
                    LIMIT 1;

                    IF v_porciones > 0 THEN
                        SET v_porciones = v_porciones + diff;
                        SET v_unidades = v_porciones / 8;
                    ELSE
                        SET v_unidades = v_unidades + diff;
                    END IF;

                    UPDATE active_products
                    SET unidades = v_unidades,
                        porciones = v_porciones
                    WHERE id_producto = NEW.id_producto
                    AND sucursal   = NEW.sucursal;

                    -- si vuelve a haber stock, reactivar
                    IF v_unidades > 0 OR v_porciones > 0 THEN
                        UPDATE productos
                        SET estado = 1
                        WHERE id = NEW.id_producto
                        AND sucursal = NEW.sucursal;
                    END IF;
                END IF;
            END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `active_products`
--
ALTER TABLE `active_products`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documento` (`documento`);

--
-- Indices de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `entidades`
--
ALTER TABLE `entidades`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `operadores`
--
ALTER TABLE `operadores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `product_ingredients`
--
ALTER TABLE `product_ingredients`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sell_cart`
--
ALTER TABLE `sell_cart`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `active_products`
--
ALTER TABLE `active_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `entidades`
--
ALTER TABLE `entidades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `inversion`
--
ALTER TABLE `inversion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `operadores`
--
ALTER TABLE `operadores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `product_ingredients`
--
ALTER TABLE `product_ingredients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `sell_cart`
--
ALTER TABLE `sell_cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
