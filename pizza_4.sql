-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20241206.16f3583c6d
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 13-10-2025 a las 09:49:50
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
(39, 10, 5.000, 0, 25000, 'las_americas', 'admin'),
(40, 14, 1.875, 15, 5000, 'las_americas', 'admin'),
(42, 19, 1.000, 0, 7500, 'las_americas', 'admin'),
(43, 21, 11.000, 0, 5625, 'las_americas', 'root'),
(44, 25, 3.000, 0, 20000, 'las_americas', 'root');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `id` int NOT NULL,
  `estado` enum('1','0') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT '0',
  `base` int NOT NULL DEFAULT '0',
  `ventas` int NOT NULL DEFAULT '0',
  `descuentos` int NOT NULL DEFAULT '0',
  `ingresos` int NOT NULL DEFAULT '0',
  `egresos` int NOT NULL DEFAULT '0',
  `usuario` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `codcaja` int DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `caja`
--

INSERT INTO `caja` (`id`, `estado`, `base`, `ventas`, `descuentos`, `ingresos`, `egresos`, `usuario`, `sucursal`, `codcaja`, `fecha`) VALUES
(2, '0', 100000, 54000, 1000, 54000, 100000, 'root', 'las_americas', 2, '2025-10-09 05:18:29'),
(3, '1', 100000, 25625, 0, 25625, 0, 'root', 'las_americas', 3, '2025-10-10 17:31:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int NOT NULL,
  `categoria` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `imagen` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `categoria`, `imagen`, `estado`) VALUES
