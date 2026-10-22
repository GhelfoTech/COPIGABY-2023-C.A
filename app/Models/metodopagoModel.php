<?php

namespace App\models;

use App\config\ConectDB;
use App\traits\MetodoPagoTrait;
use PDO;
use PDOException;

class metodopagoModel extends ConectDB {
    use MetodoPagoTrait;
    private $conex;

    private $codigo_metodo;
    private $nombre_metodo;
    private $estado;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    public function getAllMetodos() {
        try {
            $stmt = $this->conex->prepare("SELECT * FROM metodo_pago ORDER BY codigo_metodo DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function addMetodo($nombre) {
        $validacion = $this->validarMetodoPago(['nombre_metodo' => $nombre, 'estado' => 1]);
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $query = "INSERT INTO metodo_pago (nombre_metodo, estado) VALUES (?, 1)";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([trim($nombre)]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateMetodo($id, $nombre, $estado) {
        $validacion = $this->validarMetodoPago(['nombre_metodo' => $nombre, 'estado' => $estado, 'codigo_metodo' => $id]);
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $query = "UPDATE metodo_pago SET nombre_metodo = ?, estado = ? WHERE codigo_metodo = ?";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([
                trim($nombre),
                (int) $estado,
                (int) $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteMetodo($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE metodo_pago SET estado = 0 WHERE codigo_metodo = ?");
            $success = $stmt->execute([(int) $id]);
            return ["status" => $success ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}