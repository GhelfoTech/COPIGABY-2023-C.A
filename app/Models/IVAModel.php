<?php

namespace App\models;

use App\config\ConectDB;
use App\traits\IvaTrait;
use PDO;
use PDOException;

class ivaModel extends ConectDB {
    use IvaTrait;
    private $conex;

    private $codigo_iva;
    private $porcentaje_iva;
    private $fecha;
    private $estado;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todos los registros de IVA.
     */
    public function getAllIvas() {
        try {
            $stmt = $this->conex->prepare("SELECT codigo_IVA, porcentaje_iva, fecha, estado FROM iva ORDER BY fecha DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Registra un nuevo porcentaje de IVA.
     */
    public function addIva($porcentaje_iva) {
        $validacion = $this->validarIva(['porcentaje_iva' => $porcentaje_iva, 'estado' => 1]);
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $query = "INSERT INTO iva (porcentaje_iva, fecha, estado) VALUES (?, NOW(), 1)";
            $stmt = $this->conex->prepare($query);
            $ejecutado = $stmt->execute([$porcentaje_iva]);
            return $ejecutado
                ? ['status' => 'success']
                : ['status' => 'error', 'message' => 'No se pudo registrar el IVA'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Actualiza un registro de IVA existente.
     */
    public function updateIva($id, $porcentaje_iva, $estado) {
        $validacion = $this->validarIva(['porcentaje_iva' => $porcentaje_iva, 'estado' => $estado]);
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $query = "UPDATE iva SET porcentaje_iva = ?, estado = ? WHERE codigo_IVA = ?";
            $stmt = $this->conex->prepare($query);
            $ejecutado = $stmt->execute([$porcentaje_iva, $estado, $id]);
            return $ejecutado
                ? ['status' => 'success']
                : ['status' => 'error', 'message' => 'No se pudo actualizar el IVA'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Desactivación lógica (borrado).
     */
    public function deleteIva($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE iva SET estado = 0 WHERE codigo_IVA = ?");
            return ["status" => $stmt->execute([$id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}