(1, 'pizzas', '../res/images/categories/pizzas/pizzas_68eb35edbb331.png', 1),
(2, 'hamburguesas', '../res/images/categories/hamburguesas/hamburguesas_68eb38d70598c.jpg', 1),
(5, 'jugos', '../res/images/categories/jugos/jugos_68eb43d0e9ffc.png', 1),
(6, 'perros calientes', '../res/images/categories/perros calientes/perros_calientes_68eb446eb5b6f.png', 1),
(7, 'salchipapas', '../res/images/categories/salchipapas/salchipapas_68eb447bc0f0b.png', 1),
(8, 'pollo broaster', '../res/images/categories/pollo broaster/pollo_broaster_68eb448e87683.png', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_principal`
--

CREATE TABLE `categoria_principal` (
  `id` int NOT NULL,
  `categoria` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `categoria_principal`
--

INSERT INTO `categoria_principal` (`id`, `categoria`) VALUES
(1, 'pizzas');

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
(1, 'Anna Does', '15847885', 'Cra 21 #54-99 Pasto', '3105488822', 38500, '2025-10-03 07:40:22');

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
  `efectivo` int NOT NULL DEFAULT '0',
  `nequi` int NOT NULL DEFAULT '0',
  `daviplata` int NOT NULL DEFAULT '0',
  `bancolombia` int NOT NULL DEFAULT '0',
  `consignacion` int NOT NULL DEFAULT '0',
  `otro` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `entidades`
--

INSERT INTO `entidades` (`id`, `efectivo`, `nequi`, `daviplata`, `bancolombia`, `consignacion`, `otro`) VALUES
(1, 480625, 100000, 63000, 0, 0, 0);

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
(15, 'Carne de res', 25, 'gramo', 5712.500, 500, '2025-10-29', '2025-09-17 02:38:29', 'las_americas', 'admin'),
(16, 'Piña', 10, 'gramo', 2500.000, 300, '2025-10-29', '2025-09-17 02:38:53', 'las_americas', 'admin'),
(17, 'Pollo', 20, 'gramo', 905.000, 200, '2025-10-30', '2025-09-17 02:39:14', 'las_americas', 'admin'),
(18, 'Queso mozzarella', 10, 'gramo', 3987.500, 1000, '2025-10-31', '2025-09-17 02:39:51', 'las_americas', 'admin'),
(19, 'Masa', 1, 'gramo', 7450.000, 200, '2025-10-11', '2025-09-26 00:21:55', 'las_americas', 'admin'),
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
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int NOT NULL,
  `codcaja` int DEFAULT NULL,
  `tipo` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `concepto` varchar(500) COLLATE utf8mb4_spanish_ci NOT NULL,
  `entidad` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'efectivo',
  `valor` int NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `codcaja`, `tipo`, `concepto`, `entidad`, `valor`, `sucursal`, `fecha`) VALUES
(23, 2, 'venta', 'Hamburguesa Phoenix vendido por root', 'efectivo', 25000, 'las_americas', '2025-10-09 05:18:41'),
(24, 2, 'venta', 'Pizza Ranchera Big vendido por root', 'efectivo', 9000, 'las_americas', '2025-10-09 05:18:41'),
(25, 2, 'venta', 'Hamburguesa Phoenix vendido por root', 'efectivo', 25000, 'las_americas', '2025-10-09 05:37:02'),
(26, 2, 'venta', 'Pizza Ranchera Big vendido por root', 'efectivo', 4500, 'las_americas', '2025-10-09 05:37:51'),
(27, NULL, 'transferencia', 'Transferencia de $50.000 desde efectivo hacia daviplata por root', 'efectivo', 50000, 'las_americas', '2025-10-09 06:48:02'),
(28, 2, 'egreso', 'Gasto de $100.000 por Reparaciones de techo registrado por root', 'efectivo', 100000, 'las_americas', '2025-10-09 07:44:52'),
(29, NULL, 'transferencia', 'Transferencia de $13.000 desde efectivo hacia daviplata por root', 'efectivo', 13000, 'las_americas', '2025-10-10 17:31:21'),
(30, NULL, 'venta', 'Pizza Ranchera Big vendido por root', 'efectivo', 4500, 'las_americas', '2025-10-10 17:33:34'),
(31, NULL, 'venta', 'Pizzon de pollo vendido por root', 'efectivo', 20000, 'las_americas', '2025-10-10 17:33:34'),
(32, NULL, 'venta', 'Pizzon de pollo vendido por root', 'efectivo', 20000, 'las_americas', '2025-10-10 19:03:26'),
(33, 3, 'venta', 'Pizza Italiana vendido por root', 'efectivo', 5625, 'las_americas', '2025-10-10 20:03:06'),
(41, NULL, 'abono', 'Nuevo abono a Queso fiado', 'efectivo', 20000, 'las_americas', '2025-10-11 03:18:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `obligaciones`
--

CREATE TABLE `obligaciones` (
  `id` int NOT NULL,
  `concepto` varchar(500) COLLATE utf8mb4_spanish_ci NOT NULL,
  `valor` int NOT NULL,
  `abonado` int NOT NULL,
  `saldo` int NOT NULL,
  `historico` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `foto_factura` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT '../res/icons/image.svg',
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

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
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int NOT NULL,
  `idpedido` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `idsesion` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `pedido` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `nombre` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `telefono` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `direccion` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `coordenadas` varchar(256) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `comentario` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('tomado','proceso','despachado','entregado','rechazado','recibido') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'recibido',
  `motivo` text COLLATE utf8mb4_spanish_ci,
  `sucursal` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `sesion` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `idpedido`, `idsesion`, `pedido`, `nombre`, `telefono`, `direccion`, `coordenadas`, `comentario`, `fecha`, `estado`, `motivo`, `sucursal`, `sesion`) VALUES
(9, '68ecc07ab1616', 'session_1760317836969', '[{\"idproducto\":21,\"precio\":5625,\"cantidad\":1,\"subtotal\":5625},{\"idproducto\":10,\"precio\":25000,\"cantidad\":1,\"subtotal\":25000}]', 'Fernando Javier Osorio Sanchez', '3005549947', 'Cra 3a #43-46 El Refúgio', '', 'Este es otro pedido de prueba, pero si quieres no es de prueba', '2025-10-13 09:03:54', 'recibido', NULL, NULL, NULL),
(10, '68ecc12f94c8c', 'session_1760317836969', '[{\"idproducto\":21,\"precio\":5625,\"cantidad\":1,\"subtotal\":5625},{\"idproducto\":10,\"precio\":25000,\"cantidad\":1,\"subtotal\":25000}]', 'Fernando Javier Osorio', '3005549947', 'Cra 7a 43-46 El Refugio', '', 'Esta es la ultima prueba de mensajes desde la app, ya no enviaste nada', '2025-10-13 09:06:55', 'recibido', NULL, NULL, NULL),
(11, '68ecc186a6612', 'session_1760293929573', '[{\"idproducto\":14,\"precio\":5000,\"cantidad\":1,\"subtotal\":5000}]', 'Jhon Does Doe', '3215548877', 'Cra 123', '1.6041150000000002,-77.1289025', 'Hola test', '2025-10-13 09:08:22', 'recibido', NULL, NULL, NULL),
(12, '68ecc2de02229', 'session_1760317836969', '[{\"idproducto\":21,\"precio\":5625,\"cantidad\":1,\"subtotal\":5625},{\"idproducto\":10,\"precio\":25000,\"cantidad\":1,\"subtotal\":25000},{\"idproducto\":25,\"precio\":20000,\"cantidad\":1,\"subtotal\":20000}]', 'Otro tipo', '3234456644', 'Cra 2a', '', 'Ola', '2025-10-13 09:14:06', 'recibido', NULL, NULL, NULL),
(13, '68ecc364f270c', 'session_1760317836969', '[{\"idproducto\":25,\"precio\":20000,\"cantidad\":1,\"subtotal\":20000},{\"idproducto\":21,\"precio\":5625,\"cantidad\":1,\"subtotal\":5625}]', 'Anna Does do', '3217764344', 'Cra 12 a #45-58', '', 'Este es un test con los valores reestablecidos', '2025-10-13 09:16:20', 'recibido', NULL, NULL, NULL),
(14, '68ecc4a69a9b5', 'session_1760317836969', '[{\"idproducto\":25,\"precio\":20000,\"cantidad\":1,\"subtotal\":20000},{\"idproducto\":21,\"precio\":5625,\"cantidad\":1,\"subtotal\":5625},{\"idproducto\":19,\"precio\":7500,\"cantidad\":1,\"subtotal\":7500}]', 'Proba do oruebas', '321777484838', 'Cghaajjs', '', 'Jddkiskksksks', '2025-10-13 09:21:42', 'recibido', NULL, NULL, NULL),
(15, '68ecc649eb1f9', 'session_1760317836969', '[{\"idproducto\":25,\"precio\":20000,\"cantidad\":3,\"subtotal\":60000},{\"idproducto\":21,\"precio\":5625,\"cantidad\":1,\"subtotal\":5625},{\"idproducto\":19,\"precio\":7500,\"cantidad\":1,\"subtotal\":7500},{\"idproducto\":14,\"precio\":5000,\"cantidad\":1,\"subtotal\":5000},{\"idproducto\":10,\"precio\":25000,\"cantidad\":1,\"subtotal\":25000}]', 'Fernando Sanchez', '3005549947', 'Cra 12a #14-75', '', 'Esta es una prueba con los valores escapados en utf-8', '2025-10-13 09:28:41', 'recibido', NULL, NULL, NULL),
(16, '68ecc6ed3518b', 'session_1760317836969', '[{\"idproducto\":25,\"precio\":20000,\"cantidad\":1,\"subtotal\":20000},{\"idproducto\":21,\"precio\":5625,\"cantidad\":1,\"subtotal\":5625},{\"idproducto\":19,\"precio\":7500,\"cantidad\":3,\"subtotal\":22500},{\"idproducto\":14,\"precio\":5000,\"cantidad\":3,\"subtotal\":15000}]', 'Fernando Osorio', '3005549947', 'Cra 12a #43-47', '', 'Este es un intento de prueba con los valores codificados', '2025-10-13 09:31:25', 'recibido', NULL, NULL, NULL),
(17, '68ecc7785d56c', 'session_1760317836969', '[{\"idproducto\":25,\"precio\":20000,\"cantidad\":3,\"subtotal\":60000},{\"idproducto\":21,\"precio\":5625,\"cantidad\":2,\"subtotal\":11250},{\"idproducto\":19,\"precio\":7500,\"cantidad\":3,\"subtotal\":22500}]', 'Fernando Osorio', '3005549947', 'Cra 23 #1-45', '', 'Esta es otra prueba codificando cdarcteres', '2025-10-13 09:33:44', 'recibido', NULL, NULL, NULL),
(18, '68ecc86733793', 'session_1760317836969', '[{\"idproducto\":25,\"precio\":20000,\"cantidad\":3,\"subtotal\":60000},{\"idproducto\":21,\"precio\":5625,\"cantidad\":2,\"subtotal\":11250},{\"idproducto\":19,\"precio\":7500,\"cantidad\":1,\"subtotal\":7500}]', 'Fernando Javier Osorio Sánchez', '3005549947', 'Cra 12 #24-33 El Refúgio', '', 'Hola este es un texto que llevará acéntos para determinar fallos por decodificación de caractéres', '2025-10-13 09:37:43', 'recibido', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int NOT NULL,
  `id_code` int NOT NULL,
  `cod_barras` varchar(256) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
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
  `barcode` text COLLATE utf8mb4_spanish_ci,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `id_code`, `cod_barras`, `producto`, `precio`, `categoria`, `descripcion`, `talla`, `estado`, `oferta`, `vencimiento`, `portada`, `foto_1`, `foto_2`, `foto_3`, `barcode`, `fecha_registro`, `sucursal`, `usuario`) VALUES
(10, 1234, NULL, 'Hamburguesa Phoenix', 25000, 'hamburguesas', 'una hamburguesa', 'innecesario', 1, 0, NULL, '../res/images/products/hamburguesaHamburguesa_Phoenix/Hamburguesa_Phoenix_68d6f66359700.jpg', '../res/images/products/hamburguesaPhoenix/Phoenix_68d20be217c76.png', '../res/images/products/hamburguesaPhoenix/Phoenix_68d20be217d6f.png', '../res/images/products/hamburguesaPhoenix/Phoenix_68d20be217f72.jpeg', NULL, '2025-09-23 02:54:26', 'las_americas', 'admin'),
(14, 1111, NULL, 'Pizza Ranchera Big', 40000, 'pizzas', 'Pizza ranchera del rancho verde y ni tan verde por que todo está lleno de polvo y acabado como la cara de tu tía', 'L', 1, 10, NULL, '../res/images/products/pizzaPizza_Ranchera_Big/Pizza_Ranchera_Big_68d5e4a75a9ce.png', NULL, '../res/images/products/pizzaPizza_Ranchera_Big/Pizza_Ranchera_Big_68d5e4a75ab1e.png', NULL, NULL, '2025-09-26 00:56:07', 'las_americas', 'admin'),
(19, 2222, NULL, 'Jugo Hit caja', 7500, 'jugos', 'jugo hit', 'innecesario', 1, 0, NULL, '../res/images/products/jugosJugo_Hit_caja/Jugo_Hit_caja_68ec97fb84e98.png', NULL, NULL, NULL, NULL, '2025-09-26 22:42:01', 'las_americas', 'admin'),
(21, 3333, NULL, 'Pizza Italiana', 45000, 'pizzas', 'Pizza Italiana con Pepperonni\r\n- Pepperoni\r\n- Masa\r\n- Queso Mozzarella\r\n- Salsa', 'L', 1, 0, NULL, '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68de034491f08.png', '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68de03449206e.png', '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68de034492192.png', '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68de034492287.png', NULL, '2025-10-02 04:44:52', 'las_americas', 'root'),
(25, 4578, '236014578', 'Pizzon de pollo', 20000, 'pizzas', 'Pizzon de polllo', 'M', 1, 0, NULL, '../res/images/products/pizzaPizzon_de_pollo/Pizzon_de_pollo_68e56c70ab374.jpg', NULL, NULL, NULL, '../res/images/products/pizzaPizzon_de_pollo/236014578.png', '2025-10-07 19:39:28', 'las_americas', 'root');

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
(32, 21, '19', 300, 'gramo', 300, 'las_americas', 'root'),
(33, 22, '15', 100, 'gramo', 2500, 'las_americas', 'root'),
(34, 22, '17', 120, 'gramo', 2400, 'las_americas', 'root'),
(35, 22, '18', 200, 'gramo', 2000, 'las_americas', 'root'),
(36, 23, '15', 100, 'gramo', 2500, 'las_americas', 'root'),
(37, 23, '17', 150, 'gramo', 3000, 'las_americas', 'root'),
(38, 23, '18', 200, 'gramo', 2000, 'las_americas', 'root'),
(39, 24, '15', 150, 'gramo', 3750, 'las_americas', 'root'),
(40, 24, '17', 100, 'gramo', 2000, 'las_americas', 'root'),
(41, 24, '18', 200, 'gramo', 2000, 'las_americas', 'root'),
(42, 25, '15', 100, 'gramo', 2500, 'las_americas', 'root'),
(43, 25, '17', 100, 'gramo', 2000, 'las_americas', 'root'),
(44, 25, '18', 100, 'gramo', 1000, 'las_americas', 'root');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ruleta`
--

CREATE TABLE `ruleta` (
  `id` int NOT NULL,
  `estado` enum('1','0') COLLATE utf8mb4_spanish_ci NOT NULL,
  `premio1` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `premio2` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `premio3` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `premio4` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `premio5` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `premio6` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `premiada` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `premio` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `intentos` int NOT NULL,
  `unico` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `ruleta`
--

INSERT INTO `ruleta` (`id`, `estado`, `premio1`, `premio2`, `premio3`, `premio4`, `premio5`, `premio6`, `premiada`, `premio`, `intentos`, `unico`) VALUES
(1, '1', 'Perdiste', 'Ganáste', 'Otra véz', 'Perdíste', 'Ganáste', 'Perdíste', '[\"\",\"p2\",\"\",\"\",\"p5\",\"\"]', 'Pizza Hawaiana', 3, '68ea1bf5451d2');

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
-- Estructura de tabla para la tabla `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `id` int NOT NULL,
  `idsesion` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `idproducto` int NOT NULL,
  `producto` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio` int NOT NULL,
  `subtotal` int NOT NULL
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
  `metodopago` enum('efectivo','nequi','daviplata','bancolombia','davivienda','consignacion','otro') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'efectivo',
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

INSERT INTO `ventas` (`id`, `consecutivo`, `idventa`, `metodopago`, `idcaja`, `id_producto`, `producto`, `cantidad`, `porciones`, `precio`, `total`, `recibido`, `unico`, `descuento`, `cliente`, `clidoc`, `usuario`, `sucursal`, `fechareg`) VALUES
(58, '00000001', '68e745b15ff52', 'efectivo', 1, 10, 'Hamburguesa Phoenix', 1, 1, 25000, 25000, 50000, 1, 0, 'Indefinido', '0000000000', 'root', 'las_americas', '2025-10-09 05:18:41'),
(59, '00000001', '68e745b15ff52', 'efectivo', 1, 14, 'Pizza Ranchera Big', 2, 2, 4500, 9000, 50000, 1, 1000, 'Indefinido', '0000000000', 'root', 'las_americas', '2025-10-09 05:18:41'),
(60, '00000002', '68e749fec2c59', 'efectivo', 2, 10, 'Hamburguesa Phoenix', 1, 1, 25000, 25000, 30000, 1, 0, 'Indefinido', '0000000000', 'root', 'las_americas', '2025-10-09 05:37:02'),
(61, '00000003', '68e74a2f41241', 'efectivo', 2, 14, 'Pizza Ranchera Big', 1, 1, 4500, 4500, 10000, 1, 500, 'Indefinido', '0000000000', 'root', 'las_americas', '2025-10-09 05:37:51'),
(62, '00000004', '68e9436e0a17c', 'efectivo', 2, 14, 'Pizza Ranchera Big', 1, 1, 4500, 4500, 50000, 1, 500, 'Anna Does', '15847885', 'root', 'las_americas', '2025-10-10 17:33:34'),
(63, '00000004', '68e9436e0a17c', 'efectivo', 2, 25, 'Pizzon de pollo', 1, 1, 20000, 20000, 50000, 1, 0, 'Anna Does', '15847885', 'root', 'las_americas', '2025-10-10 17:33:34'),
(64, '00000005', '68e9587ef17ac', 'efectivo', 3, 25, 'Pizzon de pollo', 1, 1, 20000, 20000, 20000, 1, 0, 'Indefinido', '0000000000', 'root', 'las_americas', '2025-10-10 19:03:26'),
(65, '00000006', '68e9667ada307', 'efectivo', 3, 21, 'Pizza Italiana', 1, 1, 5625, 5625, 6000, 1, 0, 'Indefinido', '0000000000', 'root', 'las_americas', '2025-10-10 20:03:06');

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

                UPDATE caja
                SET ventas = COALESCE(ventas, 0) - OLD.total,
                    descuentos = COALESCE(descuentos, 0) - OLD.descuento,
                    ingresos = COALESCE(ingresos, 0) - OLD.total
                WHERE codcaja = OLD.idcaja;

                IF OLD.metodopago = 'efectivo' THEN 
                    UPDATE entidades SET efectivo = COALESCE(efectivo, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'nequi' THEN 
                    UPDATE entidades SET nequi = COALESCE(nequi, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'daviplata' THEN 
                    UPDATE entidades SET daviplata = COALESCE(daviplata, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'bancolombia' THEN 
                    UPDATE entidades SET bancolombia = COALESCE(bancolombia, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'davivienda' THEN 
                    UPDATE entidades SET davivienda = COALESCE(davivienda, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'consignacion' THEN 
                    UPDATE entidades SET consignacion = COALESCE(consignacion, 0) - OLD.total;
                ELSEIF OLD.metodopago = 'otros' THEN 
                    UPDATE entidades SET otros = COALESCE(otros, 0) - OLD.total;
                END IF;

                INSERT INTO movimientos
                (codcaja,tipo,concepto,entidad,valor,sucursal)
                VALUES (OLD.idcaja,'devolucion',CONCAT('Devolución de ',OLD.producto,' por ',OLD.usuario),OLD.metodopago,OLD.total,OLD.sucursal);

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

                UPDATE caja
                SET ventas = COALESCE(ventas, 0) + NEW.total,
                descuentos = COALESCE(descuentos, 0) + NEW.descuento,
                ingresos = COALESCE(ingresos, 0) + NEW.total
                WHERE codcaja = NEW.idcaja;

                IF NEW.metodopago = 'efectivo' THEN 
                    UPDATE entidades SET efectivo = COALESCE(efectivo, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'nequi' THEN 
                    UPDATE entidades SET nequi = COALESCE(nequi, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'daviplata' THEN 
                    UPDATE entidades SET daviplata = COALESCE(daviplata, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'bancolombia' THEN 
                    UPDATE entidades SET bancolombia = COALESCE(bancolombia, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'davivienda' THEN 
                    UPDATE entidades SET davivienda = COALESCE(davivienda, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'consignacion' THEN 
                    UPDATE entidades SET consignacion = COALESCE(consignacion, 0) + NEW.total;
                ELSEIF NEW.metodopago = 'otros' THEN 
                    UPDATE entidades SET otros = COALESCE(otros, 0) + NEW.total;
                END IF;

                INSERT INTO movimientos
                (codcaja,tipo,concepto,entidad,valor,sucursal)
                VALUES (NEW.idcaja,'venta',CONCAT(NEW.producto,' vendido por ',NEW.usuario),NEW.metodopago,NEW.total,NEW.sucursal);

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

                    UPDATE caja
                    SET ventas = COALESCE(ventas, 0) - OLD.total,
                    descuentos = COALESCE(descuentos, 0) - OLD.descuento,
                    ingresos = COALESCE(ingresos, 0) - OLD.total
                    WHERE codcaja = OLD.idcaja;

                    IF OLD.metodopago = 'efectivo' THEN 
                        UPDATE entidades SET efectivo = COALESCE(efectivo, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'nequi' THEN 
                        UPDATE entidades SET nequi = COALESCE(nequi, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'daviplata' THEN 
                        UPDATE entidades SET daviplata = COALESCE(daviplata, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'bancolombia' THEN 
                        UPDATE entidades SET bancolombia = COALESCE(bancolombia, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'davivienda' THEN 
                        UPDATE entidades SET davivienda = COALESCE(davivienda, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'consignacion' THEN 
                        UPDATE entidades SET consignacion = COALESCE(consignacion, 0) - OLD.total;
                    ELSEIF OLD.metodopago = 'otros' THEN 
                        UPDATE entidades SET otros = COALESCE(otros, 0) - OLD.total;
                    END IF;

                    INSERT INTO movimientos
                    (codcaja,tipo,concepto,entidad,valor,sucursal)
                    VALUES (OLD.idcaja,'devolucion',CONCAT('Devolución de ',OLD.producto,' por ',OLD.usuario),OLD.metodopago,OLD.total,OLD.sucursal);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codcaja` (`codcaja`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categoria_principal`
--
ALTER TABLE `categoria_principal`
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
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `obligaciones`
--
ALTER TABLE `obligaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `operadores`
--
ALTER TABLE `operadores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_code` (`id_code`);

--
-- Indices de la tabla `product_ingredients`
--
ALTER TABLE `product_ingredients`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ruleta`
--
ALTER TABLE `ruleta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sell_cart`
--
ALTER TABLE `sell_cart`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `shopping_cart`
--
ALTER TABLE `shopping_cart`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `categoria_principal`
--
ALTER TABLE `categoria_principal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `obligaciones`
--
ALTER TABLE `obligaciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `operadores`
--
ALTER TABLE `operadores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `product_ingredients`
--
ALTER TABLE `product_ingredients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `ruleta`
--
ALTER TABLE `ruleta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sell_cart`
--
ALTER TABLE `sell_cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
