<?php

namespace App\models;

use App\config\ConectDB;
use App\traits\PedidosTrait;
use App\Models\casheaModel;
use PDO;
use PDOException;

class pedidoModel extends ConectDB {
    use PedidosTrait;

    private $conex;

    private $codigo_pedido;
    private $cedula_cliente;
    private $cedula_usuario;
    private $fecha_pedido;
    private $estado;
    private $subtotal;
    private $monto_iva;
    private $porcentaje_iva;
    private $monto_total;
    private $tasa_cambio;

    /** @var monedaModel|null */
    private $monedaModel;

    /** Métodos que requieren banco y referencia */

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
        $this->monedaModel = new monedaModel();
    }

    private function logPdoError(string $context, PDOException $e): void {
        error_log(sprintf(
            '[pedidoModel::%s] %s | SQLSTATE=%s',
            $context,
            $e->getMessage(),
            $e->getCode()
        ));
    }

    /**
     * Obtiene todos los pedidos con nombres de cliente y usuario.
     */
    public function getAllPedidos() {
        try {
            $query = "SELECT p.codigo_pedido, p.cedula_cliente, p.cedula_usuario, p.fecha_pedido, p.estado,
                             p.subtotal, p.monto_iva, p.porcentaje_iva, p.monto_total, p.tasa_cambio,
                             c.nombre AS nombre_cliente, u.nombre_usuario,
                             mp.nombre_metodo
                      FROM pedido p
                      LEFT JOIN cliente c ON p.cedula_cliente = c.cedula_cliente
                      LEFT JOIN usuario u ON p.cedula_usuario = u.cedula_usuario
                      LEFT JOIN pagos pg ON pg.codigo_pedido = p.codigo_pedido AND pg.estado = 1
                      LEFT JOIN detalle_pago dpg ON dpg.codigo_pago = pg.codigo_pago
                      LEFT JOIN metodo_pago mp ON dpg.codigo_metodo = mp.codigo_metodo
                      ORDER BY p.codigo_pedido DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getAllPedidos', $e);
            return [];
        }
    }

    /**
     * Registra un pedido completo: cabecera, detalle, pago e inventario,
     * y genera automáticamente el crédito en cashea_credito si el método es Cashea.
     */
    public function addPedido(array $datos, array $items, array $pago, array $casheaData = []) {
        $validacion = $this->validarPedido($datos, $items, $pago);
        if ($validacion !== true) {
            return $validacion;
        }

        $cedulaUsuario = $datos['cedula_usuario'] ?? '';
        $cedulaCliente = $datos['cedula_cliente'] ?? '';

        $subtotal      = $this->formatearMonto((float) ($datos['subtotal'] ?? 0));
        $montoIva      = $this->formatearMonto((float) ($datos['monto_iva'] ?? 0));
        $porcentajeIva = $this->formatearMonto((float) ($datos['porcentaje_iva'] ?? 0));
        $montoTotal    = $this->formatearMonto((float) ($datos['monto_total'] ?? 0));
        $tasaCambio    = $this->formatearMonto((float) ($datos['tasa_actual'] ?? $this->getTasaActual()));

        try {
            $this->conex->beginTransaction();

            $stmtPedido = $this->conex->prepare(
                'INSERT INTO pedido (cedula_cliente, cedula_usuario, fecha_pedido, estado,
                                     subtotal, monto_iva, porcentaje_iva, monto_total, tasa_cambio)
                 VALUES (?, ?, ?, 1, ?, ?, ?, ?, ?)'
            );
            $stmtPedido->execute([
                $cedulaCliente,
                $cedulaUsuario,
                $datos['fecha_pedido'],
                $this->formatearMontoDecimal($subtotal),
                $this->formatearMontoDecimal($montoIva),
                $this->formatearMontoDecimal($porcentajeIva),
                $this->formatearMontoDecimal($montoTotal),
                $this->formatearMontoDecimal($tasaCambio),
            ]);

            $codigoPedido = (int) $this->conex->lastInsertId();
            if ($codigoPedido <= 0) {
                throw new PDOException('No se pudo obtener el código del pedido insertado');
            }

            $this->insertarDetallesYDescontarStock($codigoPedido, $items);
            $this->registrarPago($codigoPedido, $pago, $montoTotal);

            // Registro automático en cashea_credito si se seleccionó CASHEA
            $esCashea = !empty($casheaData) || $this->esMetodoCashea((int) ($pago['codigo_metodo'] ?? 0));
            if ($esCashea) {
                $this->registrarCreditoCashea($codigoPedido, $montoTotal, $casheaData);
            }

            $this->conex->commit();

            return [
                'status'        => 'success',
                'message'       => $esCashea 
                    ? 'Pedido registrado exitosamente y crédito Cashea generado.' 
                    : 'Pedido registrado exitosamente',
                'codigo_pedido' => $codigoPedido,
                'es_cashea'     => $esCashea,
            ];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            $this->logPdoError('addPedido', $e);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Actualiza un pedido existente reajustando detalle, pago e inventario.
     */
    public function updatePedido(int $id, array $datos, array $items, array $pago) {
        if ($id <= 0) {
            return ['status' => 'error', 'message' => 'Pedido no válido'];
        }

        $cedulaCliente = $datos['cedula_cliente'] ?? '';
        if (empty($cedulaCliente)) {
            return ['status' => 'error', 'message' => 'Debe seleccionar un cliente válido'];
        }

        if (empty($items)) {
            return ['status' => 'error', 'message' => 'El pedido debe incluir al menos un ítem'];
        }

        $validacionPago = $this->validarDatosPago($pago);
        if ($validacionPago !== true) {
            return $validacionPago;
        }

        $estado = isset($datos['estado']) ? ((int) $datos['estado'] ? 1 : 0) : 1;
        $subtotal      = $this->formatearMonto((float) ($datos['subtotal'] ?? 0));
        $montoIva      = $this->formatearMonto((float) ($datos['monto_iva'] ?? 0));
        $porcentajeIva = $this->formatearMonto((float) ($datos['porcentaje_iva'] ?? 0));
        $montoTotal    = $this->formatearMonto((float) ($datos['monto_total'] ?? 0));
        $tasaCambio    = $this->formatearMonto((float) ($datos['tasa_actual'] ?? $this->getTasaActual()));

        try {
            $this->conex->beginTransaction();

            $pedidoActual = $this->getPedidoById($id);
            if (!$pedidoActual) {
                throw new PDOException('El pedido indicado no existe');
            }

            if ((int) $pedidoActual['estado'] === 1) {
                $this->restaurarStockPedido($id);
            }

            $stmtDelDetalle = $this->conex->prepare('DELETE FROM detalle_pedido WHERE codigo_pedido = ?');
            $stmtDelDetalle->execute([$id]);

            $stmtDelPagos = $this->conex->prepare('DELETE FROM pagos WHERE codigo_pedido = ?');
            $stmtDelPagos->execute([$id]);

            $stmtUpdate = $this->conex->prepare(
                'UPDATE pedido SET cedula_cliente = ?, estado = ?,
                                   subtotal = ?, monto_iva = ?, porcentaje_iva = ?,
                                   monto_total = ?, tasa_cambio = ?
                 WHERE codigo_pedido = ?'
            );
            $stmtUpdate->execute([
                $cedulaCliente,
                $estado,
                $this->formatearMontoDecimal($subtotal),
                $this->formatearMontoDecimal($montoIva),
                $this->formatearMontoDecimal($porcentajeIva),
                $this->formatearMontoDecimal($montoTotal),
                $this->formatearMontoDecimal($tasaCambio),
                $id,
            ]);

            if ($estado === 1) {
                $this->insertarDetallesYDescontarStock($id, $items);
                if ($montoTotal <= 0) {
                    throw new PDOException('El total del pedido debe ser mayor a cero');
                }
                $this->registrarPago($id, $pago, $montoTotal);
            }

            $this->conex->commit();
            return ['status' => 'success', 'message' => 'Pedido actualizado con éxito'];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            $this->logPdoError('updatePedido', $e);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Anulación lógica del pedido con reversión de inventario.
     */
    public function deletePedido(int $id) {
        try {
            $this->conex->beginTransaction();

            $pedido = $this->getPedidoById($id);
            if (!$pedido) {
                throw new PDOException('Pedido no encontrado');
            }

            if ((int) $pedido['estado'] === 1) {
                $this->restaurarStockPedido($id);
            }

            $stmtPedido = $this->conex->prepare('UPDATE pedido SET estado = 0 WHERE codigo_pedido = ?');
            $stmtPedido->execute([$id]);

            $stmtPago = $this->conex->prepare('UPDATE pagos SET estado = 0 WHERE codigo_pedido = ?');
            $stmtPago->execute([$id]);

            // Anular crédito Cashea si existiera
            $stmtCashea = $this->conex->prepare('UPDATE cashea_credito SET estado = 0 WHERE id_pedido = ?');
            $stmtCashea->execute([$id]);

            $this->conex->commit();
            return ['status' => 'success', 'message' => 'Pedido anulado correctamente'];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            $this->logPdoError('deletePedido', $e);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Cabecera detallada de un pedido.
     */
    public function getPedidoById($id) {
        try {
            $query = "SELECT p.*, c.nombre AS nombre_cliente, c.telefono AS telefono_cliente,
                             u.nombre_usuario,
                             pg.codigo_pago, dpg.codigo_metodo, dpg.codigo_moneda, pg.fecha_pago AS fecha_pago,
                             mp.nombre_metodo
                      FROM pedido p
                      INNER JOIN cliente c ON p.cedula_cliente = c.cedula_cliente
                      INNER JOIN usuario u ON p.cedula_usuario = u.cedula_usuario
                      LEFT JOIN pagos pg ON pg.codigo_pedido = p.codigo_pedido AND pg.estado = 1
                      LEFT JOIN detalle_pago dpg ON dpg.codigo_pago = pg.codigo_pago
                      LEFT JOIN metodo_pago mp ON dpg.codigo_metodo = mp.codigo_metodo
                      WHERE p.codigo_pedido = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([(int) $id]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pedido && !empty($pedido['codigo_pago'])) {
                $pedido['pago'] = $this->getDetallePagoByPago((int) $pedido['codigo_pago']);
            }

            if ($pedido) {
                // Obtener datos del crédito Cashea si este pedido fue financiado
                $stmtCashea = $this->conex->prepare('SELECT * FROM cashea_credito WHERE id_pedido = ? LIMIT 1');
                $stmtCashea->execute([(int) $id]);
                $pedido['cashea'] = $stmtCashea->fetch(PDO::FETCH_ASSOC) ?: null;

                $pedido['subtotal']       = $this->formatearMonto((float) ($pedido['subtotal'] ?? 0));
                $pedido['monto_iva']      = $this->formatearMonto((float) ($pedido['monto_iva'] ?? 0));
                $pedido['porcentaje_iva'] = $this->formatearMonto((float) ($pedido['porcentaje_iva'] ?? 0));
                $pedido['monto_total']    = $this->formatearMonto((float) ($pedido['monto_total'] ?? 0));
                $pedido['tasa_actual']    = $this->formatearMonto((float) ($pedido['tasa_cambio'] ?? $this->getTasaActual()));
                $pedido['codigo_IVA']     = $this->resolverCodigoIvaPedido($pedido);
            }

            return $pedido ?: null;
        } catch (PDOException $e) {
            $this->logPdoError('getPedidoById', $e);
            return null;
        }
    }

    /**
     * Ítems de un pedido con tipo inferido para edición.
     */
     public function getItemsByPedido($id) {
         try {
             $query = "SELECT dp.*,
                              pi.nombre_producto,
                              s.nombre_servicio,
                              s.precio AS precio_servicio,
                              CASE
                                  WHEN dp.codigo_producto IS NOT NULL THEN 'producto'
                                  ELSE 'servicio'
                              END AS tipo
                       FROM detalle_pedido dp
                       LEFT JOIN producto_insumo pi ON dp.codigo_producto = pi.codigo_producto
                       LEFT JOIN servicio s ON dp.codigo_servicio = s.codigo_servicio
                       WHERE dp.codigo_pedido = ?
                       ORDER BY dp.codigo_detalle_pedido ASC";
             $checkStmt = $this->conex->prepare("SHOW COLUMNS FROM detalle_pedido LIKE 'codigo_unidad_medida'");
             $checkStmt->execute();
             if ($checkStmt->rowCount() > 0) {
                 $query = "SELECT dp.*,
                                  pi.nombre_producto,
                                  s.nombre_servicio,
                                  s.precio AS precio_servicio,
                                  um.nombre AS nombre_medida,
                                  um.abreviatura AS abreviatura_medida,
                                  CASE
                                      WHEN dp.codigo_producto IS NOT NULL THEN 'producto'
                                      ELSE 'servicio'
                                  END AS tipo
                       FROM detalle_pedido dp
                       LEFT JOIN producto_insumo pi ON dp.codigo_producto = pi.codigo_producto
                       LEFT JOIN servicio s ON dp.codigo_servicio = s.codigo_servicio
                       LEFT JOIN unidad_medida um ON dp.codigo_unidad_medida = um.codigo_media
                       WHERE dp.codigo_pedido = ?
                       ORDER BY dp.codigo_detalle_pedido ASC";
             }
             $stmt = $this->conex->prepare($query);
             $stmt->execute([$id]);
             $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
             foreach ($rows as &$row) {
                 if (!isset($row['nombre_medida'])) $row['nombre_medida'] = null;
                 if (!isset($row['abreviatura_medida'])) $row['abreviatura_medida'] = null;
                 if (!isset($row['codigo_unidad_medida'])) $row['codigo_unidad_medida'] = null;
             }
             return $rows;
         } catch (PDOException $e) {
             $this->logPdoError('getItemsByPedido', $e);
             return [];
         }
     }

    public function getClientesActivos() {
        try {
            $query = 'SELECT cedula_cliente, nombre FROM cliente ORDER BY nombre ASC';
            $checkStmt = $this->conex->prepare("SHOW COLUMNS FROM cliente LIKE 'estado'");
            $checkStmt->execute();
            if ($checkStmt->rowCount() > 0) {
                $query = 'SELECT cedula_cliente, nombre FROM cliente WHERE estado = 1 ORDER BY nombre ASC';
            }
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getClientesActivos', $e);
            return [];
        }
    }

    public function getProductosActivos() {
        try {
            $checkMedia = $this->conex->prepare("SHOW COLUMNS FROM producto_insumo LIKE 'codigo_media'");
            $checkMedia->execute();
            $hasMedia = $checkMedia->rowCount() > 0;

            if ($hasMedia) {
                $query = 'SELECT p.codigo_producto, p.nombre_producto, p.stock_actual,
                                 COALESCE(p.precio, 0) AS precio,
                                 p.codigo_media,
                                 COALESCE(um.cantidad_unidad, 1) AS cantidad_unidad,
                                 um.abreviatura AS abreviatura_medida,
                                 um.nombre AS nombre_medida
                          FROM producto_insumo p
                          LEFT JOIN unidad_medida um ON p.codigo_media = um.codigo_media AND um.estado = 1
                          WHERE p.estado = 1
                          ORDER BY p.nombre_producto ASC';
            } else {
                $query = 'SELECT p.codigo_producto, p.nombre_producto, p.stock_actual,
                                 COALESCE(p.precio, 0) AS precio,
                                 1 AS cantidad_unidad
                          FROM producto_insumo p
                          WHERE p.estado = 1
                          ORDER BY p.nombre_producto ASC';
            }

            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$row) {
                if (!isset($row['precio'])) $row['precio'] = 0;
                if (!isset($row['codigo_media'])) $row['codigo_media'] = null;
                if (!isset($row['cantidad_unidad'])) $row['cantidad_unidad'] = 1;
                if (!isset($row['abreviatura_medida'])) $row['abreviatura_medida'] = null;
                if (!isset($row['nombre_medida'])) $row['nombre_medida'] = null;
                $row['cantidad_unidad'] = (int) $row['cantidad_unidad'];
            }
            return $rows;
        } catch (PDOException $e) {
            $this->logPdoError('getProductosActivos', $e);
            return [];
        }
    }

    public function getServiciosActivos() {
        try {
            $stmt = $this->conex->prepare(
                'SELECT codigo_servicio, nombre_servicio, descripcion, precio, estado
                 FROM servicio WHERE estado = 1 ORDER BY nombre_servicio ASC'
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getServiciosActivos', $e);
            return [];
        }
    }

    public function getMetodosActivos() {
        try {
            $stmt = $this->conex->prepare('SELECT codigo_metodo, nombre_metodo FROM metodo_pago WHERE estado = 1 ORDER BY nombre_metodo ASC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getMetodosActivos', $e);
            return [];
        }
    }

    public function getBancosActivos() {
        try {
            $stmt = $this->conex->prepare('SELECT codigo_banco, nombre_banco FROM banco WHERE estado = 1 ORDER BY nombre_banco ASC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getBancosActivos', $e);
            return [];
        }
    }

    /**
     * Moneda activa global del sistema (nombre, símbolo, tasa).
     */
    public function getMonedaActiva(): ?array {
        return $this->monedaModel->getMonedaActiva();
    }

    /**
     * Tasa de cambio de la moneda activa global.
     */
    public function getTasaActual(): float {
        return $this->monedaModel->getTasaActual();
    }

    /**
     * Porcentaje de IVA vigente (registro activo más reciente en tabla iva).
     */
    public function getIvaActivo(): array {
        $ivas = $this->getIvasActivos();
        if (!empty($ivas)) {
            return $ivas[0];
        }
        return ['codigo_IVA' => 1, 'porcentaje_iva' => 16.0, 'estado' => 1];
    }

    /**
     * Todos los registros de IVA activos para el selector del formulario.
     */
    public function getIvasActivos(): array {
        try {
            $stmt = $this->conex->prepare(
                'SELECT codigo_IVA, porcentaje_iva, estado
                 FROM iva
                 WHERE estado = 1
                 ORDER BY fecha DESC, codigo_IVA DESC'
            );
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(static function (array $row): array {
                return [
                    'codigo_IVA'     => (int) $row['codigo_IVA'],
                    'porcentaje_iva' => (float) $row['porcentaje_iva'],
                    'estado'         => (int) $row['estado'],
                ];
            }, $rows);
        } catch (PDOException $e) {
            $this->logPdoError('getIvasActivos', $e);
            return [];
        }
    }

    /**
     * Obtiene un IVA activo por su código.
     */
    public function getIvaByCodigo(int $codigoIva): ?array {
        if ($codigoIva <= 0) {
            return null;
        }

        try {
            $stmt = $this->conex->prepare(
                'SELECT codigo_IVA, porcentaje_iva, estado
                 FROM iva
                 WHERE codigo_IVA = ? AND estado = 1
                 LIMIT 1'
            );
            $stmt->execute([$codigoIva]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            return [
                'codigo_IVA'     => (int) $row['codigo_IVA'],
                'porcentaje_iva' => (float) $row['porcentaje_iva'],
                'estado'         => (int) $row['estado'],
            ];
        } catch (PDOException $e) {
            $this->logPdoError('getIvaByCodigo', $e);
            return null;
        }
    }

    public function getMonedaDefault(): int {
        $moneda = $this->getMonedaActiva();
        return $moneda ? (int) $moneda['codigo_moneda'] : 510;
    }

    /* ------------------------------------------------------------------
     * Métodos privados de soporte transaccional
     * ------------------------------------------------------------------ */

    private function insertarDetallesYDescontarStock(int $codigoPedido, array $items): float {
        $checkStmt = $this->conex->prepare("SHOW COLUMNS FROM detalle_pedido LIKE 'codigo_unidad_medida'");
        $checkStmt->execute();
        $hasCodigoMedia = $checkStmt->rowCount() > 0;

        if ($hasCodigoMedia) {
            $stmtDetalle = $this->conex->prepare(
                'INSERT INTO detalle_pedido (codigo_pedido, codigo_producto, codigo_servicio, codigo_unidad_medida, cantidad, precio_venta, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
        } else {
            $stmtDetalle = $this->conex->prepare(
                'INSERT INTO detalle_pedido (codigo_pedido, codigo_producto, codigo_servicio, cantidad, precio_venta, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
        }

        $montoTotal = 0.0;

        foreach ($items as $index => $item) {
            $linea = (int) $index + 1;
            $tipo = (string) ($item['tipo'] ?? '');
            $cantidad = (float) ($item['cantidad'] ?? 0);
            $precioVenta = (float) ($item['precio_venta'] ?? 0);

            if ($cantidad <= 0) {
                throw new PDOException("Cantidad inválida en la línea {$linea}");
            }

            [$codigoProducto, $codigoServicio, $precioResuelto] = $this->resolverCodigosDetalle($item, $tipo, $linea);
            if ($tipo === 'servicio' && $precioVenta <= 0) {
                $precioVenta = $precioResuelto;
            }

            if ($precioVenta < 0) {
                throw new PDOException("Precio inválido en la línea {$linea}");
            }

            $subtotal = $this->formatearMonto($cantidad * $precioVenta);
            $montoTotal += $subtotal;

            $codigoMedia = $item['codigo_unidad_medida'] ?? null;
            if ($hasCodigoMedia && !empty($codigoMedia)) {
                $stmtCheck = $this->conex->prepare('SELECT 1 FROM unidad_medida WHERE codigo_media = ?');
                $stmtCheck->execute([(int) $codigoMedia]);
                if (!$stmtCheck->fetchColumn()) {
                    $codigoMedia = null;
                }
            }
            if ($hasCodigoMedia) {
                $stmtDetalle->execute([
                    $codigoPedido,
                    $codigoProducto,
                    $codigoServicio,
                    $codigoMedia,
                    $this->formatearMontoDecimal($cantidad),
                    $this->formatearMontoDecimal($precioVenta),
                    $this->formatearMontoDecimal($subtotal),
                ]);
            } else {
                $stmtDetalle->execute([
                    $codigoPedido,
                    $codigoProducto,
                    $codigoServicio,
                    $this->formatearMontoDecimal($cantidad),
                    $this->formatearMontoDecimal($precioVenta),
                    $this->formatearMontoDecimal($subtotal),
                ]);
            }

            $this->descontarInventario($tipo, $codigoProducto, $codigoServicio, $cantidad, $linea);
        }

        return $this->formatearMonto($montoTotal);
    }

    private function resolverCodigosDetalle(array $item, string $tipo, int $linea): array {
        if ($tipo === 'producto') {
            $codigoProducto = (int) ($item['codigo_producto'] ?? 0);
            if ($codigoProducto <= 0) {
                throw new PDOException("Producto no válido en la línea {$linea}");
            }
            return [$codigoProducto, null, null];
        }

        if ($tipo === 'servicio') {
            $codigoServicio = (int) ($item['codigo_servicio'] ?? 0);
            if ($codigoServicio <= 0) {
                throw new PDOException("Servicio no válido en la línea {$linea}");
            }

            $stmt = $this->conex->prepare('SELECT precio FROM servicio WHERE codigo_servicio = ? AND estado = 1 LIMIT 1');
            $stmt->execute([$codigoServicio]);
            $precioServicio = $stmt->fetchColumn();

            if ($precioServicio === false) {
                throw new PDOException("El servicio de la línea {$linea} no existe o no está activo");
            }
            return [null, $codigoServicio, (float) $precioServicio];
        }

        throw new PDOException("Tipo de ítem no reconocido en la línea {$linea}");
    }

    private function descontarInventario(string $tipo, ?int $codigoProducto, ?int $codigoServicio, float $cantidad, int $linea): void {
        if ($tipo === 'producto') {
            $stmtStock = $this->conex->prepare(
                'UPDATE producto_insumo SET stock_actual = stock_actual - ?
                 WHERE codigo_producto = ? AND stock_actual >= ?'
            );
            $stmtStock->execute([(int) $cantidad, $codigoProducto, (int) $cantidad]);

            if ($stmtStock->rowCount() === 0) {
                throw new PDOException("Stock insuficiente para el producto en la línea {$linea}");
            }
            return;
        }

        if ($tipo === 'servicio' && $codigoServicio) {
            $materiales = $this->getMaterialesByServicio((int) $codigoServicio);
            $stmtStock = $this->conex->prepare(
                'UPDATE producto_insumo SET stock_actual = stock_actual - ?
                 WHERE codigo_producto = ? AND stock_actual >= ?'
            );

            foreach ($materiales as $mat) {
                $cantidadNecesaria = (float) $mat['cantidad_usada'] * $cantidad;
                if ($cantidadNecesaria <= 0) {
                    continue;
                }
                $stmtStock->execute([(int) $cantidadNecesaria, (int) $mat['codigo_producto'], (int) $cantidadNecesaria]);
                if ($stmtStock->rowCount() === 0) {
                    throw new PDOException("Stock insuficiente del material #{$mat['codigo_producto']} para el servicio en la línea {$linea}");
                }
            }
        }
    }

    private function getMaterialesByServicio(int $codigoServicio): array {
        try {
            $stmt = $this->conex->prepare(
                'SELECT codigo_producto, cantidad_usada
                 FROM servicio_material
                 WHERE codigo_servicio = ?'
            );
            $stmt->execute([$codigoServicio]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getMaterialesByServicio', $e);
            return [];
        }
    }

    private function restaurarStockPedido(int $codigoPedido): void {
        $items = $this->getItemsByPedido($codigoPedido);
        $stmtSumar = $this->conex->prepare('UPDATE producto_insumo SET stock_actual = stock_actual + ? WHERE codigo_producto = ?');

        foreach ($items as $item) {
            $cantidad = (float) $item['cantidad'];
            if ($item['tipo'] === 'producto') {
                $stmtSumar->execute([(int) $cantidad, (int) $item['codigo_producto']]);
                continue;
            }

            if ($item['tipo'] === 'servicio' && !empty($item['codigo_servicio'])) {
                $materiales = $this->getMaterialesByServicio((int) $item['codigo_servicio']);
                foreach ($materiales as $mat) {
                    $cantidadRestaurar = (float) $mat['cantidad_usada'] * $cantidad;
                    if ($cantidadRestaurar > 0) {
                        $stmtSumar->execute([(int) $cantidadRestaurar, (int) $mat['codigo_producto']]);
                    }
                }
            }
        }
    }

    private function formatearMonto(float $valor): float {
        return (float) number_format($valor, 2, '.', '');
    }

    private function formatearMontoDecimal(float $valor): string {
        return number_format($valor, 2, '.', '');
    }

    private function resolverCodigoIvaPedido(array $pedido): int {
        $subtotal = (float) ($pedido['subtotal'] ?? 0);
        $montoIva = (float) ($pedido['monto_iva'] ?? 0);

        if ($subtotal > 0) {
            $porcentaje = $this->formatearMonto(($montoIva / $subtotal) * 100);
            try {
                $stmt = $this->conex->prepare(
                    'SELECT codigo_IVA FROM iva
                     WHERE estado = 1 AND porcentaje_iva = ?
                     ORDER BY fecha DESC, codigo_IVA DESC
                     LIMIT 1'
                );
                $stmt->execute([$porcentaje]);
                $codigo = $stmt->fetchColumn();
                if ($codigo !== false) {
                    return (int) $codigo;
                }
            } catch (PDOException $e) {
                $this->logPdoError('resolverCodigoIvaPedido', $e);
            }
        }

        if (isset($pedido['porcentaje_iva']) && (float) $pedido['porcentaje_iva'] > 0) {
            try {
                $stmt = $this->conex->prepare(
                    'SELECT codigo_IVA FROM iva
                     WHERE estado = 1 AND porcentaje_iva = ?
                     ORDER BY fecha DESC, codigo_IVA DESC
                     LIMIT 1'
                );
                $stmt->execute([(float) $pedido['porcentaje_iva']]);
                $codigo = $stmt->fetchColumn();
                if ($codigo !== false) {
                    return (int) $codigo;
                }
            } catch (PDOException $e) {
                $this->logPdoError('resolverCodigoIvaPedido', $e);
            }
        }

        return (int) $this->getIvaActivo()['codigo_IVA'];
    }

    private function registrarPago(int $codigoPedido, array $pago, float $montoTotal): void {
        $codigoMetodo = (int) $pago['codigo_metodo'];
        $monto = $this->formatearMonto($montoTotal);

        if ($monto <= 0) {
            throw new PDOException('El total del pedido debe ser mayor a cero');
        }

        $stmtPago = $this->conex->prepare('INSERT INTO pagos (codigo_pedido, fecha_pago, estado) VALUES (?, NOW(), 1)');
        $stmtPago->execute([$codigoPedido]);

        $codigoPago = (int) $this->conex->lastInsertId();
        $codigoMoneda = $this->getMonedaDefault();

        $stmtDetallePago = $this->conex->prepare('INSERT INTO detalle_pago (codigo_pago, codigo_moneda, codigo_metodo, monto) VALUES (?, ?, ?, ?)');
        $stmtDetallePago->execute([
            $codigoPago,
            $codigoMoneda,
            $codigoMetodo,
            $this->formatearMontoDecimal($monto),
        ]);

        $codigoDetallePago = (int) $this->conex->lastInsertId();

        if ($this->metodoRequiereBanco($codigoMetodo)) {
            $referencia = trim((string) ($pago['referencia'] ?? ''));
            if ($referencia === '') {
                throw new PDOException('La referencia del pago no es válida');
            }

            $stmtRef = $this->conex->prepare('INSERT IGNORE INTO referencia (referencia, numero_comprobante) VALUES (?, ?)');
            $stmtRef->execute([$referencia, $referencia]);

            $stmtTransf = $this->conex->prepare(
                'INSERT INTO detalle_transferencia (codigo_detalle_pago, codigo_banco, codigo_referencia)
                 VALUES (?, ?, ?)'
            );
            $stmtTransf->execute([
                $codigoDetallePago,
                (int) $pago['codigo_banco'],
                $referencia,
            ]);
        }
    }

    private function getDetallePagoByPago(int $codigoPago): ?array {
        try {
            $stmt = $this->conex->prepare(
                'SELECT dp.codigo_detalle_pago, dp.codigo_moneda, dp.codigo_metodo, dp.monto,
                        dt.codigo_banco, b.nombre_banco, r.numero_comprobante, r.referencia
                 FROM detalle_pago dp
                 LEFT JOIN detalle_transferencia dt ON dt.codigo_detalle_pago = dp.codigo_detalle_pago
                 LEFT JOIN banco b ON dt.codigo_banco = b.codigo_banco
                 LEFT JOIN referencia r ON dt.codigo_referencia = r.referencia
                 WHERE dp.codigo_pago = ?
                 LIMIT 1'
            );
            $stmt->execute([$codigoPago]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Verifica si un método de pago corresponde a Cashea.
     */
    public function esMetodoCashea(int $codigoMetodo): bool {
        try {
            $stmt = $this->conex->prepare("SELECT 1 FROM metodo_pago WHERE codigo_metodo = ? AND UPPER(nombre_metodo) LIKE '%CASHEA%' LIMIT 1");
            $stmt->execute([$codigoMetodo]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Registra automáticamente el crédito Cashea para un pedido.
     */
    private function registrarCreditoCashea(int $codigoPedido, float $montoTotal, array $casheaData): void {
        $nivel = (int) ($casheaData['nivel_cliente'] ?? 2);
        if ($nivel < 1 || $nivel > 6) {
            $nivel = 2;
        }

        $reglas = casheaModel::getNivelesReglas();
        $regla = $reglas[$nivel] ?? $reglas[2];

        $cuotas = (int) ($casheaData['num_cuotas'] ?? 3);
        if (!in_array($cuotas, $regla['cuotas_permitidas'], true)) {
            $cuotas = $regla['cuotas_permitidas'][0] ?? 3;
        }

        $pctInicial = (float) ($regla['porcentaje_inicial'] ?? 50);
        $pctFin = (float) ($regla['porcentaje_financiado'] ?? (100 - $pctInicial));

        $montoInicial = isset($casheaData['monto_inicial']) && (float) $casheaData['monto_inicial'] > 0
            ? (float) $casheaData['monto_inicial']
            : round($montoTotal * ($pctInicial / 100), 2);

        $montoFinanciado = isset($casheaData['monto_financiado']) && (float) $casheaData['monto_financiado'] > 0
            ? (float) $casheaData['monto_financiado']
            : round($montoTotal - $montoInicial, 2);

        $pctFinanciado = isset($casheaData['porcentaje_financiado']) && (int) $casheaData['porcentaje_financiado'] > 0
            ? (int) $casheaData['porcentaje_financiado']
            : (int) $pctFin;

        $stmt = $this->conex->prepare("
            INSERT INTO cashea_credito (
                id_pedido, monto_inicial_pagado, monto_financiado,
                porcentaje_financiado, num_cuotas, nivel_cliente,
                monto_pendiente, fecha, estado
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 1)
        ");
        $stmt->execute([
            $codigoPedido,
            $this->formatearMontoDecimal($montoInicial),
            $this->formatearMontoDecimal($montoFinanciado),
            $pctFinanciado,
            $cuotas,
            $nivel,
            $this->formatearMontoDecimal($montoFinanciado)
        ]);
    }
}