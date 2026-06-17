<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class pedidoModel extends ConectDB {
    private $conex;

    /** Métodos que requieren banco y referencia */
    private const METODOS_CON_BANCO = [563, 564];

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
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
                             c.nombre AS nombre_cliente, u.nombre_usuario,
                             mp.nombre_metodo,
                             COALESCE(
                                 (SELECT SUM(dp.subtotal) FROM detalle_pedido dp WHERE dp.codigo_pedido = p.codigo_pedido),
                                 0
                             ) AS monto_total
                      FROM pedido p
                      LEFT JOIN cliente c ON p.cedula_cliente = c.cedula_cliente
                      LEFT JOIN usuario u ON p.cedula_usuario = u.cedula_usuario
                      LEFT JOIN pagos pg ON pg.codigo_pedido = p.codigo_pedido AND pg.estado = 1
                      LEFT JOIN metodo_pago mp ON pg.codigo_metodo = mp.codigo_metodo
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
     * Registra un pedido completo: cabecera, detalle, pago e inventario.
     */
    public function addPedido(array $datos, array $items, array $pago) {
        if (empty($items)) {
            return ['status' => 'error', 'message' => 'El pedido debe incluir al menos un ítem'];
        }

        $cedulaUsuario = $datos['cedula_usuario'] ?? '';
        $cedulaCliente = $datos['cedula_cliente'] ?? '';

        if (empty($cedulaUsuario) || empty($cedulaCliente)) {
            return ['status' => 'error', 'message' => 'Cliente o usuario no válido para registrar el pedido'];
        }

        $validacionPago = $this->validarDatosPago($pago);
        if ($validacionPago !== true) {
            return $validacionPago;
        }

        try {
            $this->conex->beginTransaction();

            $stmtPedido = $this->conex->prepare(
                'INSERT INTO pedido (cedula_cliente, cedula_usuario, fecha_pedido, estado)
                 VALUES (?, ?, NOW(), 1)'
            );
            $stmtPedido->execute([$cedulaCliente, $cedulaUsuario]);

            $codigoPedido = (int) $this->conex->lastInsertId();
            if ($codigoPedido <= 0) {
                throw new PDOException('No se pudo obtener el código del pedido insertado');
            }

            $montoTotal = $this->insertarDetallesYDescontarStock($codigoPedido, $items);
            $this->registrarPago($codigoPedido, $pago, $montoTotal);

            $this->conex->commit();

            return [
                'status'        => 'success',
                'message'       => 'Pedido registrado exitosamente',
                'codigo_pedido' => $codigoPedido,
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
                'UPDATE pedido SET cedula_cliente = ?, estado = ? WHERE codigo_pedido = ?'
            );
            $stmtUpdate->execute([$cedulaCliente, $estado, $id]);

            if ($estado === 1) {
                $montoTotal = $this->insertarDetallesYDescontarStock($id, $items);
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
                             pg.codigo_pago, pg.codigo_metodo, pg.fecha_pago AS fecha_pago,
                             mp.nombre_metodo,
                             COALESCE(
                                 (SELECT SUM(dp.subtotal) FROM detalle_pedido dp WHERE dp.codigo_pedido = p.codigo_pedido),
                                 0
                             ) AS monto_total
                      FROM pedido p
                      INNER JOIN cliente c ON p.cedula_cliente = c.cedula_cliente
                      INNER JOIN usuario u ON p.cedula_usuario = u.cedula_usuario
                      LEFT JOIN pagos pg ON pg.codigo_pedido = p.codigo_pedido AND pg.estado = 1
                      LEFT JOIN metodo_pago mp ON pg.codigo_metodo = mp.codigo_metodo
                      WHERE p.codigo_pedido = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([(int) $id]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pedido && !empty($pedido['codigo_pago'])) {
                $pedido['pago'] = $this->getDetallePagoByPago((int) $pedido['codigo_pago']);
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
                                 WHEN ABS(s.precio - dp.precio_venta) < 0.01 THEN 'servicio'
                                 ELSE 'producto'
                             END AS tipo
                      FROM detalle_pedido dp
                      INNER JOIN producto_insumo pi ON dp.codigo_producto = pi.codigo_producto
                      INNER JOIN servicio s ON dp.codigo_servicio = s.codigo_servicio
                      WHERE dp.codigo_pedido = ?
                      ORDER BY dp.codigo_detalle_pedido ASC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([(int) $id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getItemsByPedido', $e);
            return [];
        }
    }

    public function getClientesActivos() {
        try {
            $stmt = $this->conex->prepare(
                'SELECT cedula_cliente, nombre FROM cliente ORDER BY nombre ASC'
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getClientesActivos', $e);
            return [];
        }
    }

    public function getProductosActivos() {
        try {
            $stmt = $this->conex->prepare(
                'SELECT codigo_producto, nombre_producto, costo, stock_actual
                 FROM producto_insumo WHERE estado = 1 ORDER BY nombre_producto ASC'
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getProductosActivos', $e);
            return [];
        }
    }

    public function getServiciosActivos() {
        try {
            $stmt = $this->conex->prepare(
                'SELECT codigo_servicio, nombre_servicio, precio
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
            $stmt = $this->conex->prepare(
                'SELECT codigo_metodo, nombre_metodo FROM metodo_pago WHERE estado = 1 ORDER BY nombre_metodo ASC'
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getMetodosActivos', $e);
            return [];
        }
    }

    public function getBancosActivos() {
        try {
            $stmt = $this->conex->prepare(
                'SELECT codigo_banco, nombre_banco FROM banco WHERE estado = 1 ORDER BY nombre_banco ASC'
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getBancosActivos', $e);
            return [];
        }
    }

    public function getTasaActual() {
        try {
            $stmt = $this->conex->prepare(
                'SELECT t.monto_bolivares
                 FROM moneda m
                 INNER JOIN tasa_cambio t ON m.codigo_tasa = t.codigo_tasa
                 WHERE m.estado = 1 AND m.simbolo = "$"
                 ORDER BY m.codigo_moneda DESC LIMIT 1'
            );
            $stmt->execute();
            $tasa = $stmt->fetchColumn();
            return $tasa !== false ? (float) $tasa : 1.0;
        } catch (PDOException $e) {
            $this->logPdoError('getTasaActual', $e);
            return 1.0;
        }
    }

    public function getMonedaDefault() {
        try {
            $stmt = $this->conex->prepare(
                'SELECT codigo_moneda FROM moneda WHERE estado = 1 AND simbolo = "$" ORDER BY codigo_moneda DESC LIMIT 1'
            );
            $stmt->execute();
            $moneda = $stmt->fetchColumn();
            return $moneda !== false ? (int) $moneda : 510;
        } catch (PDOException $e) {
            return 510;
        }
    }

    /* ------------------------------------------------------------------
     * Métodos privados de soporte transaccional
     * ------------------------------------------------------------------ */

    private function validarDatosPago(array $pago) {
        $codigoMetodo = (int) ($pago['codigo_metodo'] ?? 0);
        if ($codigoMetodo <= 0) {
            return ['status' => 'error', 'message' => 'Debe seleccionar un método de pago'];
        }

        $monto = (float) ($pago['monto'] ?? 0);
        if ($monto <= 0) {
            return ['status' => 'error', 'message' => 'El monto del pago debe ser mayor a cero'];
        }

        if ($this->metodoRequiereBanco($codigoMetodo)) {
            if (empty($pago['codigo_banco'])) {
                return ['status' => 'error', 'message' => 'Debe seleccionar el banco para este método de pago'];
            }
            if (empty(trim((string) ($pago['referencia'] ?? '')))) {
                return ['status' => 'error', 'message' => 'Debe indicar la referencia del pago'];
            }
        }

        return true;
    }

    private function metodoRequiereBanco(int $codigoMetodo): bool {
        return in_array($codigoMetodo, self::METODOS_CON_BANCO, true);
    }

    private function insertarDetallesYDescontarStock(int $codigoPedido, array $items): float {
        $stmtDetalle = $this->conex->prepare(
            'INSERT INTO detalle_pedido (codigo_pedido, codigo_producto, codigo_servicio, cantidad, precio_venta, subtotal)
             VALUES (?, ?, ?, ?, ?, ?)'
        );

        $montoTotal = 0.0;

        foreach ($items as $index => $item) {
            $linea = (int) $index + 1;
            $tipo = (string) ($item['tipo'] ?? '');
            $cantidad = (float) ($item['cantidad'] ?? 0);
            $precioVenta = (float) ($item['precio_venta'] ?? 0);

            if ($cantidad <= 0) {
                throw new PDOException("Cantidad inválida en la línea {$linea}");
            }
            if ($precioVenta < 0) {
                throw new PDOException("Precio inválido en la línea {$linea}");
            }

            $subtotal = round($cantidad * $precioVenta, 2);
            $montoTotal += $subtotal;

            [$codigoProducto, $codigoServicio] = $this->resolverCodigosDetalle($item, $tipo, $linea);

            $stmtDetalle->execute([
                $codigoPedido,
                $codigoProducto,
                $codigoServicio,
                $cantidad,
                $precioVenta,
                $subtotal,
            ]);

            $this->descontarInventario($tipo, $codigoProducto, $codigoServicio, $cantidad, $linea);
        }

        return round($montoTotal, 2);
    }

    /**
     * Resuelve los códigos FK exigidos por detalle_pedido (ambos NOT NULL).
     */
    private function resolverCodigosDetalle(array $item, string $tipo, int $linea): array {
        if ($tipo === 'producto') {
            $codigoProducto = (int) ($item['codigo_producto'] ?? 0);
            if ($codigoProducto <= 0) {
                throw new PDOException("Producto no válido en la línea {$linea}");
            }

            $stmt = $this->conex->prepare(
                'SELECT codigo_servicio FROM servicio_material WHERE codigo_producto = ? LIMIT 1'
            );
            $stmt->execute([$codigoProducto]);
            $codigoServicio = $stmt->fetchColumn();

            if ($codigoServicio === false) {
                $stmtFallback = $this->conex->prepare(
                    'SELECT codigo_servicio FROM servicio WHERE estado = 1 ORDER BY codigo_servicio ASC LIMIT 1'
                );
                $stmtFallback->execute();
                $codigoServicio = $stmtFallback->fetchColumn();
            }

            if ($codigoServicio === false) {
                throw new PDOException("No hay servicios activos para vincular el producto en la línea {$linea}");
            }

            return [$codigoProducto, (int) $codigoServicio];
        }

        if ($tipo === 'servicio') {
            $codigoServicio = (int) ($item['codigo_servicio'] ?? 0);
            if ($codigoServicio <= 0) {
                throw new PDOException("Servicio no válido en la línea {$linea}");
            }

            $stmt = $this->conex->prepare(
                'SELECT codigo_producto FROM servicio_material WHERE codigo_servicio = ? LIMIT 1'
            );
            $stmt->execute([$codigoServicio]);
            $codigoProducto = $stmt->fetchColumn();

            if ($codigoProducto === false) {
                throw new PDOException("El servicio de la línea {$linea} no tiene materiales configurados en servicio_material");
            }

            return [(int) $codigoProducto, $codigoServicio];
        }

        throw new PDOException("Tipo de ítem no reconocido en la línea {$linea}");
    }

    private function descontarInventario(
        string $tipo,
        int $codigoProducto,
        int $codigoServicio,
        float $cantidad,
        int $linea
    ): void {
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

        $stmtMateriales = $this->conex->prepare(
            'SELECT codigo_producto, cantidad_usada FROM servicio_material WHERE codigo_servicio = ?'
        );
        $stmtMateriales->execute([$codigoServicio]);
        $materiales = $stmtMateriales->fetchAll(PDO::FETCH_ASSOC);

        if (empty($materiales)) {
            throw new PDOException("El servicio de la línea {$linea} no tiene materiales para descontar");
        }

        $stmtStock = $this->conex->prepare(
            'UPDATE producto_insumo SET stock_actual = stock_actual - ?
             WHERE codigo_producto = ? AND stock_actual >= ?'
        );

        foreach ($materiales as $material) {
            $cantidadDescontar = (int) ceil($material['cantidad_usada'] * $cantidad);
            $stmtStock->execute([
                $cantidadDescontar,
                (int) $material['codigo_producto'],
                $cantidadDescontar,
            ]);

            if ($stmtStock->rowCount() === 0) {
                throw new PDOException("Stock insuficiente de materiales para el servicio en la línea {$linea}");
            }
        }
    }

    private function restaurarStockPedido(int $codigoPedido): void {
        $items = $this->getItemsByPedido($codigoPedido);

        $stmtSumar = $this->conex->prepare(
            'UPDATE producto_insumo SET stock_actual = stock_actual + ? WHERE codigo_producto = ?'
        );

        foreach ($items as $item) {
            $cantidad = (float) $item['cantidad'];

            if ($item['tipo'] === 'producto') {
                $stmtSumar->execute([(int) $cantidad, (int) $item['codigo_producto']]);
                continue;
            }

            $stmtMateriales = $this->conex->prepare(
                'SELECT codigo_producto, cantidad_usada FROM servicio_material WHERE codigo_servicio = ?'
            );
            $stmtMateriales->execute([(int) $item['codigo_servicio']]);
            $materiales = $stmtMateriales->fetchAll(PDO::FETCH_ASSOC);

            foreach ($materiales as $material) {
                $cantidadRestaurar = (int) ceil($material['cantidad_usada'] * $cantidad);
                $stmtSumar->execute([
                    $cantidadRestaurar,
                    (int) $material['codigo_producto'],
                ]);
            }
        }
    }

    private function registrarPago(int $codigoPedido, array $pago, float $montoTotal): void {
        $codigoMetodo = (int) $pago['codigo_metodo'];
        $monto = (float) ($pago['monto'] ?? $montoTotal);

        $stmtPago = $this->conex->prepare(
            'INSERT INTO pagos (codigo_pedido, codigo_metodo, fecha_pago, estado) VALUES (?, ?, NOW(), 1)'
        );
        $stmtPago->execute([$codigoPedido, $codigoMetodo]);

        $codigoPago = (int) $this->conex->lastInsertId();
        $codigoMoneda = $this->getMonedaDefault();

        $stmtDetallePago = $this->conex->prepare(
            'INSERT INTO detalle_pago (codigo_pago, codigo_moneda, codigo_metodo, monto) VALUES (?, ?, ?, ?)'
        );
        $stmtDetallePago->execute([$codigoPago, $codigoMoneda, $codigoMetodo, $monto]);

        $codigoDetallePago = (int) $this->conex->lastInsertId();

        if ($this->metodoRequiereBanco($codigoMetodo)) {
            $referencia = (int) preg_replace('/\D/', '', (string) $pago['referencia']);
            if ($referencia <= 0) {
                throw new PDOException('La referencia del pago no es válida');
            }

            $numeroComprobante = trim((string) $pago['referencia']);

            $stmtRef = $this->conex->prepare(
                'INSERT IGNORE INTO referencia (referencia, numero_comprobante) VALUES (?, ?)'
            );
            $stmtRef->execute([$referencia, $numeroComprobante]);

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
}
