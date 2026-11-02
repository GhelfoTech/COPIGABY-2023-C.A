<?php

namespace App\models;

use App\config\ConectDB;
use App\traits\ClientesTrait;
use PDO;
use PDOException;

class clienteModel extends ConectDB {
    use ClientesTrait;
    private $conex;

    private $cedula_cliente;
    private $nombre;
    private $telefono;
    private $correo;
    private $direccion;
    private $estado;

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
        $validacion = $this->validarCliente(array_merge($datos, ['estado' => 1]));
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $query = "INSERT INTO cliente (cedula_cliente, nombre, telefono, correo, direccion, estado)
                      VALUES (?, ?, ?, ?, ?, 1)";
            $stmt = $this->conex->prepare($query);
            $ejecutado = $stmt->execute([
                $datos['cedula_cliente'],
                $datos['nombre'],
                $datos['telefono'],
                trim((string) ($datos['correo'] ?? '')),
                trim((string) ($datos['direccion'] ?? ''))
            ]);
            return $ejecutado
                ? ['status' => 'success']
                : ['status' => 'error', 'message' => 'No se pudo registrar el cliente'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Actualiza la información de un cliente.
     */
    public function updateCliente($idActual, $datos) {
        $datos['cedula_cliente_actual'] = $idActual;
        $validacion = $this->validarCliente($datos);
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $query = "UPDATE cliente SET cedula_cliente = ?, nombre = ?, telefono = ?, correo = ?, direccion = ?, estado = ? 
                      WHERE cedula_cliente = ?";
            $stmt = $this->conex->prepare($query);
            $ejecutado = $stmt->execute([
                $datos['cedula_cliente'],
                $datos['nombre'],
                $datos['telefono'],
                $datos['correo'],
                $datos['direccion'],
                (int) $datos['estado'],
                $idActual
            ]);
            return $ejecutado
                ? ['status' => 'success']
                : ['status' => 'error', 'message' => 'No se pudo actualizar el cliente'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Desactivación lógica (borrado) de un cliente.
     */
    public function deleteCliente($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE cliente SET estado = 0 WHERE cedula_cliente = ?");
            return ["status" => $stmt->execute([$id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}