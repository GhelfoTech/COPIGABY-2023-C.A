<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class monedaModel extends ConectDB {
    private $conex;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todas las monedas registradas.
     */
    public function getAllMonedas() {
        try {
            $query = "SELECT m.*, t.monto_bolivares AS tasa_cambio 
                      FROM moneda m 
                      LEFT JOIN tasa_cambio t ON m.codigo_tasa = t.codigo_tasa 
                      ORDER BY m.codigo_moneda DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Registra una nueva moneda en el sistema.
     */
    public function addMoneda($datos) {
        try {
            $this->conex->beginTransaction();

            // 1. Insertar el valor en la tabla tasa_cambio
            $stmtTasa = $this->conex->prepare("INSERT INTO tasa_cambio (fecha, monto_bolivares) VALUES (NOW(), ?)");
            $stmtTasa->execute([$datos['tasa_cambio']]);
            $idTasa = $this->conex->lastInsertId();

            // 2. Insertar la moneda vinculada al ID de la tasa creada
            $query = "INSERT INTO moneda (nombre_moneda, simbolo, codigo_tasa, estado) VALUES (?, ?, ?, 1)";
            $stmt = $this->conex->prepare($query);
            $res = $stmt->execute([
                $datos['nombre_moneda'],
                $datos['simbolo'],
                $idTasa
            ]);

            $this->conex->commit();
            return $res;
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) $this->conex->rollBack();
            return false;
        }
    }

    /**
     * Actualiza la información de una moneda existente.
     */
    public function updateMoneda($id, $datos) {
        try {
            $this->conex->beginTransaction();

            // 1. Buscamos el codigo_tasa actual vinculado a esta moneda
            $stmtSearch = $this->conex->prepare("SELECT codigo_tasa FROM moneda WHERE codigo_moneda = ?");
            $stmtSearch->execute([$id]);
            $idTasa = $stmtSearch->fetchColumn();

            // 2. Actualizamos el monto en la tabla tasa_cambio
            $stmtTasa = $this->conex->prepare("UPDATE tasa_cambio SET monto_bolivares = ? WHERE codigo_tasa = ?");
            $stmtTasa->execute([$datos['tasa_cambio'], $idTasa]);

            // 3. Actualizamos la información básica de la moneda
            $query = "UPDATE moneda SET nombre_moneda = ?, simbolo = ?, estado = ? WHERE codigo_moneda = ?";
            $stmt = $this->conex->prepare($query);
            $res = $stmt->execute([
                $datos['nombre_moneda'],
                $datos['simbolo'],
                $datos['estado'],
                $id
            ]);

            $this->conex->commit();
            return $res;
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) $this->conex->rollBack();
            return false;
        }
    }

    /**
     * Desactivación lógica de una moneda.
     */
    public function deleteMoneda($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE moneda SET estado = 0 WHERE codigo_moneda = ?");
            return ["status" => $stmt->execute([$id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}