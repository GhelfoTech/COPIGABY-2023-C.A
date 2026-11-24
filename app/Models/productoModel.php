<?php

namespace App\models;

use App\config\ConectDB;
use App\traits\ProductosTrait;
use PDO;
use PDOException;

class productoModel extends ConectDB {
    use ProductosTrait;
    private $conex;

    private $codigo_producto;
    private $nombre_producto;
    private $codigo_categoria;
    private $codigo_media;
    private $descripcion;
    private $costo;
    private $porcentaje_ganancia;
    private $precio;
    private $stock_actual;
    private $stock_minimo;
    private $estado;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    public function getAllProducts() {
        try {
            $query = "SELECT p.*, c.nombre_categoria,
                COALESCE(p.costo, 0) AS costo,
                COALESCE(
                    (SELECT dc.costo_unitario FROM detalle_compra dc
                     WHERE dc.codigo_producto = p.codigo_producto
                     ORDER BY dc.codigo_compra DESC LIMIT 1),
                    0) AS costo_compra,
                COALESCE(p.porcentaje_ganancia, 0) AS porcentaje_ganancia,
                ROUND(COALESCE(
                    (SELECT dc.costo_unitario FROM detalle_compra dc
                     WHERE dc.codigo_producto = p.codigo_producto
                     ORDER BY dc.codigo_compra DESC LIMIT 1),
                    p.costo, 0) * (1 + COALESCE(p.porcentaje_ganancia, 0) / 100), 2) AS precio
                FROM producto_insumo p
                LEFT JOIN categoria c ON p.codigo_categoria = c.codigo_categoria";

            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getProductsDataTable(array $request): array {
        $draw = filter_var($request['draw'] ?? 0, FILTER_VALIDATE_INT);
        $draw = $draw === false ? 0 : max(0, $draw);

        $start = filter_var($request['start'] ?? 0, FILTER_VALIDATE_INT);
        $start = $start === false ? 0 : max(0, $start);

        $length = filter_var($request['length'] ?? 10, FILTER_VALIDATE_INT);
        $length = in_array($length, [10, 25, 50], true) ? $length : 10;

        $searchRequest = $request['search'] ?? [];
        $searchValue = is_array($searchRequest) ? ($searchRequest['value'] ?? '') : '';
        $search = is_scalar($searchValue) ? trim((string) $searchValue) : '';
        $search = substr($search, 0, 100);
    
        $precioSql = 'ROUND(COALESCE(ultima_compra.costo_unitario, p.costo, 0)
            * (1 + COALESCE(p.porcentaje_ganancia, 0) / 100), 2)';
        $fromSql = ' FROM producto_insumo p
            LEFT JOIN categoria c ON c.codigo_categoria = p.codigo_categoria
            LEFT JOIN (
                SELECT dc.codigo_producto, dc.costo_unitario
                FROM detalle_compra dc
                INNER JOIN (
                    SELECT codigo_producto, MAX(codigo_compra) AS codigo_compra
                    FROM detalle_compra
                    GROUP BY codigo_producto
                ) ultima ON ultima.codigo_producto = dc.codigo_producto
                    AND ultima.codigo_compra = dc.codigo_compra
            ) ultima_compra ON ultima_compra.codigo_producto = p.codigo_producto';

        $whereSql = '';
        $parametros = [];
        if ($search !== '') {
            $whereSql = " WHERE (
                p.nombre_producto LIKE ?
                OR CAST(p.codigo_producto AS CHAR) LIKE ?
                OR c.nombre_categoria LIKE ?
                OR CAST(p.stock_actual AS CHAR) LIKE ?
                OR CAST(p.stock_minimo AS CHAR) LIKE ?
                OR CAST(COALESCE(p.porcentaje_ganancia, 0) AS CHAR) LIKE ?
                OR CAST({$precioSql} AS CHAR) LIKE ?
                OR CASE WHEN p.estado = 1 THEN 'Activo' ELSE 'Inactivo' END LIKE ?
            )";
            $parametros = array_fill(0, 8, '%' . $search . '%');
        }

        $orderColumns = [
            0 => 'p.nombre_producto',
            1 => 'c.nombre_categoria',
            2 => 'p.stock_actual',
            3 => 'precio',
            4 => 'p.estado',
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
            $orderBy[] = 'p.codigo_producto DESC';
        }

        try {
            $stmtTotal = $this->conex->query('SELECT COUNT(*) FROM producto_insumo');
            $recordsTotal = (int) $stmtTotal->fetchColumn();

            $recordsFiltered = $recordsTotal;
            if ($search !== '') {
                $stmtFiltered = $this->conex->prepare('SELECT COUNT(*)' . $fromSql . $whereSql);
                $stmtFiltered->execute($parametros);
                $recordsFiltered = (int) $stmtFiltered->fetchColumn();
            }

            $query = 'SELECT p.*, c.nombre_categoria,
                    COALESCE(p.costo, 0) AS costo,
                    COALESCE(ultima_compra.costo_unitario, 0) AS costo_compra,
                    COALESCE(p.porcentaje_ganancia, 0) AS porcentaje_ganancia,
                    ' . $precioSql . ' AS precio'
                . $fromSql
                . $whereSql
                . ' ORDER BY ' . implode(', ', $orderBy)
                . ' LIMIT ? OFFSET ?';
            $stmt = $this->conex->prepare($query);
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
            error_log('[productoModel::getProductsDataTable] ' . $e->getMessage());
            return [
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'No se pudieron cargar los productos.',
            ];
        }
    }

