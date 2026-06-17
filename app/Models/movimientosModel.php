<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class movimientosModel extends ConectDB {
    private $conex;

    private $codigo_movimiento;
    private $fecha;
    private $cedula_usuario;
    private $tipo;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todos los movimientos con datos del usuario asociado.
     */
    public function getAllMovimientos() {
        try {
            $query = "SELECT m.codigo_movimiento, m.fecha, m.cedula_usuario, m.tipo, u.nombre_usuario
                      FROM movimientos m
                      INNER JOIN usuario u ON m.cedula_usuario = u.cedula_usuario
                      ORDER BY m.codigo_movimiento DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtiene un movimiento por su código.
     */
    public function getMovimientoById($id) {
        try {
            $query = "SELECT m.codigo_movimiento, m.fecha, m.cedula_usuario, m.tipo, u.nombre_usuario
                      FROM movimientos m
                      INNER JOIN usuario u ON m.cedula_usuario = u.cedula_usuario
                      WHERE m.codigo_movimiento = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([(int) $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Registra un nuevo movimiento.
     */
    public function addMovimiento($datos) {
        try {
            $query = "INSERT INTO movimientos (fecha, cedula_usuario, tipo) VALUES (?, ?, ?)";
            $stmt = $this->conex->prepare($query);
            $success = $stmt->execute([
                $datos['fecha'],
                (int) $datos['cedula_usuario'],
                (int) $datos['tipo']
            ]);
            return $success
                ? ["status" => "success", "message" => "Movimiento registrado con éxito."]
                : ["status" => "error", "message" => "No se pudo registrar el movimiento."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Actualiza un movimiento existente.
     */
    public function updateMovimiento($id, $datos) {
        try {
            $query = "UPDATE movimientos SET fecha = ?, cedula_usuario = ?, tipo = ?
                      WHERE codigo_movimiento = ?";
            $stmt = $this->conex->prepare($query);
            $success = $stmt->execute([
                $datos['fecha'],
                (int) $datos['cedula_usuario'],
                (int) $datos['tipo'],
                (int) $id
            ]);
            return $success
                ? ["status" => "success", "message" => "Movimiento actualizado."]
                : ["status" => "error", "message" => "No se pudo actualizar el movimiento."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Elimina un movimiento de forma permanente.
     */
    public function deleteMovimiento($id) {
        try {
            $stmt = $this->conex->prepare("DELETE FROM movimientos WHERE codigo_movimiento = ?");
            return ["status" => $stmt->execute([(int) $id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
