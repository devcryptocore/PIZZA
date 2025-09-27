-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20241206.16f3583c6d
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 27-09-2025 a las 20:37:53
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
(38, 11, 1.250, 12, 6875, 'las_americas', 'admin'),
(39, 10, 2.000, 0, 50000, 'las_americas', 'admin'),
(40, 14, 3.000, 24, 5000, 'las_americas', 'admin'),
(41, 16, 11.000, 0, 63000, 'las_americas', 'admin'),
(42, 19, 1.000, 0, 7500, 'las_americas', 'admin');

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
(15, 'Carne de res', 25, 'gramo', 8237.500, 500, '2025-09-30', '2025-09-17 02:38:29', 'las_americas', 'admin'),
(16, 'Piña', 10, 'gramo', 3500.000, 300, '2025-09-30', '2025-09-17 02:38:53', 'las_americas', 'admin'),
(17, 'Pollo', 20, 'gramo', 3225.000, 200, '2025-09-30', '2025-09-17 02:39:14', 'las_americas', 'admin'),
(18, 'Queso mozzarella', 10, 'gramo', 6837.500, 1000, '2025-09-30', '2025-09-17 02:39:51', 'las_americas', 'admin'),
(19, 'Masa', 1, 'gramo', 8400.000, 200, '2025-10-11', '2025-09-26 00:21:55', 'las_americas', 'admin'),
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
  `oferta` tinyint(1) NOT NULL DEFAULT '0',
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
(10, 'Hamburguesa Phoenix', 25000, 'hamburguesa', 'una hamburguesa', 'innecesario', 1, 0, NULL, '../res/images/products/hamburguesaHamburguesa_Phoenix/Hamburguesa_Phoenix_68d6f66359700.jpg', '../res/images/products/hamburguesaPhoenix/Phoenix_68d20be217c76.png', '../res/images/products/hamburguesaPhoenix/Phoenix_68d20be217d6f.png', '../res/images/products/hamburguesaPhoenix/Phoenix_68d20be217f72.jpeg', '2025-09-23 02:54:26', 'las_americas', 'admin'),
(11, 'Pizza Italiana', 55000, 'pizza', 'Pizza italiana con ingredientes italianos.\r\n- Pepperonni\r\n- Mozzarella\r\n- Masa', 'L', 1, 0, NULL, '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68d30fc69250c.png', '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68d30fc692875.png', '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68d30fc692a5f.png', '../res/images/products/pizzaPizza_Italiana/Pizza_Italiana_68d30fc692b45.png', '2025-09-23 21:23:18', 'las_americas', 'admin'),
(14, 'Pizza Ranchera Big', 40000, 'pizza', 'Pizza ranchera del rancho verde y ni tan verde por que todo está lleno de polvo y acabado como la cara de tu tía', 'L', 1, 0, NULL, '../res/images/products/pizzaPizza_Ranchera_Big/Pizza_Ranchera_Big_68d5e4a75a9ce.png', NULL, '../res/images/products/pizzaPizza_Ranchera_Big/Pizza_Ranchera_Big_68d5e4a75ab1e.png', NULL, '2025-09-26 00:56:07', 'las_americas', 'admin'),
(19, 'Jugo Hit caja', 7500, 'jugo', 'jugo hit', 'innecesario', 0, 0, NULL, NULL, NULL, NULL, NULL, '2025-09-26 22:42:01', 'las_americas', 'admin');

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
(28, 19, '22', 5, 'unidad', 20000, 'las_americas', 'admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sell_cart`
--

CREATE TABLE `sell_cart` (
  `id` int NOT NULL,
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
  `contraseña` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `sucursal` varchar(256) COLLATE utf8mb4_spanish_ci NOT NULL,
  `estado` int NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `documento`, `role`, `usuario`, `contraseña`, `sucursal`, `estado`, `fecha`) VALUES
(1, '10548777', 'administrator', 'admin', '12345', 'las_americas', 1, '2025-09-26 14:08:13');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `active_products`
--
ALTER TABLE `active_products`
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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `active_products`
--
ALTER TABLE `active_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `product_ingredients`
--
ALTER TABLE `product_ingredients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `sell_cart`
--
ALTER TABLE `sell_cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