    public function calculatePrice($costo, $porcentajeGanancia) {
        $costo = floatval($costo);
        $porcentajeGanancia = floatval($porcentajeGanancia);
        return round($costo * (1 + $porcentajeGanancia / 100), 2);
    }

    public function getCategories() {
        try {
            $stmt = $this->conex->prepare("SELECT codigo_categoria, nombre_categoria FROM categoria WHERE estado = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function addProduct($datos) {
        $validacion = $this->validarProducto(array_merge($datos, ['stock_actual' => $datos['stock_actual'] ?? 0, 'stock_minimo' => $datos['stock_minimo'] ?? 0]));
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $checkMedia = $this->conex->prepare("SHOW COLUMNS FROM producto_insumo LIKE 'codigo_media'");
            $checkMedia->execute();
            $hasMedia = $checkMedia->rowCount() > 0;

            if ($hasMedia && !empty($datos['codigo_media'])) {
                $query = "INSERT INTO producto_insumo (nombre_producto, codigo_categoria, codigo_media, descripcion, porcentaje_ganancia, stock_actual, stock_minimo, estado)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
                $stmt = $this->conex->prepare($query);
                $stmt->execute([
                    $datos['nombre_producto'],
                    $datos['codigo_categoria'],
                    (int) $datos['codigo_media'],
                    $datos['descripcion'],
                    // La ganancia se maneja por unidad (enteros): 1, 2, 3...
                    round(floatval($datos['porcentaje_ganancia'] ?? 0)),
                    intval($datos['stock_actual'] ?? 0),
                    intval($datos['stock_minimo'] ?? 0)
                ]);
            } else {
                $query = "INSERT INTO producto_insumo (nombre_producto, codigo_categoria, descripcion, porcentaje_ganancia, stock_actual, stock_minimo, estado)
                    VALUES (?, ?, ?, ?, ?, ?, 1)";
                $stmt = $this->conex->prepare($query);
                $stmt->execute([
                    $datos['nombre_producto'],
                    $datos['codigo_categoria'],
                    $datos['descripcion'],
                    // La ganancia se maneja por unidad (enteros): 1, 2, 3...
                    round(floatval($datos['porcentaje_ganancia'] ?? 0)),
                    intval($datos['stock_actual'] ?? 0),
                    intval($datos['stock_minimo'] ?? 0)
                ]);
            }

            $codigoProducto = (int) $this->conex->lastInsertId();
            $this->calcularYGuardarPrecio($codigoProducto);
            return ["status" => "success", "codigo_producto" => $codigoProducto];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function updateProduct($id, $datos) {
        $datos['codigo_producto'] = $id;
        $validacion = $this->validarProducto(array_merge($datos, ['stock_actual' => $datos['stock_actual'] ?? 0, 'stock_minimo' => $datos['stock_minimo'] ?? 0]));
        if ($validacion !== true) {
            return $validacion;
        }

        try {
            $checkMedia = $this->conex->prepare("SHOW COLUMNS FROM producto_insumo LIKE 'codigo_media'");
            $checkMedia->execute();
            $hasMedia = $checkMedia->rowCount() > 0;

            if ($hasMedia) {
                $codigoMedia = !empty($datos['codigo_media']) ? (int) $datos['codigo_media'] : null;
                $query = "UPDATE producto_insumo SET nombre_producto = ?, codigo_categoria = ?, codigo_media = ?, descripcion = ?, porcentaje_ganancia = ?, stock_actual = ?, stock_minimo = ?, estado = ? WHERE codigo_producto = ?";
                $stmt = $this->conex->prepare($query);
                $stmt->execute([
                    $datos['nombre_producto'],
                    $datos['codigo_categoria'],
                    $codigoMedia,
                    $datos['descripcion'],
                    // La ganancia se maneja por unidad (enteros): 1, 2, 3...
                    round(floatval($datos['porcentaje_ganancia'] ?? 0)),
                    intval($datos['stock_actual'] ?? 0),
                    intval($datos['stock_minimo'] ?? 0),
                    intval($datos['estado'] ?? 1),
                    $id
                ]);
            } else {
                $query = "UPDATE producto_insumo SET nombre_producto = ?, codigo_categoria = ?, descripcion = ?, porcentaje_ganancia = ?, stock_actual = ?, stock_minimo = ?, estado = ? WHERE codigo_producto = ?";
                $stmt = $this->conex->prepare($query);
                $stmt->execute([
                    $datos['nombre_producto'],
                    $datos['codigo_categoria'],
                    $datos['descripcion'],
                    // La ganancia se maneja por unidad (enteros): 1, 2, 3...
                    round(floatval($datos['porcentaje_ganancia'] ?? 0)),
                    intval($datos['stock_actual'] ?? 0),
                    intval($datos['stock_minimo'] ?? 0),
                    intval($datos['estado'] ?? 1),
                    $id
                ]);
            }

            $this->calcularYGuardarPrecio($id);
            return ["status" => "success"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function deleteProduct($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE producto_insumo SET estado = 0 WHERE codigo_producto = ?");
            return ["status" => $stmt->execute([$id]) ? "success" : "error"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Calcula el precio de venta (costo de la última compra o costo manual
    // multiplicado por (1 + porcentaje_ganancia/100)) y lo guarda en la
    // columna precio del producto para que otros módulos lo consulten directo.
    public function calcularYGuardarPrecio(int $codigoProducto): void {
        try {
            $stmt = $this->conex->prepare(
                "UPDATE producto_insumo p
                    SET precio = ROUND(COALESCE(
                            (SELECT dc.costo_unitario FROM detalle_compra dc
                             WHERE dc.codigo_producto = p.codigo_producto
                             ORDER BY dc.codigo_compra DESC LIMIT 1),
                            p.costo, 0) * (1 + COALESCE(p.porcentaje_ganancia, 0) / 100), 2)
                 WHERE codigo_producto = ?"
            );
            $stmt->execute([$codigoProducto]);
        } catch (PDOException $e) {
            // Si falla el recálculo, se ignora para no bloquear la operación principal
        }
    }

    // Recalcula y guarda el precio de varios productos a la vez.
    public function recalcularPrecios(array $codigosProducto): void {
        foreach (array_unique($codigosProducto) as $codigo) {
            $this->calcularYGuardarPrecio((int) $codigo);
        }
    }
}
