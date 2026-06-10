<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class clienteModel extends ConectDB {
    private $conex;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todos los clientes registrados.
     */
    public function getAllClientes() {
        try {
            $stmt = $this->conex->prepare("SELECT * FROM cliente ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Registra un nuevo cliente.
     */
    public function addCliente($datos) {
        try {
            $query = "INSERT INTO cliente (cedula_cliente, nombre, telefono, correo, direccion) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([
                $datos['cedula_cliente'],
                $datos['nombre'],
                $datos['telefono'],
                $datos['correo'],
                $datos['direccion']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualiza la información de un cliente.
     */
    public function updateCliente($idActual, $datos) {
        try {
            $query = "UPDATE cliente SET cedula_cliente = ?, nombre = ?, telefono = ?, correo = ?, direccion = ? 
                      WHERE cedula_cliente = ?";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([
                $datos['cedula_cliente'],
                $datos['nombre'],
                $datos['telefono'],
                $datos['correo'],
                $datos['direccion'],
                $idActual
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Elimina un cliente de la base de datos.
     */
    public function deleteCliente($id) {
        try {
            $stmt = $this->conex->prepare("DELETE FROM cliente WHERE cedula_cliente = ?");
            return ["status" => $stmt->execute([$id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}