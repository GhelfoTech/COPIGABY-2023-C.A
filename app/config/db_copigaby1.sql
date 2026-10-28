-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-10-2026 a las 23:57:55
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_copigaby1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banco`
--

CREATE TABLE `banco` (
  `codigo_banco` int(4) NOT NULL,
  `nombre_banco` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `banco`
--

INSERT INTO `banco` (`codigo_banco`, `nombre_banco`, `estado`) VALUES
(102, 'Banco de Venezuela', 1),
(104, 'Banco Venezolano de Crédito', 1),
(105, 'Banco Mercantil', 1),
(108, 'Banco Provincial (BBVA)', 1),
(114, 'Banco del Caribe (Bancaribe)', 1),
(115, 'Banco Exterior', 1),
(128, 'Banco Banco Caroní', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cashea_credito`
--

CREATE TABLE `cashea_credito` (
  `id_cashea_credito` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `monto_inicial_pagado` int(11) NOT NULL,
  `monto_financiado` decimal(10,2) NOT NULL,
  `porcentaje_financiado` int(11) NOT NULL,
  `monto_pendiente` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cashea_deposito`
--

CREATE TABLE `cashea_deposito` (
  `id_cashea_deposito` int(11) NOT NULL,
  `fecha_deposito` datetime NOT NULL,
  `monto_recibido` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cashea_deposito_detalle`
--

CREATE TABLE `cashea_deposito_detalle` (
  `id_cashea_deposito_detalle` int(11) NOT NULL,
  `id_cashea_credito` int(11) NOT NULL,
  `id_cashea_deposito` int(11) NOT NULL,
  `monto_aplicado` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `codigo_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`codigo_categoria`, `nombre_categoria`, `estado`) VALUES
(101, 'PAPELERIA', 1),
(102, 'MERCERIA', 1),
(103, 'BISUTERIA', 1),
(104, 'JUGUETES', 1),
(105, 'ELECTRONICA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `cedula_cliente` int(12) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `telefono` varchar(12) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`cedula_cliente`, `nombre`, `telefono`, `correo`, `direccion`, `estado`) VALUES
(19908765, 'Lucas Mendez', '04120583967', 'mlucas@gmail.com', 'Barquisimeto', 1),
(23456654, 'MICHELLE', '04245555555', 'MICHELLE@gmail.com', 'BASIL', 1),
(32137731, 'Yeilyn', '04228690511', 'Yeilyn32gmail.com', 'Andres Eloy', 1),
(32137732, 'Yeilyn Rodriguez', '04228690512', 'Yeilyn38r@gmail.com', 'Calle', 1),
(32345650, 'JUAN GOMEZ', '04245555555', 'JUAN@gmail.com', 'NICARAGUA', 1),
(32456432, 'VICTORIA', '04245678976', 'VICTORIA@gmail.com', 'LA VICTORIA', 1),
(33234543, 'Juan Perez', '04245555555', 'Juanperez@gmail.com', 'Pueblo nuevo', 1),
(123454321, 'RAMON', '04245555555', 'RAMON@gmail.com', 'ARGENTINA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `codigo_compra` int(11) NOT NULL,
  `codigo_proveedor` int(11) NOT NULL,
  `cedula_usuario` int(11) NOT NULL,
  `numero_factura_proveedor` varchar(10) NOT NULL,
  `fecha_compra` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`codigo_compra`, `codigo_proveedor`, `cedula_usuario`, `numero_factura_proveedor`, `fecha_compra`, `monto_total`, `estado`) VALUES
(518, 317317661, 323232323, '', '2026-06-25', 0.50, 1),
(519, 317317661, 323232323, '', '2026-06-25', 1.00, 1),
(520, 317317661, 323232323, '', '2026-06-25', 0.80, 1),
(521, 317317661, 323232323, '', '2026-06-25', 3.10, 1),
(522, 317317661, 323232323, '', '2026-06-25', 0.15, 1),
(523, 317317661, 323232323, '', '2026-06-25', 0.20, 1),
(524, 317317661, 323232323, '00510', '2026-07-04', 5.75, 1),
(525, 317317661, 323232323, '00511', '2026-07-04', 24.00, 1),
(526, 317317661, 323232323, '00511', '2026-07-05', 6.00, 1),
(527, 317317661, 323232323, '00510', '2026-07-07', 4.50, 1),
(530, 317317661, 323232323, '00510', '2026-07-07', 4.50, 1),
(532, 317317661, 323232323, 'TEST-17834', '2026-07-07', 3.00, 0),
(533, 317317661, 323232323, '00510', '2026-07-07', 2.00, 1),
(538, 988760998, 323232323, '00510', '2026-07-07', 2.00, 1),
(540, 988760998, 323232323, '00510', '2026-07-07', 2.00, 1),
(541, 317317661, 323232323, '', '2026-07-07', 0.20, 1),
(542, 317317661, 323232323, '00510', '2026-07-07', 0.20, 1),
(548, 317317661, 323232323, '00511', '2026-07-08', 2.00, 1),
(550, 988760998, 323232323, '00511', '2026-10-24', 2.00, 1),
(551, 317317661, 323232323, '00512', '2026-10-24', 7.00, 1),
(553, 317317661, 323232323, '00512777', '2026-10-24', 3.00, 1),
(554, 317317661, 323232323, '005122222', '2026-10-24', 5.00, 1),
(555, 988760998, 323232323, '66666', '2026-10-24', 1.30, 1),
(557, 317317661, 323232323, '777777777', '2026-10-24', 1.00, 1),
(558, 317317661, 323232323, '005116666', '2026-10-24', 2.00, 1),
(559, 317317661, 323232323, '005114444', '2026-10-24', 3.00, 1),
(560, 317317661, 323232323, '00511777', '2026-10-06', 1.50, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `codigo_detalle_compra` int(11) NOT NULL,
  `codigo_compra` int(11) NOT NULL,
  `codigo_producto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_compra`
--

INSERT INTO `detalle_compra` (`codigo_detalle_compra`, `codigo_compra`, `codigo_producto`, `cantidad`, `costo_unitario`, `subtotal`) VALUES
(87657, 518, 7654, 1.00, 0.50, 0.50),
(87658, 519, 76543, 2.00, 0.50, 1.00),
(87659, 520, 759767, 1.00, 0.30, 0.30),
(87660, 520, 759766, 1.00, 0.50, 0.50),
(87661, 521, 759160, 1.00, 3.00, 3.00),
(87662, 521, 759199, 1.00, 0.10, 0.10),
(87663, 522, 759765, 1.00, 0.15, 0.15),
(87664, 523, 98765, 1.00, 0.20, 0.20),
(87665, 524, 7654, 1.00, 0.50, 0.50),
(87666, 524, 76543, 1.00, 0.50, 0.50),
(87667, 524, 98765, 1.00, 0.20, 0.20),
(87668, 524, 759160, 1.00, 3.00, 3.00),
(87669, 524, 759199, 1.00, 0.10, 0.10),
(87670, 524, 759765, 1.00, 0.15, 0.15),
(87671, 524, 759766, 1.00, 0.50, 0.50),
(87672, 524, 759766, 1.00, 0.50, 0.50),
(87673, 524, 759767, 1.00, 0.30, 0.30),
(87674, 525, 759768, 12.00, 2.00, 24.00),
(87675, 526, 759160, 2.00, 3.00, 6.00),
(87676, 527, 759160, 1.00, 3.00, 3.00),
(87677, 527, 759769, 1.00, 1.50, 1.50),
(87679, 530, 759160, 1.00, 3.00, 3.00),
(87680, 530, 759769, 1.00, 1.50, 1.50),
(87681, 532, 759160, 1.00, 3.00, 3.00),
(87682, 533, 7654, 1.00, 0.50, 0.50),
(87683, 533, 759769, 1.00, 1.50, 1.50),
(87689, 538, 7654, 1.00, 0.50, 0.50),
(87690, 538, 759769, 1.00, 1.50, 1.50),
(87692, 540, 7654, 1.00, 0.50, 0.50),
(87693, 540, 759769, 1.00, 1.50, 1.50),
(87694, 541, 98765, 1.00, 0.20, 0.20),
(87695, 542, 98765, 1.00, 0.20, 0.20),
(87702, 548, 759769, 1.00, 1.50, 1.50),
(87703, 548, 7654, 1.00, 0.50, 0.50),
(87705, 550, 759768, 1.00, 2.00, 2.00),
(87706, 551, 759770, 1.00, 1.00, 1.00),
(87707, 551, 759770, 1.00, 6.00, 6.00),
(87709, 553, 759772, 1.00, 3.00, 3.00),
(87710, 554, 759768, 1.00, 2.00, 2.00),
(87711, 554, 759771, 1.00, 3.00, 3.00),
(87712, 555, 759767, 1.00, 0.30, 0.30),
(87713, 555, 759770, 1.00, 1.00, 1.00),
(87716, 557, 759770, 1.00, 1.00, 1.00),
(87717, 558, 759768, 1.00, 2.00, 2.00),
(87718, 559, 759771, 1.00, 3.00, 3.00),
(87719, 560, 759769, 1.00, 1.50, 1.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pago`
--

CREATE TABLE `detalle_pago` (
  `codigo_detalle_pago` int(11) NOT NULL,
  `codigo_pago` int(11) NOT NULL,
  `codigo_moneda` int(11) NOT NULL,
  `codigo_metodo` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pago`
--

INSERT INTO `detalle_pago` (`codigo_detalle_pago`, `codigo_pago`, `codigo_moneda`, `codigo_metodo`, `monto`) VALUES
(627, 625, 510, 562, 8.37),
(628, 626, 510, 562, 119.70),
(629, 627, 983, 561, 0.23),
(630, 628, 983, 563, 0.23),
(631, 629, 983, 564, 80.73);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `codigo_detalle_pedido` int(11) NOT NULL,
  `codigo_pedido` int(11) NOT NULL,
  `codigo_producto` int(11) DEFAULT NULL,
  `codigo_servicio` int(11) DEFAULT NULL,
  `cantidad` decimal(12,2) NOT NULL DEFAULT 1.00,
  `precio_venta` decimal(10,2) NOT NULL,
  `codigo_unidad_medida` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`codigo_detalle_pedido`, `codigo_pedido`, `codigo_producto`, `codigo_servicio`, `cantidad`, `precio_venta`, `codigo_unidad_medida`, `subtotal`) VALUES
(668, 626, 759766, NULL, 3.00, 0.58, NULL, 1.74),
(669, 626, 76543, NULL, 2.00, 1.00, 26, 2.00),
(670, 626, 98765, NULL, 15.00, 0.24, NULL, 3.60),
(671, 627, NULL, 10, 2.00, 50.00, NULL, 100.00),
(672, 627, 76543, NULL, 5.00, 1.00, 26, 5.00),
(673, 628, 759199, NULL, 1.00, 0.20, NULL, 0.20),
(674, 629, 759199, NULL, 1.00, 0.20, NULL, 0.20),
(675, 630, 76543, NULL, 3.00, 1.00, 26, 3.00),
(676, 630, 759768, NULL, 11.00, 6.00, NULL, 66.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_transferencia`
--

CREATE TABLE `detalle_transferencia` (
  `codigo_detalle_transferencia` int(11) NOT NULL,
  `codigo_detalle_pago` int(11) NOT NULL,
  `codigo_banco` int(11) NOT NULL,
  `codigo_referencia` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_transferencia`
--

INSERT INTO `detalle_transferencia` (`codigo_detalle_transferencia`, `codigo_detalle_pago`, `codigo_banco`, `codigo_referencia`) VALUES
(593, 630, 108, '56789'),
(594, 631, 115, '3456');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dolar`
--

CREATE TABLE `dolar` (
  `codigo_dolar` int(11) NOT NULL,
  `codigo_taza` int(11) NOT NULL,
  `codigo_efectivo` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `efectivo`
--

CREATE TABLE `efectivo` (
  `cidigo_efectivo` int(11) NOT NULL,
  `codigo_dolar` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `efectivo`
--

INSERT INTO `efectivo` (`cidigo_efectivo`, `codigo_dolar`, `estado`) VALUES
(999, 2222, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `codigo_empresa` int(11) NOT NULL,
  `rif_empresa` varchar(12) NOT NULL,
  `nombre_empresa` varchar(30) NOT NULL,
  `telefono` varchar(12) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `direccion` varchar(80) NOT NULL,
  `logo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`codigo_empresa`, `rif_empresa`, `nombre_empresa`, `telefono`, `correo`, `direccion`, `logo`) VALUES
(1111, 'J-504149358', 'COPI GABY 2023 C.A.', '04120583967', 'Inpresionescopigaby@gmail.com', 'PUEBLO NUEVO', 'LOGO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iva`
--

CREATE TABLE `iva` (
  `codigo_IVA` int(11) NOT NULL,
  `porcentaje_iva` decimal(5,2) NOT NULL,
  `fecha` date NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `iva`
--

INSERT INTO `iva` (`codigo_IVA`, `porcentaje_iva`, `fecha`, `estado`) VALUES
(1, 16.00, '2025-02-12', 1),
(2, 18.00, '2026-06-09', 1),
(3, 14.00, '2026-07-06', 1),
(4, 17.00, '2026-10-22', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `codigo_metodo` int(11) NOT NULL,
  `nombre_metodo` varchar(20) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodo_pago`
--

INSERT INTO `metodo_pago` (`codigo_metodo`, `nombre_metodo`, `estado`) VALUES
(561, 'PUNTO DE VENTA', 1),
(562, 'EFECTIVO', 1),
(563, 'TRANSFERENCIA', 1),
(564, 'PAGO MOVIL', 1),
(565, 'USTD', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moneda`
--

CREATE TABLE `moneda` (
  `codigo_moneda` int(11) NOT NULL,
  `nombre_moneda` varchar(15) NOT NULL,
  `simbolo` varchar(2) NOT NULL,
  `codigo_tasa` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `moneda`
--

INSERT INTO `moneda` (`codigo_moneda`, `nombre_moneda`, `simbolo`, `codigo_tasa`, `estado`, `activa`) VALUES
(510, 'DOLAR', '$', 112, 1, 0),
(511, 'EURO', '€', 107, 1, 0),
(983, 'USTD', 'U', 113, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `codigo_pago` int(11) NOT NULL,
  `codigo_pedido` int(11) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`codigo_pago`, `codigo_pedido`, `fecha_pago`, `estado`) VALUES
(625, 626, '2026-10-22 00:48:28', 1),
(626, 627, '2026-10-22 10:37:35', 1),
(627, 628, '2026-10-22 11:24:56', 1),
(628, 629, '2026-10-22 11:30:52', 1),
(629, 630, '2026-10-22 11:33:23', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `codigo_pedido` int(11) NOT NULL,
  `cedula_cliente` int(12) NOT NULL,
  `cedula_usuario` int(11) NOT NULL,
  `fecha_pedido` datetime NOT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `monto_iva` decimal(10,2) DEFAULT NULL,
  `porcentaje_iva` decimal(5,2) DEFAULT NULL,
  `monto_total` decimal(10,2) DEFAULT NULL,
  `tasa_cambio` decimal(10,2) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`codigo_pedido`, `cedula_cliente`, `cedula_usuario`, `fecha_pedido`, `subtotal`, `monto_iva`, `porcentaje_iva`, `monto_total`, `tasa_cambio`, `estado`) VALUES
(626, 123454321, 323232323, '2026-10-22 00:48:28', 7.34, 1.03, 14.00, 8.37, 500.00, 1),
(627, 19908765, 323232323, '2026-10-22 10:37:35', 105.00, 14.70, 14.00, 119.70, 500.00, 1),
(628, 32345650, 323232323, '2026-10-22 11:24:56', 0.20, 0.03, 17.00, 0.23, 1000.00, 1),
(629, 23456654, 323232323, '2026-10-22 11:30:52', 0.20, 0.03, 17.00, 0.23, 1000.00, 1),
(630, 33234543, 323232323, '2026-10-22 11:33:23', 69.00, 11.73, 17.00, 80.73, 1000.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_insumo`
--

CREATE TABLE `producto_insumo` (
  `codigo_producto` int(11) NOT NULL,
  `nombre_producto` varchar(50) NOT NULL,
  `codigo_categoria` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `porcentaje_ganancia` decimal(10,0) NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_actual` int(11) NOT NULL,
  `stock_minimo` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  `costo` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto_insumo`
--

INSERT INTO `producto_insumo` (`codigo_producto`, `nombre_producto`, `codigo_categoria`, `descripcion`, `porcentaje_ganancia`, `precio`, `stock_actual`, `stock_minimo`, `estado`, `costo`) VALUES
(7654, 'Lapiz', 101, 'Mongol', 100, 1.00, 7, 6, 1, 0.50),
(76543, 'CINTA', 102, 'XXXXXXXXXXXXXXXXXXX', 100, 1.00, 42, 10, 1, 0.00),
(98765, 'PULSERA', 103, 'XXXXXXXXXXXXXXXXX', 20, 0.24, 1, 2, 1, 0.00),
(759160, 'BLOCK', 101, 'TTTTT', 50, 4.50, 0, 2, 1, 0.00),
(759199, 'HOJAS CARTA', 101, 'XXXXXXXXXXXXXXXXX', 100, 0.20, 19, 2, 1, 0.00),
(759765, 'HOJAS OFICIO', 101, 'XXXXXXXXXXXXXXXXXXXXXXXXXXX', 10, 0.17, 13, 2, 1, 0.00),
(759766, 'Borra', 101, 'Borra', 15, 0.58, 9, 2, 1, 0.00),
(759767, 'lapicero', 101, 'mmmmmmmmmm', 20, 0.36, 3, 5, 1, 0.00),
(759768, 'Cuaderno', 101, 'doble linea', 200, 6.00, 3, 5, 1, 0.00),
(759769, 'Colores', 101, 'xxxxxxx', 100, 3.00, 7, 3, 1, 0.00),
(759770, 'papel carta 4', 101, 'una', 28, 0.00, 4, 5, 1, 0.00),
(759771, 'LapizZZZ', 101, '', 30, 3.90, 2, 5, 1, 0.00),
(759772, 'celoven', 101, '', 50, 0.00, 1, 5, 1, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `codigo_proveedor` int(11) NOT NULL,
  `rif_proveedor` varchar(12) NOT NULL,
  `razon_social` varchar(50) NOT NULL,
  `telefono` varchar(12) NOT NULL,
  `correo` varchar(60) NOT NULL,
  `direccion` varchar(60) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`codigo_proveedor`, `rif_proveedor`, `razon_social`, `telefono`, `correo`, `direccion`, `estado`) VALUES
(317317661, 'J317317661', 'INVERSIONES BURBUJA', '04245555555', 'BURBUJA@gmail.com', 'calle carrera', 1),
(988760998, '54924', 'POINTER', '04228690511', 'mpointer@gmail.com', 'calle 6 \r\n', 1),
(988760999, '534354777', 'POINTERRR', '04120583967', '4334343434@gmail.com', 'calle  3', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `referencia`
--

CREATE TABLE `referencia` (
  `referencia` varchar(50) NOT NULL,
  `numero_comprobante` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `referencia`
--

INSERT INTO `referencia` (`referencia`, `numero_comprobante`) VALUES
('2345', '2345'),
('2347', '2347'),
('3345', '3345'),
('3456', '3456'),
('56789', '56789'),
('6789', '6789'),
('77777', '8888888'),
('7890', '7890'),
('8790', '8790'),
('8899', '8899'),
('8908', '8908'),
('8970', '8970'),
('8976', '8976'),
('987', '0987');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `codigo_rol` int(11) NOT NULL,
  `nombre_rol` varchar(15) NOT NULL,
  `descripcion` text NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`codigo_rol`, `nombre_rol`, `descripcion`, `estado`) VALUES
(1, 'admin', 'Administrador', 1),
(2, 'EMPLEADO', 'Empleado', 1),
(3, 'test_trait', 'prueba', 1),
(4, 'Test', 'acceso a nada', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `codigo_servicio` int(11) NOT NULL,
  `nombre_servicio` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`codigo_servicio`, `nombre_servicio`, `descripcion`, `precio`, `estado`) VALUES
(7, 'IMPRESION ', 'XXXXXXXXXXXXXXXXXX', 40.00, 1),
(8, 'PLASTIFICACION', 'XXXXXXXXXX', 2500.00, 1),
(9, 'Copia - B&N', 'Blanco y negro', 30.00, 1),
(10, 'Copia - Color.', 'Color.', 50.00, 1),
(11, 'Empastado', 'hhhhhhhhh', 5.00, 1),
(12, 'Encuadernado', '', 6.00, 1),
(13, 'Copia - Color', '', 0.70, 1),
(14, 'Copia - Colorr', '', 0.90, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio_material`
--

CREATE TABLE `servicio_material` (
  `codigo_servicio_material` int(11) NOT NULL,
  `codigo_producto` int(11) NOT NULL,
  `codigo_servicio` int(11) NOT NULL,
  `cantidad_usada` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio_material`
--

INSERT INTO `servicio_material` (`codigo_servicio_material`, `codigo_producto`, `codigo_servicio`, `cantidad_usada`) VALUES
(9, 7654, 12, 1),
(10, 759199, 14, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasa_cambio`
--

CREATE TABLE `tasa_cambio` (
  `codigo_tasa` int(11) NOT NULL,
  `codigo_moneda` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `monto_bolivares` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tasa_cambio`
--

INSERT INTO `tasa_cambio` (`codigo_tasa`, `codigo_moneda`, `fecha`, `monto_bolivares`) VALUES
(105, NULL, '2026-06-16 23:24:22', 500.00),
(106, NULL, '2026-06-17 00:02:28', 450.00),
(107, 511, '2026-06-17 00:02:34', 600.00),
(108, NULL, '2026-06-24 01:11:09', 450.00),
(109, NULL, '2026-06-24 01:11:21', 450.00),
(110, NULL, '2026-07-06 22:04:19', 470.00),
(111, NULL, '2026-07-06 22:17:20', 500.00),
(112, 510, '2026-07-06 22:34:00', 500.00),
(113, 983, '2026-10-22 10:42:26', 1000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `codigo_media` int(11) NOT NULL,
  `nombre` varchar(15) NOT NULL,
  `abreviatura` varchar(4) NOT NULL,
  `cantidad_unidad` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`codigo_media`, `nombre`, `abreviatura`, `cantidad_unidad`, `estado`) VALUES
(26, 'METRO', 'M', 1, 1),
(27, 'Caja', 'C', 12, 1),
(28, 'Centrimetro', 'cm', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `cedula_usuario` int(12) NOT NULL,
  `telefono` varchar(12) NOT NULL,
  `nombre_usuario` varchar(20) NOT NULL,
  `codigo_rol` int(11) NOT NULL,
  `password` varchar(8) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`cedula_usuario`, `telefono`, `nombre_usuario`, `codigo_rol`, `password`, `estado`) VALUES
(2232323, '04228690511', 'ADMINN', 2, '1234', 1),
(88888888, '04245555555', 'temp_test', 2, 'test1', 0),
(222222222, '04245555555', 'empleado', 2, 'empleado', 1),
(323232323, '04245555555', 'ADMIN', 1, '1234', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `banco`
--
ALTER TABLE `banco`
  ADD PRIMARY KEY (`codigo_banco`);

--
-- Indices de la tabla `cashea_credito`
--
ALTER TABLE `cashea_credito`
  ADD PRIMARY KEY (`id_cashea_credito`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `cashea_deposito`
--
ALTER TABLE `cashea_deposito`
  ADD PRIMARY KEY (`id_cashea_deposito`);

--
-- Indices de la tabla `cashea_deposito_detalle`
--
ALTER TABLE `cashea_deposito_detalle`
  ADD PRIMARY KEY (`id_cashea_deposito_detalle`),
  ADD KEY `id_cashea_credito` (`id_cashea_credito`),
  ADD KEY `id_cashea_deposito` (`id_cashea_deposito`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`codigo_categoria`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`cedula_cliente`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`codigo_compra`),
  ADD KEY `codigo_proveedor` (`codigo_proveedor`),
  ADD KEY `codigo_usuario` (`cedula_usuario`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`codigo_detalle_compra`),
  ADD KEY `codigo_compra` (`codigo_compra`),
  ADD KEY `codigo_producto` (`codigo_producto`);

--
-- Indices de la tabla `detalle_pago`
--
ALTER TABLE `detalle_pago`
  ADD PRIMARY KEY (`codigo_detalle_pago`),
  ADD KEY `codigo_pago` (`codigo_pago`),
  ADD KEY `codigo_moneda` (`codigo_moneda`),
  ADD KEY `codigo_metodo` (`codigo_metodo`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`codigo_detalle_pedido`),
  ADD KEY `codigo_pedido` (`codigo_pedido`),
  ADD KEY `codigo_producto` (`codigo_producto`),
  ADD KEY `codigo_servicio` (`codigo_servicio`),
  ADD KEY `codigo_unidad_medida` (`codigo_unidad_medida`);

--
-- Indices de la tabla `detalle_transferencia`
--
ALTER TABLE `detalle_transferencia`
  ADD PRIMARY KEY (`codigo_detalle_transferencia`),
  ADD KEY `codigo_referencia` (`codigo_referencia`),
  ADD KEY `codigo_banco` (`codigo_banco`),
  ADD KEY `idx_codigo_detalle_pago` (`codigo_detalle_pago`);

--
-- Indices de la tabla `dolar`
--
ALTER TABLE `dolar`
  ADD PRIMARY KEY (`codigo_dolar`),
  ADD KEY `codigo_taza` (`codigo_taza`),
  ADD KEY `codigo_efectivo` (`codigo_efectivo`);

--
-- Indices de la tabla `efectivo`
--
ALTER TABLE `efectivo`
  ADD PRIMARY KEY (`cidigo_efectivo`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`codigo_empresa`);

--
-- Indices de la tabla `iva`
--
ALTER TABLE `iva`
  ADD PRIMARY KEY (`codigo_IVA`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`codigo_metodo`);

--
-- Indices de la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD PRIMARY KEY (`codigo_moneda`),
  ADD KEY `codigo_tasa` (`codigo_tasa`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`codigo_pago`),
  ADD KEY `codigo_pedido` (`codigo_pedido`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`codigo_pedido`),
  ADD KEY `codigo_usuario` (`cedula_usuario`),
  ADD KEY `codigo_cliente` (`cedula_cliente`);

--
-- Indices de la tabla `producto_insumo`
--
ALTER TABLE `producto_insumo`
  ADD PRIMARY KEY (`codigo_producto`),
  ADD KEY `codigo_categoria` (`codigo_categoria`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`codigo_proveedor`);

--
-- Indices de la tabla `referencia`
--
ALTER TABLE `referencia`
  ADD PRIMARY KEY (`referencia`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`codigo_rol`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`codigo_servicio`);

--
-- Indices de la tabla `servicio_material`
--
ALTER TABLE `servicio_material`
  ADD PRIMARY KEY (`codigo_servicio_material`),
  ADD KEY `codigo_servicio` (`codigo_servicio`),
  ADD KEY `nombre_producto` (`codigo_producto`);

--
-- Indices de la tabla `tasa_cambio`
--
ALTER TABLE `tasa_cambio`
  ADD PRIMARY KEY (`codigo_tasa`),
  ADD KEY `idx_tasa_moneda` (`codigo_moneda`);

--
-- Indices de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`codigo_media`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cedula_usuario`),
  ADD KEY `codigo_rol` (`codigo_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cashea_credito`
--
ALTER TABLE `cashea_credito`
  MODIFY `id_cashea_credito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cashea_deposito`
--
ALTER TABLE `cashea_deposito`
  MODIFY `id_cashea_deposito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cashea_deposito_detalle`
--
ALTER TABLE `cashea_deposito_detalle`
  MODIFY `id_cashea_deposito_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `codigo_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `codigo_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=561;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `codigo_detalle_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87720;

--
-- AUTO_INCREMENT de la tabla `detalle_pago`
--
ALTER TABLE `detalle_pago`
  MODIFY `codigo_detalle_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=632;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `codigo_detalle_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=677;

--
-- AUTO_INCREMENT de la tabla `detalle_transferencia`
--
ALTER TABLE `detalle_transferencia`
  MODIFY `codigo_detalle_transferencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=595;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `codigo_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1112;

--
-- AUTO_INCREMENT de la tabla `iva`
--
ALTER TABLE `iva`
  MODIFY `codigo_IVA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `codigo_metodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=566;

--
-- AUTO_INCREMENT de la tabla `moneda`
--
ALTER TABLE `moneda`
  MODIFY `codigo_moneda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=984;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `codigo_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=630;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `codigo_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=631;

--
-- AUTO_INCREMENT de la tabla `producto_insumo`
--
ALTER TABLE `producto_insumo`
  MODIFY `codigo_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=759773;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `codigo_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=988761000;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `codigo_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `codigo_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `servicio_material`
--
ALTER TABLE `servicio_material`
  MODIFY `codigo_servicio_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tasa_cambio`
--
ALTER TABLE `tasa_cambio`
  MODIFY `codigo_tasa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `codigo_media` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cashea_credito`
--
ALTER TABLE `cashea_credito`
  ADD CONSTRAINT `cashea_credito_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`codigo_pedido`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cashea_deposito_detalle`
--
ALTER TABLE `cashea_deposito_detalle`
  ADD CONSTRAINT `cashea_deposito_detalle_ibfk_1` FOREIGN KEY (`id_cashea_credito`) REFERENCES `cashea_credito` (`id_cashea_credito`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cashea_deposito_detalle_ibfk_2` FOREIGN KEY (`id_cashea_deposito`) REFERENCES `cashea_deposito` (`id_cashea_deposito`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`codigo_proveedor`) REFERENCES `proveedor` (`codigo_proveedor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `compra_ibfk_2` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuario` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`codigo_compra`) REFERENCES `compra` (`codigo_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`codigo_producto`) REFERENCES `producto_insumo` (`codigo_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_pago`
--
ALTER TABLE `detalle_pago`
  ADD CONSTRAINT `detalle_pago_ibfk_1` FOREIGN KEY (`codigo_pago`) REFERENCES `pagos` (`codigo_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pago_ibfk_2` FOREIGN KEY (`codigo_moneda`) REFERENCES `moneda` (`codigo_moneda`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pago_ibfk_3` FOREIGN KEY (`codigo_metodo`) REFERENCES `metodo_pago` (`codigo_metodo`);

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`codigo_pedido`) REFERENCES `pedido` (`codigo_pedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`codigo_producto`) REFERENCES `producto_insumo` (`codigo_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_3` FOREIGN KEY (`codigo_servicio`) REFERENCES `servicio` (`codigo_servicio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_4` FOREIGN KEY (`codigo_unidad_medida`) REFERENCES `unidad_medida` (`codigo_media`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_transferencia`
--
ALTER TABLE `detalle_transferencia`
  ADD CONSTRAINT `detalle_transferencia_ibfk_1` FOREIGN KEY (`codigo_referencia`) REFERENCES `referencia` (`referencia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_transferencia_ibfk_2` FOREIGN KEY (`codigo_banco`) REFERENCES `banco` (`codigo_banco`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_transferencia_ibfk_3` FOREIGN KEY (`codigo_detalle_pago`) REFERENCES `detalle_pago` (`codigo_detalle_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `dolar`
--
ALTER TABLE `dolar`
  ADD CONSTRAINT `dolar_ibfk_1` FOREIGN KEY (`codigo_taza`) REFERENCES `tasa_cambio` (`codigo_tasa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dolar_ibfk_2` FOREIGN KEY (`codigo_efectivo`) REFERENCES `efectivo` (`cidigo_efectivo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `moneda`
--
ALTER TABLE `moneda`
  ADD CONSTRAINT `moneda_ibfk_1` FOREIGN KEY (`codigo_tasa`) REFERENCES `tasa_cambio` (`codigo_tasa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`codigo_pedido`) REFERENCES `pedido` (`codigo_pedido`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_3` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuario` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pedido_ibfk_4` FOREIGN KEY (`cedula_cliente`) REFERENCES `cliente` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto_insumo`
--
ALTER TABLE `producto_insumo`
  ADD CONSTRAINT `producto_insumo_ibfk_1` FOREIGN KEY (`codigo_categoria`) REFERENCES `categoria` (`codigo_categoria`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicio_material`
--
ALTER TABLE `servicio_material`
  ADD CONSTRAINT `servicio_material_ibfk_1` FOREIGN KEY (`codigo_servicio`) REFERENCES `servicio` (`codigo_servicio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `servicio_material_ibfk_2` FOREIGN KEY (`codigo_producto`) REFERENCES `producto_insumo` (`codigo_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`codigo_rol`) REFERENCES `rol` (`codigo_rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
