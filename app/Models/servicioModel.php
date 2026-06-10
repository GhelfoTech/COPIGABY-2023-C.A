<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class servicioModel extends ConectDB {
    private $conex;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todos los servicios registrados en la base de datos.
     */
    public function getAllServices() {
        try {
            $stmt = $this->conex->prepare("SELECT * FROM servicio ORDER BY codigo_servicio DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Registra un nuevo servicio.
     */
    public function addService($datos) {
        try {
            $query = "INSERT INTO servicio (nombre_servicio, descripcion, precio, tipo_servicio, estado) 
                      VALUES (?, ?, ?, ?, 1)";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([
                $datos['nombre_servicio'],
                $datos['descripcion'],
                $datos['precio'],
                $datos['tipo_servicio']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualiza un servicio existente.
     */
    public function updateService($id, $datos) {
        try {
            $query = "UPDATE servicio SET nombre_servicio = ?, descripcion = ?, precio = ?, tipo_servicio = ?, estado = ? 
                      WHERE codigo_servicio = ?";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([
                $datos['nombre_servicio'],
                $datos['descripcion'],
                $datos['precio'],
                $datos['tipo_servicio'],
                $datos['estado'],
                $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Eliminación lógica del servicio (cambio de estado).
     */
    public function deleteService($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE servicio SET estado = 0 WHERE codigo_servicio = ?");
            return ["status" => $stmt->execute([$id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}