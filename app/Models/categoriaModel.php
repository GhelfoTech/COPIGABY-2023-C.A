<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class categoriaModel extends ConectDB {
    private $codigo_categoria;
    private $nombre_categoria;
    private $estado;
    private $conex;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todas las categorías.
     */
    public function getAllCategories() {
        try {
            $consult = $this->conex->prepare("SELECT * FROM categoria");
            $consult->execute();
            return $consult->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Registra una nueva categoría.
     */
    public function addCategory(string $nombre) {
        try {
            $query = "INSERT INTO categoria (nombre_categoria, estado) VALUES (?, 1)";
            $stmt = $this->conex->prepare($query);
            $stmt->bindValue(1, $nombre);
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Categoría registrada exitosamente"];
            }
            return ["status" => "error", "message" => "Error al registrar"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Actualiza una categoría existente.
     */
    public function updateCategory(int $id, string $nombre, int $estado) {
        try {
            $query = "UPDATE categoria SET nombre_categoria = ?, estado = ? WHERE codigo_categoria = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->bindValue(1, $nombre);
            $stmt->bindValue(2, $estado);
            $stmt->bindValue(3, $id);
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Categoría actualizada con éxito"];
            }
            return ["status" => "error", "message" => "No se pudo actualizar"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Eliminación lógica de la categoría (cambio de estado).
     */
    public function deleteCategory(int $id) {
        try {
            $query = "UPDATE categoria SET estado = 0 WHERE codigo_categoria = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->bindValue(1, $id);
            return ["status" => $stmt->execute() ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}

   
