<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class pagosModel extends ConectDB {
    private $conex;

    private $codigo_pago;
    private $codigo_pedido;
    private $codigo_metodo;
    private $fecha_pago;
    private $estado;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todos los pagos con información de pedido y método de pago.
     */
    public function getAllPagos() {
        try {
            $query = "SELECT pg.codigo_pago, pg.codigo_pedido, pg.codigo_metodo, pg.fecha_pago, pg.estado,
                             mp.nombre_metodo, p.cedula_cliente, c.nombre AS nombre_cliente
                      FROM pagos pg
                      INNER JOIN metodo_pago mp ON pg.codigo_metodo = mp.codigo_metodo
                      INNER JOIN pedido p ON pg.codigo_pedido = p.codigo_pedido
                      LEFT JOIN cliente c ON p.cedula_cliente = c.cedula_cliente
                      ORDER BY pg.codigo_pago DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtiene un pago por su código.
     */
    public function getPagoById($id) {
        try {
            $query = "SELECT pg.codigo_pago, pg.codigo_pedido, pg.codigo_metodo, pg.fecha_pago, pg.estado,
                             mp.nombre_metodo, p.cedula_cliente, c.nombre AS nombre_cliente
                      FROM pagos pg
                      INNER JOIN metodo_pago mp ON pg.codigo_metodo = mp.codigo_metodo
                      INNER JOIN pedido p ON pg.codigo_pedido = p.codigo_pedido
                      LEFT JOIN cliente c ON p.cedula_cliente = c.cedula_cliente
                      WHERE pg.codigo_pago = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([(int) $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Registra un nuevo pago.
     */
    public function addPago($datos) {
        try {
            $query = "INSERT INTO pagos (codigo_pedido, codigo_metodo, fecha_pago, estado)
                      VALUES (?, ?, ?, 1)";
            $stmt = $this->conex->prepare($query);
            $success = $stmt->execute([
                (int) $datos['codigo_pedido'],
                (int) $datos['codigo_metodo'],
                $datos['fecha_pago']
            ]);
            return $success
                ? ["status" => "success", "message" => "Pago registrado con éxito."]
                : ["status" => "error", "message" => "No se pudo registrar el pago."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Actualiza un pago existente.
     */
    public function updatePago($id, $datos) {
        try {
            $query = "UPDATE pagos SET codigo_pedido = ?, codigo_metodo = ?, fecha_pago = ?, estado = ?
                      WHERE codigo_pago = ?";
            $stmt = $this->conex->prepare($query);
            $success = $stmt->execute([
                (int) $datos['codigo_pedido'],
                (int) $datos['codigo_metodo'],
                $datos['fecha_pago'],
                (int) $datos['estado'],
                (int) $id
            ]);
            return $success
                ? ["status" => "success", "message" => "Pago actualizado."]
                : ["status" => "error", "message" => "No se pudo actualizar el pago."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Desactivación lógica de un pago.
     */
    public function deletePago($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE pagos SET estado = 0 WHERE codigo_pago = ?");
            return ["status" => $stmt->execute([(int) $id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Pedidos activos para el selector del formulario.
     */
    public function getPedidosActivos() {
        try {
            $stmt = $this->conex->prepare(
                "SELECT codigo_pedido, cedula_cliente, fecha_pedido FROM pedido WHERE estado = 1 ORDER BY codigo_pedido DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Métodos de pago activos para el selector del formulario.
     */
    public function getMetodosActivos() {
        try {
            $stmt = $this->conex->prepare(
                "SELECT codigo_metodo, nombre_metodo FROM metodo_pago WHERE estado = 1 ORDER BY nombre_metodo ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
