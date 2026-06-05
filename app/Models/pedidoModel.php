<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class pedidoModel extends ConectDB {
    private $conex;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todos los pedidos con nombres de cliente y usuario.
     */
    public function getAllPedidos() {
        try {
            $query = "SELECT p.codigo_pedido, p.codigo_cliente, p.codigo_usuario, p.fecha_pedido,
                             p.tasa_aplicada, p.estado,
                             c.nombre AS nombre_cliente, u.nombre_usuario,
                             COALESCE(
                                 (SELECT SUM(dp.subtotal) FROM detalle_pedido dp WHERE dp.codigo_pedido = p.codigo_pedido),
                                 0
                             ) AS monto_total
                      FROM pedido p
                      INNER JOIN cliente c ON p.codigo_cliente = c.codigo_cliente
                      INNER JOIN usuario u ON p.codigo_usuario = u.codigo_usuario
                      ORDER BY p.codigo_pedido DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Registra un pedido con sus detalles en transacción y descuenta stock de productos.
     */
    public function addPedido(array $datos, array $items) {
        if (empty($items)) {
            return ["status" => "error", "message" => "El pedido debe incluir al menos un ítem"];
        }

        try {
            $this->conex->beginTransaction();

            $queryPedido = "INSERT INTO pedido (codigo_cliente, codigo_usuario, fecha_pedido, tasa_aplicada, estado)
                            VALUES (?, ?, NOW(), ?, 1)";
            $stmtPedido = $this->conex->prepare($queryPedido);
            $stmtPedido->bindValue(1, (int) $datos['codigo_cliente'], PDO::PARAM_INT);
            $stmtPedido->bindValue(2, (int) $datos['codigo_usuario'], PDO::PARAM_INT);
            $stmtPedido->bindValue(3, $datos['tasa_aplicada']);
            $stmtPedido->execute();

            $codigoPedido = (int) $this->conex->lastInsertId();

            $queryDetalle = "INSERT INTO detalle_pedido (codigo_pedido, codigo_producto, codigo_servicio, cantidad, precio_venta, subtotal)
                             VALUES (?, ?, ?, ?, ?, ?)";
            $stmtDetalle = $this->conex->prepare($queryDetalle);

            $queryStock = "UPDATE producto_insumo SET stock_actual = stock_actual - ?
                           WHERE codigo_producto = ? AND stock_actual >= ?";
            $stmtStock = $this->conex->prepare($queryStock);

            $placeholderProducto = $this->getPlaceholderProducto();
            $placeholderServicio = $this->getPlaceholderServicio();

            foreach ($items as $item) {
                $tipo = $item['tipo'] ?? '';
                $cantidad = (float) ($item['cantidad'] ?? 0);
                $precioVenta = (float) ($item['precio_venta'] ?? 0);
                $subtotal = round($cantidad * $precioVenta, 2);

                if ($cantidad <= 0 || $precioVenta < 0) {
                    throw new PDOException('Cantidad o precio inválido en uno de los ítems');
                }

                if ($tipo === 'producto') {
                    $codigoProducto = (int) ($item['codigo_producto'] ?? 0);
                    $codigoServicio = $placeholderServicio;

                    if ($codigoProducto <= 0) {
                        throw new PDOException('Producto no válido');
                    }

                    $stmtStock->bindValue(1, $cantidad);
                    $stmtStock->bindValue(2, $codigoProducto, PDO::PARAM_INT);
                    $stmtStock->bindValue(3, $cantidad);
                    $stmtStock->execute();

                    if ($stmtStock->rowCount() === 0) {
                        throw new PDOException('Stock insuficiente para el producto seleccionado');
                    }
                } elseif ($tipo === 'servicio') {
                    $codigoServicio = (int) ($item['codigo_servicio'] ?? 0);
                    $codigoProducto = $placeholderProducto;

                    if ($codigoServicio <= 0) {
                        throw new PDOException('Servicio no válido');
                    }
                } else {
                    throw new PDOException('Tipo de ítem no reconocido');
                }

                $stmtDetalle->bindValue(1, $codigoPedido, PDO::PARAM_INT);
                $stmtDetalle->bindValue(2, $codigoProducto, PDO::PARAM_INT);
                $stmtDetalle->bindValue(3, $codigoServicio, PDO::PARAM_INT);
                $stmtDetalle->bindValue(4, $cantidad);
                $stmtDetalle->bindValue(5, $precioVenta);
                $stmtDetalle->bindValue(6, $subtotal);
                $stmtDetalle->execute();
            }

            $this->conex->commit();
            return ["status" => "success", "message" => "Pedido registrado exitosamente", "codigo_pedido" => $codigoPedido];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Anulación lógica del pedido.
     */
    public function deletePedido(int $id) {
        try {
            $stmt = $this->conex->prepare("UPDATE pedido SET estado = 0 WHERE codigo_pedido = ?");
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            return ["status" => $stmt->execute() ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Clientes disponibles para el formulario.
     */
    public function getClientesActivos() {
        try {
            $stmt = $this->conex->prepare("SELECT codigo_cliente, cedula, nombre FROM cliente ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Productos activos para el formulario.
     */
    public function getProductosActivos() {
        try {
            $stmt = $this->conex->prepare(
                "SELECT codigo_producto, nombre_producto, costo, stock_actual
                 FROM producto_insumo WHERE estado = 1 ORDER BY nombre_producto ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Servicios activos para el formulario.
     */
    public function getServiciosActivos() {
        try {
            $stmt = $this->conex->prepare(
                "SELECT codigo_servicio, nombre_servicio, precio
                 FROM servicio WHERE estado = 1 ORDER BY nombre_servicio ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Tasa de cambio activa para el encabezado del pedido.
     */
    public function getTasaActual() {
        try {
            $stmt = $this->conex->prepare(
                "SELECT tasa_cambio FROM moneda WHERE estado = 1 ORDER BY codigo_moneda DESC LIMIT 1"
            );
            $stmt->execute();
            $tasa = $stmt->fetchColumn();
            return $tasa !== false ? (float) $tasa : 1.0;
        } catch (PDOException $e) {
            return 1.0;
        }
    }

    /**
     * Referencia mínima requerida por FK cuando el ítem es un servicio.
     */
    private function getPlaceholderProducto(): int {
        $stmt = $this->conex->query("SELECT codigo_producto FROM producto_insumo ORDER BY codigo_producto ASC LIMIT 1");
        $id = $stmt ? $stmt->fetchColumn() : false;
        return $id !== false ? (int) $id : 1;
    }

    /**
     * Referencia mínima requerida por FK cuando el ítem es un producto.
     */
    private function getPlaceholderServicio(): int {
        $stmt = $this->conex->query("SELECT codigo_servicio FROM servicio ORDER BY codigo_servicio ASC LIMIT 1");
        $id = $stmt ? $stmt->fetchColumn() : false;
        return $id !== false ? (int) $id : 1;
    }
}
