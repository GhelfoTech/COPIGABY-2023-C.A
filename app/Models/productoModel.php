<?php

namespace App\models;

use App\config\ConectDB;
use PDO;
use PDOException;

class productoModel extends ConectDB {
    private $conex;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Obtiene todos los productos con sus relaciones.
     */
    public function getAllProducts() {
        try {
            $query = "SELECT p.*, c.nombre_categoria, i.porcentaje_iva, um.nombre AS nombre_medida
                      FROM producto_insumo p
                      INNER JOIN categoria c ON p.codigo_categoria = c.codigo_categoria
                      INNER JOIN iva i ON p.codigo_IVA = i.codigo_IVA
                      INNER JOIN unidad_medida um ON p.codigo_medida = um.codigo_media";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtiene categorías activas para el select.
     */
    public function getCategories() {
        try {
            $stmt = $this->conex->prepare("SELECT codigo_categoria, nombre_categoria FROM categoria WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    /**
     * Obtiene IVAs para el select.
     */
    public function getIvas() {
        try {
            $stmt = $this->conex->prepare("SELECT codigo_IVA, porcentaje_iva FROM iva WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    /**
     * Obtiene unidades de medida activas para el select.
     */
    public function getMedidas() {
        try {
            $stmt = $this->conex->prepare("SELECT codigo_media, nombre FROM unidad_medida WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    /**
     * Registra un nuevo producto.
     */
    public function addProduct($datos) {
        try {
            $query = "INSERT INTO producto_insumo (nombre_producto, codigo_IVA, codigo_categoria, codigo_medida, descripcion, stock_actual, stock_minimo, costo, estado) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([
                $datos['nombre_producto'],
                $datos['codigo_IVA'],
                $datos['codigo_categoria'],
                $datos['codigo_medida'],
                $datos['descripcion'],
                $datos['stock_actual'],
                $datos['stock_minimo'],
                $datos['costo']
            ]);
            return ["status" => "success"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Actualiza un producto.
     */
    public function updateProduct($id, $datos) {
        try {
            $query = "UPDATE producto_insumo SET nombre_producto = ?, codigo_IVA = ?, codigo_categoria = ?, codigo_medida = ?, descripcion = ?, 
                      stock_actual = ?, stock_minimo = ?, costo = ?, estado = ? WHERE codigo_producto = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([
                $datos['nombre_producto'],
                $datos['codigo_IVA'],
                $datos['codigo_categoria'],
                $datos['codigo_medida'],
                $datos['descripcion'],
                $datos['stock_actual'],
                $datos['stock_minimo'],
                $datos['costo'],
                $datos['estado'],
                $id
            ]);
            return ["status" => "success"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Borrado lógico.
     */
    public function deleteProduct($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE producto_insumo SET estado = 0 WHERE codigo_producto = ?");
            return ["status" => $stmt->execute([$id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}