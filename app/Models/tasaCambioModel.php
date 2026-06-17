<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class tasaCambioModel extends ConectDB {
    private $conex;

    private $codigo_tasa;
    private $fecha;
    private $monto_bolivares;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todas las tasas de cambio registradas.
     */
    public function getAllTasas() {
        try {
            $query = "SELECT codigo_tasa, fecha, monto_bolivares FROM tasa_cambio ORDER BY fecha DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtiene una tasa de cambio por su código.
     */
    public function getTasaById($id) {
        try {
            $query = "SELECT codigo_tasa, fecha, monto_bolivares FROM tasa_cambio WHERE codigo_tasa = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([(int) $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Obtiene la tasa de cambio más reciente.
     */
    public function getTasaActual() {
        try {
            $query = "SELECT codigo_tasa, fecha, monto_bolivares FROM tasa_cambio ORDER BY fecha DESC LIMIT 1";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Registra una nueva tasa de cambio.
     */
    public function addTasa($datos) {
        try {
            $query = "INSERT INTO tasa_cambio (fecha, monto_bolivares) VALUES (?, ?)";
            $stmt = $this->conex->prepare($query);
            $success = $stmt->execute([
                $datos['fecha'],
                $datos['monto_bolivares']
            ]);
            return $success
                ? ["status" => "success", "message" => "Tasa de cambio registrada con éxito."]
                : ["status" => "error", "message" => "No se pudo registrar la tasa de cambio."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Actualiza una tasa de cambio existente.
     */
    public function updateTasa($id, $datos) {
        try {
            $query = "UPDATE tasa_cambio SET fecha = ?, monto_bolivares = ? WHERE codigo_tasa = ?";
            $stmt = $this->conex->prepare($query);
            $success = $stmt->execute([
                $datos['fecha'],
                $datos['monto_bolivares'],
                (int) $id
            ]);
            return $success
                ? ["status" => "success", "message" => "Tasa de cambio actualizada."]
                : ["status" => "error", "message" => "No se pudo actualizar la tasa de cambio."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Elimina una tasa de cambio de forma permanente.
     */
    public function deleteTasa($id) {
        try {
            $stmt = $this->conex->prepare("DELETE FROM tasa_cambio WHERE codigo_tasa = ?");
            return ["status" => $stmt->execute([(int) $id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
