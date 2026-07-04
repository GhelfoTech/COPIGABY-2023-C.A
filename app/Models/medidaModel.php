<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class medidaModel extends ConectDB {
    private $conex;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todas las unidades de medida.
     */
    public function getAllMedidas() {
        try {
            $stmt = $this->conex->prepare("SELECT * FROM unidad_medida ORDER BY codigo_media DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function addMedida($nombre, $abreviatura = null) {
        try {
            $query = "INSERT INTO unidad_medida (nombre, abreviatura, estado) VALUES (?, ?, 1)";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([$nombre, $abreviatura]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateMedida($id, $nombre, $abreviatura = null, $estado) {
        try {
            $query = "UPDATE unidad_medida SET nombre = ?, abreviatura = ?, estado = ? WHERE codigo_media = ?";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([$nombre, $abreviatura, $estado, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

     /**
      * Desactivación lógica (borrado).
      */
     public function deleteMedida($id) {
         try {
             $stmt = $this->conex->prepare("UPDATE unidad_medida SET estado = 0 WHERE codigo_media = ?");
             return ["status" => $stmt->execute([$id]) ? "success" : "error"];
         } catch (PDOException $e) {
             return ["status" => "error", "message" => $e->getMessage()];
         }
     }
 }