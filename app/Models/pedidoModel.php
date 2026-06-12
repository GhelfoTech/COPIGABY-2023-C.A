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
     * Registra errores PDO en el log del servidor (no silencioso).
     */
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
            $query = "SELECT p.codigo_pedido, p.cedula_cliente, p.cedula_usuario, p.fecha_pedido,
                             p.tasa_aplicada, p.estado,
                             c.nombre AS nombre_cliente, u.nombre_usuario,
                             COALESCE(
                                 (SELECT SUM(dp.subtotal) FROM detalle_pedido dp WHERE dp.codigo_pedido = p.codigo_pedido),
                                 0
                             ) AS monto_total
                      FROM pedido p
                      LEFT JOIN cliente c ON p.cedula_cliente = c.cedula_cliente
                      LEFT JOIN usuario u ON p.cedula_usuario = u.cedula_usuario
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
     * Registra un pedido con sus detalles en transacción y descuenta stock de productos.
     */
    public function addPedido(array $datos, array $items) {
        if (empty($items)) {
            return ['status' => 'error', 'message' => 'El pedido debe incluir al menos un ítem'];
        }

        $cedulaUsuario = $datos['codigo_usuario'] ?? ''; 
        $cedulaCliente = $datos['codigo_cliente'] ?? '';
        $tasaAplicada  = (float) ($datos['tasa_aplicada'] ?? 1.0);
        if (empty($cedulaUsuario) || empty($cedulaCliente)) {
            return ['status' => 'error', 'message' => 'Cliente o usuario no válido para registrar el pedido'];
        }

        try {
            $this->conex->beginTransaction();

            $stmtPedido = $this->conex->prepare(
                'INSERT INTO pedido (cedula_cliente, cedula_usuario, fecha_pedido, tasa_aplicada, estado) 
                 VALUES (:cliente, :usuario, NOW(), :tasa, 1)'
            );
            $stmtPedido->execute([
                ':cliente' => $cedulaCliente,
                ':usuario' => $cedulaUsuario,
                ':tasa'    => $tasaAplicada,
            ]);

            $codigoPedido = (int) $this->conex->lastInsertId();
            if ($codigoPedido <= 0) {
                throw new PDOException('No se pudo obtener el código del pedido insertado');
            }

            $stmtDetalle = $this->conex->prepare(
                'INSERT INTO detalle_pedido (codigo_pedido, codigo_producto, codigo_servicio, cantidad, precio_venta, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );

            $stmtStock = $this->conex->prepare(
                'UPDATE producto_insumo SET stock_actual = stock_actual - ?
                 WHERE codigo_producto = ? AND stock_actual >= ?'
            );

            foreach ($items as $index => $item) {
                $linea = (int) $index + 1;
                $tipo = (string) ($item['tipo'] ?? '');
                $cantidad = (int) ($item['cantidad'] ?? 0);
                $precioVenta = (float) ($item['precio_venta'] ?? 0);

                if ($cantidad < 1) {
                    throw new PDOException("Cantidad inválida en la línea {$linea}");
                }
                if ($precioVenta < 0) {
                    throw new PDOException("Precio inválido en la línea {$linea}");
                }

                $subtotal = round($cantidad * $precioVenta, 2);

                if ($tipo === 'producto') {
                    $codigoProducto = (int) ($item['codigo_producto'] ?? 0);
                    $codigoServicio = null;

                    if ($codigoProducto <= 0) {
                        throw new PDOException("Producto no válido en la línea {$linea}");
                    }

                    $stmtStock->execute([$cantidad, $codigoProducto, $cantidad]);
                    if ($stmtStock->rowCount() === 0) {
                        throw new PDOException("Stock insuficiente para el producto en la línea {$linea}");
                    }
                } elseif ($tipo === 'servicio') {
                    $codigoServicio = (int) ($item['codigo_servicio'] ?? 0);
                    $codigoProducto = null;

                    if ($codigoServicio <= 0) {
                        throw new PDOException("Servicio no válido en la línea {$linea}");
                    }
                } else {
                    throw new PDOException("Tipo de ítem no reconocido en la línea {$linea}");
                }

                $stmtDetalle->bindValue(1, $codigoPedido, PDO::PARAM_INT);
                $stmtDetalle->bindValue(
                    2,
                    $codigoProducto,
                    $codigoProducto === null ? PDO::PARAM_NULL : PDO::PARAM_INT
                );
                $stmtDetalle->bindValue(
                    3,
                    $codigoServicio,
                    $codigoServicio === null ? PDO::PARAM_NULL : PDO::PARAM_INT
                );
                $stmtDetalle->bindValue(4, $cantidad, PDO::PARAM_INT);
                $stmtDetalle->bindValue(5, $precioVenta);
                $stmtDetalle->bindValue(6, $subtotal);
                $stmtDetalle->execute();
            }

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

            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Actualiza los datos modificables del encabezado del pedido.
     */
    public function updatePedido(int $id, array $datos) {
        $cedulaCliente = $datos['codigo_cliente'] ?? '';
        $estado = (int) ($datos['estado'] ?? 0);
        $tasaAplicada = (float) ($datos['tasa_aplicada'] ?? 1.0);

        if ($id <= 0 || empty($cedulaCliente)) {
            return ['status' => 'error', 'message' => 'Pedido o cliente no válido'];
        }

        if ($tasaAplicada <= 0) $tasaAplicada = 1.0;
        $estado = $estado ? 1 : 0;

        try {
            $this->conex->beginTransaction();

            $stmt = $this->conex->prepare(
                'UPDATE pedido SET cedula_cliente = ?, tasa_aplicada = ?, estado = ?
                 WHERE codigo_pedido = ?'
            );
            $success = $stmt->execute([
                $cedulaCliente,
                $tasaAplicada,
                $estado,
                $id
            ]);

            if (!$success) {
                throw new PDOException('No se pudo actualizar el pedido');
            }

            $this->conex->commit();

            return [
                'status'  => 'success',
                'message' => 'Pedido actualizado con éxito',
            ];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            $this->logPdoError('updatePedido', $e);

            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Anulación lógica del pedido.
     */
    public function deletePedido(int $id) {
        try {
            $stmt = $this->conex->prepare('UPDATE pedido SET estado = 0 WHERE codigo_pedido = ?');
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            return ['status' => $stmt->execute() ? 'success' : 'error'];
        } catch (PDOException $e) {
            $this->logPdoError('deletePedido', $e);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Clientes disponibles para el formulario.
     */
    public function getClientesActivos() {
        try {
            $stmt = $this->conex->prepare('SELECT cedula_cliente, nombre FROM cliente ORDER BY nombre ASC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logPdoError('getClientesActivos', $e);
            return [];
        }
    }

    /**
     * Productos activos para el formulario.
     */
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

    /**
     * Servicios activos para el formulario.
     */
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

    /**
     * Tasa de cambio activa para el encabezado del pedido.
     */
    public function getTasaActual() {
        try {
            $stmt = $this->conex->prepare(
                'SELECT t.monto_bolivares 
                 FROM moneda m 
                 INNER JOIN tasa_cambio t ON m.codigo_tasa = t.codigo_tasa 
                 WHERE m.estado = 1 ORDER BY m.codigo_moneda DESC LIMIT 1'
            );
            $stmt->execute();
            $tasa = $stmt->fetchColumn();
            return $tasa !== false ? (float) $tasa : 1.0;
        } catch (PDOException $e) {
            $this->logPdoError('getTasaActual', $e);
            return 1.0;
        }
    }

}
