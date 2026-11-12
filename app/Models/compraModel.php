<?php

namespace App\models;

use App\config\ConectDB;
use App\traits\ComprasTrait;
use PDO;
use PDOException;

class compraModel extends ConectDB {
    use ComprasTrait;
    private $conex;

    private $codigo_compra;
    private $codigo_proveedor;
    private $cedula_usuario;
    private $numero_factura_proveedor;
    private $fecha_compra;
    private $monto_total;
    private $estado;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todas las compras registradas con información de proveedor y usuario.
     */
    public function getAllCompras() {
        try {
            $query = "SELECT c.*, p.razon_social AS nombre_proveedor, u.nombre_usuario 
                      FROM compra c
                      INNER JOIN proveedor p ON c.codigo_proveedor = p.codigo_proveedor
                      INNER JOIN usuario u ON c.cedula_usuario = u.cedula_usuario
                      ORDER BY c.codigo_compra DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Registra una compra completa con sus detalles y actualiza stock.
     */
    public function addCompra($datos, $items) {
        $validacion = $this->validarCompra($datos, $items);
        if ($validacion !== true) {
            return $validacion;
        }

        $monedaActiva = (new monedaModel())->getMonedaActiva();
        $tasaCambio = (float) ($monedaActiva['tasa_cambio'] ?? 0);
        if (!is_finite($tasaCambio) || $tasaCambio <= 0) {
            return ["status" => "error", "message" => "No hay una tasa de cambio válida para registrar la compra"];
        }

        try {
            $this->conex->beginTransaction();

            // 1. Insertar Cabecera usando cedula_usuario
            $query = "INSERT INTO compra (codigo_proveedor, cedula_usuario, numero_factura_proveedor, fecha_compra, monto_total, tasa_cambio, estado)
                      VALUES (?, ?, ?, ?, ?, ?, 1)";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([
                $datos['codigo_proveedor'],
                $datos['cedula_usuario'],
                $datos['numero_factura_proveedor'],
                $datos['fecha_compra'] . date(' H:i:s'),
                $datos['monto_total'],
                $tasaCambio,
            ]);

            $codigoCompra = $this->conex->lastInsertId();

            // 2. Insertar Detalles y Actualizar Stock
            $queryDetalle = "INSERT INTO detalle_compra (codigo_compra, codigo_producto, cantidad, costo_unitario, subtotal)
                             VALUES (?, ?, ?, ?, ?)";
            $stmtDetalle = $this->conex->prepare($queryDetalle);

            $queryStock = "UPDATE producto_insumo SET stock_actual = stock_actual + ?
                           WHERE codigo_producto = ?";
            $stmtStock = $this->conex->prepare($queryStock);

            foreach ($items as $item) {
                $item['costo'] = round((float) $item['costo'], 2);
                $subtotal = round($item['cantidad'] * $item['costo'], 2);

                // Guardar detalle
                $stmtDetalle->execute([
                    $codigoCompra,
                    $item['codigo_producto'],
                    $item['cantidad'],
                    $item['costo'],
                    $subtotal
                ]);

                // Actualizar inventario (sumar stock únicamente)
                $stmtStock->execute([
                    $item['cantidad'],
                    $item['codigo_producto']
                ]);

            }

            $this->conex->commit();

            $productoModel = new productoModel();
            foreach (array_unique(array_column($items, 'codigo_producto')) as $codigoProducto) {
                $productoModel->calcularYGuardarPrecio((int) $codigoProducto);
            }

            return ["status" => "success"];
        } catch (\Throwable $e) {
            if ($this->conex->inTransaction()) $this->conex->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Actualiza los datos modificables de una compra.
     */
    public function updateCompra(int $id, array $datos) {
        $validacion = $this->validarCompra(array_merge($datos, ['codigo_compra' => $id]), []);
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $query = "UPDATE compra SET codigo_proveedor = ?, numero_factura_proveedor = ?,
                      fecha_compra = ?, monto_total = ?, estado = ?
                      WHERE codigo_compra = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->bindValue(1, (int) $datos['codigo_proveedor'], PDO::PARAM_INT);
            $stmt->bindValue(2, $datos['numero_factura_proveedor']);
            $stmt->bindValue(3, $datos['fecha_compra'] . date(' H:i:s'));
            $stmt->bindValue(4, $datos['monto_total']);
            $stmt->bindValue(5, (int) $datos['estado'], PDO::PARAM_INT);
            $stmt->bindValue(6, $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Compra actualizada con éxito"];
            }
            return ["status" => "error", "message" => "No se pudo actualizar"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Anulación lógica de una compra.
     */
    public function deleteCompra($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE compra SET estado = 0 WHERE codigo_compra = ?");
            return ["status" => $stmt->execute([$id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Obtiene proveedores para el selector del modal.
     */
    public function getProviders() {
        try {
            $stmt = $this->conex->prepare("SELECT codigo_proveedor, razon_social FROM proveedor WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    /**
     * Obtiene productos para el selector de la compra.
     */
    public function getCategories() {
        try {
            $stmt = $this->conex->prepare("SELECT codigo_categoria, nombre_categoria FROM categoria WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function getProducts() {
        try {
            $query = "SELECT p.codigo_producto, p.nombre_producto,
                             COALESCE(
                                 (SELECT dc.costo_unitario FROM detalle_compra dc
                                  WHERE dc.codigo_producto = p.codigo_producto
                                  ORDER BY dc.codigo_compra DESC LIMIT 1),
                             0) AS costo
                      FROM producto_insumo p WHERE p.estado = 1";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    /**
     * Obtiene la cabecera detallada de una compra específica.
     */
    public function getCompraById($id) {
        try {
            $query = "SELECT c.*, p.razon_social, p.rif_proveedor, p.telefono, p.direccion, u.nombre_usuario 
                      FROM compra c
                      INNER JOIN proveedor p ON c.codigo_proveedor = p.codigo_proveedor
                      INNER JOIN usuario u ON c.cedula_usuario = u.cedula_usuario
                      WHERE c.codigo_compra = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return null; }
    }

    public function updateCompraItems(int $id, array $items, ?array $datosCompra = null): array
    {
        if ($id <= 0 || !$items) {
            return ['status' => 'error', 'message' => 'La compra y sus productos deben ser válidos'];
        }

        $cantidades = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                return ['status' => 'error', 'message' => 'Los datos de los productos no son válidos'];
            }

            $codigoDetalle = filter_var($item['codigo_detalle_compra'] ?? null, FILTER_VALIDATE_INT);
            $cantidad = $item['cantidad'] ?? null;
            if (
                $codigoDetalle === false
                || $codigoDetalle <= 0
                || array_key_exists($codigoDetalle, $cantidades)
                || !is_numeric($cantidad)
                || !is_finite((float) $cantidad)
                || (float) $cantidad <= 0
                || (float) $cantidad > PHP_INT_MAX
                || floor((float) $cantidad) !== (float) $cantidad
            ) {
                return ['status' => 'error', 'message' => 'Cada cantidad debe ser un número entero mayor a cero'];
            }
            $cantidades[$codigoDetalle] = (int) $cantidad;
        }

        if ($datosCompra !== null) {
            $codigoProveedor = filter_var($datosCompra['codigo_proveedor'] ?? null, FILTER_VALIDATE_INT);
            $estadoNuevo = filter_var($datosCompra['estado'] ?? null, FILTER_VALIDATE_INT);
            $numeroFactura = trim((string) ($datosCompra['numero_factura_proveedor'] ?? ''));
            $fechaCompra = trim((string) ($datosCompra['fecha_compra'] ?? ''));
            if (
                $codigoProveedor === false
                || $codigoProveedor <= 0
                || !in_array($estadoNuevo, [0, 1], true)
                || $numeroFactura === ''
                || strlen($numeroFactura) > 10
            ) {
                return ['status' => 'error', 'message' => 'Los datos de la compra no son válidos'];
            }

            $datosCompra = [
                'codigo_proveedor' => $codigoProveedor,
                'numero_factura_proveedor' => $numeroFactura,
                'fecha_compra' => $fechaCompra,
                'estado' => $estadoNuevo,
            ];
        }

        try {
            $this->conex->beginTransaction();

            $stmtCompra = $this->conex->prepare(
                'SELECT estado, monto_total, cedula_usuario
                 FROM compra WHERE codigo_compra = ? FOR UPDATE'
            );
            $stmtCompra->execute([$id]);
            $compra = $stmtCompra->fetch(PDO::FETCH_ASSOC);
            if (!$compra) {
                $this->conex->rollBack();
                return ['status' => 'error', 'message' => 'La compra indicada no existe'];
            }
            if ((int) $compra['estado'] !== 1) {
                $this->conex->rollBack();
                return ['status' => 'error', 'message' => 'No se pueden editar productos de una compra anulada'];
            }

            if ($datosCompra !== null) {
                $validacion = $this->validarCompra(
                    array_merge($datosCompra, [
                        'codigo_compra' => $id,
                        'cedula_usuario' => $compra['cedula_usuario'],
                        'monto_total' => $compra['monto_total'],
                    ]),
                    []
                );
                if ($validacion !== true) {
                    $this->conex->rollBack();
                    return $validacion;
                }
            }

            $stmtDetalles = $this->conex->prepare(
                'SELECT codigo_detalle_compra, codigo_producto, cantidad, costo_unitario
                 FROM detalle_compra
                 WHERE codigo_compra = ?
                 ORDER BY codigo_detalle_compra
                 FOR UPDATE'
            );
            $stmtDetalles->execute([$id]);
            $detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);
            if (count($detalles) !== count($cantidades)) {
                $this->conex->rollBack();
                return ['status' => 'error', 'message' => 'La lista de productos cambió; vuelve a abrir el detalle de la compra'];
            }

            foreach ($detalles as $detalle) {
                $codigoDetalle = (int) $detalle['codigo_detalle_compra'];
                if (!array_key_exists($codigoDetalle, $cantidades)) {
                    $this->conex->rollBack();
                    return ['status' => 'error', 'message' => 'La lista de productos cambió; vuelve a abrir el detalle de la compra'];
                }
            }

            $stmtStock = $this->conex->prepare(
                'SELECT stock_actual FROM producto_insumo WHERE codigo_producto = ? FOR UPDATE'
            );
            $stmtActualizarStock = $this->conex->prepare(
                'UPDATE producto_insumo SET stock_actual = ? WHERE codigo_producto = ?'
            );
            $stmtDetalle = $this->conex->prepare(
                'UPDATE detalle_compra
                 SET cantidad = ?, subtotal = ?
                 WHERE codigo_detalle_compra = ? AND codigo_compra = ?'
            );

            $montoTotal = 0.0;
            $productosActualizados = [];
            foreach ($detalles as $detalle) {
                $codigoDetalle = (int) $detalle['codigo_detalle_compra'];
                $codigoProducto = (int) $detalle['codigo_producto'];
                $cantidadAnterior = (float) $detalle['cantidad'];
                $cantidadNueva = $cantidades[$codigoDetalle];

                $stmtStock->execute([$codigoProducto]);
                $stockActual = $stmtStock->fetchColumn();
                if ($stockActual === false) {
                    $this->conex->rollBack();
                    return ['status' => 'error', 'message' => 'No se encontró un producto asociado a la compra'];
                }

                // El inventario es entero; conserva el efecto de redondeo de cantidades históricas decimales.
                $deltaStock = $cantidadNueva - (int) round($cantidadAnterior, 0, PHP_ROUND_HALF_UP);
                $stockNuevo = (int) $stockActual + $deltaStock;
                if ($stockNuevo < 0) {
                    $this->conex->rollBack();
                    return [
                        'status' => 'error',
                        'message' => 'No se puede reducir la cantidad: el inventario ya no tiene suficientes unidades disponibles',
                    ];
                }
                if ($deltaStock !== 0) {
                    $stmtActualizarStock->execute([$stockNuevo, $codigoProducto]);
                }

                $subtotal = round($cantidadNueva * (float) $detalle['costo_unitario'], 2);
                $stmtDetalle->execute([$cantidadNueva, $subtotal, $codigoDetalle, $id]);
                $montoTotal += $subtotal;
                $productosActualizados[$codigoProducto] = $codigoProducto;
            }

            if ($datosCompra === null) {
                $stmtTotal = $this->conex->prepare(
                    'UPDATE compra SET monto_total = ? WHERE codigo_compra = ?'
                );
                $stmtTotal->execute([round($montoTotal, 2), $id]);
            } else {
                $stmtTotal = $this->conex->prepare(
                    'UPDATE compra
                     SET codigo_proveedor = ?, numero_factura_proveedor = ?, fecha_compra = ?,
                         estado = ?, monto_total = ?
                     WHERE codigo_compra = ?'
                );
                $stmtTotal->execute([
                    $datosCompra['codigo_proveedor'],
                    $datosCompra['numero_factura_proveedor'],
                    $datosCompra['fecha_compra'] . date(' H:i:s'),
                    $datosCompra['estado'],
                    round($montoTotal, 2),
                    $id,
                ]);
            }

            $stmtPrecio = $this->conex->prepare(
                'UPDATE producto_insumo p
                 SET precio = ROUND(COALESCE(
                     (SELECT dc.costo_unitario FROM detalle_compra dc
                      WHERE dc.codigo_producto = p.codigo_producto
                      ORDER BY dc.codigo_compra DESC LIMIT 1),
                     p.costo, 0) * (1 + COALESCE(p.porcentaje_ganancia, 0) / 100), 2)
                 WHERE codigo_producto = ?'
            );
            foreach ($productosActualizados as $codigoProducto) {
                $stmtPrecio->execute([$codigoProducto]);
            }
            $this->conex->commit();

            return [
                'status' => 'success',
                'message' => $datosCompra === null
                    ? 'Cantidades, inventario y monto de la compra actualizados'
                    : 'Compra, productos, inventario y monto actualizados correctamente',
                'monto_total' => round($montoTotal, 2),
            ];
        } catch (\Throwable $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            error_log('[compraModel] No se pudieron actualizar las cantidades de la compra: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'No se pudieron actualizar las cantidades de la compra'];
        }
    }

    /**
     * Obtiene los productos (puente) de una compra específica.
     */
    public function getItemsByCompra($id) {
        try {
            $query = "SELECT dc.*, p.nombre_producto 
                      FROM detalle_compra dc
                      INNER JOIN producto_insumo p ON dc.codigo_producto = p.codigo_producto
                      WHERE dc.codigo_compra = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }
}
