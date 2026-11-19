<?php

namespace App\models;

use App\config\ConectDB;
use App\traits\ServiciosTrait;
use PDO;
use PDOException;

class servicioModel extends ConectDB {
    use ServiciosTrait;
    private $conex;

    private $codigo_servicio;
    private $nombre_servicio;
    private $descripcion;
    private $precio;
    private $estado;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    public function getAllServicios() {
        try {
            $stmt = $this->conex->prepare("SELECT * FROM servicio ORDER BY codigo_servicio DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getServiciosDataTable(array $request): array {
        $draw = filter_var($request['draw'] ?? 0, FILTER_VALIDATE_INT);
        $draw = $draw === false ? 0 : max(0, $draw);

        $start = filter_var($request['start'] ?? 0, FILTER_VALIDATE_INT);
        $start = $start === false ? 0 : max(0, $start);

        $length = filter_var($request['length'] ?? 10, FILTER_VALIDATE_INT);
        $length = in_array($length, [10, 25, 50], true) ? $length : 10;

        $searchRequest = $request['search'] ?? [];
        $searchValue = is_array($searchRequest) ? ($searchRequest['value'] ?? '') : '';
        $search = is_scalar($searchValue) ? substr(trim((string) $searchValue), 0, 100) : '';

        $whereSql = '';
        $parametros = [];
        if ($search !== '') {
            $whereSql = " WHERE (
                CAST(codigo_servicio AS CHAR) LIKE ?
                OR nombre_servicio LIKE ?
                OR descripcion LIKE ?
                OR CAST(precio AS CHAR) LIKE ?
                OR CASE WHEN estado = 1 THEN 'Activo' ELSE 'Inactivo' END LIKE ?
            )";
            $parametros = array_fill(0, 5, '%' . $search . '%');
        }

        $orderColumns = [
            0 => 'codigo_servicio',
            1 => 'nombre_servicio',
            2 => 'descripcion',
            3 => 'precio',
            4 => 'estado',
        ];
        $orderBy = [];
        $orders = is_array($request['order'] ?? null) ? $request['order'] : [];
        foreach ($orders as $order) {
            if (!is_array($order)) {
                continue;
            }

            $column = filter_var($order['column'] ?? null, FILTER_VALIDATE_INT);
            if ($column === false || !isset($orderColumns[$column])) {
                continue;
            }

            $direction = strtolower((string) ($order['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
            $orderBy[] = $orderColumns[$column] . ' ' . $direction;
        }
        if (!$orderBy) {
            $orderBy[] = 'codigo_servicio DESC';
        }

        try {
            $recordsTotal = (int) $this->conex->query('SELECT COUNT(*) FROM servicio')->fetchColumn();
            $recordsFiltered = $recordsTotal;
            if ($search !== '') {
                $stmtFiltered = $this->conex->prepare('SELECT COUNT(*) FROM servicio' . $whereSql);
                $stmtFiltered->execute($parametros);
                $recordsFiltered = (int) $stmtFiltered->fetchColumn();
            }

            $stmt = $this->conex->prepare(
                'SELECT codigo_servicio, nombre_servicio, descripcion, precio, estado
                 FROM servicio'
                . $whereSql
                . ' ORDER BY ' . implode(', ', $orderBy)
                . ' LIMIT ? OFFSET ?'
            );
            $indice = 1;
            foreach ($parametros as $parametro) {
                $stmt->bindValue($indice++, $parametro, PDO::PARAM_STR);
            }
            $stmt->bindValue($indice++, $length, PDO::PARAM_INT);
            $stmt->bindValue($indice, $start, PDO::PARAM_INT);
            $stmt->execute();

            return [
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            ];
        } catch (PDOException $e) {
            error_log('[servicioModel::getServiciosDataTable] ' . $e->getMessage());
            return [
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'No se pudieron cargar los servicios.',
            ];
        }
    }

    public function addServicio($datos, $materiales = []) {
        $validacion = $this->validarServicio(array_merge($datos, ['estado' => 1]));
        if ($validacion !== true) {
            return $validacion;
        }
        $validacionMateriales = $this->validarMateriales($materiales);
        if ($validacionMateriales !== true) {
            return $validacionMateriales;
        }

        try {
            $this->conex->beginTransaction();

            $query = "INSERT INTO servicio (nombre_servicio, descripcion, precio, estado) VALUES (?, ?, ?, 1)";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([
                trim($datos['nombre_servicio']),
                trim($datos['descripcion']),
                round((float) $datos['precio'], 2)
            ]);

            $idServicio = $this->conex->lastInsertId();

            if (!empty($materiales)) {
                $queryMat = "INSERT INTO servicio_material (codigo_producto, codigo_servicio, cantidad_usada) VALUES (?, ?, ?)";
                $stmtMat = $this->conex->prepare($queryMat);
                foreach ($materiales as $mat) {
                    $stmtMat->execute([$mat['codigo_producto'], $idServicio, $mat['cantidad_usada']]);
                }
            }

            $this->conex->commit();
            return ["status" => "success"];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) $this->conex->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function updateServicio($id, $datos, $materiales = []) {
        $datos['codigo_servicio'] = $id;
        $validacion = $this->validarServicio($datos);
        if ($validacion !== true) {
            return $validacion;
        }
        $validacionMateriales = $this->validarMateriales($materiales);
        if ($validacionMateriales !== true) {
            return $validacionMateriales;
        }

        try {
            $this->conex->beginTransaction();

            $query = "UPDATE servicio SET nombre_servicio = ?, descripcion = ?, precio = ?, estado = ? WHERE codigo_servicio = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([
                trim($datos['nombre_servicio']),
                trim($datos['descripcion']),
                round((float) $datos['precio'], 2),
                $datos['estado'],
                $id
            ]);

            // Actualizar materiales: eliminamos los anteriores e insertamos los nuevos
            $stmtDel = $this->conex->prepare("DELETE FROM servicio_material WHERE codigo_servicio = ?");
            $stmtDel->execute([$id]);

            if (!empty($materiales)) {
                $queryMat = "INSERT INTO servicio_material (codigo_producto, codigo_servicio, cantidad_usada) VALUES (?, ?, ?)";
                $stmtMat = $this->conex->prepare($queryMat);
                foreach ($materiales as $mat) {
                    $stmtMat->execute([$mat['codigo_producto'], $id, $mat['cantidad_usada']]);
                }
            }

            $this->conex->commit();
            return ["status" => "success"];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) $this->conex->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function deleteServicio($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE servicio SET estado = 0 WHERE codigo_servicio = ?");
            return ["status" => $stmt->execute([$id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function getMaterialesByServicio($id) {
        try {
            $query = "SELECT sm.*, p.nombre_producto 
                      FROM servicio_material sm 
                      INNER JOIN producto_insumo p ON sm.codigo_producto = p.codigo_producto 
                      WHERE sm.codigo_servicio = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    private function validarMateriales(array $materiales) {
        foreach ($materiales as $index => $material) {
            if (!is_array($material)) {
                return ['status' => 'error', 'message' => 'Los datos de los materiales no son válidos'];
            }
            $cantidad = $material['cantidad_usada'] ?? null;
            if (
                !isset($material['codigo_producto'])
                || (int) $material['codigo_producto'] <= 0
                || !is_numeric($cantidad)
                || !is_finite((float) $cantidad)
                || (float) $cantidad <= 0
                || floor((float) $cantidad) !== (float) $cantidad
            ) {
                $linea = (int) $index + 1;
                return ['status' => 'error', 'message' => "La cantidad de material debe ser un número entero mayor a cero en la línea {$linea}"];
            }
        }
        return true;
    }
}