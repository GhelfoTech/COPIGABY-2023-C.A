<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class empresaModel extends ConectDB {
    private $conex;

    private $codigo_empresa;
    private $rif_empresa;
    private $nombre_empresa;
    private $telefono;
    private $correo;
    private $direccion;
    private $logo;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todas las empresas registradas.
     */
    public function getAllEmpresas() {
        try {
            $query = "SELECT codigo_empresa, rif_empresa, nombre_empresa, telefono, correo, direccion, logo
                      FROM empresa ORDER BY codigo_empresa DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtiene una empresa por su código.
     */
    public function getEmpresaById($id) {
        try {
            $query = "SELECT codigo_empresa, rif_empresa, nombre_empresa, telefono, correo, direccion, logo
                      FROM empresa WHERE codigo_empresa = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([(int) $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Registra una nueva empresa.
     */
    public function addEmpresa($datos) {
        try {
            $query = "INSERT INTO empresa (rif_empresa, nombre_empresa, telefono, correo, direccion, logo)
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conex->prepare($query);
            $success = $stmt->execute([
                $datos['rif_empresa'],
                $datos['nombre_empresa'],
                $datos['telefono'],
                $datos['correo'],
                $datos['direccion'],
                $datos['logo']
            ]);
            return $success
                ? ["status" => "success", "message" => "Empresa registrada con éxito."]
                : ["status" => "error", "message" => "No se pudo registrar la empresa."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Actualiza los datos de una empresa existente.
     */
    public function updateEmpresa($id, $datos) {
        try {
            $query = "UPDATE empresa SET rif_empresa = ?, nombre_empresa = ?, telefono = ?,
                      correo = ?, direccion = ?, logo = ? WHERE codigo_empresa = ?";
            $stmt = $this->conex->prepare($query);
            $success = $stmt->execute([
                $datos['rif_empresa'],
                $datos['nombre_empresa'],
                $datos['telefono'],
                $datos['correo'],
                $datos['direccion'],
                $datos['logo'],
                (int) $id
            ]);
            return $success
                ? ["status" => "success", "message" => "Empresa actualizada."]
                : ["status" => "error", "message" => "No se pudo actualizar la empresa."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Elimina una empresa de forma permanente.
     */
    public function deleteEmpresa($id) {
        try {
            $stmt = $this->conex->prepare("DELETE FROM empresa WHERE codigo_empresa = ?");
            return ["status" => $stmt->execute([(int) $id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
