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
     * Obtiene todos los servicios registrados.
     */
    public function getAllServices() {
        try {
            $query = "SELECT * FROM servicio ORDER BY codigo_servicio DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Registra un servicio y sus materiales asociados en una transacción.
     */
    public function addService(array $datos, array $materiales = []) {
        try {
            $this->conex->beginTransaction();

            $query = "INSERT INTO servicio (nombre_servicio, descripcion, precio, tipo_servicio, estado)  
                      VALUES (:nombre, :descripcion, :precio, :tipo, 1)";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([
                ':nombre'      => $datos['nombre_servicio'],
                ':descripcion' => $datos['descripcion'],
                ':precio'      => $datos['precio'],
                ':tipo'        => $datos['tipo_servicio']
            ]);

            $idServicio = $this->conex->lastInsertId();

            if (!empty($materiales)) {
                $queryMat = "INSERT INTO servicio_material (codigo_servicio, codigo_producto, cantidad_usada) 
                             VALUES (:id, :prod, :cant)";
                $stmtMat = $this->conex->prepare($queryMat);
                foreach ($materiales as $mat) {
                    $stmtMat->execute([
                        ':id'   => $idServicio,
                        ':prod' => $mat['codigo_producto'],
                        ':cant' => $mat['cantidad_usada']
                    ]);
                }
            }

            $this->conex->commit();
            return ['status' => 'success', 'message' => 'Servicio registrado correctamente'];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) $this->conex->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Actualiza el servicio y sincroniza los materiales.
     */
    public function updateService(int $id, array $datos, array $materiales = []) {
        try {
            $this->conex->beginTransaction();

            $query = "UPDATE servicio SET nombre_servicio = :nombre, descripcion = :descripcion, 
                      precio = :precio, tipo_servicio = :tipo, estado = :estado 
                      WHERE codigo_servicio = :id";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([
                ':nombre'      => $datos['nombre_servicio'],
                ':descripcion' => $datos['descripcion'],
                ':precio'      => $datos['precio'],
                ':tipo'        => $datos['tipo_servicio'],
                ':estado'      => $datos['estado'],
                ':id'          => $id
            ]);

            // Sincronización de materiales: Borrar y re-insertar
            $this->conex->prepare("DELETE FROM servicio_material WHERE codigo_servicio = ?")->execute([$id]);

            if (!empty($materiales)) {
                $queryMat = "INSERT INTO servicio_material (codigo_servicio, codigo_producto, cantidad_usada) 
                             VALUES (?, ?, ?)";
                $stmtMat = $this->conex->prepare($queryMat);
                foreach ($materiales as $mat) {
                    $stmtMat->execute([$id, $mat['codigo_producto'], $mat['cantidad_usada']]);
                }
            }

            $this->conex->commit();
            return ['status' => 'success', 'message' => 'Servicio actualizado correctamente'];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) $this->conex->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Desactivación lógica.
     */
    public function deleteService(int $id) {
        try {
            $stmt = $this->conex->prepare("UPDATE servicio SET estado = 0 WHERE codigo_servicio = ?");
            return ['status' => $stmt->execute([$id]) ? 'success' : 'error'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtiene un servicio con sus materiales para la edición AJAX.
     */
    public function getServiceWithMaterials(int $id) {
        try {
            $stmt = $this->conex->prepare("SELECT * FROM servicio WHERE codigo_servicio = ?");
            $stmt->execute([$id]);
            $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($servicio) {
                $queryMat = "SELECT sm.*, p.nombre_producto 
                             FROM servicio_material sm
                             INNER JOIN producto_insumo p ON sm.codigo_producto = p.codigo_producto
                             WHERE sm.codigo_servicio = ?";
                $stmtMat = $this->conex->prepare($queryMat);
                $stmtMat->execute([$id]);
                $servicio['materiales'] = $stmtMat->fetchAll(PDO::FETCH_ASSOC);
            }
            return $servicio;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Productos disponibles para asignar como materiales.
     */
    public function getProductosDisponibles() {
        try {
            $stmt = $this->conex->prepare("SELECT codigo_producto, nombre_producto, stock_actual FROM producto_insumo WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}