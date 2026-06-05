<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class compraModel extends ConectDB {
    private $conex;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todas las compras registradas con información de proveedor y usuario.
     */
    public function getAllCompras() {
        try {
            $query = "SELECT c.*, p.razon_social AS nombre_proveedor, u.nombre_usuario 
                      FROM compra c
                      INNER JOIN proveedor p ON c.codigo_proveedor = p.codigo_proveedor
                      INNER JOIN usuario u ON c.codigo_usuario = u.codigo_usuario
                      ORDER BY c.codigo_compra DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Registra el encabezado de una nueva compra.
     */
    public function addCompra($datos) {
        try {
            $query = "INSERT INTO compra (codigo_proveedor, codigo_usuario, numero_factura_proveedor, fecha_compra, monto_total, estado) 
                      VALUES (?, ?, ?, ?, ?, 1)";
            $stmt = $this->conex->prepare($query);
            return $stmt->execute([
                $datos['codigo_proveedor'],
                $datos['codigo_usuario'],
                $datos['numero_factura_proveedor'],
                $datos['fecha_compra'],
                $datos['monto_total']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualiza los datos modificables de una compra.
     */
    public function updateCompra(int $id, array $datos) {
        try {
            $query = "UPDATE compra SET codigo_proveedor = ?, numero_factura_proveedor = ?,
                      fecha_compra = ?, monto_total = ?, estado = ?
                      WHERE codigo_compra = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->bindValue(1, (int) $datos['codigo_proveedor'], PDO::PARAM_INT);
            $stmt->bindValue(2, $datos['numero_factura_proveedor']);
            $stmt->bindValue(3, $datos['fecha_compra']);
            $stmt->bindValue(4, $datos['monto_total']);
            $stmt->bindValue(5, (int) $datos['estado'], PDO::PARAM_INT);
            $stmt->bindValue(6, $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Compra actualizada con éxito"];
            }
            return ["status" => "error", "message" => "No se pudo actualizar"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Anulación lógica de una compra.
     */
    public function deleteCompra($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE compra SET estado = 0 WHERE codigo_compra = ?");
            return ["status" => $stmt->execute([$id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Obtiene proveedores para el selector del modal.
     */
    public function getProviders() {
        try {
            $stmt = $this->conex->prepare("SELECT codigo_proveedor, razon_social FROM proveedor WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }
}