<?php

namespace App\models;

use App\config\ConectDB;
use App\traits\RolTrait;
use PDO;
use PDOException;

class rolModel extends ConectDB {
    use RolTrait;
    private $conex;

    private $codigo_rol;
    private $nombre_rol;
    private $descripcion;
    private $estado;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    public function getAllRoles() {
        try {
            $stmt = $this->conex->prepare("SELECT * FROM rol ORDER BY codigo_rol DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function addRol($datos) {
        $validacion = $this->validarRol(array_merge($datos, ['estado' => 1]));
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $query = "INSERT INTO rol (nombre_rol, descripcion, estado) VALUES (?, ?, 1)";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([
                trim($datos['nombre_rol']),
                trim($datos['descripcion'])
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateRol($id, $datos) {
        $datos['codigo_rol'] = $id;
        $validacion = $this->validarRol($datos);
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $query = "UPDATE rol SET nombre_rol = ?, descripcion = ?, estado = ? WHERE codigo_rol = ?";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([
                trim($datos['nombre_rol']),
                trim($datos['descripcion']),
                (int) $datos['estado'],
                (int) $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteRol($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE rol SET estado = 0 WHERE codigo_rol = ?");
            $success = $stmt->execute([(int) $id]);
            return ["status" => $success ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}