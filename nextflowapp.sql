-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20241206.16f3583c6d
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 02-11-2025 a las 17:50:38
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
-- Estructura de tabla para la tabla `about_section`
--

CREATE TABLE `about_section` (
  `unico` varchar(1) COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'X',
  `nosotros` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci,
  `faqs` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci,
  `contacto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

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
(1, 1, 0.000, 0, 30000, 'system', 'admin'),
(2, 2, 3.000, 0, 20000, 'system', 'admin'),
(3, 3, 3.000, 0, 30000, 'system', 'admin'),
(4, 4, 3.000, 0, 40000, 'system', 'admin'),
(5, 6, 1.625, 13, 7500, 'system', 'admin'),
(6, 9, 2.000, 0, 30000, 'system', 'admin');

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
(1, '0', 100000, 75000, 0, 75000, 10000, 'admin', 'system', 1, '2025-10-27 20:57:21'),
(2, '1', 100000, 127500, 0, 127500, 7500, 'admin', 'system', 2, '2025-10-30 13:03:07');

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
(1, 'pollo', '../res/images/categories/pollo/pollo_68ffdc1cb252e.jpg', 1),
(2, 'carnes', '../res/images/categories/carnes/carnes_68ffdfcf8f282.jpg', 1),
(3, 'pizzas', '../res/images/categories/pizzas/pizzas_68ffdfdb26d78.webp', 1),
(4, 'pastas', '../res/images/categories/pastas/pastas_68ffdfe6b6c76.jpg', 1),
(5, 'ensaladas', '../res/images/categories/ensaladas/ensaladas_68ffdff89304f.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_principal`
--

CREATE TABLE `categoria_principal` (
  `id` int NOT NULL,
  `categoria` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `categoria_principal`
--

INSERT INTO `categoria_principal` (`id`, `categoria`) VALUES
(1, 9),
(2, 6),
(3, 4),
(4, 3),
(5, 2),
(6, 1);

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
(1, 'Anna Doe', '321654987', 'Cra 1a #25-34', '32054785544', 45000, '2025-10-27 21:23:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company`
--

CREATE TABLE `company` (
  `unica` varchar(1) COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'X',
  `organizacion` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `ptelefono` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `stelefono` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `email` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `direccion` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `nit` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `encargado` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `documento` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fecharegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logotipo` text COLLATE utf8mb4_spanish_ci,
  `bot_id` varchar(256) COLLATE utf8mb4_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `company`
--

INSERT INTO `company` (`unica`, `organizacion`, `ptelefono`, `stelefono`, `email`, `direccion`, `nit`, `encargado`, `documento`, `fecharegistro`, `logotipo`, `bot_id`) VALUES
('X', 'The Grill', '3106574835', '3106574835', 'thegrill@example.com', 'Cra 12a #47-35 Diagonal C', '98523366-1', 'Jhon Doe', '123456789', '2025-10-27 20:51:03', '../res/images/company//logo_68ffdb37e85a1.webp', NULL);

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidades`
--

CREATE TABLE `entidades` (
  `unico` varchar(1) COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'X',
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

INSERT INTO `entidades` (`unico`, `efectivo`, `nequi`, `daviplata`, `bancolombia`, `consignacion`, `otro`) VALUES
('X', 593000, 7000, 85000, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredientes`
--

CREATE TABLE `ingredientes` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `costo` int NOT NULL,
  `costo_und` decimal(30,15) NOT NULL,
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

INSERT INTO `ingredientes` (`id`, `nombre`, `costo`, `costo_und`, `unidad`, `stock`, `stock_minimo`, `vencimiento`, `creado`, `sucursal`, `usuario`) VALUES
(1, 'Arróz', 200000, 25.000000000000000, 'gramo', 5400.000, 500, '2025-11-06', '2025-10-27 20:53:30', 'system', 'admin'),
(2, 'Pollo', 80000, 16.000000000000000, 'gramo', 1900.000, 1000, '2025-11-03', '2025-10-27 20:54:08', 'system', 'admin'),
(3, 'Queso Mozzarrella', 100000, 20.000000000000000, 'gramo', 4600.000, 1000, '2025-12-06', '2025-10-27 21:04:38', 'system', 'admin'),
(4, 'Carne de Res', 250000, 25.000000000000000, 'gramo', 10000.000, 1000, '2025-11-08', '2025-10-27 21:05:05', 'system', 'admin'),
(5, 'Pasta', 50000, 25.000000000000000, 'gramo', 800.000, 500, '2026-01-02', '2025-10-27 21:05:38', 'system', 'admin'),
(6, 'Tomate', 25000, 5.000000000000000, 'gramo', 4000.000, 500, '2025-11-08', '2025-10-27 21:06:24', 'system', 'admin'),
(7, 'Cebolla', 25000, 5.000000000000000, 'gramo', 4100.000, 500, '2025-11-08', '2025-10-27 21:06:41', 'system', 'admin'),
(8, 'Jamón', 50000, 10.000000000000000, 'gramo', 4800.000, 500, '2025-11-08', '2025-10-27 21:07:15', 'system', 'admin'),
(9, 'Camarones', 100000, 20.000000000000000, 'gramo', 4700.000, 1000, '2025-11-03', '2025-10-27 21:07:48', 'system', 'admin'),
(10, 'Pepperonni', 50000, 25.000000000000000, 'gramo', 1600.000, 500, '2025-11-13', '2025-10-27 21:08:17', 'system', 'admin'),
(11, 'Carne de cordero', 200000, 40.000000000000000, 'gramo', 4400.000, 1000, '2025-11-08', '2025-10-27 21:09:26', 'system', 'admin');

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
(1, 1, 'Arróz', 2000, 'gramo', 50000, '2025-10-27 20:53:30', 'admin', 'system'),
(2, 2, 'Pollo', 5000, 'gramo', 80000, '2025-10-27 20:54:08', 'admin', 'system'),
(3, 1, 'Arróz', 5000, 'gramo', 125000, '2025-10-27 21:03:37', 'admin', 'system'),
(4, 3, 'Queso Mozzarrella', 5000, 'gramo', 100000, '2025-10-27 21:04:38', 'admin', 'system'),
(5, 4, 'Carne de Res', 10000, 'gramo', 250000, '2025-10-27 21:05:05', 'admin', 'system'),
(6, 5, 'Pasta', 2000, 'gramo', 50000, '2025-10-27 21:05:38', 'admin', 'system'),
(7, 6, 'Tomate', 5000, 'gramo', 25000, '2025-10-27 21:06:24', 'admin', 'system'),
(8, 7, 'Cebolla', 5000, 'gramo', 25000, '2025-10-27 21:06:41', 'admin', 'system'),
(9, 8, 'Jamón', 5000, 'gramo', 50000, '2025-10-27 21:07:15', 'admin', 'system'),
(10, 9, 'Camarones', 5000, 'gramo', 100000, '2025-10-27 21:07:48', 'admin', 'system'),
(11, 10, 'Pepperonni', 2000, 'gramo', 50000, '2025-10-27 21:08:17', 'admin', 'system'),
(12, 11, 'Carne de cordero', 5000, 'gramo', 200000, '2025-10-27 21:09:26', 'admin', 'system'),
(15, 1, 'Arróz', 1000, 'gramo', 25000, '2025-10-30 12:56:40', 'admin', 'system');

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
(1, 1, 'venta', 'Pollo a la naranja vendido por admin', 'efectivo', 30000, 'system', '2025-10-27 20:57:54'),
(2, 1, 'egreso', 'Gasto de $10.000 por Pago a domiciliario registrado por admin', 'efectivo', 10000, 'system', '2025-10-27 20:59:52'),
(3, 1, 'venta', 'Pizza Italiana vendido por admin', 'efectivo', 15000, 'system', '2025-10-27 21:23:29'),
(4, 1, 'venta', 'Pasta con camarones vendido por admin', 'efectivo', 30000, 'system', '2025-10-27 21:23:29'),
(5, 2, 'venta', 'Pollo a la naranja vendido por admin', 'efectivo', 30000, 'system', '2025-10-30 13:03:27'),
(6, 2, 'venta', 'Pollo a la naranja vendido por admin', 'efectivo', 30000, 'system', '2025-10-30 13:17:21'),
(7, 2, 'venta', 'Pizza Italiana vendido por admin', 'nequi', 7500, 'system', '2025-10-30 13:22:37'),
(8, NULL, 'transferencia', 'Transferencia de $25.000 desde efectivo hacia daviplata por admin', 'efectivo', 25000, 'system', '2025-10-30 13:48:14'),
(9, 2, 'egreso', 'Gasto de $500 por test 500 registrado por admin', 'nequi', 500, 'system', '2025-10-30 13:52:45'),
(10, 2, 'egreso', 'Gasto de $7.000 por Cosas registrado por admin', 'efectivo', 7000, 'system', '2025-10-30 14:02:16'),
(11, 2, 'venta', 'Pollo a la naranja vendido por admin', 'daviplata', 60000, 'system', '2025-10-30 14:07:12');

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
  `apellido` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
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
(1, 'Jhon', 'Doe', '123456789', '3106574835', 'Cra 12a #47-35 Diagonal C', 'thegrill@example.com', '../res/images/company//logo_68ffdb37e85a1.webp', '2025-10-27 20:51:03');

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
(1, '68ffe403c39d4', 'session_1761600375529', '[{\"idproducto\":1,\"precio\":30000,\"cantidad\":1,\"subtotal\":30000},{\"idproducto\":2,\"precio\":20000,\"cantidad\":1,\"subtotal\":20000}]', 'Juan Perez', '321507455', 'Cra 2a #23-34 Centro', '', 'Frente al parque', '2025-10-27 21:28:35', 'recibido', NULL, NULL, NULL);

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
(1, 1, '582530001', 'Pollo a la naranja', 30000, 'pollo', 'Delicioso pollo a la naranja, ingredientes frescos y salsas de la casa', 'innecesario', 0, 0, NULL, '../res/images/products/polloPollo_a_la_naranja/Pollo_a_la_naranja_68ffdc6413c76.png', NULL, NULL, NULL, '../res/images/products/polloPollo_a_la_naranja/582530001.png', '2025-10-27 20:56:04', 'system', 'admin'),
(2, 2, '637180002', 'Ensalada de verduras', 20000, 'ensaladas', 'Deliciosa ensalada de verduras frescas, del campo a su mesa', 'innecesario', 1, 0, NULL, '../res/images/products/ensaladasEnsalada_de_verduras/Ensalada_de_verduras_68ffe03f1df55.png', NULL, NULL, NULL, '../res/images/products/ensaladasEnsalada_de_verduras/637180002.png', '2025-10-27 21:12:31', 'system', 'admin'),
(3, 3, '732420003', 'Filetes de pollo', 30000, 'pollo', 'Filetes de pollo con verduras y pasta', 'innecesario', 1, 0, NULL, '../res/images/products/polloFiletes_de_pollo/Filetes_de_pollo_68ffe096d5f48.png', NULL, NULL, NULL, '../res/images/products/polloFiletes_de_pollo/732420003.png', '2025-10-27 21:13:58', 'system', 'admin'),
(4, 5, '718500005', 'Cordero a la parrilla', 40000, 'carnes', 'Deliciosa carne de cordero a la parrilla, en su punto', 'innecesario', 1, 0, NULL, '../res/images/products/carnesCordero_a_la_parrilla/Cordero_a_la_parrilla_68ffe0dc08ac1.png', NULL, NULL, NULL, '../res/images/products/carnesCordero_a_la_parrilla/718500005.png', '2025-10-27 21:15:08', 'system', 'admin'),
(6, 6, '453570006', 'Pizza Italiana', 60000, 'pizzas', 'Pizza de Italiana original, sabores originales', 'L', 1, 0, NULL, '../res/images/products/pizzasPizza_Italiana/Pizza_Italiana_68ffe1820cc93.png', NULL, NULL, NULL, '../res/images/products/pizzasPizza_Italiana/453570006.png', '2025-10-27 21:17:54', 'system', 'admin'),
(9, 7, '577510007', 'Pasta con camarones', 30000, 'pastas', 'Deliciosa pasta con camarones en salsa de la casa', 'innecesario', 1, 0, NULL, '../res/images/products/pastasPasta_con_camarones/Pasta_con_camarones_68ffe203e140e.png', NULL, NULL, NULL, '../res/images/products/pastasPasta_con_camarones/577510007.png', '2025-10-27 21:20:03', 'system', 'admin');

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
(1, 1, '1', 400, 'gramo', 10000, 'system', 'admin'),
(2, 1, '2', 400, 'gramo', 6400, 'system', 'admin'),
(3, 2, '6', 150, 'gramo', 750, 'system', 'admin'),
(4, 2, '7', 150, 'gramo', 750, 'system', 'admin'),
(5, 3, '1', 200, 'gramo', 5000, 'system', 'admin'),
(6, 3, '2', 300, 'gramo', 4800, 'system', 'admin'),
(7, 3, '5', 100, 'gramo', 2500, 'system', 'admin'),
(8, 3, '6', 50, 'gramo', 250, 'system', 'admin'),
(9, 3, '7', 50, 'gramo', 250, 'system', 'admin'),
(10, 4, '6', 50, 'gramo', 250, 'system', 'admin'),
(11, 4, '7', 50, 'gramo', 250, 'system', 'admin'),
(12, 4, '11', 200, 'gramo', 8000, 'system', 'admin'),
(13, 6, '2', 100, 'gramo', 1600, 'system', 'admin'),
(14, 6, '3', 200, 'gramo', 4000, 'system', 'admin'),
(15, 6, '6', 50, 'gramo', 250, 'system', 'admin'),
(16, 6, '8', 100, 'gramo', 1000, 'system', 'admin'),
(17, 6, '10', 200, 'gramo', 5000, 'system', 'admin'),
(18, 9, '5', 300, 'gramo', 7500, 'system', 'admin'),
(19, 9, '6', 50, 'gramo', 250, 'system', 'admin'),
(20, 9, '7', 50, 'gramo', 250, 'system', 'admin'),
(21, 9, '9', 100, 'gramo', 2000, 'system', 'admin');

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

--
-- Volcado de datos para la tabla `shopping_cart`
--

INSERT INTO `shopping_cart` (`id`, `idsesion`, `idproducto`, `producto`, `cantidad`, `precio`, `subtotal`) VALUES
(3, 'session_1761600802785', 3, 'Filetes de pollo', 1, 30000, 30000),
(4, 'session_1761600802785', 6, 'Pizza Italiana', 3, 7500, 22500);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

CREATE TABLE `sucursales` (
  `id` int NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `ubicacion` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `direccion` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `telefono` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `foto` varchar(500) COLLATE utf8mb4_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telegram_bot`
--

CREATE TABLE `telegram_bot` (
  `identificator` varchar(1) COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'X',
  `tkn` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `chatid` varchar(256) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `telegram_bot`
--

INSERT INTO `telegram_bot` (`identificator`, `tkn`, `chatid`, `fecha`) VALUES
('X', '8207105568:AAEG1yPhGRh3PJHPklzuWX7vndpjI77IM_Q', NULL, '2025-11-02 05:55:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `documento` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `rol` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `usuario` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `contrasena` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `estado` enum('1','0') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT '1',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `documento`, `rol`, `usuario`, `contrasena`, `sucursal`, `estado`, `fecha`) VALUES
(1, '123456789', 'administrador', 'admin', '$2y$12$ETf9jQO6eMloYqBVBMFUe.H1zZrXQXIFEdygRzfnZ3wN/v8h1nmTG', 'system', '1', '2025-10-27 20:51:24');

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
(1, '00000001', '68ffdcd20c71a', 'efectivo', 1, 1, 'Pollo a la naranja', 1, 1, 30000, 30000, 50000, 1, 0, 'Indefinido', '0000000000', 'admin', 'system', '2025-10-27 20:57:54'),
(2, '00000002', '68ffe2d1743ad', 'efectivo', 1, 6, 'Pizza Italiana', 2, 2, 7500, 15000, 50000, 1, 0, 'Anna Doe', '321654987', 'admin', 'system', '2025-10-27 21:23:29'),
(3, '00000002', '68ffe2d1743ad', 'efectivo', 1, 9, 'Pasta con camarones', 1, 1, 30000, 30000, 50000, 1, 0, 'Anna Doe', '321654987', 'admin', 'system', '2025-10-27 21:23:29'),
(4, '00000003', '6903621f1b367', 'efectivo', 2, 1, 'Pollo a la naranja', 1, 1, 30000, 30000, 50000, 1, 0, 'Indefinido', '0000000000', 'admin', 'system', '2025-10-30 13:03:27'),
(5, '00000004', '69036561ae6a2', 'efectivo', 2, 1, 'Pollo a la naranja', 1, 1, 30000, 30000, 40000, 1, 0, 'Indefinido', '0000000000', 'admin', 'system', '2025-10-30 13:17:21'),
(6, '00000005', '6903669d2c893', 'nequi', 2, 6, 'Pizza Italiana', 1, 1, 7500, 7500, 7500, 1, 0, 'Indefinido', '0000000000', 'admin', 'system', '2025-10-30 13:22:37'),
(7, '00000006', '690371102a5dc', 'daviplata', 2, 1, 'Pollo a la naranja', 2, 2, 30000, 60000, 60000, 1, 0, 'Indefinido', '0000000000', 'admin', 'system', '2025-10-30 14:07:12');

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
                DECLARE isunidad INT;

                SELECT unidades, porciones
                INTO v_unidades, v_porciones
                FROM active_products
                WHERE id_producto = NEW.id_producto
                AND sucursal   = NEW.sucursal
                LIMIT 1;

                IF v_porciones > 0 THEN
                    SET v_porciones = v_porciones - NEW.cantidad;
                    SET v_unidades = v_porciones / 8;
                    SET isunidad = 0;
                ELSE
                    SET v_unidades = v_unidades - NEW.cantidad;
                    SET v_porciones = 0;
                    SET isunidad = 1;
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

                IF v_unidades <= 0 AND isunidad = 1 OR v_porciones <= 0 AND isunidad = 0 THEN
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
-- Indices de la tabla `about_section`
--
ALTER TABLE `about_section`
  ADD PRIMARY KEY (`unico`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categoria` (`categoria`);

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
-- Indices de la tabla `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`unica`);

--
-- Indices de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `entidades`
--
ALTER TABLE `entidades`
  ADD PRIMARY KEY (`unico`);

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
  ADD UNIQUE KEY `id_code` (`id_code`),
  ADD UNIQUE KEY `cod_barras` (`cod_barras`);

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
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `categoria_principal`
--
ALTER TABLE `categoria_principal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `inversion`
--
ALTER TABLE `inversion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `obligaciones`
--
ALTER TABLE `obligaciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `operadores`
--
ALTER TABLE `operadores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `product_ingredients`
--
ALTER TABLE `product_ingredients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `ruleta`
--
ALTER TABLE `ruleta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sell_cart`
--
ALTER TABLE `sell_cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
